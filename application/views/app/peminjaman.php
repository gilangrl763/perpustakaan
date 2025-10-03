<?
if ($this->USER_GROUP == "ARSIPARIS") {
} else {
    redirect('app/loadUrl/app/error_403');
}

$this->load->library("crfs_protect");
$csrf = new crfs_protect('_crfs_6Dp51Fm');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

if ($this->input->get("STATUS") == "") {
    $STATUS = "SIMPAN";
}
else {
    $STATUS = $this->input->get("STATUS");
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

    <title>AP1-ARFF</title>

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

    <!-- DATATABLE -->
    <link rel="stylesheet" type="text/css" href="libraries/DataTables-1.10.7/media/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="libraries/DataTables-1.10.7/examples/resources/syntax/shCore.css">
    <link rel="stylesheet" type="text/css" href="libraries/DataTables-1.10.7/examples/resources/demo.css">
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
    <script type="text/javascript" language="javascript" src="libraries/DataTables-1.10.7/media/js/jquery.js"></script>
    <script type="text/javascript" language="javascript" src="libraries/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" language="javascript" src="libraries/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
    <script type="text/javascript" language="javascript" src="libraries/DataTables-1.10.7/examples/resources/demo.js"></script>

    <script type="text/javascript" language="javascript" class="init">
        $(document).ready(function() {

            oTable = $('#example').dataTable({
                bJQueryUI: true,
                "iDisplayLength": 50,
                /* UNTUK MENGHIDE KOLOM ID */
                "aoColumns": [
                    {
                        bVisible: false
                    },
                    {
                        bVisible: false
                    },
                    {
                        bVisible: false
                    },
                    {
                        bVisible: false
                    },
                    {
                        bVisible: false
                    },
                    {
                        bVisible: false
                    },
                    {
                        bVisible: false
                    },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ],
                "bSort": true,
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": "peminjaman_json/json/?reqToken=<?= $csrf->getToken()?>&STATUS=<?=$STATUS?>",
                columnDefs: [{
                    className: 'never',
                    targets: [0, 1, 2, 3, 4, 5, 6]
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

                // UNTUK NONAKTIFKAN
                if(anSelectedStatus == 'AKTIF'){
                    $('#btnNonAktif').show();
                    $('#btnAktif').hide();
                }
                else{
                    $('#btnNonAktif').hide();
                    $('#btnAktif').show();
                }
                // END
            });

            $('#rightclickarea').bind('contextmenu', function(e) {
                if (anSelectedData == '') {
                    return false;
                }
                var $cmenu = $(this).next();
                $('<div class="overlay"></div>').css({
                    left: '0px',
                    top: '0px',
                    position: 'absolute',
                    width: '100%',
                    height: '100%',
                    zIndex: '0'
                }).click(function() {
                    $(this).remove();
                    $cmenu.hide();
                }).bind('contextmenu', function() {
                    return false;
                }).appendTo(document.body);
                $(this).next().css({
                    left: e.pageX,
                    top: e.pageY,
                    zIndex: '1'
                }).show();

                return false;
            });

            $('.vmenu .first_li').on('click', function() {
                if ($(this).children().size() == 1) {
                    if ($(this).children().text() == 'Ubah') {
                        $("#btnEdit").click();
                    } else if ($(this).children().text() == 'Hapus') {
                        $("#btnDeleteRow").click();
                    }
                    $('.vmenu').hide();
                    $('.overlay').hide();
                }
            });

            $(".first_li , .sec_li, .inner_li span").hover(function() {
                    $(this).css({
                        backgroundColor: '#E0EDFE',
                        cursor: 'pointer'
                    });

                    if ($(this).children().size() > 0) {
                        $(this).find('.inner_li').show();
                        $(this).css({
                            cursor: 'default'
                        });
                    }
                },
                function() {
                    $(this).css('background-color', '#fff');
                    $(this).find('.inner_li').hide();
                });

            $('#btnAdd').on('click', function() {
                document.location.href = 'app/loadUrl/app/peminjaman_add';
            });

            $('#btnEdit').on('click', function() {
                if (anSelectedId == "") {
                    return false;
                }

                document.location.href = "app/loadUrl/app/peminjaman_add/?id=" + anSelectedId;
            });

            $('#btnAktif').on('click', function() {
                if (anSelectedData == "") {
                    $.messager.alert('Informasi', 'Pilih data terlebih dahulu', 'info');
                    return false;
                }

                konfirmasiData("peminjaman_json/aktif", anSelectedId, "Apakah anda ingin mengaktifkan data?", "app/loadUrl/app/peminjaman/?STATUS="+$('#STATUS'). combobox('getValues'));
            });

            $('#btnNonAktif').on('click', function() {
                if (anSelectedData == "") {
                    $.messager.alert('Informasi', 'Pilih data terlebih dahulu', 'info');
                    return false;
                }

                konfirmasiData("peminjaman_json/non_aktif", anSelectedId, "Apakah anda ingin menonaktifkan data?", "app/loadUrl/app/peminjaman/?STATUS="+$('#STATUS'). combobox('getValues'));
            });

            $('#btnDelete').on('click', function() {
                if (anSelectedData == "") {
                    $.messager.alert('Informasi', 'Pilih data terlebih dahulu', 'info');
                    return false;
                }
                deleteData("peminjaman_json/delete", anSelectedId, "app/loadUrl/app/peminjaman/?id=<?=$id?>");
            });

            $('#STATUS').combobox({
                onSelect: function(param){
                    oTable.fnReloadAjax("peminjaman_json/json/?reqToken=<?=$csrf->getToken()?>&STATUS="+$('#STATUS'). combobox('getValues'));
                }
            });
        });
    </script>
    <link rel="stylesheet" href="css/gaya.css" type="text/css">
    <link rel="stylesheet" href="libraries/font-awesome-4.7.0/css/font-awesome.css" type="text/css">

</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="judul-halaman">Peminjaman</div>
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
                                    <th>PEMINJAMAN_ID</th>
                                    <th>PEMINDAHAN_ID</th>
                                    <th>PERUSAHAAN_ID</th>
                                    <th>CABANG_ID</th>
                                    <th>SATUAN_KERJA_ID</th>
                                    <th>PEGAWAI_ID</th>
                                    <th>STATUS</th>
                                    <th width="10%">Tanggal Pinjam</th>
                                    <th width="10%">Waktu Pinjam</th>
                                    <th width="10%">Tanggal Kembali</th>
                                    <th width="10%">Waktu Kembali</th>
                                    <th width="10%">Keterangan</th>
                                    <th width="10%">Dokumen</th>
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