<?php 

/**
 * @author Felix Schürmeyer
 * @date 29/07/2019
 * @version 0.1
 * @param $file (array) example $_FILES
 * @param $dir (string) example "upload"
 * @param $allowedFormats (array) mimeTypes ['image/jpeg',"text/x-php"],'
 * @param $inputFieldName (string) example name="Datein[]" => "Datein"
 * @param $maxFiles (int) example 5
 * @param $maxSizes (int) example 10 => 10M
 */


class UploadFiles{

    function __construct(array $file,string $dir,array $allowedFormats,string $inputFieldName,int $maxFiles,int $maxSizes){

        session_start();


        $this->files = $this->convertArray($file,$inputFieldName);    
        $this->uploadDir = $_SERVER['DOCUMENT_ROOT'].'/../'.$dir;
        $this->formats = $allowedFormats;

        $this->maxFilesDetection($maxFiles);
        /* $this->fileSizeDetection($maxSizes); */
        $this->fileTypeDetection();
    }

    private function maxFilesDetection($maxFiles){
        if(count($this->files) >= $maxFiles){
            $_SESSION['uploadElm'] = 'MaxFiles';
            header('Location:' . $_SERVER['HTTP_REFERER']);
        }
    }

    private function fileTypeDetection(){
        /**
         * Detect File Type
         */

        foreach($this->files as $file){
            if(!in_array(mime_content_type($file['file']),$this->formats)){
            
                $_SESSION['uploadElm'] = 'wrongFormat';
                header('Location:' . $_SERVER['HTTP_REFERER']);
            }
        }
    }

    private function fileSizeDetection($maxSizes){

        foreach($this->files as $file){
            if(($maxSizes * 1048576) > $file['size']){
                $_SESSION['uploadElm'] = 'SizeToBig';
                header('Location:' . $_SERVER['HTTP_REFERER']);
            }
        }
 
    }

    public function getFiles(){
        return $this->files;
    }

    private function convertArray($item,$inputFieldName){

        $converted = [];

        foreach($item[$inputFieldName]['name'] as $iter => $name){
            $converted[$iter]['name'] = $name;
            $converted[$iter]['type'] = $item[$inputFieldName]['type'][$iter];
            $converted[$iter]['file'] = $item[$inputFieldName]['tmp_name'][$iter];
            $converted[$iter]['error'] = $item[$inputFieldName]['error'][$iter];
            $converted[$iter]['size'] = $item[$inputFieldName]['size'][$iter];
        }

        return $converted;
    }

    public function saveFiles(){

        $this->createUploadFolder();

        if($this->valideRequest()){

            foreach($this->files as $file){

                if(in_array(mime_content_type($file['file']),$this->formats)){

                    $location = $this->uploadDir .'/'. basename($file['name']);
                    if(!move_uploaded_file($file['file'],$location)){
                    return false; 
                    }else{
                        chmod($location,0644);
                    }

                }

            }

            return true;
        }else{
            return false;
        }

    }

    private function valideRequest(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            return true;
        }else{
            return false;
        }
    }

    private function createUploadFolder(){
        
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777);
            return true;
        } else {
            return true;
        }

    }

    public static function beforeUpload($items){
        session_start();
        /* JS & CSS File for Styling */

        echo '<script>';

            echo 'var ts = [];';
            echo 'ts["file"] ="' . $items[0].'";';
            echo 'ts["files"] ="' . $items[1].'";';

        echo '</script>';

        echo '<script src="upload.class.js"></script>';
        echo '<link rel="stylesheet" href="upload.class.css">';
    }

    public static function resultManager(){
        if(isset($_SESSION['uploadElm'])){
            if($_SESSION['uploadElm'] ==  'MaxFiles'){
                echo '<span class="err">Die Maximale Datei Anzahl wurde überschritten</span>';
            }

            if($_SESSION['uploadElm'] ==  'wrongFormat'){
                echo '<span class="err">Du hast eine Datei in einem nicht Untertützend Format Hochgeladen</span>';
            }

            if($_SESSION['uploadElm'] ==  'SizeToBig'){
                echo '<span class="err">Die Datei ist zu Groß zum Hochladen</span>';
            }


            $_SESSION['uploadElm'] = '';
        }

    }
}