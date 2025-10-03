<?
include_once("functions/string.func.php");
include_once("functions/date.func.php");

$this->load->library("crfs_protect"); 
$csrf = new crfs_protect('_crfs_lupa_password');

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Subsidiary Integration Portal, PT Angkasa Pura I (Persero)">
  <meta name="author" content="PT Angkasa Pura I (Persero)">

  <title>SIP | PT Angkasa Pura I (Persero)</title>
  <base href="<?=base_url()?>"/>
  <link rel="shortcut icon" href="images/icon.ico">

  <link rel="stylesheet" type="text/css" href="css/login/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="css/login/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="css/login/animate.css">
  <link rel="stylesheet" type="text/css" href="css/login/select2.css">
  <link rel="stylesheet" type="text/css" href="css/login/util.css">
  <link rel="stylesheet" type="text/css" href="css/login/main.css">
  <link rel="stylesheet" href="libraries/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
  <style>
    .content-subsidiary {
      float: right;
      position: absolute;
      color: #f4f4f4;
      bottom: 0;
      right: 0;
      margin-bottom: 30px;
      margin-right: 20px;
      text-align: right;
    }
    .judul-subsidiary {
      width: 100%;
      float: right;
      margin-bottom: 20px;
      font-size: 12px;
    }
    .logo-subsidiary {
      width: 170px;
      float: right;
    }
    .p-b-36{
      padding-bottom: 15px !important;
    }
    .p-l-75{
      padding-left: 50px !important;
    }
    .p-t-45{
      padding-top: 20px !important;
    }
    .p-t-50{
      padding-top: 50px !important;
    }
    .s2-txt3{
      font-size: 12px !important;
    }
  </style>
</head>

<body cz-shortcut-listen="true">
  <div class="size1 bg0 where1-parent" style="background-image: url('images/bg-login.png');">

    <!-- BANNER -->
    <div class="flex-c-m bg-img1 size2 where1 overlay1 where2 respon2" style="background-image: url('images/bg-01.jpg');">
      <div class="wsize2 flex-w flex-c-m cd100 js-tilt">
        <img src="images/logo-aplikasi-putih.png">
      </div>
      <div class="content-subsidiary">
        <div class="judul-subsidiary">Anak Perusahaan PT Angkasa Pura I (Persero)</div>
        <div class="logo-subsidiary"><img src="images/logo-ap1-hotel.png" width="*" height="40px"></div>
        <div class="logo-subsidiary"><img src="images/logo-ap1-retail.png" width="*" height="40px"></div>
        <div class="logo-subsidiary"><img src="images/logo-ap1-property.png" width="*" height="40px"></div>
        <div class="logo-subsidiary"><img src="images/logo-ap1-logistics" width="*" height="40px"></div>
        <div class="logo-subsidiary"><img src="images/logo-ap1-support.png" width="*" height="40px"></div>
      </div>
    </div>

    <!-- FORM -->
    <div class="size3 flex-col-sb flex-w p-l-75 p-r-75 p-t-45 p-b-45 respon1">
      <div class="wrap-pic1">
        <img src="images/logo-ap1.jpg" alt="LOGO">
      </div>

      <div class="p-t-50 p-b-60">
        <p class="m1-txt1 p-b-36">RESET PASSWORD</p>

        <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
          
          <div class="wrap-input100 m-b-10 validate-input" data-validate="Masukkan Username/E-mail yang telah terdaftar">
            <input class="s2-txt1 placeholder0 input100" type="text" name="reqUser" placeholder="Masukkan Username/E-mail yang telah terdaftar">
            <span class="focus-input100"></span>
          </div>

          <div class="w-full">
            <button class="flex-c-m s2-txt2 size4 bg1 bor1 hov1 trans-04">
              RESET PASSWORD
            </button>
          </div>

          <?=$csrf->echoInputField();?>

        </form>

        <p class="s2-txt3 p-t-18"><a href="login"><i class="fa fa-long-arrow-left"></i> Kembali</a> <span style="color:#ba0000;float: right;"><?=$pesan?></span></p>
      </div>

      <div class="flex-w">
        <p class="s2-txt3 p-t-18">Copyright &copy; <?=date("Y")?>. All Rights Reserved.
          <br><b>PT Angkasa Pura I (Persero)</b>
          <br>Kantor Pusat Jakarta
          <br>Kota Baru Bandar Kemayoran Blok B 12 Kav. 2
          <br>Jakarta 10610, Indonesia
        </p>
      </div>
    </div>
  </div>

  <center><div id="loading"></div></center><br>

  <script type="text/javascript" async="" src="js/login/analytics.js"></script><script src="js/login/jquery-3.js"></script>
  <script src="js/login/popper.js"></script>
  <script src="js/login/bootstrap.js"></script>
  <script src="js/login/select2.js"></script>
  <script src="js/login/moment.js"></script>
  <script src="js/login/moment-timezone.js"></script>
  <script src="js/login/moment-timezone-with-data.js"></script>
  <script src="js/login/countdowntime.js"></script>
  <script>
    $('.cd100').countdown100({
      /*Set Endtime here*/
      /*Endtime must be > current time*/
      endtimeYear: 0,
      endtimeMonth: 0,
      endtimeDate: 35,
      endtimeHours: 18,
      endtimeMinutes: 0,
      endtimeSeconds: 0,
      timeZone: "" 
      // ex:  timeZone: "America/New_York"
      //go to " http://momentjs.com/timezone/ " to get timezone
    });
  </script>
  <script src="js/login/tilt.js"></script>
  <script>
    $('.js-tilt').tilt({
      scale: 1.1
    })
  </script>
  <script src="js/login/main.js"></script>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async="" src="js/login/js"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-23581568-13');
</script>
<script defer="defer" src="js/login/beacon.js" data-cf-beacon="{&quot;rayId&quot;:&quot;65b7bf0f8e2cd33e&quot;,&quot;token&quot;:&quot;cd0b4b3a733644fc843ef0b185f98241&quot;,&quot;version&quot;:&quot;2021.5.2&quot;,&quot;si&quot;:10}"></script>


<!-- EASYUI -->
<link rel="stylesheet" type="text/css" href="libraries/easyui/themes/default/easyui.css">
<script type="text/javascript" src="libraries/easyui/jquery.easyui.min.js"></script> 
<script type="text/javascript" src="libraries/easyui/kalender-easyui.js"></script> 
<script type="text/javascript" src="libraries/easyui/globalfunction.js"></script> 
<link rel="stylesheet" href="css/gaya-multifile.css" type="text/css">

<script type="text/javascript"> 

$(document).ready(function(){
  $(function(){
    $('#ff').form({

      url:'login/lupa_password',
      onSubmit:function(){
        if($(this).form('enableValidation').form('validate'))
        {
          var win = $.messager.progress({
            title:'SIP | PT Angkasa Pura 1',
            msg:'proses simpan...'
          });
          $("#loading").show(1000).html("<img src='load.gif' height='50'>");                   
        }

        return $(this).form('enableValidation').form('validate');
      },
      success:function(data){
        $.messager.progress('close');
        $.messager.alertLink('Info', "Data Berhasil Dikirim", 'info', 'login');
      }
    });
    
  });
});

</script>


</body>
</html>