<?php 
use PDFMerger\PDFMerger;

include_once("libraries/phpqrcode/qrlib.php");
include_once("libraries/vendor/autoload.php");
include_once("libraries/MPDF60/mpdf.php");
include_once("libraries/PDFMerger/PDFMerger.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");

class ReportKeluarPDF
{
	var $reqId;
	var $reqTemplate;

	public function __construct($reqId, $reqTemplate)
	{

		$this->reqId = $reqId;
		$this->reqTemplate = $reqTemplate;
	}

	function generate() {
	
		$FILE_DIR_TEMPLATE = "uploads/eksternal/";
		$FILE_DIR 		   = "uploads/eksternal/".$this->reqId."/";
		
		if (!file_exists($FILE_DIR)) {
		    mkdir($FILE_DIR, 0777, true);
		}
		
		$CI =& get_instance();
		$CI->load->library("suratkeluarinfo");	
		$suratkeluarinfo = new suratkeluarinfo();
		// var_dump($this->reqId);exit;
		$suratkeluarinfo->getInfo($this->reqId);
		
		$mpdf = new mPDF('c','A4');
		$mpdf->AddPage('P', // L - landscape, P - portrait
					'', '', '', '',
					8, // margin_left
					8, // margin right
					10, // margin top
					28, // margin bottom
					2, // margin header
					2);  
		//$mpdf=new mPDF('c','A4'); 
		//$mpdf=new mPDF('utf-8', array(297,420));
		
		$mpdf->SetDisplayMode('fullpage');
		
		$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list
		
		// LOAD a stylesheet
		$stylesheet = file_get_contents('css/gaya-surat.css');
		$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
		
		$html .= file_get_contents(base_url()."report/loadUrl/report/".$this->reqTemplate."/?reqJenisSurat=EKSTERNAL&reqId=".$this->reqId);	
		
		$mpdf->WriteHTML($html,2);
		
		$saveAs = (generateZero($suratkeluarinfo->SURAT_KELUAR_ID, 6).generateZero($suratkeluarinfo->SATUAN_KERJA_ID_ASAL, 6));
		
		//$mpdf->Output('aanwijzing.pdf','I');
		
		$mpdf->Output($FILE_DIR.$saveAs.".pdf","F");
				
		/*  JIKA TTD SUDAH MUNCUL MAKA FIX KAN  */
		if($suratkeluarinfo->TTD_KODE == "")
		{}
		else
		{
			$CI =& get_instance();
			$CI->load->model("SuratKeluar");	
			$surat_keluar = new SuratKeluar();
			
			$surat_keluar->setField("FIELD", "SURAT_PDF"); 
			$surat_keluar->setField("FIELD_VALUE", $saveAs.".pdf"); 
			$surat_keluar->setField("LAST_UPDATE_USER", "SYSTEM"); 
			$surat_keluar->setField("SURAT_KELUAR_ID", $this->reqId); 
			$surat_keluar->updateByField();
		}		
		
		
		if((int)$suratkeluarinfo->JUMLAH_LAMPIRAN > 0)
		{
			
			$pdf = new PDFMerger();
			$pdf->addPDF($FILE_DIR.$saveAs.".pdf", 'all');
			
			
			$CI =& get_instance();
			$CI->load->model("SuratKeluar");	
			$surat_keluar_attachment = new SuratKeluar();
			$surat_keluar_attachment->selectByParamsAttachment(array("A.SURAT_KELUAR_ID" => $this->reqId));
			while($surat_keluar_attachment->nextRow())
			{
				$pdf->addPDF($FILE_DIR.$surat_keluar_attachment->getField("ATTACHMENT"), 'all');
			}
			
			$pdf->merge('file', $FILE_DIR.$saveAs.".pdf");
		}
	
		return $saveAs.".pdf";

	}
	
}

?>