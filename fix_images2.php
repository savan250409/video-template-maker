<?php
/**
 * Re-picks category logos and template thumbnails using the LARGEST image in each
 * source folder (the real artwork/photo) instead of img_0, which is often a tiny
 * transparent overlay that renders blank.
 * Idempotent-ish: overwrites logos/thumbnails to the best image each run.
 * Run:  php fix_images2.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\{Category, AnimatedTemplate};

$base = __DIR__ . '/135_Pixstory_Templates';
$pub  = storage_path('app/public');

/** Largest png/jpg/jpeg under a folder (recursive). */
function largestImage(string $folder): ?string {
    if (!is_dir($folder)) return null;
    $best = null; $bestSize = -1;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if (!$f->isFile()) continue;
        if (!preg_match('/\.(png|jpe?g)$/i', $f->getFilename())) continue;
        // skip obvious demo duplicates so we prefer the working res/images
        $sz = $f->getSize();
        if ($sz > $bestSize) { $bestSize = $sz; $best = $f->getRealPath(); }
    }
    return $best;
}

/* 1) Category logos ---------------------------------------------------------- */
$catFix = 0;
foreach (Category::where('type', 'template')->get() as $cat) {
    $src = largestImage("$base/{$cat->name}");
    if (!$src) { echo "  ! no image for category {$cat->name}\n"; continue; }
    $ext  = strtolower(pathinfo($src, PATHINFO_EXTENSION));
    $name = "cat_{$cat->id}.$ext";
    copy($src, "$pub/uploads/category/template/$name");
    $tdir = "$pub/uploads/category/template/thumbnail";
    if (!is_dir($tdir)) mkdir($tdir, 0775, true);
    copy($src, "$tdir/$name");
    // remove any stale different-extension logo
    if ($cat->logo && $cat->logo !== $name) {
        @unlink("$pub/uploads/category/template/{$cat->logo}");
        @unlink("$pub/uploads/category/template/thumbnail/{$cat->logo}");
    }
    $cat->logo = $name;
    $cat->save();
    $catFix++;
    echo "  [cat] {$cat->name} -> $name (" . number_format(filesize($src)) . " b)\n";
}

/* 2) Template thumbnails ----------------------------------------------------- */
$tplFix = 0;
foreach (AnimatedTemplate::whereNotNull('zip')->get() as $t) {
    $folder = "$base/" . ($t->category ? $t->category->name : '') . "/{$t->title}";
    $src = largestImage($folder);
    if (!$src) continue;
    $ext  = strtolower(pathinfo($src, PATHINFO_EXTENSION));
    $name = "{$t->zip}.$ext";
    $dir  = "$pub/uploads/template/thumbnail";
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    copy($src, "$dir/$name");
    if ($t->thumbnail && $t->thumbnail !== $name) @unlink("$dir/{$t->thumbnail}");
    $t->thumbnail = $name;
    $t->save();
    $tplFix++;
}
echo "\nCategories updated: $catFix | Template thumbnails updated: $tplFix\n";
