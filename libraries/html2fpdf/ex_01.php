<?
require("html2fpdf.php");
ob_start();
?>
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<table width="100%"  border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td>Test&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Test&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>
<?
$htmlbuffer = ob_get_contents();
ob_end_clean();

$pdf = new HTML2FPDF();

$pdf->AddPage();
$pdf->WriteHTML($htmlbuffer);
$pdf->Output();
?>