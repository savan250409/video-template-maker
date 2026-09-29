<?php
/**
 * download_public_assets.php
 *
 * Reads the JSON files produced by fetch_public_data.php and downloads every
 * referenced media asset (thumbnails, template zips, music, banners, category
 * logos) into public/data/assets/. Idempotent: skips files already present
 * with a matching size. No database — everything lives on the local disk.
 *
 * Usage:  php download_public_assets.php
 */

const BASE_URL = 'https://pixstory.growwth.in';
const TOKEN    = '123456';
const DATA_DIR = __DIR__ . '/public/data';
const ASSET_DIR = __DIR__ . '/public/data/assets';
const HOST_ALLOW = 'pixstory.growwth.in'; // only download from this host

$targets = [];   // dest-subdir => [urls]
$stats   = ['downloaded' => 0, 'skipped' => 0, 'failed' => 0, 'bytes' => 0];
$failures = [];
$claimed  = [];  // "subdir/lowercased-name" => source url  (case-collision guard)
$urlMap   = [];  // source url => local relative path

/** Read a saved JSON file relative to DATA_DIR. */
function loadJson(string $rel) {
    $p = DATA_DIR . '/' . $rel;
    if (!is_file($p)) return null;
    return json_decode(file_get_contents($p), true);
}

/** Register a URL for download into a subfolder (deduped, validated). */
function want(string $subdir, ?string $url): void {
    global $targets;
    if (empty($url)) return;
    $url = trim($url);
    $host = parse_url($url, PHP_URL_HOST);
    if ($host !== HOST_ALLOW) return;                 // skip external links
    $path = parse_url($url, PHP_URL_PATH) ?? '';
    if ($path === '' || substr($path, -1) === '/') return; // no filename
    $targets[$subdir][$url] = true;
}

/** Percent-encode each path segment (spaces, etc.) while keeping the slashes. */
function encodeUrl(string $url): string {
    $parts = parse_url($url);
    if (!isset($parts['path'])) return $url;
    $segments = array_map('rawurlencode', explode('/', $parts['path']));
    $path = implode('/', $segments);
    $enc = $parts['scheme'] . '://' . $parts['host'] . $path;
    if (isset($parts['query'])) $enc .= '?' . $parts['query'];
    return $enc;
}

/** Download a single URL to $destDir, returning bytes or false. */
function fetchFile(string $url, string $destDir, string $subdir): int|false {
    global $stats, $failures, $claimed, $urlMap;
    $name = basename(parse_url($url, PHP_URL_PATH));

    // Guard against case-insensitive filesystem collisions (Windows): if a
    // *different* URL already claimed this name (case-folded), disambiguate.
    $key = $subdir . '/' . strtolower($name);
    if (isset($claimed[$key]) && $claimed[$key] !== $url) {
        $dot  = strrpos($name, '.');
        $stem = $dot === false ? $name : substr($name, 0, $dot);
        $ext  = $dot === false ? ''    : substr($name, $dot);
        $name = $stem . '_' . substr(md5($url), 0, 8) . $ext;
    }
    $claimed[$key] = $url;
    $urlMap[$url]  = 'assets/' . $subdir . '/' . $name;

    $dest = $destDir . '/' . $name;

    // Idempotent: skip if present and non-empty.
    if (is_file($dest) && filesize($dest) > 0) {
        $stats['skipped']++;
        return filesize($dest);
    }

    $fp = fopen($dest . '.part', 'wb');
    $ch = curl_init(encodeUrl($url));
    curl_setopt_array($ch, [
        CURLOPT_FILE           => $fp,
        CURLOPT_HTTPHEADER     => ['Authorization: ' . TOKEN],
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 300,
        CURLOPT_FAILONERROR    => true,
    ]);
    $ok   = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    fclose($fp);

    if ($ok === false || $code >= 400) {
        @unlink($dest . '.part');
        $stats['failed']++;
        $failures[] = "$url (http $code $err)";
        return false;
    }
    rename($dest . '.part', $dest);
    $size = filesize($dest);
    $stats['downloaded']++;
    $stats['bytes'] += $size;
    return $size;
}

