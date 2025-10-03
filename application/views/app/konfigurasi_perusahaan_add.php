<?
$this->load->library("crfs_protect"); 
$csrf = new crfs_protect('_crfs_6Dp51Fm');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

$this->load->model("Perusahaan");
$perusahaan = new Perusahaan();

$id = $this->input->get("id");

if($id == ""){
    $mode = "insert";
}
else{
    $mode = "update";

    $perusahaan->selectByParams(array("A.PERUSAHAAN_ID" => $id));
    // echo $perusahaan->query;
    $perusahaan->firstRow();

    $KODE      = $perusahaan->getField("KODE");
    $NAMA      = $perusahaan->getField("NAMA");
    $ALAMAT    = $perusahaan->getField("ALAMAT");
    $TELEPON   = $perusahaan->getField("TELEPON");
    $EMAIL     = $perusahaan->getField("EMAIL");
    $WEBSITE   = $perusahaan->getField("WEBSITE");
    $LOGO      = $perusahaan->getField("LOGO");
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>TMJ | Aplikasi Manajemen Kearsipan</title>
        <base href="<?= base_url() ?>" />
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Aplikasi Manajemen Kearsipan PT Trans Marga Jateng">
        <meta name="author" content="PT Trans Marga Jateng">

        <link rel="icon" type="image/png" href="images/favicon.ico" />

        <link href="libraries/bootstrap-3.3.7/docs/dist/css/bootstrap.css" rel="stylesheet">
        <link href="libraries/bootstrap-3.3.7/docs/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
        <link href="libraries/bootstrap-3.3.7/docs/examples/navbar-fixed-top/navbar-fixed-top.css" rel="stylesheet">
        <script src="libraries/bootstrap-3.3.7/docs/assets/js/ie-emulation-modes-warning.js"></script>

        <link rel="stylesheet" href="css/gaya.css" type="text/css">
        <link rel="stylesheet" href="libraries/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    </head>

    <body>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="judul-halaman">
                        <a href="app/loadUrl/app/konfigurasi_perusahaan">Perusahaan</a> 
                        &bull; Kelola Perusahaan
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
                                    <label class="control-label col-md-2">Nama</label>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <input class="form-control easyui-validatebox" type="text" name="NAMA" 
                                                value="<?=$NAMA?>" required style="width:100%" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="form-group">
                                    <label class="control-label col-md-2">Alamat</label>
                                    <div class='col-md-10'>
                                        <div class='form-group'>
                                            <div class='col-md-6'>
                                                <textarea name="ALAMAT" style="height:100px; width:100%;"><?=$ALAMAT?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="form-group">
                                    <label class="control-label col-md-2">Telp.</label>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <input class="form-control easyui-validatebox" type="text" name="TELEPON" 
                                                value="<?=$TELEPON?>" required style="width:100%" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="form-group">
                                    <label class="control-label col-md-2">E-mail</label>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <input class="form-control easyui-validatebox" type="email" name="EMAIL" 
                                                value="<?=$EMAIL?>" required data-options="validType:'email'" style="width:100%" >
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-2">Website</label>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <input class="form-control easyui-validatebox" type="text" name="WEBSITE" 
                                                value="<?=$WEBSITE?>" required style="width:100%" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="form-group">
                                    <label for="reqKeterangan" class="control-label col-md-2">Logo</label>
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <div class="col-md-11">
                                                <input name="LOGO" type="file" class="with-preview maxsize-21000" accept=".jpg,.jpeg,.png" value="" 
                                                <?if($LOGO == ""){?> required <?}?>/>
    
                                                <input type="hidden" name="LOGOTemp" value="<?=$LOGO?>"/>
    
                                                <?
                                                if(file_exists('uploads/perusahaan/'.$LOGO)){
                                                ?>
                                                <br><a href="uploads/perusahaan/<?=$LOGO?>" target="_blank"><?=$LOGO?></a>
                                                <?
                                                }
                                                ?>
                                                <br><span class="text-muted">Max. ukuran file adalah 20MB, ekstensi .jpg,.jpeg,.png</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
    
                                <input type="hidden" name="id" value="<?=$id?>"/>
                                <input type="hidden" name="mode" value="<?=$mode?>"/>
                                
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
        function submitForm(){
            $('#ff').form('submit',{
                url:'perusahaan_json/add',
                onSubmit:function(){
                    if($(this).form('validate'))
                    {
                        var win = $.messager.progress({
                            title:'TMJ | Aplikasi Manajemen Kearsipan',
                            msg:'process data...'
                        });
                    }                   
                    return $(this).form('validate');
                },
                success:function(data){
                    $.messager.progress('close');
                    arrData = data.split("|");
                    if(arrData[0] == "GAGAL"){
                        $.messager.alert('Info',arrData[1],'info');
                    }
                    else{
                        $.messager.alertLink('Info', arrData[1], 'info', '', 'app/loadUrl/app/konfigurasi_perusahaan_add/?id='+arrData[2]);
                    }      
                }
            });
        }

        function clearForm(){
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
              link_list: [
                  { title: 'My page 1', value: 'https://www.tiny.cloud' },
                  { title: 'My page 2', value: 'http://www.moxiecode.com' }
              ],
              image_list: [
                  { title: 'My page 1', value: 'https://www.tiny.cloud' },
                  { title: 'My page 2', value: 'http://www.moxiecode.com' }
              ],
              image_class_list: [
                  { title: 'None', value: '' },
                  { title: 'Some class', value: 'class-name' }
              ],
              importcss_append: true,
              templates: [
                  { title: 'New Table', description: 'creates a new table', content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>' },
                  { title: 'Starting my story', description: 'A cure for writers block', content: 'Once upon a time...' },
                  { title: 'New list with dates', description: 'New List with dates', content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>' }
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
              
              setup: function (ed) {
                  ed.on('focus', function () {
                      $(this.contentAreaContainer.parentElement).find("div.mce-toolbar-grp").show();
                      $(this.contentAreaContainer.parentElement).find("div.mce-toolbar").show();
                      $(this.contentAreaContainer.parentElement).find("div.mce-tinymce").show();
                      //$(this.contentAreaContainer.parentElement).find("div.mce-container-body.mce-stack-layout").show();                    
                  });
                  ed.on('blur', function () {
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


