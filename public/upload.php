<?php
require($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'upload.class.php');

// @todo: Debug nicht Veröffentlichen (oder nur auskommentiert)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('max_file_uploads', 25);

$handler = new UploadFiles($_FILES, 'upload', ['image/jpeg', "text/x-php"], 'files', 10, 5);

if ($handler->saveFiles()) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}




