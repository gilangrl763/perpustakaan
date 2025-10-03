<?php
defined('BASEPATH') or exit('No direct script access allowed');

include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");

class penyusutan_akhir_json extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		/* MOBILE */
		$this->IS_MOBILE = false;
		$reqTokenMobile = $this->input->get("reqTokenMobile");
		if (empty($reqTokenMobile)) {
			$reqTokenMobile = $this->input->post("reqTokenMobile");
		}

		if (!empty($reqTokenMobile)) {
			$this->kauth->localAuthenticate("", "", $reqTokenMobile);
			$this->IS_MOBILE = true;
		}
		/* END MOBILE */


		if (!$this->kauth->getInstance()->hasIdentity()) {
			redirect('login');
		}

		$this->db->query("SET DATESTYLE TO PostgreSQL,European;");

		$this->ID 					= $this->kauth->getInstance()->getIdentity()->ID;
		$this->USER_LOGIN_ID 		= $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->USER_GROUP 			= $this->kauth->getInstance()->getIdentity()->USER_GROUP;
		$this->PERUSAHAAN_ID 		= $this->kauth->getInstance()->getIdentity()->PERUSAHAAN_ID;
		$this->KODE_PERUSAHAAN 		= $this->kauth->getInstance()->getIdentity()->KODE_PERUSAHAAN;
		$this->NAMA_PERUSAHAAN 		= $this->kauth->getInstance()->getIdentity()->NAMA_PERUSAHAAN;
		$this->CABANG_ID 			= $this->kauth->getInstance()->getIdentity()->CABANG_ID;
		$this->KODE_CABANG 			= $this->kauth->getInstance()->getIdentity()->KODE_CABANG;
		$this->NAMA_CABANG 			= $this->kauth->getInstance()->getIdentity()->NAMA_CABANG;
		$this->SATUAN_KERJA_ID 		= $this->kauth->getInstance()->getIdentity()->SATUAN_KERJA_ID;
		$this->KODE_SATUAN_KERJA 	= $this->kauth->getInstance()->getIdentity()->KODE_SATUAN_KERJA;
		$this->NAMA_SATUAN_KERJA 	= $this->kauth->getInstance()->getIdentity()->NAMA_SATUAN_KERJA;
		$this->PEGAWAI_ID 			= $this->kauth->getInstance()->getIdentity()->PEGAWAI_ID;
		$this->KODE_PEGAWAI 		= $this->kauth->getInstance()->getIdentity()->KODE_PEGAWAI;
		$this->NAMA_PEGAWAI 		= $this->kauth->getInstance()->getIdentity()->NAMA_PEGAWAI;
		$this->JABATAN 				= $this->kauth->getInstance()->getIdentity()->JABATAN;
		$this->MULTIROLE 			= $this->kauth->getInstance()->getIdentity()->MULTIROLE;
	}

	function json()
	{
		$this->load->library("crfs_protect"); 
		$csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false){
			exit();
		}

		$this->load->model("PenyusutanAkhir");
		$penyusutan_akhir = new PenyusutanAkhir();

		$STATUS = $this->input->get("STATUS");

		$aColumns		= array("PENYUSUTAN_AKHIR_ID","STATUS","KODE","NAMA","KETERANGAN","STATUS_KET");
		$aColumnsAlias	= array("PENYUSUTAN_AKHIR_ID","STATUS","KODE","NAMA","KETERANGAN","STATUS_KET");

		/*
		 * Ordering
		 */
		if (isset($_GET['iSortCol_0'])) {
			$sOrder = " ORDER BY ";

			//Go over all sorting cols
			for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
				//If need to sort by current col
				if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
					//Add to the order by clause
					$sOrder .= $aColumnsAlias[intval($_GET['iSortCol_' . $i])];

					//Determine if it is sorted asc or desc
					if (strcasecmp(($_GET['sSortDir_' . $i]), "asc") == 0) {
						$sOrder .= " asc, ";
					} else {
						$sOrder .= " desc, ";
					}
				}
			}

			//Remove the last space / comma
			$sOrder = substr_replace($sOrder, "", -2);

			//Check if there is an order by clause
			if (trim($sOrder) == "ORDER BY PENYUSUTAN_AKHIR_ID asc") {
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY A.CREATED_DATE asc";
			}
		}

		/*
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables.
		 */
		$sWhere = "";
		$nWhereGenearalCount = 0;
		if (isset($_GET['sSearch'])) {
			$sWhereGenearal = $_GET['sSearch'];
		} else {
			$sWhereGenearal = '';
		}

		if ($_GET['sSearch'] != "") {
			//Set a default where clause in order for the where clause not to fail
			//in cases where there are no searchable cols at all.
			$sWhere = " AND (";
			for ($i = 0; $i < count($aColumnsAlias) + 1; $i++) {
				//If current col has a search param
				if ($_GET['bSearchable_' . $i] == "true") {
					//Add the search to the where clause
					$sWhere .= $aColumnsAlias[$i] . " LIKE '%" . $_GET['sSearch'] . "%' OR ";
					$nWhereGenearalCount += 1;
				}
			}
			$sWhere = substr_replace($sWhere, "", -3);
			$sWhere .= ')';
		}

		/* Individual column filtering */
		$sWhereSpecificArray = array();
		$sWhereSpecificArrayCount = 0;
		for ($i = 0; $i < count($aColumnsAlias); $i++) {
			if ($_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
				//If there was no where clause
				if ($sWhere == "") {
					$sWhere = "AND ";
				} else {
					$sWhere .= " AND ";
				}

				//Add the clause of the specific col to the where clause
				$sWhere .= $aColumnsAlias[$i] . " LIKE '%' || :whereSpecificParam" . $sWhereSpecificArrayCount . " || '%' ";

				//Inc sWhereSpecificArrayCount. It is needed for the bind var.
				//We could just do count($sWhereSpecificArray) - but that would be less efficient.
				$sWhereSpecificArrayCount++;

				//Add current search param to the array for later use (binding).
				$sWhereSpecificArray[] =  $_GET['sSearch_' . $i];
			}
		}

		//If there is still no where clause - set a general - always true where clause
		if ($sWhere == "") {
			$sWhere = " AND 1=1";
		}

		//Bind variables.
		if (isset($_GET['iDisplayStart'])) {
			$dsplyStart = $_GET['iDisplayStart'];
		} else {
			$dsplyStart = 0;
		}
		if (isset($_GET['iDisplayLength']) && $_GET['iDisplayLength'] != '-1') {
			$dsplyRange = $_GET['iDisplayLength'];
			if ($dsplyRange > (2147483645 - intval($dsplyStart))) {
				$dsplyRange = 2147483645;
			} else {
				$dsplyRange = intval($dsplyRange);
			}
		} else {
			$dsplyRange = 2147483645;
		}
		
		if($STATUS != 'ALL'){
			$statement .= " AND A.STATUS = '$STATUS' ";
		}
		
		$statement .= " AND (UPPER(A.NAMA) LIKE '%" . strtoupper($_GET['sSearch']) . "%')";

		$allRecord = $penyusutan_akhir->getCountByParamsMonitoring(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == ""){
			$allRecordFilter = $allRecord;
		}
		else{
			$allRecordFilter =  $penyusutan_akhir->getCountByParamsMonitoring(array(), $statement);
		}

		$penyusutan_akhir->selectByParamsMonitoring(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $perusahaan->query;exit;

		if($this->IS_MOBILE == true)
		{
			$arrResult["rowCount"] = $penyusutan_akhir->rowCount;
			$arrResult["rowResult"] = $penyusutan_akhir->rowResult;
			echo json_encode($arrResult);
			return;
		}
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($penyusutan_akhir->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if($aColumns[$i] == "STATUS_KET") {
					if($penyusutan_akhir->getField("STATUS") == "AKTIF"){
						$badge_color = "badge-success";
					}
					else{
						$badge_color = "badge-danger";
					}
					$row[] = "<span class='badge ".$badge_color."'>".str_replace("_", " ", $penyusutan_akhir->getField("STATUS"))."</span>";
				}else{
					$row[] = $penyusutan_akhir->getField($aColumns[$i]);
				}
			}
			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}


	function add()
	{
		$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->post('_crfs_7mj4R5iP')) && $this->IS_MOBILE == false){
			exit();
		}

		$this->load->model("PenyusutanAkhir");
		$penyusutan_akhir = new PenyusutanAkhir();

		$id 		= $this->input->post("id");
		$mode 		= $this->input->post("mode");

		$KODE			= $this->input->post("KODE");
		$NAMA			= $this->input->post("NAMA");
		$KETERANGAN		= setQuote($_POST['KETERANGAN']);

		/******* START VALIDASI ******/
		if($mode == "insert") {
			$adaKode = $penyusutan_akhir->getCountByParams(array("KODE"=>$KODE));

			if($adaKode > 0){
				echo "GAGAL|Kode telah tersedia";
				return;
			}
		}
		else{
			$adaKode = $penyusutan_akhir->getCountByParams(array("KODE"=>$KODE,"NOT PENYUSUTAN_AKHIR_ID"=>$id));

			if($adaKode > 0){
				echo "GAGAL|Kode telah tersedia";
				return;
			}
		}
		/******* END VALIDASI ******/

		$penyusutan_akhir->setField("PENYUSUTAN_AKHIR_ID", $id);
		$penyusutan_akhir->setField("KODE", $KODE);
		$penyusutan_akhir->setField("NAMA", $NAMA);
		$penyusutan_akhir->setField("KETERANGAN", $KETERANGAN);
		$penyusutan_akhir->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$penyusutan_akhir->setField("UPDATED_BY", $this->USER_LOGIN_ID);

		

		if($mode == "insert") {
			if($penyusutan_akhir->insert()){
				$id = $penyusutan_akhir->id;

				$KODE = "PA".generateZero($id, 6);

				$penyusutan_akhir = new PenyusutanAkhir();
				$penyusutan_akhir->setField("PENYUSUTAN_AKHIR_ID", $id);
				$penyusutan_akhir->setField("FIELD", "KODE");
				$penyusutan_akhir->setField("FIELD_VALUE", $KODE);
				if($penyusutan_akhir->updateByField()){
					echo "BERHASIL|Data berhasil disimpan|".$id;
				} 
				else{
					echo "GAGAL|Data gagal disimpan";
				}
			} 
			else{
				echo "GAGAL|Data gagal disimpan";
			}
		} 
		else{
			if($penyusutan_akhir->update()){
				echo "BERHASIL|Data berhasil disimpan|".$id;
			} 
			else{
				echo "GAGAL|Data gagal disimpan";
			}
		}
	}

	function delete()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("PenyusutanAkhir");
		$penyusutan_akhir = new PenyusutanAkhir();

		$penyusutan_akhir->setField("PENYUSUTAN_AKHIR_ID", $reqId);
		
		if ($penyusutan_akhir->delete()){
			echo "Data berhasil dihapus";
		}
		else{
			echo "Data gagal dihapus. Terdapat relasi dengan data lainnya!" ;
		}
	}

	function aktif()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("PenyusutanAkhir");
		$penyusutan_akhir = new PenyusutanAkhir();

		$penyusutan_akhir->setField("PENYUSUTAN_AKHIR_ID", $reqId);
		$penyusutan_akhir->setField("FIELD", "STATUS");
		$penyusutan_akhir->setField("FIELD_VALUE", "AKTIF");
		if ($penyusutan_akhir->updateByField()){
			echo "Data berhasil diaktifkan";
		}
		else{
			echo "Data gagal diaktifkan" ;
		}
	}

	function non_aktif()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("PenyusutanAkhir");
		$penyusutan_akhir = new PenyusutanAkhir();

		$penyusutan_akhir->setField("PENYUSUTAN_AKHIR_ID", $reqId);
		$penyusutan_akhir->setField("FIELD", "STATUS");
		$penyusutan_akhir->setField("FIELD_VALUE", "TIDAK_AKTIF");
		if ($penyusutan_akhir->updateByField()){
			echo "Data berhasil dinonaktifkan";
		}
		else{
			echo "Data gagal dinonaktifkan" ;
		}
	}


	function combo()
	{
		$this->load->model("PenyusutanAkhir");
		$penyusutan_akhir = new PenyusutanAkhir();

		$i = 0;
		$penyusutan_akhir->selectByParams(array("STATUS" => "AKTIF"),-1,-1,$statement);
		// echo $penyusutan_akhir->query;exit;
		while ($penyusutan_akhir->nextRow()) {
			$arr_json[$i]['id']		= $penyusutan_akhir->getField("PENYUSUTAN_AKHIR_ID");
			$arr_json[$i]['text']	= $penyusutan_akhir->getField("NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}
}
