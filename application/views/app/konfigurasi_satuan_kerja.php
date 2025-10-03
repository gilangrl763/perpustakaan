<?
$this->load->library("crfs_protect"); 
$csrf = new crfs_protect('_crfs_6Dp51Fm');

include_once("functions/string.func.php");
include_once("functions/date.func.php");


if ($this->input->get("PERUSAHAAN_ID") == "") {
    $PERUSAHAAN_ID = $this->PERUSAHAAN_ID;
} else {
    $PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");
}

if ($this->input->get("CABANG_ID") == "") {
    $CABANG_ID = $this->CABANG_ID;
} else {
    $PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");
}

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

    <link rel="stylesheet" href="css/gaya.css" type="text/css">
    <link rel="stylesheet" href="libraries/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="judul-halaman">Satuan Kerja</div>
                <div class="konten">
                    <div class="area-menu-aksi">
                        <a class="btn btn-primary" id="btnAdd"><i class="fa fa-plus"></i> Tambah</a>
                        <a class="btn btn-info" id="btnEdit"><i class="fa fa-pencil"></i> Ubah</a>
                        <a class="btn btn-danger" id="btnDelete"><i class="fa fa-trash"></i> Hapus</a>
                        <a class="btn btn-success" id="btnAktif" style="display:none"><i class="fa fa-toggle-on"></i> Aktif</a>
                        <a class="btn btn-warning" id="btnNonAktif" style="display:none"><i class="fa fa-toggle-off"></i> Tidak Aktif</a>
                        <label class="ms-3">Perusahaan :</label>
                        <input type="text" name="PERUSAHAAN_ID" class="easyui-combobox" 
                        id="PERUSAHAAN_ID" data-options="width:'400',editable:false, valueField:'id',textField:'text',url:'perusahaan_json/combo',
                        onSelect: function(rec){
                        var url = 'cabang_json/combo/?PERUSAHAAN_ID='+rec.id;
                        $('#CABANG_ID').combobox('reload', url);
                        $('#CABANG_ID').combobox('setValue', '');
                        }" value="<?= $PERUSAHAAN_ID ?>" required />
                        <label class="ms-3">Cabang :</label>
                        <input type="text" name="CABANG_ID" class="easyui-combobox" 
                        id="CABANG_ID" data-options="width:'400',editable:false, valueField:'id',textField:'text',url:'cabang_json/combo'" 
                        value="<?= $CABANG_ID ?>" required />
                        <label class="ms-3">Status :</label>
                        <input name="STATUS" class="easyui-combobox textbox form-control" 
                        id="STATUS" data-options="width:'120',panelHeight:'100',panelWidth:'120',editable:false,valueField:'id',textField:'text',url:'combo_json/comboStatusAktif'" 
                        value="<?= $STATUS ?>" required />
                    </div>
                    <section>
                        <div id="tableContainer" class="tableContainer tableContainer-treegrid">
                            <table id="treeSatker" class="easyui-treegrid" style="min-width:100%;" data-options="
                                    url: 'satuan_kerja_json/treetable_master/?PERUSAHAAN_ID=<?= $PERUSAHAAN_ID ?>&CABANG_ID=<?= $CABANG_ID ?>&STATUS=<?= $STATUS ?>',
                                    dnd: true,
                                    animate:true,           
                                    pagination: true,            
                                    pageSize: 500,
                                    pageList: [500, 1000],
                                    
                                    method: 'get',
                                    idField: 'id',
                                    treeField: 'NAMA',
                                    
                                    onBeforeLoad: function(row,param){
                                        if (!row) {    // load top level rows
                                            param.id = 0;    // set id=0, indicate to load new page rows
                                        }
                                    }
                                ">
                                <thead>
                                    <tr>
                                        <th data-options="field:'NAMA'">Jabatan</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </section>
                </div>

            </div>
        </div>
    </div>

    <script type='text/javascript' src="libraries/bootstrap/js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var anSelectedId = "";

            $('#PERUSAHAAN_ID').combobox({
                onSelect: function(param) {
                    var url = 'cabang_json/combo/?PERUSAHAAN_ID=' + param.id;
                    $('#CABANG_ID').combobox('reload', url);
                    $('#CABANG_ID').combobox('setValue', '');
                }
            });

            $('#CABANG_ID').combobox({
                onSelect: function(param) {
                    var urlApp = "satuan_kerja_json/treetable_master/?PERUSAHAAN_ID=" + $('#PERUSAHAAN_ID').combobox('getValue') +
                        "&CABANG_ID=" + $('#CABANG_ID').combobox('getValue') +
                        "&STATUS=" + $('#STATUS').combobox('getValue');
                    $('#treeSatker').treegrid({
                        url: urlApp
                    });
                }
            });

            $('#STATUS').combobox({
                onSelect: function(param) {
                    var urlApp = "satuan_kerja_json/treetable_master/?PERUSAHAAN_ID=" + $('#PERUSAHAAN_ID').combobox('getValue') +
                        "&CABANG_ID=" + $('#CABANG_ID').combobox('getValue') +
                        "&STATUS=" + $('#STATUS').combobox('getValue');
                    $('#treeSatker').treegrid({
                        url: urlApp
                    });
                }
            });


            $('input[name=PENCARIAN]').keyup(function() {
                var value = this.value;
                $("html, body").animate({
                    scrollTop: 0
                });

                var urlApp = 'satuan_kerja_json/treetable_master/?PENCARIAN=' + value;
                $('#treeSatker').treegrid({
                    url: urlApp
                });
            });

            $('#treeSatker').treegrid({
                onDblClickRow: function(param) {
                    document.location.href = "app/index/konfigurasi_satuan_kerja_add?reqId=" + param.id;
                },
                onClickRow: function(param) {
                    if (param.STATUS == "AKTIF") {
                        $('#btnNonAktif').show();
                        $('#btnAktif').hide();
                    } else {
                        $('#btnNonAktif').hide();
                        $('#btnAktif').show();
                    }

                    anSelectedId = param.id;
                }
            });



            $('#btnAdd').on('click', function() {
                document.location.href = "app/loadUrl/app/konfigurasi_satuan_kerja_add/";
            });

            $('#btnEdit').on('click', function() {
                if (anSelectedId == "") {
                    return false;
                }

                document.location.href = "app/loadUrl/app/konfigurasi_satuan_kerja_add/?id=" + anSelectedId;
            });

            $('#btnAktif').on('click', function() {
                if (anSelectedId == "") {
                    $.messager.alert('Informasi', 'Pilih data terlebih dahulu', 'info');
                    return false;
                }

                konfirmasiData("satuan_kerja_json/aktif", anSelectedId, "Apakah anda ingin mengaktifkan data?", "app/loadUrl/app/konfigurasi_satuan_kerja/?STATUS="+$('#STATUS'). combobox('getValues'));
            });

            $('#btnNonAktif').on('click', function() {
                if (anSelectedId == "") {
                    $.messager.alert('Informasi', 'Pilih data terlebih dahulu', 'info');
                    return false;
                }

                konfirmasiData("satuan_kerja_json/non_aktif", anSelectedId, "Apakah anda ingin menonaktifkan data?", "app/loadUrl/app/konfigurasi_satuan_kerja/?STATUS="+$('#STATUS'). combobox('getValues'));
            });

            $('#btnDelete').on('click', function() {
                if (anSelectedData == "") {
                    $.messager.alert('Informasi', 'Pilih data terlebih dahulu', 'info');
                    return false;
                }

                deleteData("satuan_kerja_json/delete", anSelectedId, "app/loadUrl/app/konfigurasi_satuan_kerja/?STATUS="+$('#STATUS'). combobox('getValues'));
            });
        });
    </script>
</body>

</html>