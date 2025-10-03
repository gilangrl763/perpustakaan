<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
ini_set('memory_limit','2048M');

$PNG_TEMP_DIR = "uploads/qr/"; 
$documentName = basename (($_SERVER['PHP_SELF']));
$documentReport = str_replace("_pdf", "", $documentName);
$PREFIX_REPORT = strtoupper($documentReport);

include_once("libraries/MPDF60/mpdf.php");
$this->load->library("kauth");
$userLogin = new kauth(); 

$reqId = $this->input->get("reqId");

$mpdf = new mPDF('c','A4');

$mpdf->AddPage('P',  // L - landscape, P - portrait
            '', '', '', '',
            15, //margin_left
            15, //margin right
            10, //margin top
            20, //margin bottom
            0,  //margin header
            5,  //margin footer
            2);  

$mpdf->mirroMargins = true;

$mpdf->SetDisplayMode('fullpage');

$mpdf->list_indent_first_level = 0;  // 1 or 0 - whether to indent the first level of a list

//LOAD a stylesheet
// $stylesheet = file_get_contents('css/gaya-pdf.css');
// $mpdf->WriteHTML($stylesheet,1);    //The parameter 1 tells that this is css/style only and no body/html/text

$arrData = array("reqId" => $reqId);
$html .= $this->load->view("report/".$documentReport, $arrData, true); 
        
/* SET FOOTER */   
$mpdf->SetHTMLFooter('
<table width="100%" class="footer">
    <tr>
        <td width="85%">
            <span>SIMANDE</span>
        </td>
        <td width="15%" style="text-align: center; ">Hal. {PAGENO} dari {nbpg}</td>
    </tr>
</table>

');
/* SET FOOTER */

$mpdf->WriteHTML($html,2);
$mpdf->Output($documentReport.'.pdf','I');
exit;
?>