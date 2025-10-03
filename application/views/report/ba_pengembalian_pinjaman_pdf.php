<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
ini_set('memory_limit','2048M');

$documentName = basename (($_SERVER['PHP_SELF']));
$documentReport = str_replace("_pdf", "", $documentName);
$PREFIX_REPORT = strtoupper($documentReport);

include_once("libraries/MPDF60/mpdf.php");

$id = $this->input->get("id");

$mpdf = new mPDF('c','A4','','','20','20','8','8');

$mpdf->mirroMargins = true;

$mpdf->SetDisplayMode('fullpage');

$mpdf->list_indent_first_level = 0;  // 1 or 0 - whether to indent the first level of a list

//LOAD a stylesheet
$stylesheet = file_get_contents('css/report.css');
$mpdf->WriteHTML($stylesheet,1);    //The parameter 1 tells that this is css/style only and no body/html/text

$arrData = array("id" => $id);
$html .= $this->load->view("report/".$documentReport, $arrData, true);

$mpdf->WriteHTML($html,2);

$mpdf->Output($documentReport.'.pdf','I');
?>