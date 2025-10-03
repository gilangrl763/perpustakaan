<?
$this->load->library("crfs_protect"); 
$csrf = new crfs_protect('_crfs_6Dp51Fm');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

$this->load->model("Pemindahan");
$pemindahan = new Pemindahan();

$id = $this->input->get("id");

if ($id == "") {
    $mode = "insert";
    $KURUN_WAKTU = date("Y");
} else {
    $mode = "update";

    $pemindahan->selectByParams(array("PEMINDAHAN_ID" => $id));
    // echo $pemindahan->query;exit;
    $pemindahan->firstRow();
    $PERUSAHAAN_ID           = $pemindahan->getField("PERUSAHAAN_ID");
    $CABANG_ID               = $pemindahan->getField("CABANG_ID");
    $SATUAN_KERJA_ID         = $pemindahan->getField("SATUAN_KERJA_ID");
    $KLASIFIKASI_ID          = $pemindahan->getField("KLASIFIKASI_ID");
    $KETERANGAN              = $pemindahan->getField("KETERANGAN");
    $KURUN_WAKTU             = $pemindahan->getField("KURUN_WAKTU");
    $TINGKAT_PERKEMBANGAN_ID = $pemindahan->getField("TINGKAT_PERKEMBANGAN_ID");
    $RETENSI_AKTIF           = $pemindahan->getField("RETENSI_AKTIF");
    $RETENSI_INAKTIF         = $pemindahan->getField("RETENSI_INAKTIF");
    $TAHUN_PINDAH            = $pemindahan->getField("TAHUN_PINDAH");
    $TAHUN_MUSNAH            = $pemindahan->getField("TAHUN_MUSNAH");
    $MEDIA_SIMPAN_ID         = $pemindahan->getField("MEDIA_SIMPAN_ID");
    $LOKASI_SIMPAN_ID        = $pemindahan->getField("LOKASI_SIMPAN_ID");
    $KONDISI_FISIK_ID        = $pemindahan->getField("KONDISI_FISIK_ID");
    $JUMLAH_BERKAS           = $pemindahan->getField("JUMLAH_BERKAS");
    $DOKUMEN                 = $pemindahan->getField("DOKUMEN");
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
                url: 'pemindahan_json/add',
                onSubmit: function() {
                    if ($(this).form('validate')) {
                        var win = $.messager.progress({
                            title: 'IFM | PT Garuda Daya Pratama Sejahtera',
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
                        $.messager.alertLink('Info', arrData[1], 'info', '', 'app/loadUrl/app/pemindahan_add/?id=' + arrData[2]);
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
                    <a href="app/loadUrl/app/pemindahan">Pemindahan</a>
                    &bull; Form Pemindahan
                </div>
                <div class="area-form">
                    <div class="konten">
                        <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
    
                            <div class="form-group">
                                <label class="control-label col-md-2">Satuan Kerja</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control easyui-combotree" id="SATUAN_KERJA_ID" 
                                            name="SATUAN_KERJA_ID" data-options="width:'725',panelHeight:'120',panelWidth:'725',editable:false,
                                            valueField:'id',textField:'text',url:'satuan_kerja_json/combotree',
                                            onSelect: function(rec){
                                                $('#PERUSAHAAN_ID').val(rec.PERUSAHAAN_ID);
                                                $('#CABANG_ID').val(rec.CABANG_ID);
                                            }" value="<?= $SATUAN_KERJA_ID ?>" />
                                            <input type="hidden" id="PERUSAHAAN_ID" name="PERUSAHAAN_ID" value="<?= $PERUSAHAAN_ID ?>" />
                                            <input type="hidden" id="CABANG_ID" name="CABANG_ID" value="<?= $CABANG_ID ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="form-group">
                                <label class="control-label col-md-2">Klasifikasi</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control easyui-combotree" id="KLASIFIKASI_ID" 
                                            name="KLASIFIKASI_ID" data-options="width:'725',panelHeight:'120',panelWidth:'725',editable:false,
                                            valueField:'id',textField:'text',url:'klasifikasi_json/combotree_pemindahan',
                                            onSelect: function(rec){
                                                $('#RETENSI_AKTIF').val(rec.RETENSI_AKTIF);
                                                $('#RETENSI_INAKTIF').val(rec.RETENSI_INAKTIF);

                                                tahun_pindah = Number($('#KURUN_WAKTU').val()) + Number($('#RETENSI_INAKTIF').val());
                                                $('#TAHUN_PINDAH').val(tahun_pindah);
                                                
                                                tahun_musnah = tahun_pindah + Number($('#RETENSI_INAKTIF').val());
                                                $('#TAHUN_MUSNAH').val(tahun_musnah);
                                            }" value="<?= $KLASIFIKASI_ID ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Kurun Waktu</label>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <input class="form-control easyui-numberbox" type="text" id="KURUN_WAKTU" name="KURUN_WAKTU" 
                                            value="<?= $KURUN_WAKTU ?>" required style="width:100%">
                                        </div>
                                    </div>
                                </div>
                                <label class="control-label col-md-1">Retensi Inaktif</label>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <input type="hidden" id="RETENSI_AKTIF" name="RETENSI_AKTIF" value="<?= $RETENSI_AKTIF ?>" />
                                            <input class="form-control easyui-validatebox" type="text" id="RETENSI_INAKTIF" name="RETENSI_INAKTIF" 
                                            value="<?= $RETENSI_INAKTIF ?>" readonly style="width:100%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Tahun Pindah</label>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <input class="form-control easyui-validatebox" type="text" id="TAHUN_PINDAH" name="TAHUN_PINDAH" 
                                            value="<?= $TAHUN_PINDAH ?>" readonly style="width:100%">
                                        </div>
                                    </div>
                                </div>
                                <label class="control-label col-md-1">Tahun Musnah</label>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <input class="form-control easyui-validatebox" type="text" id="TAHUN_MUSNAH" name="TAHUN_MUSNAH" 
                                            value="<?= $TAHUN_MUSNAH ?>" readonly style="width:100%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Tingkat Perkembangan</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control easyui-combobox" id="TINGKAT_PERKEMBANGAN_ID" 
                                            name="TINGKAT_PERKEMBANGAN_ID" data-options="width:'250',panelHeight:'120',panelWidth:'250',editable:false,
                                            valueField:'id',textField:'text',url:'tingkat_perkembangan_json/combo'" value="<?= $TINGKAT_PERKEMBANGAN_ID ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Kondisi Fisik</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control easyui-combobox" id="KONDISI_FISIK_ID" 
                                            name="KONDISI_FISIK_ID" data-options="width:'250',panelHeight:'120',panelWidth:'250',editable:false,
                                            valueField:'id',textField:'text',url:'kondisi_fisik_json/combo'" value="<?= $KONDISI_FISIK_ID ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Media Simpan</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control easyui-combobox" id="MEDIA_SIMPAN_ID" 
                                            name="MEDIA_SIMPAN_ID" data-options="width:'250',panelHeight:'120',panelWidth:'250',editable:false,
                                            valueField:'id',textField:'text',url:'media_simpan_json/combo'" value="<?= $MEDIA_SIMPAN_ID ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Lokasi Simpan</label>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control easyui-combobox" id="LOKASI_SIMPAN_ID" 
                                            name="LOKASI_SIMPAN_ID" data-options="width:'750',panelHeight:'120',panelWidth:'750',editable:false,
                                            valueField:'id',textField:'text',url:'lokasi_simpan_json/combo'" value="<?= $LOKASI_SIMPAN_ID ?>" />
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
                                            <input name="DOKUMEN" type="file" class="with-preview maxsize-21000" accept=".pdf" value="" 
                                            <? if ($DOKUMEN == "") { ?> required <? } ?> />
    
                                            <input type="hidden" name="DOKUMENTemp" value="<?= $DOKUMEN ?>" />
                                            <?
                                            if (file_exists('uploads/pemindahan/'. $DOKUMEN)) {
                                            ?>
                                                <br><a href="uploads/pemindahan/<?= $DOKUMEN ?>" target="_blank"><?= $DOKUMEN ?></a>
                                            <?
                                            }
                                            ?>
                                            <br><span class="text-muted">Max. ukuran file adalah 20MB, ekstensi .pdf</span>
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