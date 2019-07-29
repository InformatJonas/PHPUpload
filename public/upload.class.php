<?php 
class uploadFiles{

    function __construct(array $file,string $dir,array $allowedFormats,string $inputFieldName,$maxFiles,$maxSizes){

        ini_set('max_file_uploads', $maxFiles);
        ini_set('post_max_size',$maxFiles * $maxSizes . 'M');
        ini_set('max_file_uploads',$maxSizes.'M');

        $this->files = $this->convertArray($file,$inputFieldName);

        if(count($this->files) >= $maxFiles){
            header('Location:' . $_SERVER['HTTP_REFERER'] . '?x=maxFilesDetection');
        }
     

        $this->uploadDir = $_SERVER['DOCUMENT_ROOT'].'/../'.$dir;
        $this->formats = $allowedFormats;
    }

    function getFiles(){
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
}