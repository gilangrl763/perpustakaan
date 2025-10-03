<!DOCTYPE html>
<html lang="en">
<head>
<title>Error</title>
<base href="<?=base_url()?>">
<style type="text/css">
@import url("https://fonts.googleapis.com/css2?family=Titan+One&display=swap");
html {
  width: 100%;
  height: 100vh;
}
body {
  margin: 0;
  padding: 0;
  width: 100%;
  box-sizing: border-box;
  display: grid;
  place-items: center;
  overflow: hidden;
  background: #fff;
  line-height: 0.7;
}
.icon{
  margin-top: 100px;
}
.error-message{
  font-family: 'Calibri';
  color: #575757;
  width: 50%;
  text-align: center;
  margin-bottom: 20px;
}
.btn-back {
  font-family: 'Calibri';
  background-color: #FF912B;
  border: none;
  color: white;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
}
.btn-back:hover {
  background-color: #FF7536;
  color: white;
}
</style>
</head>
<body>
  <div class="icon">
    <img src="images/error-page.png">
  </div>
  <div class="error-message">
    <span>
      <h2><?php echo $heading;?></h2>
      <?php echo $message;?>
    </span>
  </div>
  <button class="btn-back" onclick="kembali()">KEMBALI</button>

<script>
function kembali() {
  window.history.back();
}
</script>
</body>
</html>