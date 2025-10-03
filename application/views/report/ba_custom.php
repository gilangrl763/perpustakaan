<?
/* INCLUDE FILE */
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("libraries/phpqrcode/qrlib.php");

$PNG_TEMP_DIR = "uploads/qr/"; 
$documentName = basename (($_SERVER['PHP_SELF']));
$documentReport = str_replace("_pdf", "", $documentName);
$PREFIX_REPORT = strtoupper($documentReport);

$this->load->library("AES");  
$this->load->library("kauth");  
$userLogin = new kauth(); 

$reqId = $this->input->get("reqId");
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>
<body>
  Tesssss
</body>
</html>