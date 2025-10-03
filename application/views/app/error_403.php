<?
$this->load->library("kauth"); $userLogin = new kauth();
?>

<style type="text/css">
@import url("https://fonts.googleapis.com/css2?family=Titan+One&display=swap");
.konten-error {
    margin-top: 150px;
}
.icon{
  margin-top: 50px;
  margin: 0;
  padding: 0;
  width: 100%;
  box-sizing: border-box;
  display: grid;
  place-items: center;
  overflow: hidden;
}
.error-message{
  margin: 0;
  padding: 0;
  width: 100%;
  box-sizing: border-box;
  display: grid;
  place-items: center;
  overflow: hidden;
  font-family: 'Calibri';
  color: #575757;
  text-align: center;
  margin-bottom: 20px;
}
.btn-back {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  display: grid;
  width: 100px;
  place-items: center;
  font-family: 'Calibri';
  background-color: #17c2a0;
  border: none;
  color: white;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin-top: 20px;
}
.btn-back:hover {
  background-color: #017e9e;
  color: white;
}
</style>

<div class="row konten-error">
  <div class="col-md-12">
    <div class="icon">
      <img src="images/error-page.png" width="*" height="300px">
    </div>
    <div class="error-message">
      <span>
        <h2>Error 403 Forbidden Page</h2>
        Maaf, anda tidak memiliki hak akses untuk halaman ini!
      </span>
      <button class="btn-back" onclick="kembali()">KEMBALI</button>
    </div>
    
  </div>
</div>

<script>
function kembali() {
  window.history.back();
}
</script>

<!-- EASYUI -->
<link rel="stylesheet" type="text/css" href="libraries/easyui/themes/default/easyui.css">
<script type="text/javascript" src="libraries/easyui/jquery.easyui.min.js"></script> 
<script type="text/javascript" src="libraries/easyui/kalender-easyui.js"></script> 
<script type="text/javascript" src="libraries/easyui/globalfunction.js"></script> 

<link rel="stylesheet" href="css/gaya-multifile.css" type="text/css">

<!--// plugin-specific resources //--> 
<script src='libraries/multifile-master/jquery.form.js' type="text/javascript" language="javascript"></script> 
<script src='libraries/multifile-master/jquery.MetaData.js' type="text/javascript" language="javascript"></script> 
<script src='libraries/multifile-master/jquery.MultiFile.js' type="text/javascript" language="javascript"></script> 