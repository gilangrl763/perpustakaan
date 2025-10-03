<?
$this->load->library("crfs_protect"); 
$csrf = new crfs_protect('_crfs_6Dp51Fm');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

$this->load->model("SatuanKerja");
$satuan_kerja = new SatuanKerja();

$id = $this->input->get("id");

if ($id == "") {
    $mode = "insert";
} else {
    $mode = "update";

    $satuan_kerja->selectByParams(array("A.SATUAN_KERJA_ID" => $id));
    // echo $satuan_kerja->query;
    $satuan_kerja->firstRow();

    $KODE                       = $satuan_kerja->getField("KODE");
    $NAMA                       = $satuan_kerja->getField("NAMA");
    $SATUAN_KERJA_ID_PARENT     = $satuan_kerja->getField("SATUAN_KERJA_ID_PARENT");
    $PEGAWAI_ID                 = $satuan_kerja->getField("PEGAWAI_ID");
    $PERUSAHAAN_ID              = $satuan_kerja->getField("PERUSAHAAN_ID");
    $CABANG_ID                  = $satuan_kerja->getField("CABANG_ID");
}
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

    <title>Dashboard Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="libraries/bootstrap-3.3.7/docs/dist/css/bootstrap.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="libraries/bootstrap-3.3.7/docs/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <!--<link href="libraries/bootstrap-3.3.7/docs/examples/dashboard/dashboard.css" rel="stylesheet">-->

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="libraries/bootstrap-3.3.7/docs/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    <link href="css/gaya.css" rel="stylesheet">
    <link rel="stylesheet" href="libraries/font-awesome-4.7.0/css/font-awesome.css" type="text/css">

    <!-- Bootstrap core JavaScript
        ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.min.js"></script>
    <script>
        window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')
    </script>
    <script src="libraries/bootstrap-3.3.7/docs/dist/js/bootstrap.min.js"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="libraries/bootstrap-3.3.7/docs/assets/js/vendor/holder.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="libraries/bootstrap-3.3.7/docs/assets/js/ie10-viewport-bug-workaround.js"></script>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="judul-halaman">
                    <a href="app/loadUrl/app/konfigurasi_satuan_kerja">Satuan Kerja</a>
                    &bull; Kelola Satuan Kerja
                </div>
                <div class="area-form">
                    <div class="konten">
                        <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
    
                            <div class="form-group">
                                <label class="control-label col-md-2">Perusahaan</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control easyui-combobox" id="PERUSAHAAN_ID" name="PERUSAHAAN_ID" 
                                            data-options="width:'715',panelHeight:'120',panelWidth:'715',editable:false,
                                            valueField:'id',textField:'text',url:'perusahaan_json/combo',
                                            onSelect: function(rec){
                                                var url = 'cabang_json/combo/?PERUSAHAAN_ID='+rec.id;
                                                $('#CABANG_ID').combobox('reload', url);
                                                $('#CABANG_ID').combobox('setValue', '');
                                                $('#SATUAN_KERJA_ID_PARENT').combotree('setValue', '');
                                            }" value="<?= $PERUSAHAAN_ID ?>" required />
                                        </div>
                                    </div>
    
                                </div>
                            </div>
    
                            <div class="form-group">
                                <label class="control-label col-md-2">Cabang</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control easyui-combobox" id="CABANG_ID" 
                                            name="CABANG_ID" data-options="width:'715',panelHeight:'120',panelWidth:'715',editable:false,
                                            valueField:'id',textField:'text',url:'cabang_json/combo',
                                            onSelect: function(rec){
                                                var url = 'satuan_kerja_json/combotree_form/?PERUSAHAAN_ID='+$('#PERUSAHAAN_ID').combobox('getValue')+'&CABANG_ID='+rec.id;
                                                $('#SATUAN_KERJA_ID_PARENT').combotree('reload', url);
                                                $('#SATUAN_KERJA_ID_PARENT').combotree('setValue', '');
                                            }" value="<?= $CABANG_ID ?>" required />
                                        </div>
                                    </div>
    
                                </div>
                            </div>
    
                            <div class="form-group">
                                <label class="control-label col-md-2">Satuan Kerja Induk</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                        <input type="text" class="form-control easyui-combotree" id="SATUAN_KERJA_ID_PARENT" name="SATUAN_KERJA_ID_PARENT" 
                                            data-options="width:'715',panelHeight:'120',panelWidth:'715',editable:false,
                                            valueField:'id',textField:'text',url:'satuan_kerja_json/combotree_form/?id=<?=$this->PERUSAHAAN_ID?>'" 
                                            value="<?=$SATUAN_KERJA_ID_PARENT?>" required/>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="form-group">
                                <label class="control-label col-md-2">Kode</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input class="form-control easyui-validatebox" type="text" name="KODE" value="<?= $KODE ?>" readonly style="width:100%">
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="form-group">
                                <label class="control-label col-md-2">Nama</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input class="form-control easyui-textbox" type="text" name="NAMA" 
                                            value="<?= $NAMA ?>" required style="width:100%">
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="form-group" style="display:none;">
                                <label class="control-label col-md-2">Pejabat</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="hidden" name="PEGAWAI_ID" value="<?= $PEGAWAI_ID ?>" />
                                            <input class="form-control easyui-textbox" type="text" name="PEJABAT" 
                                            value="<?= $PEJABAT ?>" readonly style="width:100%">
                                        </div>
                                        <div class="col-md-1">
                                            <a class="btn btn-xs btn-info" onclick="top.openPopup('app/loadUrl/app/popup_pegawai')">
                                                <i class="fa fa-search"></i> Cari Pejabat</a>
                                        </div>
                                    </div>
                                </div>
                            </div> 
    
                            <input type="hidden" name="id" value="<?= $id ?>" />
                            <input type="hidden" name="mode" value="<?= $mode ?>" />
    
                            <div class="form-group">
                                <label class="control-label col-md-2">&nbsp;</label>
                                <div class='col-md-10'>
                                    <a href="javascript:void(0)" class="btn btn-primary" onclick="submitForm()"><i class="fa fa-save"></i> Submit</a>
                                    <a href="javascript:void(0)" class="btn btn-warning" onclick="clearForm()"><i class="fa fa-trash"></i> Clear</a>
                                </div>
                            </div>
                            <?=$csrf->echoInputField();?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- EASYUI 1.4.5 -->
    <link rel="stylesheet" type="text/css" href="libraries/jquery-easyui-1.4.5/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="libraries/jquery-easyui-1.4.5/themes/icon.css">
    <script type="text/javascript" src="libraries/jquery-easyui-1.4.5/jquery.easyui.min.js"></script>

    <script>
        function submitForm() {
            $('#ff').form('submit', {
                url: 'satuan_kerja_json/add',
                onSubmit: function() {
                    if ($(this).form('validate')) {
                        var win = $.messager.progress({
                            title: 'TMJ | Aplikasi Manajemen Kearsipan',
                            msg: 'process data...'
                        });
                    }
                    return $(this).form('validate');
                },
                success: function(data) {
                    $.messager.progress('close');
                    arrData = data.split("|");
                    if (arrData[0] == "GAGAL") {
                        $.messager.alert('Info', arrData[1], 'info');
                    } else {
                        $.messager.alertLink('Info', arrData[1], 'info', '', 'app/loadUrl/app/konfigurasi_satuan_kerja_add/?id=' + arrData[2]);
                    }
                }
            });
        }

        function clearForm() {
            $('#ff').form('clear');
        }
    </script>
</body>

</html>