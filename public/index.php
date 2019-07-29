<style>
    input[type="file"]{
        opacity: 0;
        z-index: -1;
        position: absolute;
        top: -1px;
        left: 0;
        width: 0.1px;
        height: 0.1px;
        color: red;
    }

    input[type="file"]:focus + label[for="files"]{
        outline: 2px solid red;
    }

    strong{
        margin-bottom: 20px;
        display: block;
    }
</style>

<?php if($_GET['x'] == 'maxFilesDetection'){ ?>
    <strong>Du hast die Maximal Anzahl Überschriten</strong>
<?php } ?>


<form enctype="multipart/form-data" action="upload.php" method="POST">

<input type="file" multiple name="Datein[]" id="files"> 
<label for="files">File Upload</label>

<button>Senden</button>

</form>