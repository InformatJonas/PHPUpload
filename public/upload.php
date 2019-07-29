<?php require($_SERVER['DOCUMENT_ROOT'] . '/upload.class.php');

$handler = new uploadFiles($_FILES,'upload',['image/jpeg',"text/x-php"],'Datein',10,5);

if($handler->saveFiles()){
 
}




