<?php 
use PDFMerger\PDFMerger;
include_once("libraries/phpqrcode/qrlib.php");
include_once("libraries/vendor/autoload.php");
include_once("libraries/MPDF60/mpdf.php");
include_once("libraries/PDFMerger/PDFMerger.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");

class DisposisiPDF
{
	var $reqId;
	var $reqDisposisiId;
	
	public function __construct($reqId, $reqDisposisiId)
	{

		$this->reqId = $reqId;
		$this->reqDisposisiId = $reqDisposisiId;
	}

	function generate() {
	
		$FILE_DIR_TEMPLATE = "uploads/";
		$FILE_DIR 		   = "uploads/".$this->reqId."/";
		
		if (!file_exists($FILE_DIR)) {
		    mkdir($FILE_DIR, 0777, true);
		}
		$CI =& get_instance();
		$CI->load->library("suratmasukinfo");	
		$suratmasukinfo = new suratmasukinfo();
		// var_dump($this->reqId);exit;
		$suratmasukinfo->getInfo($this->reqId);
		
		$mpdf = new mPDF('c','A4');
		$mpdf->AddPage('P', // L - landscape, P - portrait
					'', '', '', '',
					20, // margin_left
					20, // margin right
					20, // margin top
					20, // margin bottom
					2, // margin header
					2);  
		//$mpdf=new mPDF('c','A4'); 
		//$mpdf=new mPDF('utf-8', array(297,420));
		
		$mpdf->SetDisplayMode('fullpage');
		
		$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list
		
		// LOAD a stylesheet
		$stylesheet = file_get_contents('css/gaya-surat.css');
		$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
		
		$html .= file_get_contents(base_url()."report/loadUrl/report/disposisi/?reqJenisSurat=INTERNAL&reqId=".$this->reqId."&reqDisposisiId=".$this->reqDisposisiId);	
		
		$mpdf->WriteHTML($html,2);
		
		$saveAs = (generateZero($suratmasukinfo->SURAT_MASUK_ID, 6).generateZero($reqDisposisiId, 3).generateZero($suratmasukinfo->SATUAN_KERJA_ID_ASAL, 6));
		
		//$mpdf->Output('aanwijzing.pdf','I');
		
		$mpdf->Output($FILE_DIR.$saveAs.".pdf","F");
				
		return $saveAs.".pdf";

	}
	
}

?>