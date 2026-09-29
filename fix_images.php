<?php
/**
 * Fixes admin-panel image display for the imported data:
 *  1. Category logos -> also placed in uploads/category/<type>/thumbnail/ (admin list reads from there)
 *  2. Template zips  -> extracted to uploads/template/<id>/<zip>/ so the animated data.html preview renders
 * Idempotent: safe to re-run.
 * Run:  php fix_images.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\{Category, AnimatedTemplate};

$pub = storage_path('app/public');

/* 1) Category logo thumbnails ------------------------------------------------ */
$catFixed = 0;
foreach (Category::whereNotNull('logo')->get() as $cat) {
    $src   = "$pub/uploads/category/{$cat->type}/{$cat->logo}";
    $thumb = "$pub/uploads/category/{$cat->type}/thumbnail";
    if (!is_dir($thumb)) mkdir($thumb, 0775, true);
    $dst = "$thumb/{$cat->logo}";
    if (is_file($src) && !is_file($dst)) { copy($src, $dst); $catFixed++; }
}
echo "Category thumbnails ensured: $catFixed\n";

/* 2) Extract template zips for animated previews ----------------------------- */
$extracted = 0; $skipped = 0; $missing = 0;
foreach (AnimatedTemplate::whereNotNull('zip')->get() as $t) {
    $zipPath = "$pub/uploads/template/zip/{$t->zip}.zip";
    if (!is_file($zipPath)) { $missing++; continue; }
    $target = "$pub/uploads/template/{$t->id}/{$t->zip}";
    if (is_file("$target/res/data.html")) { $skipped++; continue; }
    if (!is_dir($target)) mkdir($target, 0775, true);
    $zip = new ZipArchive;
    if ($zip->open($zipPath) === true) {
        $zip->extractTo($target);
        $zip->close();
        $extracted++;
    }
}
echo "Templates extracted: $extracted | already-present: $skipped | zip-missing: $missing\n";
echo "Done.\n";
