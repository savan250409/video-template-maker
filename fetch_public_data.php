<?php
/**
 * fetch_public_data.php
 *
 * Pulls every readable endpoint from the live server and stores the raw
 * responses as JSON files under public/data/ (publicly servable, no DB).
 *
 * Usage:  php fetch_public_data.php
 */

const BASE_URL = 'https://pixstory.growwth.in';
const TOKEN    = '123456';
const OUT_DIR  = __DIR__ . '/public/data';
const PAGE_LIMIT = 50;   // API caps limit at 50
const MAX_PAGES  = 500;  // safety guard

$summary = [];

/** Perform an authenticated GET and return [httpCode, decodedArray|null, rawBody]. */
function apiGet(string $path): array {
    $url = BASE_URL . $path;
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: ' . TOKEN, 'Accept: application/json'],
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    if ($body === false) {
        fwrite(STDERR, "  ! curl error for $path: $err\n");
        return [0, null, ''];
    }
    $decoded = json_decode($body, true);
    return [$code, $decoded, $body];
}

/** Save decoded data as pretty JSON and record a summary count. */
function save(string $name, $data, ?int $count = null): void {
    global $summary;
    $file = OUT_DIR . '/' . $name . '.json';
    file_put_contents(
        $file,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );
    $c = $count ?? (is_array($data) ? count($data) : 1);
    $summary[$name] = $c;
    echo "  saved $name.json ($c record" . ($c === 1 ? '' : 's') . ")\n";
}

/** Page through a getTemplates feed until it returns no more items. */
function fetchAllTemplates(string $baseQuery): array {
    $all = [];
    for ($page = 1; $page <= MAX_PAGES; $page++) {
        $sep  = str_contains($baseQuery, '?') ? '&' : '?';
        [$code, $data] = apiGet($baseQuery . $sep . "page=$page&limit=" . PAGE_LIMIT);
        if ($code !== 200 || !isset($data['msg']) || !is_array($data['msg'])) break;
        $items = $data['msg'];
        if (count($items) === 0) break;
        foreach ($items as $item) $all[] = $item;
        if (count($items) < PAGE_LIMIT) break; // last page
    }
    return $all;
}

/** Page through getAllMusicList (returns a bare array) until empty. */
function fetchAllMusic(string $catId): array {
    $all = [];
    for ($page = 1; $page <= MAX_PAGES; $page++) {
        [$code, $data] = apiGet("/getAllMusicList?cat_id=$catId&page=$page&limit=" . PAGE_LIMIT);
        if ($code !== 200 || !is_array($data)) break;
        if (count($data) === 0) break;
        foreach ($data as $item) $all[] = $item;
        if (count($data) < PAGE_LIMIT) break;
    }
    return $all;
}

// ---------------------------------------------------------------------------

if (!is_dir(OUT_DIR)) {
    mkdir(OUT_DIR, 0755, true);
    mkdir(OUT_DIR . '/templates_by_category', 0755, true);
}
if (!is_dir(OUT_DIR . '/templates_by_category')) {
    mkdir(OUT_DIR . '/templates_by_category', 0755, true);
}

echo "Fetching from " . BASE_URL . "\n\n";

// 1. Home screen -----------------------------------------------------------
echo "[1] getTemplatesByCategory (home)\n";
[$c, $d] = apiGet('/getTemplatesByCategory');
if ($c === 200 && is_array($d)) save('getTemplatesByCategory', $d, count($d));
else fwrite(STDERR, "  ! failed (http $c)\n");

// 2. Categories (all three types) ------------------------------------------
$categoryIds = [];
foreach (['template', 'music', 'banner'] as $type) {
    echo "[2] getAllCategory?type=$type\n";
    [$c, $d] = apiGet("/getAllCategory?type=$type");
    if ($c === 200 && isset($d['msg'])) {
        save("categories_$type", $d['msg'], count($d['msg']));
        if ($type === 'template') {
            foreach ($d['msg'] as $cat) $categoryIds[] = $cat['id'];
        }
    } else fwrite(STDERR, "  ! failed (http $c)\n");
}

// 3. All templates (authoritative complete set via cat=-2 = newest) --------
echo "[3] getTemplates cat=-2 (all templates, fully paginated)\n";
$allTemplates = fetchAllTemplates('/getTemplates?cat=-2');
save('templates_all', $allTemplates, count($allTemplates));

// 3b. Per-category templates (type=latest, fully paginated) ----------------
echo "[3b] per-category templates\n";
$perCatTotal = 0;
foreach ($categoryIds as $cid) {
    $items = fetchAllTemplates("/getTemplates?cat=$cid&type=latest");
    file_put_contents(
        OUT_DIR . "/templates_by_category/$cid.json",
        json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );
    $perCatTotal += count($items);
    echo "  cat $cid: " . count($items) . " templates\n";
}
$summary['templates_by_category_total'] = $perCatTotal;

// 3c. Popular feed (cat=-1 = most created) ---------------------------------
echo "[3c] getTemplates cat=-1 (most created)\n";
$popular = fetchAllTemplates('/getTemplates?cat=-1');
save('templates_popular', $popular, count($popular));

// 4. Music (all) -----------------------------------------------------------
echo "[4] getAllMusicList cat_id=-1 (all music, fully paginated)\n";
$allMusic = fetchAllMusic('-1');
save('music_all', $allMusic, count($allMusic));

// 5. Banners ---------------------------------------------------------------
echo "[5] getAllBanners\n";
[$c, $d] = apiGet('/getAllBanners');
if ($c === 200) save('banners', $d, is_array($d) ? count($d) : 0);
else fwrite(STDERR, "  ! failed (http $c)\n");

// 6. Settings --------------------------------------------------------------
echo "[6] getAllSettings\n";
[$c, $d] = apiGet('/getAllSettings');
if ($c === 200 && is_array($d)) save('settings', $d, 1);
else fwrite(STDERR, "  ! failed (http $c)\n");

// Manifest for verification ------------------------------------------------
$manifest = [
    'source'       => BASE_URL,
    'generated_at' => date('c'),
    'counts'       => $summary,
];
file_put_contents(
    OUT_DIR . '/index.json',
    json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

echo "\n=== DONE ===\n";
echo json_encode($summary, JSON_PRETTY_PRINT) . "\n";

// Integrity check: master list should cover every per-category template id.
$masterIds = array_column($allTemplates, 'id');
$catIdsSeen = [];
foreach ($categoryIds as $cid) {
    $items = json_decode(file_get_contents(OUT_DIR . "/templates_by_category/$cid.json"), true) ?: [];
    foreach ($items as $it) $catIdsSeen[] = $it['id'];
}
$missing = array_diff(array_unique($catIdsSeen), $masterIds);
echo "\nIntegrity: master templates=" . count(array_unique($masterIds))
   . ", per-category unique=" . count(array_unique($catIdsSeen))
   . ", missing from master=" . count($missing) . "\n";
if ($missing) echo "MISSING IDS: " . implode(',', $missing) . "\n";
