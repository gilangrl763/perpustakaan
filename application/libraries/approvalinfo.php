<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kauth
 *
 * @author user
 */
  class ApprovalInfo{

	var $NIP;
	var $USER_TYPE_ID;
	
    /******************** CONSTRUCTOR **************************************/
    function ApprovalInfo(){
		
		$CI =& get_instance();
		if (!$CI->kauth->getInstance()->hasIdentity()) {
			//redirect('app');
		}
		$this->NIP =  $CI->kauth->getInstance()->getIdentity()->NIP;
		$this->USER_TYPE_ID = $CI->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
		
    }
	
	public function getIdByToken($reqToken, $reqDokumen)
	{
		$CI =& get_instance();
		
		
		$primaryId = $CI->db->query(" SELECT PRIMARY_ID FROM EPROC_KATALOG.APPROVAL_MANAGER WHERE MD5(APPROVAL_MANAGER_KATALOG_ID) = '".$reqToken."' AND TABEL = '".$reqDokumen."' ")->row()->primary_id;
		if(!empty($primaryId))
			return $primaryId;
			
		$primaryId = $CI->db->query(" SELECT PRIMARY_ID FROM EPROC_KATALOG.APPROVAL_MANAGER WHERE MD5(PRIMARY_ID) = '".$reqToken."' ")->row()->primary_id;
		return $primaryId;		
	}
	
	public function getRevisi($paketId, $documentId)
	{
		
		$CI =& get_instance();
		
		$CI->load->model("ApprovalManager");
		
		$approval_manager = new ApprovalManager();
		
		$adaPostingatauApprove = $approval_manager->getCountByParams(array("PRIMARY_ID" => $paketId, "TABEL" => $documentId), " AND COALESCE(NULLIF(APPROVAL, ''), 'X') IN ('X', 'APPROVED') ");
		
		
		$approval_manager->selectByParams(array("PRIMARY_ID" => $paketId, "TABEL" => $documentId, "APPROVAL" => "REVISI"));
		
		$catatanRevisi = "";
		
		while($approval_manager->nextRow())
		{
			if($approval_manager->getField("REVISI") == ""){
				continue;
			}
				
			if($catatanRevisi == ""){
				$catatanRevisi = "<b>[".$approval_manager->getField("PEGAWAI")." ".$approval_manager->getField("APPROVAL_DATE_CHAR")."]</b> ".$approval_manager->getField("REVISI");
			}
			else{	
				$catatanRevisi .= "<br><b>[".$approval_manager->getField("PEGAWAI")." ".$approval_manager->getField("APPROVAL_DATE_CHAR")."]</b> ".$approval_manager->getField("REVISI");
			}
			
		}
		
		if($catatanRevisi == "")
		{}
		else
		{
		?>
            <div class="alert alert-danger" style="margin:10px">
              <strong>CATATAN REVISI :</strong><br></br><?=$catatanRevisi?>
            </div>
        <?
		}
		
	}
	
	public function getIsApproved($paketId, $documentId)
	{
		
		$CI =& get_instance();
		$CI->load->model("ApprovalManager");
		
		$approval_manager = new ApprovalManager();
		$jumlahApproval = $approval_manager->getCountByParams(array("PRIMARY_ID" => $paketId, "TABEL" => $documentId, "NOT COALESCE(APPROVAL, 'X')" => "REVISI"));
		$jumlahApproved = $approval_manager->getCountByParams(array("PRIMARY_ID" => $paketId, "TABEL" => $documentId, "APPROVAL" => "APPROVED"));
		
		if($jumlahApproval == 0)
		{
			$documentId = str_replace("_", " ", $documentId);
			return $documentId." belum dibuat.";	
		}
		
		
		$documentId = str_replace("_", " ", $documentId);
		if($jumlahApproval == 1)
		{
			if($jumlahApproved == 0)
				return "Belum dilakukan approval ".$documentId.".";
			else
				return "1";	
		}
		else
		{
			$minimalApproval = (ceil($jumlahApproval / 2) + 1);
			if($jumlahApproved >= $minimalApproval)
				return "1";		
			else
			{
				return "Dibutuhkan ".$minimalApproval." dari ".$jumlahApproval." approval ".$documentId.", saat ini hanya ".$jumlahApproved.".";
			}
						
		}
		
	}


	public function getIsApprovedPrakatalog($paketId, $rekananId, $documentId)
	{
		
		$CI =& get_instance();
		$CI->load->model("ApprovalManager");
		
		$approval_manager = new ApprovalManager();
		$jumlahApproval = $approval_manager->getCountByParams(array("PRIMARY_ID" => $paketId, "REKANAN_ID" => $rekananId, "TABEL" => $documentId, "NOT COALESCE(APPROVAL, 'X')" => "REVISI"));
		$jumlahApproved = $approval_manager->getCountByParams(array("PRIMARY_ID" => $paketId, "REKANAN_ID" => $rekananId, "TABEL" => $documentId, "APPROVAL" => "APPROVED"));
		
		if($jumlahApproval == 0)
		{
			$documentId = str_replace("_", " ", $documentId);
			return $documentId." belum dibuat.";	
		}
		
		if($documentId == "NEGOSIASI_CETAK_PRAKATALOG"){
			$documentId = "BA_NEGOSIASI";
		}
		
		$documentId = str_replace("_", " ", $documentId);
		if($jumlahApproval == 1)
		{
			if($jumlahApproved == 0)
				return "Belum dilakukan approval ".$documentId.".";
			else
				return "1";	
		}
		else
		{
			$minimalApproval = (ceil($jumlahApproval / 2) + 1);
			if($jumlahApproved >= $minimalApproval)
				return "1";		
			else
			{
				return "Dibutuhkan ".$minimalApproval." dari ".$jumlahApproval." approval ".$documentId.", saat ini hanya ".$jumlahApproved.".";
			}
						
		}
		
	}
	
	
	public function getViewApprovalPanitia($paketId, $documentId, $buttonTitle="")
	{
		
		$CI =& get_instance();
		$CI->load->model("ApprovalManager");
		
		$approval_manager = new ApprovalManager();
		$sudahPosting = $approval_manager->getCountByParams(array("PRIMARY_ID" => $paketId, "TABEL" => $documentId));
		
		$CI->load->model("PaketPanitia");
		$paket_panitia_validasi = new PaketPanitia;
		$adaAksesMenu = $paket_panitia_validasi->getCountByParamsPanitiaValidasi(array("PAKET_ID" => $paketId, "NIP" => $this->NIP));
		
		if($sudahPosting == 0)
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
             <?
             if($adaAksesMenu > 0)
			 {
			 	//PERMINTAAN UPDATE 18-09-2020
			 	if($documentId == "NEGOSIASI_CETAK"){
			 		$documentIdNama = "BA_NEGOSIASI";
			 	}
			 	if($documentId == "NEGOSIASI_CETAK_KONTRAK_PAYUNG"){
			 		$documentIdNama = "BA_NEGOSIASI_KONTRAK_PAYUNG";
			 	}
			 	else{
			 		$documentIdNama = $documentId;
			 	}
			 ?>
			 <button type="button" class="btn btn-primary" onclick="kirimApprovalPanitia('POSTING <?=(str_replace("_", " ", $documentIdNama))?> ?','<?=$documentId?>','<?=$paketId?>')">Posting <?=$buttonTitle?></button>
        <?	
			 }
		}
		else
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
        <?				
		}	
		
	}


	public function getViewApprovalPanitiaPrakatalog($paketId, $rekananId, $documentId, $buttonTitle="")
	{
		
		$CI =& get_instance();
		$CI->load->model("ApprovalManager");
		
		$approval_manager = new ApprovalManager();
		$sudahPosting = $approval_manager->getCountByParams(array("PRIMARY_ID" => $paketId, "REKANAN_ID" => $rekananId, "TABEL" => $documentId));
		
		$CI->load->model("PaketPanitia");
		$paket_panitia_validasi = new PaketPanitia;
		$adaAksesMenu = $paket_panitia_validasi->getCountByParamsPanitiaValidasi(array("PAKET_ID" => $paketId, "NIP" => $this->NIP));
		
		if($sudahPosting == 0)
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>&reqRekananId=<?=$rekananId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
             <?
             if($adaAksesMenu > 0)
			 {
			 	//PERMINTAAN UPDATE 18-09-2020
			 	if($documentId == "NEGOSIASI_CETAK_PRAKATALOG"){
			 		$documentIdNama = "BA_NEGOSIASI";
			 	}
			 	else{
			 		$documentIdNama = $documentId;
			 	}
			 ?>
			 <button type="button" class="btn btn-primary" onclick="kirimApprovalPanitiaPrakatalog('POSTING <?=(str_replace("_", " ", $documentIdNama))?> ?','<?=$documentId?>','<?=$paketId?>','<?=$rekananId?>')">Posting <?=$buttonTitle?></button>
        <?	
			 }
		}
		else
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>&reqRekananId=<?=$rekananId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
        <?				
		}	
		
	}
	
	
	public function getViewApprovalSM($paketId, $documentId, $buttonTitle="")
	{
		
		$CI =& get_instance();
		$CI->load->model("ApprovalManager");
		
		$approval_manager = new ApprovalManager();
		$sudahPosting = $approval_manager->getCountByParams(array("PRIMARY_ID" => $paketId, "TABEL" => $documentId));
		
		$CI->load->model("PaketPanitia");
		$paket_panitia_validasi = new PaketPanitia;
		$adaAksesMenu = $paket_panitia_validasi->getCountByParamsPanitiaValidasi(array("PAKET_ID" => $paketId, "NIP" => $this->NIP));
		
		if($documentId == "NOTA_DINAS_SM_KEPADA_VP" || $documentId == "NOTA_DINAS_SM_KEPADA_VP_KONTRAK_PAYUNG"){
			$judulDokumen = "NOTA_DINAS_HASIL";
		}
		else{
			$judulDokumen = $documentId;
		}

		if($sudahPosting == 0)
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
             <?
             if($adaAksesMenu > 0)
			 {
			 ?>
			 <button type="button" class="btn btn-primary" onClick="kirimApprovalSM('POSTING <?=(str_replace("_", " ", $judulDokumen))?> ?','<?=$documentId?>','<?=$paketId?>')">Posting <?=$buttonTitle?></button>
        <?	
			 }
		}
		else
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
        <?				
		}	
		
	}



	public function getViewApprovalSMPrakatalog($paketId, $rekananId, $documentId, $buttonTitle="")
	{
		
		$CI =& get_instance();
		$CI->load->model("ApprovalManager");
		
		$approval_manager = new ApprovalManager();
		$sudahPosting = $approval_manager->getCountByParams(array("PRIMARY_ID" => $paketId, "REKANAN_ID" => $rekananId, "TABEL" => $documentId));
		  
		$CI->load->model("PaketPanitia");
		$paket_panitia_validasi = new PaketPanitia;
		$adaAksesMenu = $paket_panitia_validasi->getCountByParamsPanitiaValidasi(array("PAKET_ID" => $paketId, "NIP" => $this->NIP));
		
		if($documentId == "NOTA_DINAS_SM_KEPADA_VP_PRAKATALOG"){
			$judulDokumen = "NOTA_DINAS_HASIL";
		}
		else{
			$judulDokumen = $documentId;
		}

		if($sudahPosting == 0)
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>&reqRekananId=<?=$rekananId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
             <?
             if($adaAksesMenu > 0)
			 {
			 ?>
			 <button type="button" class="btn btn-primary" onclick="kirimApprovalSMPrakatalog('POSTING <?=(str_replace("_", " ", $judulDokumen))?> ?','<?=$documentId?>','<?=$paketId?>','<?=$rekananId?>')">Posting <?=$buttonTitle?></button>
        <?	
			 }
		}
		else
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>&reqRekananId=<?=$rekananId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
        <?				
		}	
		
	}
	
	
	
	public function getViewApprovalVP($paketId, $documentId, $buttonTitle="")
	{
		
		$CI =& get_instance();
		$CI->load->model("ApprovalManager");
		
		$approval_manager = new ApprovalManager();
		$sudahPosting = $approval_manager->getCountByParams(array("PRIMARY_ID" => $paketId, "TABEL" => $documentId));
		
		$CI->load->model("PaketPanitia");
		$paket_panitia_validasi = new PaketPanitia;
		$adaAksesMenu = $paket_panitia_validasi->getCountByParamsPanitiaValidasi(array("PAKET_ID" => $paketId, "NIP" => $this->NIP));
		
		if($documentId == "NOTA_DINAS_VP_KEPADA_PEJABAT_BERWENANG" || $documentId == "NOTA_DINAS_VP_KEPADA_PEJABAT_BERWENANG_KONTRAK_PAYUNG"){
			$judulDokumen = "NOTA_DINAS_PENETAPAN_PEMENANG";
		}
		elseif($documentId == "NOTA_DINAS_VP_KEPADA_PEJABAT_BERWENANG_ADDENDUM"){
			$judulDokumen = "NOTA_DINAS_HASIL_LELANG";
		}
		else{
			$judulDokumen = $documentId;
		}

		if($sudahPosting == 0)
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
             <?
             if($adaAksesMenu > 0)
			 {
			 ?>
			 <button type="button" class="btn btn-primary" onClick="kirimApprovalVP('POSTING <?=(str_replace("_", " ", $judulDokumen))?> ?','<?=$documentId?>','<?=$paketId?>')">Posting <?=$buttonTitle?></button>
        <?	
			 }
		}
		else
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
        <?				
		}	
		
	}


	public function getViewApprovalVPPrakatalog($paketId, $rekananId, $documentId, $buttonTitle="")
	{
		
		$CI =& get_instance();
		$CI->load->model("ApprovalManager");

		$approval_manager = new ApprovalManager();
		$sudahPosting = $approval_manager->getCountByParams(array("PRIMARY_ID" => $paketId, "REKANAN_ID" => $rekananId, "TABEL" => $documentId));
		
		$CI->load->model("PaketPanitia");
		$paket_panitia_validasi = new PaketPanitia;
		$adaAksesMenu = $paket_panitia_validasi->getCountByParamsPanitiaValidasi(array("PAKET_ID" => $paketId, "NIP" => $this->NIP));


		if($documentId == "NOTA_DINAS_VP_KEPADA_PEJABAT_BERWENANG_PRAKATALOG"){
			$judulDokumen = "NOTA_DINAS_PENETAPAN_PEMENANG";
		}
		else if($documentId == "NOTA_DINAS_VP_PENUNJUKKAN_PEMENANG_PRAKATALOG"){
			$judulDokumen = "NOTA_DINAS_USULAN_PENUNJUKKAN_PELAKSANA";
		}
		else{
			$judulDokumen = $documentId;
		}



		if($sudahPosting == 0)
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>&reqRekananId=<?=$rekananId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
             <?
             if($adaAksesMenu > 0)
			 {
			 ?>
			 <button type="button" class="btn btn-primary" onclick="kirimApprovalVPPrakatalog('POSTING <?=(str_replace("_", " ", $judulDokumen))?> ?','<?=$documentId?>','<?=$paketId?>','<?=$rekananId?>')">Posting <?=$buttonTitle?></button>
        <?	
			 }
		}
		else
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>&reqRekananId=<?=$rekananId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
        <?				
		}	
		
	}
	
	
	public function getViewApprovalSMAanwijzing($paketId, $documentId)
	{
		
		$CI =& get_instance();
		$CI->load->model("ApprovalManager");
		
		$approval_manager = new ApprovalManager();
		$sudahPosting = $approval_manager->getCountByParams(array("PRIMARY_ID" => $paketId, "TABEL" => $documentId));
		
		$CI->load->model("PaketPanitia");
		$paket_panitia_validasi = new PaketPanitia;
		$adaAksesMenu = $paket_panitia_validasi->getCountByParamsPanitiaValidasi(array("PAKET_ID" => $paketId, "NIP" => $this->NIP));
		
		if($sudahPosting == 0)
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>" target="_blank" >Cetak</a>
             <?
             if($adaAksesMenu > 0)
			 {
			 ?>
			 <button type="button" class="btn btn-primary" onclick="kirimApprovalSM('POSTING <?=(str_replace("_", " ", $documentId))?> ?','<?=$documentId?>','<?=$paketId?>')">Posting</button>
        <?	
			 }
		}
		else
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>" target="_blank" >Cetak</a>
        <?		
		
		 	 $CI =& get_instance();
			 $statusApproval = $CI->db->query(" SELECT AANWIJZING_PUBLISH FROM PAKET WHERE PAKET_ID = '".$paketId."' ")->row()->aanwijzing_publish;
			 
			 if($statusApproval == "R")
			 {
			 ?>
				 <button type="button" class="btn btn-primary" onClick="kirimApprovalSM('POSTING <?=(str_replace("_", " ", $documentId))?> ?','<?=$documentId?>','<?=$paketId?>')">Posting</button>
             <?	 
			 }		
		}	
		
	}
	
	
	
	public function getViewApprovalPanitiaPihakLain($paketId, $documentId, $buttonTitle="")
	{
		
		$CI =& get_instance();
		$CI->load->model("ApprovalManager");
		
		$approval_manager = new ApprovalManager();
		$sudahPosting = $approval_manager->getCountByParams(array("PRIMARY_ID" => $paketId, "TABEL" => $documentId), " AND NOT COALESCE(NULLIF(APPROVAL, ''), 'X') = 'REVISI' ");
		
		$CI->load->model("PaketPanitia");
		$paket_panitia_validasi = new PaketPanitia;
		$adaAksesMenu = $paket_panitia_validasi->getCountByParamsPanitiaValidasi(array("PAKET_ID" => $paketId, "NIP" => $this->NIP));
		
		if($sudahPosting == 0)
		{
			if($this->USER_TYPE_ID == 6)
			{}
			else
			{
			?>
                 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
                 <?
                 if($adaAksesMenu > 0)
                 {
                 ?>
                 <button type="button" class="btn btn-primary" onClick="kirimApprovalPanitiaPihakLain('POSTING <?=(str_replace("_", " ", $documentId))?> ?','<?=$documentId?>','<?=$paketId?>')">Posting <?=$buttonTitle?></button>
            <?	
                 }
			}
		}
		else
		{
			
			if($this->USER_TYPE_ID == 6)
			{
				if($this->getIsApproved($paketId, $documentId) == 1)
				{
			?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
            <?
				}
			}
			else
			{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
        <?		
			}
		}	
		
	}


	public function getViewApprovalPanitiaPihakLainRekanan($paketId, $documentId, $buttonTitle="")
	{
		
		$CI =& get_instance();
		$CI->load->model("ApprovalManager");
		
		$approval_manager = new ApprovalManager();
		$sudahPosting = $approval_manager->getCountByParams(array("PRIMARY_ID" => $paketId, "TABEL" => $documentId));
		
		$CI->load->model("PaketPanitia");
		$paket_panitia_validasi = new PaketPanitia;
		$adaAksesMenu = $paket_panitia_validasi->getCountByParamsPanitiaValidasi(array("PAKET_ID" => $paketId, "NIP" => $this->NIP));
		
		if($sudahPosting == 0)
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
             <?
             if($adaAksesMenu > 0)
			 {
			 ?>
			 <button type="button" class="btn btn-primary" onClick="kirimApprovalPanitiaPihakLainRekanan('POSTING <?=(str_replace("_", " ", $documentId))?> ?','<?=$documentId?>','<?=$paketId?>')">Posting</button>
        <?	
			 }
		}
		else
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$paketId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
        <?				
		}	
		
	}


	public function getViewApprovalPrakualifikasi($reqRekananId, $documentId, $buttonTitle="")
	{
		
		$CI =& get_instance();
		$CI->load->model("ApprovalManager");
		
		$approval_manager = new ApprovalManager();
		$sudahPosting = $approval_manager->getCountByParams(array("PRIMARY_ID" => $reqRekananId, "TABEL" => $documentId));
		
		$CI->load->model("RegionalPic");
		$regional_pic = new RegionalPic;
		$adaAksesMenu = $regional_pic->getCountByParamsMonitoring(array("REGIONAL_ID" => "6", "NIP" => $this->NIP));
		
		if($sudahPosting == 0)
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$reqRekananId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
             <?
             if($adaAksesMenu > 0)
			 {
			 ?>
			 <button type="button" class="btn btn-primary" onClick="kirimApprovalPrakualifikasi('POSTING <?=(str_replace("_", " ", $documentId))?> ?','<?=$documentId?>','<?=$reqRekananId?>')">Posting <?=$buttonTitle?></button>
        <?	
			 }
		}
		else
		{
		?>
			 <a class="btn-kembali" href="app/loadUrl/report_layout/<?=strtolower($documentId)?>_pdf/?reqId=<?=$reqRekananId?>" target="_blank" >Cetak <?=$buttonTitle?></a>
        <?				
		}	
		
	}


	public function getViewApprovalPrakualifikasiTahap1($reqRekananId, $documentId, $buttonTitle="")
	{
		
		$CI =& get_instance();
		$CI->load->model("ApprovalManager");
		
		$approval_manager = new ApprovalManager();
		$sudahPosting = $approval_manager->getCountByParams(array("PRIMARY_ID" => $reqRekananId, "TABEL" => $documentId));
		
		$CI->load->model("RegionalPic");
		$regional_pic = new RegionalPic;
		$adaAksesMenu = $regional_pic->getCountByParamsMonitoring(array("REGIONAL_ID" => "6", "NIP" => $this->NIP));
		// echo $adaAksesMenu;
		if($sudahPosting == 0)
		{
            if($adaAksesMenu > 0)
			{
			?>
			<button type="button" class="btn btn-primary" onClick="kirimApprovalPrakualifikasi('POSTING <?=(str_replace("_", " ", $documentId))?> ?','<?=$documentId?>','<?=$reqRekananId?>')">Posting <?=$buttonTitle?></button>
        <?	
			}
		}		
	}


}
	
  /***** INSTANTIATE THE GLOBAL OBJECT */
  $approvalinfo = new ApprovalInfo();

?>