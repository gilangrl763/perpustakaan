<?
$this->load->library("crfs_protect"); 
$csrf = new crfs_protect('_crfs_6Dp51Fm');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

$this->load->model("LokasiSimpan");
$lokasi_simpan = new LokasiSimpan();

$id = $this->input->get("id");

if ($id == "") {
    $mode = "insert";
} else {
    $mode = "update";

    $lokasi_simpan->selectByParams(array("A.LOKASI_SIMPAN_ID" => $id));
    // echo $lokasi_simpan->query;
    $lokasi_simpan->firstRow();

    $PERUSAHAAN_ID  = $lokasi_simpan->getField("PERUSAHAAN_ID");
    $CABANG_ID      = $lokasi_simpan->getField("CABANG_ID");
    $KODE           = $lokasi_simpan->getField("KODE");
    $RUANG          = $lokasi_simpan->getField("RUANG");
    $LEMARI         = $lokasi_simpan->getField("LEMARI");
    $NOMOR_BOKS     = $lokasi_simpan->getField("NOMOR_BOKS");
    $NOMOR_FOLDER   = $lokasi_simpan->getField("NOMOR_FOLDER");
    $KETERANGAN     = $lokasi_simpan->getField("KETERANGAN");
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
                    <a href="app/loadUrl/app/master_data_lokasi_simpan">Lokasi Simpan</a>
                    &bull; Kelola Lokasi Simpan
                </div>
                <div class="area-form">
                    <div class="konten">
                        <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">

                            <div class="form-group">
                                <label class="control-label col-md-2">Kode</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input class="form-control easyui-validatebox" type="text" name="KODE" 
                                            value="<?=$KODE?>" readonly style="width:100%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Perusahaan</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control easyui-combobox" id="PERUSAHAAN_ID" 
                                            name="PERUSAHAAN_ID" data-options="width:'745',panelHeight:'120',panelWidth:'745',editable:false,
                                            valueField:'id',textField:'text',url:'perusahaan_json/combo',
                                            onSelect: function(rec){
                                                var url = 'cabang_json/combo/?PERUSAHAAN_ID='+rec.id;
                                                $('#CABANG_ID').combobox('reload', url);
                                                $('#CABANG_ID').combobox('setValue', '');
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
                                            name="CABANG_ID" data-options="width:'745',panelHeight:'120',panelWidth:'745',editable:false,
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
                                <label class="control-label col-md-2">Ruang</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input class="form-control easyui-textbox" type="text" name="RUANG" 
                                            value="<?= $RUANG ?>" data-options="required:true" style="width:100%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Lemari</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input class="form-control easyui-textbox" type="text" name="LEMARI" 
                                            value="<?= $LEMARI ?>" data-options="required:true" style="width:100%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Nomor Boks</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input class="form-control easyui-textbox" type="text" name="NOMOR_BOKS" 
                                            value="<?= $NOMOR_BOKS ?>" data-options="required:true" style="width:100%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Nomor Folder</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input class="form-control easyui-textbox" type="text" name="NOMOR_FOLDER" 
                                            value="<?= $NOMOR_FOLDER ?>" data-options="required:true" style="width:100%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Keterangan</label>
                                <div class='col-md-10'>
                                    <div class='form-group'>
                                        <div class='col-md-6'>
                                            <textarea name="KETERANGAN" style="height:100px; width:100%;"><?= $KETERANGAN ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="id" value="<?=$id?>" />
                            <input type="hidden" name="mode" value="<?=$mode?>" />

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
                url: 'lokasi_simpan_json/add',
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
                        $.messager.alertLink('Info', arrData[1], 'info', '', 'app/loadUrl/app/master_data_lokasi_simpan_add/?id=' + arrData[2]);
                    }
                }
            });
        }

        function clearForm() {
            $('#ff').form('clear');
        }
    </script>
    <!-- TINY MCE -->
    <script src="libraries/tinymce_full/js/tinymce/tinymce.min.js"></script>
    <script type="text/javascript">
        tinymce.init({
            selector: "textarea",
            height: 200,
            plugins: "print preview powerpaste casechange importcss tinydrive searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker imagetools textpattern noneditable help formatpainter permanentpen pageembed charmap tinycomments mentions quickbars linkchecker emoticons advtable",
            menubar: 'file edit view insert format tools table tc help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment',
            font_formats: "Calibri=calibri; Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Oswald=oswald; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats",
            image_advtab: true,
            link_list: [{
                    title: 'My page 1',
                    value: 'https://www.tiny.cloud'
                },
                {
                    title: 'My page 2',
                    value: 'http://www.moxiecode.com'
                }
            ],
            image_list: [{
                    title: 'My page 1',
                    value: 'https://www.tiny.cloud'
                },
                {
                    title: 'My page 2',
                    value: 'http://www.moxiecode.com'
                }
            ],
            image_class_list: [{
                    title: 'None',
                    value: ''
                },
                {
                    title: 'Some class',
                    value: 'class-name'
                }
            ],
            importcss_append: true,
            templates: [{
                    title: 'New Table',
                    description: 'creates a new table',
                    content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>'
                },
                {
                    title: 'Starting my story',
                    description: 'A cure for writers block',
                    content: 'Once upon a time...'
                },
                {
                    title: 'New list with dates',
                    description: 'New List with dates',
                    content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>'
                }
            ],
            template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
            template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
            image_caption: true,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            noneditable_noneditable_class: 'mceNonEditable',
            toolbar_mode: 'sliding',
            spellchecker_ignore_list: ['Ephox', 'Moxiecode'],
            tinycomments_mode: 'embedded',
            content_style: '.mymention{ color: gray; }',
            contextmenu: 'link image imagetools table configurepermanentpen',
            a11y_advanced_options: true,

            setup: function(ed) {
                ed.on('focus', function() {
                    $(this.contentAreaContainer.parentElement).find("div.mce-toolbar-grp").show();
                    $(this.contentAreaContainer.parentElement).find("div.mce-toolbar").show();
                    $(this.contentAreaContainer.parentElement).find("div.mce-tinymce").show();
                    //$(this.contentAreaContainer.parentElement).find("div.mce-container-body.mce-stack-layout").show();                    
                });
                ed.on('blur', function() {
                    $(this.contentAreaContainer.parentElement).find("div.mce-toolbar-grp").hide();
                    $(this.contentAreaContainer.parentElement).find("div.mce-toolbar").hide();
                    $(this.contentAreaContainer.parentElement).find("div.mce-tinymce").hide();
                    //$(this.contentAreaContainer.parentElement).find("div.mce-container-body.mce-stack-layout").hide();
                });
                ed.on("init", function() {
                    $(this.contentAreaContainer.parentElement).find("div.mce-toolbar-grp").hide();
                    $(this.contentAreaContainer.parentElement).find("div.mce-toolbar").hide();
                    $(this.contentAreaContainer.parentElement).find("div.mce-tinymce").hide();
                    //$(this.contentAreaContainer.parentElement).find("div.mce-container-body.mce-stack-layout").hide();
                });
            }

        });
    </script>
</body>

</html>