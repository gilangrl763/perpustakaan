<?
$this->load->library("crfs_protect");
$csrf = new crfs_protect('_crfs_6Dp51Fm');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

$this->load->model("Faq");
$faq = new Faq();
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

    <title>TMJ-ARSIP</title>
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
                <div class="judul-halaman">Frequently Asked Questions (F.A.Q.)</div>
                <div class="konten">
                    <div class="row">
                        <?
                        $faq->selectByParams(array("STATUS"=>"AKTIF"));
                        // echo $faq->query;exit;
                        while($faq->nextRow())     
                        {
                        ?>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h4 class="faq-question" data-wow-delay=".1s"><b><?=$faq->getField("PERTANYAAN")?></b></h4>
                                <br><?=$faq->getField("JAWABAN")?>
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
</body>
</html>