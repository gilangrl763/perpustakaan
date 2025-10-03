<?
$this->load->library("crfs_protect");
$csrf = new crfs_protect('_crfs_login');

$this->load->model("Area");
$area = new Area();

$area->selectByParamsMonitoring(array("MD5(A.KODE || '6!l4nGR0mY')"=>$reqParse1));
// echo $area->query;exit;
$area->firstRow();
$KODE              = $area->getField("KODE");
$PERUSAHAAN_ID     = $area->getField("PERUSAHAAN_ID");
$CABANG_ID         = $area->getField("CABANG_ID");
$GEDUNG_ID         = $area->getField("GEDUNG_ID");
$AREA_ID           = $area->getField("AREA_ID");
$NAMA_CABANG       = $area->getField("NAMA_CABANG");
$NAMA_GEDUNG       = $area->getField("NAMA_GEDUNG");
$NAMA_PERUSAHAAN   = $area->getField("NAMA_PERUSAHAAN");
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
  <link rel="stylesheet" type="text/css" href="libraries/login_v18/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="libraries/login_v18/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="libraries/login_v18/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
  <link rel="stylesheet" type="text/css" href="libraries/login_v18/vendor/animate/animate.css">
  <link rel="stylesheet" type="text/css" href="libraries/login_v18/vendor/css-hamburgers/hamburgers.min.css">
  <link rel="stylesheet" type="text/css" href="libraries/login_v18/vendor/animsition/css/animsition.min.css">
  <link rel="stylesheet" type="text/css" href="libraries/login_v18/vendor/select2/select2.min.css">
  <link rel="stylesheet" type="text/css" href="libraries/login_v18/vendor/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" type="text/css" href="libraries/login_v18/css/util.css">
  <link rel="stylesheet" type="text/css" href="libraries/login_v18/css/main.css">
</head>

<body>
  <div class="limiter">
    <div class="container-login100">
      <div class="wrap-login100">
        <form class="login100-form validate-form" method="post" action="login/action">
          <span class="login100-form-title p-b-35">
            <img class="img-fluid" src="images/logo-colored.png" alt="LOGO" width="50%" height="*">
          </span>
          <span class="login100-form-title p-b-25">
            <h4>Aplikasi Manajemen Kearsipan</h4>
          </span>

          <div class="wrap-input100 validate-input">
            <input class="input100" type="text" id="reqUserLogin" name="reqUserLogin">
            <span class="focus-input100"></span>
            <span class="label-input100">Username</span>
          </div>

          <div class="wrap-input100 validate-input" data-validate="Password is required">
            <input class="input100" type="password" id="reqUserPass" name="reqUserPass">
            <span class="focus-input100"></span>
            <span class="label-input100">Password</span>
          </div>

          <div class="flex-sb-m w-full p-t-2 p-b-20">
            <div class="contact100-form-checkbox">
              <input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me" onclick="tampilkanPassword(this)">
              <label class="label-checkbox100" for="ckb1">Tampilkan Password</label>
            </div>

            <div>
              <a href="login/loadUrl/login/lupa_password" class="txt1">Lupa Password?</a>
            </div>
          </div>

          <?
          if ($pesan != "") {
          ?>
            <div class="flex-sb-m w-full m-b-10">
              <span class="badge badge-danger text-center p-t-7 p-b-7 p-e-7 p-s-7"><i class="fa fa-exclamation-triangle"></i> <?= $pesan ?></span>
            </div>
          <?
          }
          ?>

          <div class="container-login100-form-btn">
            <button class="login100-form-btn">Login</button>
          </div>

          <div class="row m-t-240">
            <div class="col-md-5">
              <a href="https://play.google.com/store/apps/details?id=id.co.ap1.firefighting" target="_blank">
                <img class="img-fluid" src="images/icon-google-play.png" alt="LOGO" width="45%" height="*"></a>
              <a href="https://play.google.com/store/apps/details?id=id.co.ap1.firefighting" target="_blank">
                <img class="img-fluid" src="images/icon-app-store.png" alt="LOGO" width="45%" height="*"></a>
            </div>
            <div class="col-md-7 text-right">
              <span class="txt2">
                <b>PT Trans Marga Jateng</b>
                <br><?= date("Y") ?> &copy; All Rights Reserved.
              </span>
            </div>
          </div>
        </form>

        <div class="login100-more"">
          <div class="row">
            <div class="col-md-12">
              <p>Area : <?=$KODE?></p>
              <p>Perusahaan : <?=$NAMA_PERUSAHAAN?></p>
              <p>Cabang : <?=$NAMA_CABANG?></p>
              <p>Gedung : <?=$NAMA_GEDUNG?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function tampilkanPassword(cb) {
      var x = document.getElementById("reqUserPass");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    }
  </script>

  <script src="libraries/login_v18/vendor/jquery/jquery-3.2.1.min.js"></script>
  <script src="libraries/login_v18/vendor/animsition/js/animsition.min.js"></script>
  <script src="libraries/login_v18/vendor/bootstrap/js/popper.js"></script>
  <script src="libraries/login_v18/vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="libraries/login_v18/vendor/select2/select2.min.js"></script>
  <script src="libraries/login_v18/vendor/daterangepicker/moment.min.js"></script>
  <script src="libraries/login_v18/vendor/daterangepicker/daterangepicker.js"></script>
  <script src="libraries/login_v18/vendor/countdowntime/countdowntime.js"></script>
  <script src="libraries/login_v18/js/main.js"></script>
</body>

</html>