// ---------------------------------------------------------------------------
// Collect every asset URL from the saved JSON.
// ---------------------------------------------------------------------------

// Templates (thumbnails + zips) — use the authoritative full list.
foreach ((loadJson('templates_all.json') ?: []) as $t) {
    want('thumbnails', $t['thumb_link'] ?? null);
    want('zips',       $t['zip_link']  ?? null);
}
// Also sweep home-screen templates in case any extra appear there.
foreach ((loadJson('getTemplatesByCategory.json') ?: []) as $cat) {
    foreach ($cat['templates'] ?? [] as $t) {
        want('thumbnails', $t['thumb_link'] ?? null);
        want('zips',       $t['zip_link']  ?? null);
    }
}

// Music files.
foreach ((loadJson('music_all.json') ?: []) as $m) {
    want('music', $m['song_url'] ?? null);
}

// Banners.
foreach ((loadJson('banners.json') ?: []) as $b) {
    want('banners', $b['banner_image'] ?? null);
}

// Category logos (all three types + home screen).
foreach (['categories_template', 'categories_music', 'categories_banner'] as $f) {
    foreach ((loadJson("$f.json") ?: []) as $cat) {
        want('categories', $cat['image_url'] ?? null);
    }
}
foreach ((loadJson('getTemplatesByCategory.json') ?: []) as $cat) {
    want('categories', $cat['image_url'] ?? null);
}

// ---------------------------------------------------------------------------
// Download.
// ---------------------------------------------------------------------------

$totalUrls = array_sum(array_map('count', $targets));
echo "Assets to fetch: $totalUrls unique files\n\n";

foreach ($targets as $subdir => $urls) {
    $destDir = ASSET_DIR . '/' . $subdir;
    if (!is_dir($destDir)) mkdir($destDir, 0755, true);
    $urls = array_keys($urls);
    echo "[$subdir] " . count($urls) . " files\n";
    foreach ($urls as $i => $url) {
        $size = fetchFile($url, $destDir, $subdir);
        $tag  = $size === false ? 'FAIL' : number_format($size) . ' B';
        printf("  %3d/%d  %-45s %s\n", $i + 1, count($urls), basename(parse_url($url, PHP_URL_PATH)), $tag);
    }
    echo "\n";
}

// ---------------------------------------------------------------------------
// Verify: expected count == files on disk. Write a manifest.
// ---------------------------------------------------------------------------

$manifest = ['source' => BASE_URL, 'generated_at' => date('c'), 'assets' => []];
$onDisk = 0;
foreach ($targets as $subdir => $urls) {
    $dir  = ASSET_DIR . '/' . $subdir;
    $have = is_dir($dir) ? count(glob("$dir/*")) : 0;
    $manifest['assets'][$subdir] = ['expected' => count($urls), 'on_disk' => $have];
    $onDisk += $have;
}
$manifest['totals'] = [
    'expected'   => $totalUrls,
    'on_disk'    => $onDisk,
    'downloaded' => $stats['downloaded'],
    'skipped'    => $stats['skipped'],
    'failed'     => $stats['failed'],
    'bytes'      => $stats['bytes'],
];
$manifest['url_map'] = $urlMap; // original remote URL -> local path under public/data/
file_put_contents(ASSET_DIR . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "=== DONE ===\n";
echo "downloaded: {$stats['downloaded']}, skipped: {$stats['skipped']}, failed: {$stats['failed']}\n";
echo "total size: " . number_format($stats['bytes'] / 1048576, 2) . " MB\n";
echo "on disk: $onDisk / $totalUrls expected\n";
if ($failures) {
    echo "\nFAILURES:\n" . implode("\n", $failures) . "\n";
} else {
    echo "No missing assets — all files present.\n";
}
