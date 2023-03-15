<?php
require("./../src/config.php");

use Steampixel\Route;

Route::add('/', function() {
    global $twig;
    $posts = Post::getPage();
    $t = array("posts" => $posts);
    $twig->display("index.html.twig", $t);
});
Route::add('/upload', function() {
    global $twig;
    $twig->display("upload.html.twig");
});
Route::add('/upload', function() {
    global $twig;

    Post::upload($_FILES['uploadedFile']['tmp_name'], $_FILES['uploadedFile']['name']);

    // $twig->display("index.html");
    header('Location: /kacperhinz4gp/3/pub');
    die();
}, 'post');

Route::run('/kacperhinz4gp/3/pub');
?>