<?php
/**
 * @param $file           (array) example $_FILES
 * @param $dir            (string) example "upload"
 * @param $allowedFormats (array) mimeTypes ['image/jpeg',"text/x-php"],'
 * @param $inputFieldName (string) example name="Datein[]" => "Datein"
 * @param $maxFiles       (int) example 5
 * @param $maxSizes       (int) example 10 => 10M
 *
 * @author  Felix Schürmeyer
 * @date    29/07/2019
 * @version 0.1
 */

/**
 * Class UploadFiles
 */
class UploadFiles
{
    /**
     * @var array
     */
    private $files;

    /**
     * @var string
     */
    private $uploadDir;

    /**
     * @var array
     */
    private $formats;

    /**
     * UploadFiles constructor.
     *
     * @param array  $file
     * @param string $dir
     * @param array  $allowedFormats
     * @param string $inputFieldName
     * @param int    $maxFiles
     * @param int    $maxSizes
     */
    public function __construct(array $file, string $dir, array $allowedFormats, string $inputFieldName, int $maxFiles, int $maxSizes)
    {
        session_start();

        $this->files     = $this->convertArray($file, $inputFieldName);
        $this->uploadDir = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $dir;
        $this->formats   = $allowedFormats;

        $this->maxFilesDetection($maxFiles);
        /* $this->fileSizeDetection($maxSizes); */
        $this->fileTypeDetection();
    }

    /**
     * @param $maxFiles
     *
     * @return void
     */
    private function maxFilesDetection($maxFiles): void
    {
        if (count($this->files) >= $maxFiles) {
            $_SESSION['uploadElm'] = 'MaxFiles';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * @return void
     */
    private function fileTypeDetection(): void
    {
        /**
         * Detect File Type
         */
        foreach ($this->files as $file) {
            if (!in_array(mime_content_type($file['file']), $this->formats)) {
                $_SESSION['uploadElm'] = 'wrongFormat';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
        }
    }

    /**
     * @param $maxSizes
     *
     * @return void
     */
    private function fileSizeDetection($maxSizes): void
    {
        foreach ($this->files as $file) {
            if (($maxSizes * 1048576) > $file['size']) {
                $_SESSION['uploadElm'] = 'SizeToBig';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
        }
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array  $item
     * @param string $inputFieldName
     *
     * @return array
     */
    private function convertArray(array $item, string $inputFieldName): array
    {
        $converted = [];

        foreach ($item[$inputFieldName]['name'] as $iter => $name) {
            $converted[$iter]['name']  = $name;
            $converted[$iter]['type']  = $item[$inputFieldName]['type'][$iter];
            $converted[$iter]['file']  = $item[$inputFieldName]['tmp_name'][$iter];
            $converted[$iter]['error'] = $item[$inputFieldName]['error'][$iter];
            $converted[$iter]['size']  = $item[$inputFieldName]['size'][$iter];
        }

        return $converted;
    }

    /**
     * @return bool
     */
    public function saveFiles(): bool
    {
        $this->createUploadFolder();
        if ($this->validateRequest()) {
            foreach ($this->files as $file) {
                if (in_array(mime_content_type($file['file']), $this->formats)) {
                    $location = $this->uploadDir . DIRECTORY_SEPARATOR . basename($file['name']);
                    if (!move_uploaded_file($file['file'], $location)) {
                        return false;
                    } else {
                        chmod($location, 0644);
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    private function validateRequest(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    private function createUploadFolder(): bool
    {
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777);

            return true;
        } else {
            return true;
        }
    }

    /**
     * @param array $items
     *
     * @return void
     */
    public static function beforeUpload(array $items): void
    {
        session_start();
        /* JS & CSS File for Styling */
        echo '<script>';
        echo 'var ts = [];';
        echo 'ts["file"] ="' . $items[0] . '";';
        echo 'ts["files"] ="' . $items[1] . '";';
        echo '</script>';
        echo '<script src="upload.class.js"></script>';
        echo '<link rel="stylesheet" href="upload.class.css">';
    }

    /**
     * @return void
     */
    public static function resultManager(): void
    {
        if (isset($_SESSION['uploadElm'])) {
            if ($_SESSION['uploadElm'] == 'MaxFiles') {
                echo '<span class="err">Die maximale Datei Anzahl wurde überschritten</span>';
            }

            if ($_SESSION['uploadElm'] == 'wrongFormat') {
                echo '<span class="err">Du hast eine Datei in einem nicht unterstützende Format hochgeladen</span>';
            }

            if ($_SESSION['uploadElm'] == 'SizeToBig') {
                echo '<span class="err">Die Datei ist zu groß zum hochladen</span>';
            }

            $_SESSION['uploadElm'] = '';
        }
    }
}