<?php
use App\{files,listing,text,misc,user};

defined('DCMS') or die;

$dir = new files(FILES . '/.obmen');
$content = $dir->getNewFiles();
$files = & $content['files'];

/** @var $files \files_file[] */
$new_files = count($files);

$listing = new listing();

$post = $listing->post();
$post->highlight = true;
$post->icon('downloads');
$post->url = '/files/.obmen/';
$post->title = __('Обменник');
if ($new_files)
    $post->counter = '+' . $new_files;

for ($i = 0; $i < $new_files && $i < $dcms->widget_items_count; $i++) {
    $ank = new user($files[$i]->id_user);
    $post = $listing->post();
    $post->title = text::toValue($files[$i]->runame);
    $post->time = misc::when($files[$i]->time_add);
    $post->url = "/files" . $files[$i]->getPath() . ".htm";
    $post->image = $files[$i]->image();
    $post->icon($files[$i]->icon());
    $post->bottom = $ank->nick();
}

if ($new_files > $dcms->widget_items_count) {
    $post = $listing->post();
    $post->icon('new');
    $post->url = '/files/new.php?dir=.obmen';
    $post->title = __('Все новые файлы');
}

$listing->display();