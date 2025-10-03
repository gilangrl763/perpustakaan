<?php
defined('BASEPATH') or exit('No direct script access allowed');

include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");

class perusahaan_json extends CI_Controller
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
		
		// $this->load->library("crfs_protect"); 
		// $csrf = new crfs_protect('_crfs_7mj4R5iP');
		// if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false){
		// 	exit();
		// }

		$this->load->model("Perusahaan");
		$perusahaan = new Perusahaan();

		$STATUS = $this->input->get("STATUS");

		$aColumns		= array("PERUSAHAAN_ID","STATUS","LOGO","KODE","NAMA","ALAMAT","TELEPON","EMAIL","STATUS_KET");
		$aColumnsAlias	= array("PERUSAHAAN_ID","STATUS","LOGO","KODE","NAMA","ALAMAT","TELEPON","EMAIL","STATUS_KET");

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
			if (trim($sOrder) == "ORDER BY PERUSAHAAN_ID asc") {
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

		$allRecord = $perusahaan->getCountByParamsMonitoring(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == ""){
			$allRecordFilter = $allRecord;
		}
		else{
			$allRecordFilter =  $perusahaan->getCountByParamsMonitoring(array(), $statement);
		}

		$perusahaan->selectByParamsMonitoring(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $perusahaan->query;exit;

		if($this->IS_MOBILE == true)
		{
			$arrResult["rowCount"] = $perusahaan->rowCount;
			$arrResult["rowResult"] = $perusahaan->rowResult;
			echo json_encode($arrResult);
			return;
		}

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($perusahaan->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if($aColumns[$i] == "STATUS_KET") {
					if($perusahaan->getField("STATUS") == "AKTIF"){
						$badge_color = "badge-success";
					}
					else{
						$badge_color = "badge-danger";
					}

					$row[] = "<span class='badge ".$badge_color."'>".str_replace("_", " ", $perusahaan->getField("STATUS"))."</span>";
				}
				elseif($aColumns[$i] == "LOGO") {
					$row[] = "<img src='uploads/perusahaan/".$perusahaan->getField($aColumns[$i])."' width='*' height='30px'>";
				} 
				else{
					$row[] = $perusahaan->getField($aColumns[$i]);
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

		$this->load->model("Perusahaan");
		$perusahaan = new Perusahaan();

		$id 		= $this->input->post("id");
		$mode 		= $this->input->post("mode");

		$KODE		= $this->input->post("KODE");
		$NAMA		= $this->input->post("NAMA");
		$ALAMAT		= setQuote($_POST['ALAMAT']);
		$TELEPON	= $this->input->post("TELEPON");
		$EMAIL		= $this->input->post("EMAIL");
		$WEBSITE	= $this->input->post("WEBSITE");

		/******* START VALIDASI ******/
		if($mode == "insert") {
			$adaKode = $perusahaan->getCountByParams(array("KODE"=>$KODE));

			if($adaKode > 0){
				echo "GAGAL|Kode telah tersedia";
				return;
			}
		}
		else{
			$adaKode = $perusahaan->getCountByParams(array("KODE"=>$KODE,"NOT PERUSAHAAN_ID"=>$id));

			if($adaKode > 0){
				echo "GAGAL|Kode telah tersedia";
				return;
			}
		}
		/******* END VALIDASI ******/

		/******* START UPLOAD FILE ******/
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$FILE_DIR = "uploads/perusahaan/";

		$LOGO 		= $_FILES['LOGO'];
		$LOGOTemp 	= $this->input->post('LOGOTemp');

		$renameDokumen = md5($this->USER_LOGIN_ID . "_" . date("dmYHis")) . "." . getExtension($LOGO['name']);
		if($file->uploadToDir('LOGO', $FILE_DIR, $renameDokumen)) {
			$insertFile = $renameDokumen;
		}
		else {
			$insertFile = $LOGOTemp;
		}
		/******* END UPLOAD FILE ******/


		$perusahaan->setField("PERUSAHAAN_ID", $id);
		$perusahaan->setField("KODE", $KODE);
		$perusahaan->setField("NAMA", $NAMA);
		$perusahaan->setField("ALAMAT", $ALAMAT);
		$perusahaan->setField("TELEPON", $TELEPON);
		$perusahaan->setField("EMAIL", $EMAIL);
		$perusahaan->setField("WEBSITE", $WEBSITE);
		$perusahaan->setField("LOGO", $insertFile);
		$perusahaan->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$perusahaan->setField("UPDATED_BY", $this->USER_LOGIN_ID);

		if($mode == "insert") {
			if($perusahaan->insert()){
				$id = $perusahaan->id;

				$KODE = "PRS".generateZero($id, 6);

				$perusahaan = new Perusahaan();
				$perusahaan->setField("PERUSAHAAN_ID", $id);
				$perusahaan->setField("FIELD", "KODE");
				$perusahaan->setField("FIELD_VALUE", $KODE);
				if($perusahaan->updateByField()){
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
			if($perusahaan->update()){
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

		$this->load->model("Perusahaan");
		$perusahaan = new Perusahaan();

		$perusahaan->setField("PERUSAHAAN_ID", $reqId);
		
		if ($perusahaan->delete()){
			echo "Data berhasil dihapus";
		}
		else{
			echo "Data gagal dihapus. Terdapat relasi dengan data lainnya!" ;
		}
	}


	function aktif()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Perusahaan");
		$perusahaan = new Perusahaan();

		$perusahaan->setField("PERUSAHAAN_ID", $reqId);
		$perusahaan->setField("FIELD", "STATUS");
		$perusahaan->setField("FIELD_VALUE", "AKTIF");
		if ($perusahaan->updateByField()){
			echo "Data berhasil diaktifkan";
		}
		else{
			echo "Data gagal diaktifkan" ;
		}
	}

	function non_aktif()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Perusahaan");
		$perusahaan = new Perusahaan();

		$perusahaan->setField("PERUSAHAAN_ID", $reqId);
		$perusahaan->setField("FIELD", "STATUS");
		$perusahaan->setField("FIELD_VALUE", "TIDAK_AKTIF");
		if ($perusahaan->updateByField()){
			echo "Data berhasil dinonaktifkan";
		}
		else{
			echo "Data gagal dinonaktifkan" ;
		}
	}

	function combo()
	{
		$this->load->model("Perusahaan");
		$perusahaan = new Perusahaan();

		$i = 0;
		$perusahaan->selectByParams(array("STATUS" => "AKTIF"));
		while ($perusahaan->nextRow()) {
			$arr_json[$i]['id']		= $perusahaan->getField("PERUSAHAAN_ID");
			$arr_json[$i]['text']	= $perusahaan->getField("NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}
}
