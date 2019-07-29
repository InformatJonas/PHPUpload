<?php require($_SERVER['DOCUMENT_ROOT'] . '/upload.class.php');

$handler = new uploadFiles($_FILES,'upload',['image/jpeg',"text/x-php"],'Datein',5,5);

if($handler->saveFiles()){
    header('Location: index.php');
}




