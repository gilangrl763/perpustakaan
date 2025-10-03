<?
$this->load->library("crfs_protect"); 
$csrf = new crfs_protect('_crfs_6Dp51Fm');

include_once("functions/string.func.php");
include_once("functions/date.func.php");
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

    <style type="text/css" class="init">
    th, td { white-space: nowrap; }
    div.dataTables_wrapper {
        /*width: 100%;
        margin: 0 auto;
        border: 2px solid red;
        height: calc(100vh - 80px);*/
    }
    .alert { 
        padding: 7px;
        margin-bottom:0px;
    }
    </style>

    <!-- EASYUI 1.4.5 -->
    <link rel="stylesheet" type="text/css" href="libraries/jquery-easyui-1.4.5/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="libraries/jquery-easyui-1.4.5/themes/icon.css">
    <script type="text/javascript" src="libraries/jquery-easyui-1.4.5/jquery.easyui.min.js"></script>

    <script type="text/javascript" language="javascript" class="init">  
    $(document).ready( function () {
        $("#reqPencarian").focus();
        $('#reqUnitKerjaId').combobox({
            onSelect: function(param){
                
                if(param.id == 0)
                {
                    document.location.href = "app/loadUrl/app/daftar_alamat_tujuan_lookup/?reqJenis=<?=$reqJenis?>&reqJenisSurat=<?=$reqJenisSurat?>&reqJenisNaskahId=<?=$reqJenisNaskahId?>";  
                    return;
                }
                else if(param.id == 1)
                {
                    document.location.href = "app/loadUrl/app/kelompok_tujuan_lookup/?reqJenis=<?=$reqJenis?>&reqJenisSurat=<?=$reqJenisSurat?>&reqJenisNaskahId=<?=$reqJenisNaskahId?>";   
                    return;                 
                }
                else if(param.id == 2)
                {
                    document.location.href = "app/loadUrl/app/pegawai_tujuan_lookup/?reqJenis=<?=$reqJenis?>&reqJenisSurat=<?=$reqJenisSurat?>&reqJenisNaskahId=<?=$reqJenisNaskahId?>";   
                    return;                 
                }

                var urlApp = 'web/satuan_kerja_json/treetable/?reqUnitKerjaId='+ param.id+'&reqJenisRegister=<?=$reqJenisRegister?>&reqIsPihakEksternal=<?=$reqIsPihakEksternal?>&reqPencarian='+$("#reqPencarian").val();
                $('#treeData').treegrid(
                {
                    url: urlApp
                }); 
            }
        });
        
        
        $('input[name=reqPencarian]').keyup(function() {
            var value = this.value;
            $("html, body").animate({ scrollTop: 0 });
            
                
            var urlApp = 'web/satuan_kerja_json/treetable/?reqUnitKerjaId='+$('#reqUnitKerjaId').combobox("getValue")+'&reqJenisRegister=<?=$reqJenisRegister?>&reqPencarian='+value;
            $('#treeData').treegrid(
            {
                url: urlApp
            }); 
        });
        
        $('#treeData').treegrid({
            onDblClickRow: function(param){
                if(typeof param.JABATAN == 'undefined' || param.JABATAN == '' || param.JABATAN == null) 
                {
                    $.messager.alert('Info', "Pejabat belum ditentukan.", 'info');  
                    return;
                }

                if(typeof param.NIP == 'undefined' || param.NIP == '' || param.NIP == null) 
                {
                    $.messager.alert('Info', "Pejabat belum ditentukan.", 'info');  
                    return;
                }
                    
                var tujuan = param.JABATAN + '['+param.SATUAN_KERJA_ID_PARENT+']';
                top.document.getElementById('contentFrame').contentWindow.addSatuanKerja('<?=$reqJenis?>', param.SATUAN_KERJA_ID, tujuan);
                top.closePopup();
            }
        });

        $('#btnPilih').on('click', function (){
            rv = true;

            var arrData = new Array();
            $('input[type=checkbox]').each(function () {
                var sThisVal = (this.checked ? "1" : "0");
                if(sThisVal == "1")
                {
                    arrData.push(this.value);
                }

            });


            if(arrData.length == 0)
            {
               $.messager.alert('Informasi','Pilih data terlebih dahulu','info');
               rv = false;
               return;
            }

            if(rv == true)
            {
                var ss = [];
                var rows = $('#treeData').treegrid('getSelections');
                for(var i=0; i<rows.length; i++){
                    var row = rows[i];
                    var tujuan = row.JABATAN + '['+row.SATUAN_KERJA_ID_PARENT+']';
                    top.document.getElementById('contentFrame').contentWindow.addSatuanKerja('<?=$reqJenis?>', row.SATUAN_KERJA_ID, tujuan);
                }

                top.closePopup();
            }
        });

        $('#btnRefresh').on('click', function (){
            document.location.href = "app/loadUrl/app/satuan_kerja_tujuan_lookup/?reqJenis=<?=$reqJenis?>&reqJenisSurat=<?=$reqJenisSurat?>&reqJenisRegister=<?=$reqJenisRegister?>&reqIsPihakEksternal=<?=$reqIsPihakEksternal?>";
        });
    });
        
    $("#dnd-example tr").click(function(){
       $(this).addClass('selected').siblings().removeClass('selected');
       var id = $(this).find('td:first').attr('id');
       var title = $(this).find('td:first').attr('title');

        
    });
    </script>  
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="judul-halaman">
                    Data Pegawai
                </div>
                <div class="konten">
                    <div class="row"> 
                        <div class="col-md-12">
                            <div id="bluemenu" class="aksi-area">
                                <div class="row"> 
                                    <div class="col-md-8">
                                        <button class="btn btn-sm btn-primary float-start mr-1" id="btnPilih"><i class="fa fa-check-square-o"></i> Pilih</button>
                                        <button class="btn btn-sm btn-success float-start mr-1" id="btnRefresh"><i class="fa fa-refresh"></i> Reload</button>
                                        <input type="text" name="reqPencarian" class="easyui-validatebox form-control float-start" id="reqPencarian" 
                                        placeholder="Cari...." style="width:50%">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="badge badge-danger float-end">
                                            <i class="fa fa-exclamation-triangle"></i> Double click data yang dipilih, atau ceklis beberapa data kemudian klik <b>Pilih</b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-1">
                            <div id="tableContainer" class="tableContainer tableContainer-treegrid">
                                <table id="treeData" class="easyui-treegrid" style="min-width:100%;width:1700px; min-height:500px; height:570px;"
                                        data-options="
                                            url: 'pegawai_json/treetable',
                                            pagination: true, 
                                            method: 'get',
                                            idField: 'id',
                                            treeField: 'NAMA',
                                            checkbox: true,
                                            singleSelect:false,
                                            multiple: true,
                                            fitColumns:true,
                                            rownumbers: false,
                                            onBeforeLoad: function(row,param){
                                                if (!row) {    // load top level rows
                                                    param.id = 0;    // set id=0, indicate to load new page rows
                                                }
                                            }
                                        ">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'ck',checkbox:true"></th>
                                            <th data-options="field:'NAMA',width:150">Nama</th>
                                            <th data-options="field:'JABATAN',width:250">Jabatan</th>
                                            <th data-options="field:'SATUAN_KERJA',width:250">Satuan Kerja</th>
                                            <th data-options="field:'CABANG',width:250">Cabang</th>
                                            <th data-options="field:'PERUSAHAAN',width:250">Perusahaan</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>