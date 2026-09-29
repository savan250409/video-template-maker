<?php
/**
 * One-off importer: loads the 135_Pixstory_Templates pack into the video-template
 * module and seeds test Music + Banner data so every API can be exercised.
 * Idempotent: re-running skips rows that already exist (matched by name/title).
 * Run:  php import_test_data.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\{Category, AnimatedTemplate, Music, Banner};
use Illuminate\Support\Str;

$base = __DIR__ . '/135_Pixstory_Templates';
$pub  = storage_path('app/public');

$dirs = [
    'uploads/category/template', 'uploads/category/music', 'uploads/category/banner',
    'uploads/template/zip', 'uploads/template/thumbnail',
    'uploads/music', 'uploads/banner',
];
foreach ($dirs as $d) { if (!is_dir("$pub/$d")) mkdir("$pub/$d", 0775, true); }

function zipFolder(string $folder, string $zipPath): bool {
    $zip = new ZipArchive;
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) return false;
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach ($it as $file) {
        $real = $file->getRealPath();
        $rel  = str_replace('\\', '/', substr($real, strlen($folder) + 1));
        $zip->addFile($real, $rel);
    }
    $zip->close();
    return true;
}

$tplCount = 0; $catCount = 0;
$catDirs = glob("$base/*", GLOB_ONLYDIR);
sort($catDirs);
$sort = 1;

foreach ($catDirs as $catDir) {
    $catName = basename($catDir);

    $category = Category::where('type', 'template')->where('name', $catName)->first();
    if (!$category) {
        $category = new Category;
        $category->type          = 'template';
        $category->template_type = 'video';
        $category->sort_order    = $sort;
        $category->name          = $catName;
        $category->is_active     = 1;
        $category->save();
        $catCount++;
    }
    $sort++;
    $catLogoSet = (bool) $category->logo;

    $tplDirs = glob("$catDir/*", GLOB_ONLYDIR);
    sort($tplDirs);

    foreach ($tplDirs as $tplDir) {
        $title = basename($tplDir);
        $tjson = "$tplDir/template.json";
        if (!file_exists($tjson)) continue;
        if (AnimatedTemplate::where('category_id', $category->id)->where('title', $title)->exists()) continue;

        $jsonContent   = file_get_contents($tjson);
        $data          = json_decode($jsonContent, true) ?: [];
        $elements      = $data['elements'] ?? [];
        $totalImages   = count($elements);
        $totalEditable = count(array_filter($elements, fn($e) => (int)($e['editable'] ?? 0) === 1));
        $w = 0; $h = 0;
        foreach ($elements as $e) {
            if (isset($e['size'][0])) { $w = max($w, (int)$e['size'][0]); $h = max($h, (int)$e['size'][1]); }
        }

        $code    = Str::uuid()->toString();
        zipFolder($tplDir, "$pub/uploads/template/zip/$code.zip");

        $thumbName = null;
        $imgs = glob("$tplDir/res/images/*.{png,jpg,jpeg,PNG,JPG,JPEG}", GLOB_BRACE);
        if ($imgs) {
            sort($imgs);
            $src = $imgs[0];
            $ext = pathinfo($src, PATHINFO_EXTENSION);
            $thumbName = "$code.$ext";
            copy($src, "$pub/uploads/template/thumbnail/$thumbName");
            if (!$catLogoSet) {
                $logo = "cat_{$category->id}.$ext";
                copy($src, "$pub/uploads/category/template/$logo");
                $category->logo = $logo;
                $category->save();
                $catLogoSet = true;
            }
        }

        $t = new AnimatedTemplate;
        $t->user_id           = 1;
        $t->category_id       = $category->id;
        $t->type              = 'video';
        $t->title             = $title;
        $t->total_image_count = $totalImages;
        $t->total_editable    = $totalEditable;
        $t->zip               = $code;
        $t->zip_original_name  = $title;
        $t->thumbnail         = $thumbName;
        $t->tags              = strtolower(str_replace(' ', ',', "$catName,$title"));
        $t->is_paid           = 0;
        $t->is_active         = 1;
        $t->total_views       = random_int(10, 5000);
        $t->total_create      = random_int(1, 2000);
        $t->json              = $jsonContent;
        $t->height            = (string) $h;
        $t->width             = (string) $w;
        $t->save();
        $tplCount++;
    }
    echo "  [cat] {$catName} -> " . AnimatedTemplate::where('category_id', $category->id)->count() . " templates\n";
}

echo "\n== MUSIC ==\n";
$musicCat = Category::where('type', 'music')->where('name', 'Trending')->first();
if (!$musicCat) {
    $musicCat = new Category;
    $musicCat->type = 'music'; $musicCat->template_type = 'music';
    $musicCat->sort_order = 1;  $musicCat->name = 'Trending'; $musicCat->is_active = 1;
    $musicCat->save();
}
$audios = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) { if (strtolower($f->getFilename()) === 'audio.mp3') $audios[] = $f->getRealPath(); }
sort($audios);
$audios = array_slice($audios, 0, 10);
$mi = 0;
foreach ($audios as $a) {
    $song = basename(dirname($a));                       // parent folder name
    $file = preg_replace('/[^A-Za-z0-9_\-]/', '_', $song) . '.mp3';
    if (Music::where('music', $file)->exists()) continue;
    copy($a, "$pub/uploads/music/$file");
    $m = new Music;
    $m->category_id = $musicCat->id;
    $m->music       = $file;
    $m->is_active   = 1;
    $m->save();
    $mi++;
    echo "  [music] $file\n";
}

echo "\n== BANNER ==\n";
$bannerCat = Category::where('type', 'banner')->where('name', 'Promotions')->first();
if (!$bannerCat) {
    $bannerCat = new Category;
    $bannerCat->type = 'banner'; $bannerCat->template_type = 'banner';
    $bannerCat->sort_order = 1;   $bannerCat->name = 'Promotions'; $bannerCat->is_active = 1;
    $bannerCat->save();
}
$bannerSrc = [];
$it2 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
foreach ($it2 as $f) {
    if (preg_match('/img_0\.(png|jpg|jpeg)$/i', $f->getFilename())) $bannerSrc[] = $f->getRealPath();
    if (count($bannerSrc) >= 4) break;
}
$bi = 0;
foreach ($bannerSrc as $i => $src) {
    $ext  = pathinfo($src, PATHINFO_EXTENSION);
    $file = 'banner_' . ($i + 1) . '.' . $ext;
    $name = 'Test Banner ' . ($i + 1);
    if (Banner::where('name', $name)->exists()) continue;
    copy($src, "$pub/uploads/banner/$file");
    $b = new Banner;
    $b->category_id = $bannerCat->id;
    $b->name        = $name;
    $b->type        = $i % 2 === 0 ? 'image' : 'url';
    $b->url         = 'https://play.google.com/store/apps/details?id=com.pixstory';
    $b->banner      = $file;
    $b->is_active   = 1;
    $b->save();
    $bi++;
    echo "  [banner] $name ($file)\n";
}

echo "\n== DONE ==\n";
echo "New categories: $catCount | New templates: $tplCount | New music: $mi | New banners: $bi\n";
echo "Totals -> categories: " . Category::count()
    . " | templates: " . AnimatedTemplate::count()
    . " | music: " . Music::count()
    . " | banners: " . Banner::count() . "\n";
