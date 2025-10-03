<?
$this->load->library("crfs_protect");
$csrf = new crfs_protect('_crfs_6Dp51Fm');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

$this->load->model("UserLogin");
$user_login = new UserLogin();

$id = $this->input->get("id");

if ($id == "") {
    $mode = "insert";
} else {
    $mode = "update";

    $user_login->selectByParams(array("A.USER_LOGIN_ID" => $id));
    // echo $user_login->query;
    $user_login->firstRow();

    $USER_LOGIN                 = $user_login->getField("USER_LOGIN");
    $USER_GROUP                 = $user_login->getField("USER_GROUP");
    $NAMA                       = $user_login->getField("NAMA");
    $SATUAN_KERJA_ID            = $user_login->getField("SATUAN_KERJA_ID");
    $PEGAWAI_ID                 = $user_login->getField("PEGAWAI_ID");
    $PERUSAHAAN_ID              = $user_login->getField("PERUSAHAAN_ID");
    $CABANG_ID                  = $user_login->getField("CABANG_ID");
    $EMAIL                      = $user_login->getField("EMAIL");
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
                    <a href="app/loadUrl/app/master_user_login">User Login</a>
                    &bull; Kelola User Login
                </div>
                <div class="area-form">
                    <div class="konten">
                        <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">

                            <div class="form-group">
                                <label class="control-label col-md-2">Perusahaan</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control easyui-combobox" id="PERUSAHAAN_ID" 
                                            name="PERUSAHAAN_ID" data-options="width:'725',panelHeight:'120',panelWidth:'725',editable:false,
                                            valueField:'id',textField:'text',url:'perusahaan_json/combo',
                                            onSelect: function(rec){
                                                var url = 'cabang_json/combo/?PERUSAHAAN_ID='+rec.id;
                                                $('#CABANG_ID').combobox('reload', url);
                                                $('#CABANG_ID').combobox('setValue', '');
                                                $('#SATUAN_KERJA_ID_PARENT').combotree('setValue', '');
                                            }" value="<?= $PERUSAHAAN_ID ?>" disabled />
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
                                            name="CABANG_ID" data-options="width:'725',panelHeight:'120',panelWidth:'725',editable:false,
                                            valueField:'id',textField:'text',url:'cabang_json/combo',
                                            onSelect: function(rec){
                                                var url = 'satuan_kerja_json/combotree_form/?PERUSAHAAN_ID='+$('#PERUSAHAAN_ID').combobox('getValue')+'&CABANG_ID='+rec.id;
                                                $('#SATUAN_KERJA_ID_PARENT').combotree('reload', url);
                                                $('#SATUAN_KERJA_ID_PARENT').combotree('setValue', '');
                                            }" value="<?= $CABANG_ID ?>" disabled />
                                        </div>
                                    </div>

                                </div>
                            </div>


                            <div class="form-group">
                                <label class="control-label col-md-2">Pegawai</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control easyui-combotree" id="PEGAWAI_ID" 
                                            name="PEGAWAI_ID" data-options="width:'725',panelHeight:'120',panelWidth:'725',editable:false,
                                            valueField:'id',textField:'text',url:'pegawai_json/combo'" value="<?= $PEGAWAI_ID ?>" disabled />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">User Group</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                        <?
                                        $USER_GROUP_ID_PILIH = str_replace(",", "','", $USER_GROUP);
                                        ?>
                                        <input type="text" class="form-control easyui-combotree" id="USER_GROUP_ID_PILIH" 
                                        name="USER_GROUP_ID_PILIH" data-options="width:'300',panelHeight:'120',panelWidth:'300',editable:false,
                                        valueField:'id',textField:'text',url:'combo_json/combo_user_group',multiple:true,value:['<?=$USER_GROUP_ID_PILIH?>']" required />
                                        <input type="hidden" name="USER_GROUP" id="USER_GROUP" value="<?= $USER_GROUP ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            

                            <div class="form-group">
                                <label class="control-label col-md-2">User Login</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control easyui-validatebox" id="USER_LOGIN" 
                                            name="USER_LOGIN" value="<?=$USER_LOGIN?>" required />
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
                            <?= $csrf->echoInputField(); ?>
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
                url: 'user_login_json/add',
                onSubmit: function() {
                    $("#USER_GROUP").val($("#USER_GROUP_ID_PILIH").combotree("getValues"));
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
                        $.messager.alertLink('Info', arrData[1], 'info', '', 'app/loadUrl/app/master_user_login_add/?id=' + arrData[2]);
                    }
                }
            });
        }

        function clearForm() {
            $('#ff').form('clear');
        }

        function copyPasteEmail(){
            $("#USER_LOGIN").val($("#EMAIL").val());   
        }
    </script>
</body>

</html>