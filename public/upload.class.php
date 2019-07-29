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


class uploadFiles{

    function __construct(array $file,string $dir,array $allowedFormats,string $inputFieldName,int $maxFiles,int $maxSizes){

        ini_set('max_file_uploads', $maxFiles);
        ini_set('post_max_size',$maxFiles * $maxSizes . 'M');
        ini_set('max_file_uploads',$maxSizes.'M');

        $this->files = $this->convertArray($file,$inputFieldName);    
        $this->uploadDir = $_SERVER['DOCUMENT_ROOT'].'/../'.$dir;
        $this->formats = $allowedFormats;

        $this->maxFilesDetection($maxFiles);
    }

    private function maxFilesDetection($maxFiles){
        if(count($this->files) >= $maxFiles){
            header('Location:' . $_SERVER['HTTP_REFERER'] . '?x=maxFilesDetection');
        }
    }

    private function fileTypeDetection(){
        /**
         * Detect File Type
         */
    }

    private function fileSizeDetection(){
        /**
         * File Size Detection
         */
        var_dump($this->files);
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

    public static function beforeUpload(){
        session_start();
        /* JS & CSS File for Styling */
        echo '<script src="upload.class.js"></script>';
        echo '<link rel="stylesheet" href="upload.class.css">';
    }
}