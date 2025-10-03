<?php 
use PDFMerger\PDFMerger;

include_once("libraries/phpqrcode/qrlib.php");
include_once("libraries/vendor/autoload.php");
include_once("libraries/MPDF60/mpdf.php");
include_once("libraries/PDFMerger/PDFMerger.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");

class ReportPDF
{
	var $reqId;
	var $reqTemplate;

	public function __construct($reqId, $reqTemplate)
	{

		$this->reqId = $reqId;
		$this->reqTemplate = $reqTemplate;
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
					25, // margin_left
					20, // margin right
					10, // margin top
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
		
		$html .= file_get_contents(base_url()."report/loadUrl/report/".$this->reqTemplate."/?reqJenisSurat=INTERNAL&reqId=".$this->reqId);	
		
		$mpdf->WriteHTML($html,2);
		
		$saveAs = (generateZero($suratmasukinfo->SURAT_MASUK_ID, 6).generateZero($suratmasukinfo->SATUAN_KERJA_ID_ASAL, 6));
		
		//$mpdf->Output('aanwijzing.pdf','I');
		
		$mpdf->Output($FILE_DIR.$saveAs.".pdf","F");
				
		/*  JIKA TTD SUDAH MUNCUL MAKA FIX KAN  */
		if($suratmasukinfo->TTD_KODE == "")
		{}
		else
		{
			$CI =& get_instance();
			$CI->load->model("SuratMasuk");	
			$surat_masuk = new SuratMasuk();
			
			$surat_masuk->setField("FIELD", "SURAT_PDF"); 
			$surat_masuk->setField("FIELD_VALUE", $saveAs.".pdf"); 
			$surat_masuk->setField("LAST_UPDATE_USER", "SYSTEM"); 
			$surat_masuk->setField("SURAT_MASUK_ID", $this->reqId); 
			$surat_masuk->updateByField();
		}		
		
		
		if((int)$suratmasukinfo->JUMLAH_LAMPIRAN > 0)
		{
			
			$pdf = new PDFMerger();
			$pdf->addPDF($FILE_DIR.$saveAs.".pdf", 'all');
			
			
			$CI =& get_instance();
			$CI->load->model("SuratMasuk");	
			$surat_masuk_attachment = new SuratMasuk();
			$surat_masuk_attachment->selectByParamsAttachment(array("A.SURAT_MASUK_ID" => $this->reqId));
			while($surat_masuk_attachment->nextRow())
			{
				$pdf->addPDF($FILE_DIR.$surat_masuk_attachment->getField("ATTACHMENT"), 'all');
			}
			
			$pdf->merge('file', $FILE_DIR.$saveAs.".pdf");
		}
	
		return $saveAs.".pdf";

	}
	
}

?>