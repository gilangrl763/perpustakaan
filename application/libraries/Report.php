<?php 
use PDFMerger\PDFMerger;

include_once("libraries/phpqrcode/qrlib.php");
include_once("libraries/vendor/autoload.php");
include_once("libraries/PDFMerger/PDFMerger.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");

class Report
{
	var $reqId;
	var $reqTemplate;

	public function __construct($reqId, $reqTemplate)
	{

		$this->reqId = $reqId;
		$this->reqTemplate = $reqTemplate;
		$domPdfPath = realpath(PHPWORD_BASE_DIR . '/../vendor/dompdf/dompdf');
        PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
        PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
	}

	function generate() {
	
		$FILE_DIR_TEMPLATE = "uploads/";
		$FILE_DIR 		   = "uploads/".$this->reqId."/";
		
		if (!file_exists($FILE_DIR)) {
		    mkdir($FILE_DIR, 0777, true);
		}

		$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($FILE_DIR_TEMPLATE.$this->reqTemplate);
		
		$CI =& get_instance();
		$CI->load->library("suratmasukinfo");	
		$suratmasukinfo = new suratmasukinfo();
		$suratmasukinfo->getInfo($this->reqId);
		$templateProcessor->setValue("nomor", $suratmasukinfo->NOMOR);
		
		//$jumlahLampiran = $suratmasukinfo->JUMLAH_LAMPIRAN;
		
		$templateProcessor->setValue("lampiran", $suratmasukinfo->JUMLAH_LAMPIRAN);
		$templateProcessor->setValue("perihal", $suratmasukinfo->PERIHAL);
		$templateProcessor->setValue("tembusan", $suratmasukinfo->TEMBUSAN);
		$templateProcessor->setValue("lokasi", $suratmasukinfo->LOKASI_SURAT);
		$templateProcessor->setValue("tanggal", getFormattedDate($suratmasukinfo->TANGGAL));
		$templateProcessor->setValue("kepada", $suratmasukinfo->KEPADA);
		
		
		/* Parser HTML */
		$isOutputEscapingEnabled = \PhpOffice\PhpWord\Settings::isOutputEscapingEnabled();
		\PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(false);
		$parser = new \HTMLtoOpenXML\Parser();
		/*$html = $suratmasukinfo->ISI;
		$ooXml = $parser->fromHTML($html);*/
		
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$section = $phpWord->addSection();
		\PhpOffice\PhpWord\Shared\Html::addHtml($section, $html);
		
		//header('Content-Type: application/octet-stream');
		//header('Content-Disposition: attachment;filename="test.docx"');
		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		//$objWriter->save('php://output');
		
		$fullXml = $objWriter->getWriterPart('Document')->write();
		
		$fullXml = str_replace('<w:document xmlns:ve="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:w10="urn:schemas-microsoft-com:office:word" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml">', '', $fullXml);
		
		$fullXml = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>', '', $fullXml);
		$fullXml = str_replace('<w:body>', '', $fullXml);
		$fullXml = str_replace('</w:body>', '', $fullXml);
		$fullXml = str_replace('<w:pgSz w:orient="portrait" w:w="11905.511811024" w:h="16837.795275591"/><w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="720" w:footer="720" w:gutter="0"/><w:cols w:num="1" w:space="720"/></w:sectPr>', '', $fullXml);
		$fullXml = str_replace('<w:sectPr>', '', $fullXml);
		$fullXml = str_replace('</w:document>', '', $fullXml);
		
		//echo $fullXml;
		//exit;
		
		$templateProcessor->setValue('isi', $fullXml);
		//echo $ooXml;
		//exit;
		//$templateProcessor->setValue("isi", $suratmasukinfo->ISI);
		
		try {
			// run your code here
			
			$CI =& get_instance();
			$CI->load->model("SuratMasukParaf");	
			
			$surat_masuk_paraf = new SuratMasukParaf();
			$jumlahParaf = $surat_masuk_paraf->getCountByParams(array("A.SURAT_MASUK_ID" => $this->reqId));
			
			$templateProcessor->cloneRow("jabatanparaf", $jumlahParaf);
			
			$surat_masuk_paraf->selectByParams(array("A.SURAT_MASUK_ID" => $this->reqId));
			$i = 1;
			while($surat_masuk_paraf->nextRow())
			{
				$templateProcessor->setValue("jabatanparaf#".$i, $surat_masuk_paraf->getField("NAMA_SATKER"));
				
				if($surat_masuk_paraf->getField("KODE_PARAF") == "")
					$templateProcessor->setValue("paraf#".$i, " ");
				else
					$templateProcessor->setImageValue("paraf#".$i, $FILE_DIR.$surat_masuk_paraf->getField("KODE_PARAF").".png");
				
				$i++;
			}
		}
		catch (exception $e) {
			//code to handle the exception
		}
		
		try {
			if($this->TTD_KODE == "")
				$templateProcessor->setValue("penandatangan", " ");
			else
				$templateProcessor->setImageValue("penandatangan", $FILE_DIR.$this->TTD_KODE.".png");
		}
		catch (exception $e) {
			//code to handle the exception
		}
		
		$saveAs = (generateZero($suratmasukinfo->SURAT_MASUK_ID, 6).generateZero($suratmasukinfo->SATUAN_KERJA_ID_ASAL, 6));
		$templateProcessor->saveAs($FILE_DIR.$saveAs.".docx");
		
		$outdir = realpath("uploads")."\\".$this->reqId."\\".$saveAs;
		//$command = 'cd uploads\\'.$this->reqId.' && "C:\Program Files\LibreOffice\program\soffice.exe" --headless --convert-to pdf --outdir "'.$outdir.'" '.$saveAs.".docx";
		$command = 'docto -f '.$outdir.'.docx -O "'.$outdir.'.pdf" -T wdFormatPDF ';
		exec($command);
		
		//return;
		//unlink($FILE_DIR.$saveAs.".docx");	
		/*  JIKA TTD SUDAH MUNCUL MAKA FIX KAN  */
		if($this->TTD_KODE == "")
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