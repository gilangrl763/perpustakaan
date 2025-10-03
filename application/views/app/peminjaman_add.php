<?
$this->load->library("crfs_protect"); 
$csrf = new crfs_protect('_crfs_6Dp51Fm');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

$this->load->model("Peminjaman");
$peminjaman = new Peminjaman();

$id = $this->input->get("id");

if ($id == "") {
    $mode = "insert";
    $KURUN_WAKTU = date("Y");
} else {
    $mode = "update";

    $peminjaman->selectByParams(array("PEMINJAMAN_ID" => $id));
    // echo $peminjaman->query;exit;
    $peminjaman->firstRow();
    $PEMINDAHAN_ID      = $peminjaman->getField("PEMINDAHAN_ID");
    $PERUSAHAAN_ID      = $peminjaman->getField("PERUSAHAAN_ID");
    $CABANG_ID          = $peminjaman->getField("CABANG_ID");
    $SATUAN_KERJA_ID    = $peminjaman->getField("SATUAN_KERJA_ID");
    $PEGAWAI_ID         = $peminjaman->getField("PEGAWAI_ID");
    $TANGGAL_PINJAM     = $peminjaman->getField("TANGGAL_PINJAM");
    $WAKTU_PINJAM       = $peminjaman->getField("WAKTU_PINJAM");
    $TANGGAL_KEMBALI    = $peminjaman->getField("TANGGAL_KEMBALI");
    $WAKTU_KEMBALI      = $peminjaman->getField("WAKTU_KEMBALI");
    $KETERANGAN         = $peminjaman->getField("KETERANGAN");
    $DOKUMEN            = $peminjaman->getField("DOKUMEN");
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

    <!-- EASYUI 1.4.5 -->
    <link rel="stylesheet" type="text/css" href="libraries/jquery-easyui-1.4.5/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="libraries/jquery-easyui-1.4.5/themes/icon.css">
    <script type="text/javascript" src="libraries/jquery-easyui-1.4.5/jquery.easyui.min.js"></script>

    <script>
        function submitForm() {
            $('#ff').form('submit', {
                url: 'peminjaman_json/add',
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
                        $.messager.alertLink('Info', arrData[1], 'info', '', 'app/loadUrl/app/peminjaman_add/?id=' + arrData[2]);
                    }
                }
            });
        }

        function clearForm() {
            $('#ff').form('clear');
        }
    </script>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="judul-halaman">
                    <a href="app/loadUrl/app/peminjaman">Peminjaman</a>
                    &bull; Form Peminjaman
                </div>
                <div class="area-form">
                    <div class="konten">
                        <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">

                        <div class="form-group">
                                <label class="control-label col-md-2">Pemindahan</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control easyui-combotree" id="PEMINDAHAN_ID" 
                                            name="PEMINDAHAN_ID" data-options="width:'725',panelHeight:'120',panelWidth:'725',editable:false,
                                            valueField:'id',textField:'text',url:'pemindahan_json/combo'" value="<?= $PEMINDAHAN_ID ?>" />
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
                                            name="PERUSAHAAN_ID" data-options="width:'725',panelHeight:'120',panelWidth:'725',editable:false,
                                            valueField:'id',textField:'text',url:'perusahaan_json/combo',
                                            onSelect: function(rec){
                                                var url = 'cabang_json/combo/?PERUSAHAAN_ID='+rec.id;
                                                $('#CABANG_ID').combobox('reload', url);
                                                $('#CABANG_ID').combobox('setValue', '');
                                            }" value="<?= $PERUSAHAAN_ID ?>"  />
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
                                            }" value="<?= $CABANG_ID ?>"  />
                                        </div>
                                    </div>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="control-label col-md-2">Satuan Kerja</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control easyui-combotree" id="SATUAN_KERJA_ID" 
                                            name="SATUAN_KERJA_ID" data-options="width:'725',panelHeight:'120',panelWidth:'725',editable:false,
                                            valueField:'id',textField:'text',url:'satuan_kerja_json/combo'" value="<?= $SATUAN_KERJA_ID ?>" />
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
                                            valueField:'id',textField:'text',url:'pegawai_json/combo'" value="<?= $PEGAWAI_ID ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Tanggal Pinjam</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-1">
                                            <input class="form-control easyui-datebox" type="text" id="TANGGAL_PINJAM" name="TANGGAL_PINJAM" 
                                            value="<?= $TANGGAL_PINJAM ?>"  style="width:100%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Waktu Pinjam</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-1">
                                            <input class="form-control easyui-timespinner" type="text" id="WAKTU_PINJAM" name="WAKTU_PINJAM" 
                                            value="<?= $WAKTU_PINJAM ?>"  style="width:100%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Tanggal Kembali</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-1">
                                            <input class="form-control easyui-datebox" type="text" id="TANGGAL_KEMBALI" name="TANGGAL_KEMBALI" 
                                            value="<?= $TANGGAL_KEMBALI ?>"  style="width:100%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Waktu Kembali</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-1">
                                            <input class="form-control easyui-timespinner" type="text" id="WAKTU_KEMBALI" name="WAKTU_KEMBALI" 
                                            value="<?= $WAKTU_KEMBALI ?>"  style="width:100%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Keterangan</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <textarea name="KETERANGAN" style="height:100px; width:100%;"><?= $KETERANGAN ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="form-group">
                                <label for="KETERANGAN" class="control-label col-md-2">Dokumen</label>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <div class="col-md-11">
                                            <input name="DOKUMEN" type="file" class="with-preview maxsize-21000" accept=".pdf,.mp4" value="" 
                                            <? if ($DOKUMEN == "") { ?>  <? } ?> />
    
                                            <input type="hidden" name="DOKUMENTemp" value="<?= $DOKUMEN ?>" />
                                            <?
                                            if (file_exists('uploads/peminjaman/'. $DOKUMEN)) {
                                            ?>
                                                <br><a href="uploads/peminjaman/<?= $DOKUMEN ?>" target="_blank"><?= $DOKUMEN ?></a>
                                            <?
                                            }
                                            ?>
                                            <br><span class="text-muted">Max. ukuran file adalah 20MB, ekstensi .pdf dan .mp4</span>
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