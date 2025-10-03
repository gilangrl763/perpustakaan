<?
if ($this->USER_GROUP == "ADMIN") {
}
else{
    redirect('app/loadUrl/app/error_403');
}

$this->load->library("crfs_protect"); 
$csrf = new crfs_protect('_crfs_6Dp51Fm');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

if ($this->input->get("STATUS") == "") {
    $STATUS = "AKTIF";
} else {
    $STATUS = $this->input->get("STATUS");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="libraries/bootstrap-3.3.7/docs/favicon.ico">

    <title>TMJ</title>
    <link href="libraries/bootstrap-3.3.7/docs/dist/css/bootstrap.css" rel="stylesheet">
    <link href="libraries/bootstrap-3.3.7/docs/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
    <script src="libraries/bootstrap-3.3.7/docs/assets/js/ie-emulation-modes-warning.js"></script>


    <!-- DATATABLE -->
    <style type="text/css" class="init">
        th,
        td {
            white-space: nowrap;
        }

        div.dataTables_wrapper {
            /*width: 100%;
            margin: 0 auto;
            border: 2px solid red;
            height: calc(100vh - 80px);*/
        }
    </style>
    <link rel="stylesheet" type="text/css" href="libraries/DataTables-1.10.7/media/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="libraries/DataTables-1.10.7/examples/resources/syntax/shCore.css">
    <script type="text/javascript" language="javascript" src="libraries/DataTables-1.10.7/media/js/jquery.js"></script>
    <script type="text/javascript" language="javascript" src="libraries/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" language="javascript" src="libraries/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>

    <link rel="stylesheet" href="css/gaya.css" type="text/css">
    <link rel="stylesheet" href="libraries/font-awesome-4.7.0/css/font-awesome.css" type="text/css">

    <script type="text/javascript" language="javascript" class="init">
        $(document).ready(function() {

            oTable = $('#example').dataTable({
                bJQueryUI: true,
                "iDisplayLength": 50,
                /* UNTUK MENGHIDE KOLOM ID */
                "aoColumns": [{
                        bVisible: false
                    },
                    {
                        bVisible: false
                    },
                    null,
                    null,
                    null,
                    null
                ],
                "bSort": true,
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": "media_simpan_json/json/?reqToken=<?=$csrf->getToken()?>&STATUS=<?= $STATUS ?>",
                columnDefs: [{
                    className: 'never',
                    targets: [0, 1]
                }],
                "sPaginationType": "full_numbers"
            });
            /* Click event handler */

            /* RIGHT CLICK EVENT */
            var anSelectedData = '';
            var anSelectedId = '';
            var anSelectedPosition = '';

            function fnGetSelected(oTableLocal) {
                var aReturn = new Array();
                var aTrs = oTableLocal.fnGetNodes();
                for (var i = 0; i < aTrs.length; i++) {
                    if ($(aTrs[i]).hasClass('selected')) {
                        aReturn.push(aTrs[i]);
                        anSelectedPosition = i;
                    }
                }
                return aReturn;
            }

            $("#example tbody").click(function(event) {
                $(oTable.fnSettings().aoData).each(function() {
                    $(this.nTr).removeClass('selected');
                });
                $(event.target.parentNode).addClass('selected');

                var anSelected = fnGetSelected(oTable);
                anSelectedData = String(oTable.fnGetData(anSelected[0]));
                var element = anSelectedData.split(',');
                anSelectedId = element[0];
                anSelectedStatus = element[1];

                if(anSelectedStatus == 'AKTIF'){
                    $('#btnNonAktif').show();
                    $('#btnAktif').hide();
                }
                else{
                    $('#btnNonAktif').hide();
                    $('#btnAktif').show();
                }
            });

            $('#btnAdd').on('click', function() {
                document.location.href = 'app/loadUrl/app/master_data_media_simpan_add';
            });

            $('#btnEdit').on('click', function() {
                if (anSelectedData == "") {
                    $.messager.alert('Informasi','Pilih data terlebih dahulu','info');
                    return false;
                }

                document.location.href='app/loadUrl/app/master_data_media_simpan_add/?id='+anSelectedId;
            });

            $('#btnDelete').on('click', function() {
                if (anSelectedData == "") {
                    $.messager.alert('Informasi', 'Pilih data terlebih dahulu', 'info');
                    return false;
                }

                deleteData("media_simpan_json/delete",anSelectedId,"app/loadUrl/app/master_data_media_simpan/?STATUS="+$('#STATUS').combobox('getValues'));
            });

            $('#btnAktif').on('click', function() {
                if (anSelectedData == "") {
                    $.messager.alert('Informasi','Pilih data terlebih dahulu','info');
                    return false;
                }

                konfirmasiData("media_simpan_json/aktif",anSelectedId,"Apakah anda ingin mengaktifkan data?","app/loadUrl/app/master_data_media_simpan/?STATUS="+$('#STATUS').combobox('getValues'));
            });

            $('#btnNonAktif').on('click', function() {
                if (anSelectedData == "") {
                    $.messager.alert('Informasi','Pilih data terlebih dahulu','info');
                    return false;
                }

                konfirmasiData("media_simpan_json/non_aktif",anSelectedId,"Apakah anda ingin menonaktifkan data?","app/loadUrl/app/master_data_media_simpan/?STATUS="+$('#STATUS'). combobox('getValues'));
            });

            $('#STATUS').combobox({
                onSelect: function(param) {
                    oTable.fnReloadAjax("media_simpan_json/json/?reqToken=<?=$csrf->getToken()?>&STATUS="+$('#STATUS').combobox('getValues'));
                }
            });
        });
    </script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="judul-halaman">Media Simpan</div>
                <div class="konten">
                    <div class="area-menu-aksi">
                        <a class="btn btn-primary" id="btnAdd"><i class="fa fa-plus"></i> Tambah</a>
                        <a class="btn btn-info" id="btnEdit"><i class="fa fa-pencil"></i> Ubah</a>
                        <a class="btn btn-danger" id="btnDelete"><i class="fa fa-trash"></i> Hapus</a>
                        <a class="btn btn-success" id="btnAktif" style="display:none"><i class="fa fa-toggle-on"></i> Aktif</a>
                        <a class="btn btn-warning" id="btnNonAktif" style="display:none"><i class="fa fa-toggle-off"></i> Tidak Aktif</a>
                        <label class="ms-3">Status :</label>
                        <input name="STATUS" class="easyui-combobox" id="STATUS" 
                        data-options="width:'120',panelHeight:'100',panelWidth:'120',editable:false,valueField:'id',textField:'text',url:'combo_json/comboStatusAktif'" 
                        value="<?= $STATUS ?>" required />
                    </div>
                    <section>
                        <table id="example" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>MEDIA_SIMPAN_ID</th>
                                    <th>STATUS</th>
                                    <th width="10%">Kode</th>
                                    <th width="20%">Nama</th>
                                    <th width="30%">Keterangan</th>
                                    <th width="10%">Status</th>
                                </tr>
                            </thead>
                        </table>
                    </section>
                </div>

            </div>
        </div>
    </div>
</body>

</html>