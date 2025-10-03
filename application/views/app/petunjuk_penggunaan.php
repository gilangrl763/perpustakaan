<?
$this->load->library("crfs_protect");
$csrf = new crfs_protect('_crfs_6Dp51Fm');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

$this->load->model("PetunjukPenggunaan");
$petunjuk_penggunaan = new PetunjukPenggunaan();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="libraries/bootstrap-3.3.7/docs/favicon.ico">

    <title>AP1-ARFF</title>
    <link href="libraries/bootstrap-3.3.7/docs/dist/css/bootstrap.css" rel="stylesheet">
    <link href="libraries/bootstrap-3.3.7/docs/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
    <script src="libraries/bootstrap-3.3.7/docs/assets/js/ie-emulation-modes-warning.js"></script>
    <link rel="stylesheet" href="css/gaya.css" type="text/css">
    <link rel="stylesheet" href="libraries/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    <style>
    .panel-body {
        padding: 10px 15px !important;
    }
    .panel-header, .panel-body {
        border-color: #f5f5f5 !important;
    }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="judul-halaman">Petunjuk Penggunaan</div>
                <div class="konten">
                    <div class="row">
                        <?
                        $petunjuk_penggunaan->selectByParams(array("A.STATUS"=>"AKTIF"));
                        // echo $petunjuk_penggunaan->query;exit;
                        while($petunjuk_penggunaan->nextRow()){
                            if(getExtension($petunjuk_penggunaan->getField("DOKUMEN")) == "pdf"){
                                $icon = "fa-file-pdf-o";
                            }
                            else{
                                $icon = "fa-file-video-o";
                            }
                        ?>
                        <div class="col-md-3" onclick="openPopup('uploads/petunjuk_penggunaan/<?=$petunjuk_penggunaan->getField("DOKUMEN")?>')">
                            <div class="thumbnail">
                                <div class="row p-2">
                                    <div class="col-md-2">
                                        <i class="fa <?=$icon?> fs-36"></i>
                                    </div>
                                    <div class="col-md-10">
                                        <p class="fs-18"><?=$petunjuk_penggunaan->getField("NAMA")?></p>
                                        <small><?=round(($petunjuk_penggunaan->getField("UKURAN_DOKUMEN")/1024/1024), 2)?> MB</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="libraries/eModal-master/dist/eModal.js"></script>
    <script>
    function openPopup(page) {
        eModal.iframe(page, 'TMJ | Aplikasi Manajemen Kearsipan')
    }

    function closePopup() {
        eModal.close();
    }
    </script>
</body>
</html>