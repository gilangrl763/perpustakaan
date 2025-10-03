<?php
defined('BASEPATH') or exit('No direct script access allowed');

include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");

class peminjaman_json extends CI_Controller
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

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");
		$CABANG_ID = $this->input->get("CABANG_ID");
		$SATUAN_KERJA_ID = $this->input->get("SATUAN_KERJA_ID");
		$STATUS = $this->input->get("STATUS");

		$aColumns		= array("PEMINJAMAN_ID","STATUS","NOMOR","TANGGAL","PERIHAL","KETERANGAN","NAMA_PERUSAHAAN","NAMA_CABANG",
			"NAMA_SATUAN_KERJA","STATUS_KET");
		
		$aColumnsAlias	= array("PEMINJAMAN_ID","STATUS","NOMOR","TANGGAL","PERIHAL","KETERANGAN","NAMA_PERUSAHAAN","NAMA_CABANG",
			"NAMA_SATUAN_KERJA","STATUS_KET");

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
			if (trim($sOrder) == "ORDER BY PEMINJAMAN_ID asc") {
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

		if($PERUSAHAAN_ID != 'ALL'){
			$statement .= " AND A.PERUSAHAAN_ID = '$PERUSAHAAN_ID' ";
		}

		if($CABANG_ID != 'ALL'){
			$statement .= " AND A.CABANG_ID = '$CABANG_ID' ";
		}

		if($SATUAN_KERJA_ID != 'ALL'){
			$statement .= " AND A.SATUAN_KERJA_ID = '$SATUAN_KERJA_ID' ";
		}

		if($STATUS != 'ALL'){
			$statement .= " AND A.STATUS = '$STATUS' ";
		}

		if($this->USER_GROUP == "PEGAWAI"){
			$statement .= " AND A.CREATED_BY = '$this->USER_LOGIN_ID' ";
		}


		$statement .= "AND (UPPER(A.NOMOR) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.PERIHAL) LIKE '%".strtoupper($_GET['sSearch'])."%'
		)";

		$allRecord = $peminjaman->getCountByParamsMonitoring(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $peminjaman->getCountByParamsMonitoring(array(), $statement);
		}

		$peminjaman->selectByParamsMonitoring(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $peminjaman->query;exit;

		if($this->IS_MOBILE == true)
		{
			$arrResult["rowCount"] = $peminjaman->rowCount;
			$arrResult["rowResult"] = $peminjaman->rowResult;
			echo json_encode($arrResult);
			return;
		}
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($peminjaman->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "STATUS_KET") {
					if($peminjaman->getField("STATUS") == "ENTRI"){
						$badge_color = "badge-info";
					}
					elseif($peminjaman->getField("STATUS") == "POSTING"){
						$badge_color = "badge-secondary";
					}
					elseif($peminjaman->getField("STATUS") == "VERIFIKASI_ARSIPARIS"){
						$badge_color = "badge-success";
					}
					else{
						$badge_color = "badge-danger";
					}

					$row[] = "<span class='badge ".$badge_color."'>".str_replace("_", " ", $peminjaman->getField("STATUS"))."</span>";
				} else {
					$row[] = $peminjaman->getField($aColumns[$i]);
				}
			}
			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}


	function json_approval()
	{
		$this->load->library("crfs_protect"); 
		$csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false){
			exit();
		}

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$STATUS = $this->input->get("STATUS");

		$aColumns		= array("PEMINJAMAN_APPROVAL_ID","PEMINJAMAN_ID","STATUS","STATUS_PEMINJAMAN","NOMOR","TANGGAL","PERIHAL",
			"KETERANGAN","NAMA_PERUSAHAAN","NAMA_CABANG","NAMA_SATUAN_KERJA","STATUS_KET","TANGGAL_APPROVE");
		
		$aColumnsAlias	= array("PEMINJAMAN_APPROVAL_ID","PEMINJAMAN_ID","STATUS","STATUS_PEMINJAMAN","NOMOR","TANGGAL","PERIHAL",
			"KETERANGAN","NAMA_PERUSAHAAN","NAMA_CABANG","NAMA_SATUAN_KERJA","STATUS_KET","TANGGAL_APPROVE");

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
			if (trim($sOrder) == "ORDER BY PEMINJAMAN_ID asc") {
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

		$statement .= " AND A.PEGAWAI_ID = '$this->PEGAWAI_ID' ";
		$statement .= " AND B.STATUS IN ('POSTING','TERKIRIM','VERIFIKASI_ARSIPARIS','DIPINJAM','DIKEMBALIKAN','SIMPAN') ";

		if($STATUS != 'ALL'){
			$statement .= " AND A.STATUS = '$STATUS' ";
		}

		$statement .= "AND (UPPER(B.NOMOR) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(B.PERIHAL) LIKE '%".strtoupper($_GET['sSearch'])."%'
		)";

		$allRecord = $peminjaman->getCountByParamsMonitoringApproval(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $peminjaman->getCountByParamsMonitoringApproval(array(), $statement);
		}

		$peminjaman->selectByParamsMonitoringApproval(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $peminjaman->query;exit;

		if($this->IS_MOBILE == true)
		{
			$arrResult["rowCount"] = $peminjaman->rowCount;
			$arrResult["rowResult"] = $peminjaman->rowResult;
			echo json_encode($arrResult);
			return;
		}
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($peminjaman->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "STATUS_KET") {
					if($peminjaman->getField("STATUS") == "ENTRI"){
						$badge_color = "badge-info";
					}
					elseif($peminjaman->getField("STATUS") == "POSTING"){
						$badge_color = "badge-secondary";
					}
					elseif($peminjaman->getField("STATUS") == "VERIFIKASI_ARSIPARIS"){
						$badge_color = "badge-success";
					}
					else{
						$badge_color = "badge-danger";
					}

					$row[] = "<span class='badge ".$badge_color."'>".str_replace("_", " ", $peminjaman->getField("STATUS"))."</span>";
				} else {
					$row[] = $peminjaman->getField($aColumns[$i]);
				}
			}
			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}


	function json_inbox()
	{
		$this->load->library("crfs_protect"); 
		$csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false){
			exit();
		}

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$STATUS = $this->input->get("STATUS");
		$TANGGAL_AWAL = $this->input->get("TANGGAL_AWAL");
		$TANGGAL_AKHIR = $this->input->get("TANGGAL_AKHIR");

		$aColumns		= array("PEMINJAMAN_TUJUAN_ID","PEMINJAMAN_ID","TERBACA","KODE","NOMOR","TERDISPOSISI","TANGGAL_KIRIM");
		$aColumnsAlias	= array("PEMINJAMAN_TUJUAN_ID","PEMINJAMAN_ID","TERBACA","KODE","NOMOR","TERDISPOSISI","TANGGAL_KIRIM");

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
			if (trim($sOrder) == "ORDER BY PEMINJAMAN_TUJUAN_ID asc") {
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY A.KODE asc";
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

		$statement .= " AND A.PEGAWAI_ID = '$this->PEGAWAI_ID' ";
		$statement .= " AND B.STATUS IN ('TERKIRIM','VERIFIKASI_ARSIPARIS','DIPINJAM','DIKEMBALIKAN','SIMPAN') ";

		if($STATUS != 'ALL'){
			$statement .= " AND A.TERDISPOSISI = '$STATUS' ";
		}

		$statement .= " AND TO_CHAR(A.TANGGAL_KIRIM,'YYYY-MM-DD') 
			BETWEEN '".dateToDB($TANGGAL_AWAL)."' AND '".dateToDB($TANGGAL_AKHIR)."' ";

		$statement .= "AND (UPPER(B.NOMOR) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(B.PERIHAL) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(A.KODE_DISPOSISI) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(A.NAMA_DISPOSISI) LIKE '%".strtoupper($_GET['sSearch'])."%'
		)";

		$allRecord = $peminjaman->getCountByParamsMonitoringTujuan(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $peminjaman->getCountByParamsMonitoringTujuan(array(), $statement);
		}

		$peminjaman->selectByParamsMonitoringTujuan(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $peminjaman->query;exit;

		if($this->IS_MOBILE == true)
		{
			$arrResult["rowCount"] = $peminjaman->rowCount;
			$arrResult["rowResult"] = $peminjaman->rowResult;
			echo json_encode($arrResult);
			return;
		}
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($peminjaman->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "KODE") {
					$row[] = "<b>".$peminjaman->getField("NAMA")."</b><br>".$peminjaman->getField("JABATAN");
				}
				elseif ($aColumns[$i] == "NOMOR") {
					$row[] = "<b>".$peminjaman->getField("NOMOR")."</b><br>".$peminjaman->getField("PERIHAL");
				} 
				elseif ($aColumns[$i] == "TERBACA") {
					if($peminjaman->getField("TERBACA") == "YA"){
						$row[] = "<i class='fa fa-envelope-open' style='color: #2e8201;font-size: 36px;'></i>";
					}
					elseif($peminjaman->getField("TERBACA") == "TIDAK"){
						$row[] = "<i class='fa fa-envelope' style='color: #2b9ef3;font-size: 36px;'></i>";
					}
				}
				elseif ($aColumns[$i] == "TERDISPOSISI") {
					if($peminjaman->getField("TERDISPOSISI") == "YA"){
						$row[] = "<span class='badge badge-success'>Terdisposisikan</span>";
					}
					elseif($peminjaman->getField("TERDISPOSISI") == "TIDAK"){
						$row[] = "<span class='badge badge-danger'>Belum Didisposisikan</span>";
					}
				}
				elseif ($aColumns[$i] == "TANGGAL_KIRIM") {
					$row[] = getFormattedDateTime2($peminjaman->getField($aColumns[$i]));
				} 
				else {
					$row[] = $peminjaman->getField($aColumns[$i]);
				}
			}
			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}

	function json_disposisi()
	{
		$this->load->library("crfs_protect"); 
		$csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false){
			exit();
		}

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$PEMINJAMAN_ID = $this->input->get("PEMINJAMAN_ID");

		$aColumns		= array("PEMINJAMAN_TUJUAN_ID","PEMINJAMAN_ID","TANGGAL_KIRIM","NAMA_DISPOSISI","NAMA","JENIS",
			"PESAN_DISPOSISI","TERBACA","TERDISPOSISI");
		$aColumnsAlias	= array("PEMINJAMAN_TUJUAN_ID","PEMINJAMAN_ID","TANGGAL_KIRIM","NAMA_DISPOSISI","NAMA","JENIS",
			"PESAN_DISPOSISI","TERBACA","TERDISPOSISI");

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
			if (trim($sOrder) == "ORDER BY PEMINJAMAN_TUJUAN_ID asc") {
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY A.KODE asc";
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

		$statement .= " AND A.PEMINJAMAN_ID='$PEMINJAMAN_ID' ";

		$statement .= "AND (UPPER(A.KODE) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(A.KODE_DISPOSISI) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(A.NAMA_DISPOSISI) LIKE '%".strtoupper($_GET['sSearch'])."%'
		)";

		$allRecord = $peminjaman->getCountByParamsTujuan(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $peminjaman->getCountByParamsTujuan(array(), $statement);
		}

		$peminjaman->selectByParamsTujuan(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $peminjaman->query;exit;

		if($this->IS_MOBILE == true)
		{
			$arrResult["rowCount"] = $peminjaman->rowCount;
			$arrResult["rowResult"] = $peminjaman->rowResult;
			echo json_encode($arrResult);
			return;
		}
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($peminjaman->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "NAMA_DISPOSISI") {
					$row[] = "<b>".$peminjaman->getField("NAMA_DISPOSISI")."</b><br>".$peminjaman->getField("JABATAN_DISPOSISI");
				}
				elseif ($aColumns[$i] == "NAMA") {
					$row[] = "<b>".$peminjaman->getField("NAMA")."</b><br>".$peminjaman->getField("JABATAN");
				}
				elseif ($aColumns[$i] == "TERBACA") {
					if($peminjaman->getField("TERBACA") == "YA"){
						$row[] = "<i class='fa fa-check-square-o' style='color: #00a62c;' title=' Terbaca pada tanggal ".getFormattedDateTime2($peminjaman->getField("TERBACA_TANGGAL"))."'></i>";
					}
					elseif($peminjaman->getField("TERBACA") == "TIDAK"){
						$row[] = "<i class='fa fa-times' style='color: #ba0013;' title='Belum dibaca'></i>";
					}
				}
				elseif ($aColumns[$i] == "TERDISPOSISI") {
					if($peminjaman->getField("TERDISPOSISI") == "YA"){
						$row[] = "<i class='fa fa-check-square-o' style='color: #00a62c;' title='Terdisposisikan pada tanggal ".getFormattedDateTime2($peminjaman->getField("TANGGAL_DISPOSISI"))."'></i>";
					}
					elseif($peminjaman->getField("TERDISPOSISI") == "TIDAK"){
						$row[] = "<i class='fa fa-times' style='color: #ba0013;'></i>";
					}
				}
				elseif ($aColumns[$i] == "TANGGAL_KIRIM") {
					$row[] = getFormattedDateTime3($peminjaman->getField($aColumns[$i]));
				} 
				else {
					$row[] = $peminjaman->getField($aColumns[$i]);
				}
			}
			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}


	function json_verifikasi()
	{
		$this->load->library("crfs_protect"); 
		$csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false){
			exit();
		}

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");
		$CABANG_ID = $this->input->get("CABANG_ID");
		$SATUAN_KERJA_ID = $this->input->get("SATUAN_KERJA_ID");
		$STATUS = $this->input->get("STATUS");

		$aColumns		= array("PEMINJAMAN_ID","STATUS","NOMOR","TANGGAL","PERIHAL","KETERANGAN","NAMA_PERUSAHAAN","NAMA_CABANG",
			"NAMA_SATUAN_KERJA","STATUS_KET");
		
		$aColumnsAlias	= array("PEMINJAMAN_ID","STATUS","NOMOR","TANGGAL","PERIHAL","KETERANGAN","NAMA_PERUSAHAAN","NAMA_CABANG",
			"NAMA_SATUAN_KERJA","STATUS_KET");

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
			if (trim($sOrder) == "ORDER BY PEMINJAMAN_ID asc") {
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

		if($PERUSAHAAN_ID != 'ALL'){
			$statement .= " AND A.PERUSAHAAN_ID = '$PERUSAHAAN_ID' ";
		}

		if($CABANG_ID != 'ALL'){
			$statement .= " AND A.CABANG_ID = '$CABANG_ID' ";
		}

		if($SATUAN_KERJA_ID != 'ALL'){
			$statement .= " AND A.SATUAN_KERJA_ID = '$SATUAN_KERJA_ID' ";
		}

		if($STATUS == 'ALL'){
			$statement .= " AND A.STATUS IN ('TERKIRIM','VERIFIKASI_ARSIPARIS','DIPINJAM','DIPINJAM_ULANG','DIKEMBALIKAN','SIMPAN') ";
		}
		else {
			$statement .= " AND A.STATUS = '$STATUS' ";
		}


		//CEK APAKAH SUDAH MENDAPATKAN DISPOSISI 
		$statement .= " AND EXISTS(SELECT 1 FROM PEMINJAMAN_TUJUAN X WHERE X.PEMINJAMAN_ID=A.PEMINJAMAN_ID 
			AND X.JENIS='DISPOSISI' AND X.PEGAWAI_ID='$this->PEGAWAI_ID')";


		$statement .= "AND (UPPER(A.NOMOR) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.PERIHAL) LIKE '%".strtoupper($_GET['sSearch'])."%'
		)";

		$allRecord = $peminjaman->getCountByParamsMonitoring(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $peminjaman->getCountByParamsMonitoring(array(), $statement);
		}

		$peminjaman->selectByParamsMonitoring(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $peminjaman->query;exit;

		if($this->IS_MOBILE == true)
		{
			$arrResult["rowCount"] = $peminjaman->rowCount;
			$arrResult["rowResult"] = $peminjaman->rowResult;
			echo json_encode($arrResult);
			return;
		}
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($peminjaman->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "STATUS_KET") {
					if($peminjaman->getField("STATUS") == "TERKIRIM"){
						$label = "Penerimaan Berkas";
						$badge_color = "badge-warning";
					}
					elseif($peminjaman->getField("STATUS") == "DIPINJAM"){
						$label = "Dipinjam";
						$badge_color = "badge-info";
					}
					elseif($peminjaman->getField("STATUS") == "DIPINJAM_ULANG"){
						$label = "Dipinjam Ulang";
						$badge_color = "badge-info";
					}
					elseif($peminjaman->getField("STATUS") == "DIKEMBALIKAN"){
						$label = "Dikembalikan";
						$badge_color = "badge-warning";
					}
					elseif($peminjaman->getField("STATUS") == "SIMPAN"){
						$label = "Tersimpan";
						$badge_color = "badge-success";
					}

					$row[] = "<span class='badge ".$badge_color."'>".$label."</span>";
				} 
				else {
					$row[] = $peminjaman->getField($aColumns[$i]);
				}
			}
			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}


	function json_home()
	{
		$this->load->library("crfs_protect"); 
		$csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false){
			exit();
		}

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");
		$CABANG_ID = $this->input->get("CABANG_ID");
		$SATUAN_KERJA_ID = $this->input->get("SATUAN_KERJA_ID");
		$STATUS = $this->input->get("STATUS");

		$aColumns		= array("PEMINJAMAN_ID","STATUS","NOMOR","TANGGAL","PERIHAL","KETERANGAN","NAMA_PERUSAHAAN","NAMA_CABANG",
			"NAMA_SATUAN_KERJA","STATUS_KET");
		
		$aColumnsAlias	= array("PEMINJAMAN_ID","STATUS","NOMOR","TANGGAL","PERIHAL","KETERANGAN","NAMA_PERUSAHAAN","NAMA_CABANG",
			"NAMA_SATUAN_KERJA","STATUS_KET");

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
			if (trim($sOrder) == "ORDER BY PEMINJAMAN_ID asc") {
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

		$statement .= " AND A.PERUSAHAAN_ID = '$this->PERUSAHAAN_ID' ";
        $statement .= " AND A.CABANG_ID = '$this->CABANG_ID' ";

        if($this->USER_GROUP == "PEGAWAI"){
            $statement .= " AND A.SATUAN_KERJA_ID = '$this->SATUAN_KERJA_ID' ";
            $statement .= " AND A.CREATED_BY = '$this->USER_LOGIN_ID' ";
        }
        elseif($this->USER_GROUP == "ARSIPARIS"){
            $statement .= " AND A.STATUS IN ('TERKIRIM','VERIFIKASI_ARSIPARIS','DIPINJAM','DIPINJAM_ULANG','DIKEMBALIKAN','DISIMPAN') ";
            $statement .= " AND EXISTS(SELECT 1 FROM PEMINJAMAN_TUJUAN X WHERE X.PEMINJAMAN_ID=A.PEMINJAMAN_ID AND X.JENIS='DISPOSISI' AND X.PEGAWAI_ID='$this->PEGAWAI_ID')";
        }


		$statement .= "AND (UPPER(A.NOMOR) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.PERIHAL) LIKE '%".strtoupper($_GET['sSearch'])."%'
		)";

		$allRecord = $peminjaman->getCountByParamsMonitoring(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $peminjaman->getCountByParamsMonitoring(array(), $statement);
		}

		$peminjaman->selectByParamsMonitoring(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $peminjaman->query;exit;

		if($this->IS_MOBILE == true)
		{
			$arrResult["rowCount"] = $peminjaman->rowCount;
			$arrResult["rowResult"] = $peminjaman->rowResult;
			echo json_encode($arrResult);
			return;
		}
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($peminjaman->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "STATUS_KET") {
					if($peminjaman->getField("STATUS") == "TERKIRIM"){
						$label = "Penerimaan Berkas";
						$badge_color = "badge-warning";
					}
					elseif($peminjaman->getField("STATUS") == "DIPINJAM"){
						$label = "Dipinjam";
						$badge_color = "badge-info";
					}
					elseif($peminjaman->getField("STATUS") == "DIPINJAM_ULANG"){
						$label = "Dipinjam Ulang";
						$badge_color = "badge-info";
					}
					elseif($peminjaman->getField("STATUS") == "DIKEMBALIKAN"){
						$label = "Dikembalikan";
						$badge_color = "badge-warning";
					}
					elseif($peminjaman->getField("STATUS") == "SIMPAN"){
						$label = "Tersimpan";
						$badge_color = "badge-success";
					}

					$row[] = "<span class='badge ".$badge_color."'>".$label."</span>";
				} 
				else {
					$row[] = $peminjaman->getField($aColumns[$i]);
				}
			}
			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}


	function json_daftar_pinjam()
	{
		$this->load->library("crfs_protect"); 
		$csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false){
			exit();
		}

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");
		$CABANG_ID = $this->input->get("CABANG_ID");
		$SATUAN_KERJA_ID = $this->input->get("SATUAN_KERJA_ID");
		$STATUS = $this->input->get("STATUS");

		$aColumns		= array("PEMINJAMAN_BERKAS_ID","PEMINJAMAN_ID","DOKUMEN","KLASIFIKASI_KODE","NOMOR_DOKUMEN","NOMOR_ALTERNATIF",
			"KETERANGAN","KURUN_WAKTU","TINGKAT_PERKEMBANGAN_NAMA","KONDISI_FISIK_NAMA","JUMLAH_BERKAS","LOKASI_SIMPAN_NOMOR_FOLDER",
			"TANGGAL_PINJAM","BATAS_TANGGAL_KEMBALI","TANGGAL_KEMBALI","NAMA_CABANG","NAMA_SATUAN_KERJA");
		$aColumnsAlias	= array("PEMINJAMAN_BERKAS_ID","PEMINJAMAN_ID","DOKUMEN","KLASIFIKASI_KODE","NOMOR_DOKUMEN","NOMOR_ALTERNATIF",
			"KETERANGAN","KURUN_WAKTU","TINGKAT_PERKEMBANGAN_NAMA","KONDISI_FISIK_NAMA","JUMLAH_BERKAS","LOKASI_SIMPAN_NOMOR_FOLDER",
			"TANGGAL_PINJAM","BATAS_TANGGAL_KEMBALI","TANGGAL_KEMBALI","NAMA_CABANG","NAMA_SATUAN_KERJA");

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
			if (trim($sOrder) == "ORDER BY PEMINJAMAN_BERKAS_ID asc") {
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY F.KLASIFIKASI_KODE asc";
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

		if($PERUSAHAAN_ID != 'ALL'){
			$statement .= " AND B.PERUSAHAAN_ID = '$PERUSAHAAN_ID' ";
		}

		if($CABANG_ID != 'ALL'){
			$statement .= " AND B.CABANG_ID = '$CABANG_ID' ";
		}

		if($SATUAN_KERJA_ID != 'ALL'){
			$statement .= " AND B.SATUAN_KERJA_ID = '$SATUAN_KERJA_ID' ";
		}

		if($STATUS == 'ALL'){
			$statement .= " AND A.STATUS IN ('DIPINJAM','DIPINJAM_ULANG','DIKEMBALIKAN','SIMPAN') ";
		}
		else{
			$statement .= " AND A.STATUS = '$STATUS' ";
		}

		$statement .= "AND (UPPER(F.KLASIFIKASI_KODE) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(F.KLASIFIKASI_NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.KETERANGAN) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.KURUN_WAKTU) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.TINGKAT_PERKEMBANGAN_NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.TAHUN_PINDAH) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.TAHUN_MUSNAH) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.MEDIA_SIMPAN_NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.LOKASI_SIMPAN_RUANG) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.LOKASI_SIMPAN_LEMARI) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.LOKASI_SIMPAN_NOMOR_BOKS) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.LOKASI_SIMPAN_NOMOR_FOLDER) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.KONDISI_FISIK_NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.JUMLAH_BERKAS) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.NOMOR_DOKUMEN) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.NOMOR_ALTERNATIF) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(C.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(D.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(E.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%'
		)";

		$allRecord = $peminjaman->getCountByParamsMonitoringBerkas(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $peminjaman->getCountByParamsMonitoringBerkas(array(), $statement);
		}

		$peminjaman->selectByParamsMonitoringBerkas(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $peminjaman->query;exit;

		if($this->IS_MOBILE == true)
		{
			$arrResult["rowCount"] = $peminjaman->rowCount;
			$arrResult["rowResult"] = $peminjaman->rowResult;
			echo json_encode($arrResult);
			return;
		}
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($peminjaman->nextRow()) {
			// Tanggal awal
			$tanggal_awal = date("Y-m-d");
			// Tanggal akhir
			$tanggal_akhir = $peminjaman->getField("BATAS_TANGGAL_KEMBALI");
			// Mengonversi tanggal ke format yang dapat dihitung
			$timestamp_awal = strtotime($tanggal_awal);
			$timestamp_akhir = strtotime($tanggal_akhir);
			// Menghitung selisih dalam detik
			$selisih_detik = $timestamp_akhir - $timestamp_awal;
			// Mengonversi selisih detik ke selisih hari
			$selisih_hari = floor($selisih_detik / (60 * 60 * 24));
			// Menggunakan fungsi abs() agar selisih tetap positif
			// $selisih_hari = abs($selisih_hari);

			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "KLASIFIKASI_KODE") {
					$row[] = "<b>".$peminjaman->getField("KLASIFIKASI_KODE")."</b><br>".$peminjaman->getField("KLASIFIKASI_NAMA");
				}
				elseif ($aColumns[$i] == "KLASIFIKASI_KODE") {
					$row[] = "<b>".$peminjaman->getField("KLASIFIKASI_KODE")."</b><br>".$peminjaman->getField("KLASIFIKASI_NAMA");
				}
				elseif ($aColumns[$i] == "DOKUMEN") {
					$row[] = "<a href='javascript:void(0)' class='btn btn-xs btn-info' onclick=openPopup('uploads/pemindahan/".$peminjaman->getField("DOKUMEN")."')><i class='fa fa-eye'></i> Dokumen</a>";
				} 
				elseif ($aColumns[$i] == "BATAS_TANGGAL_KEMBALI") {

					if($selisih_hari <= 2){
						$row[] = "<p style='color:#ff0000;'>".$peminjaman->getField($aColumns[$i])."</p>";
					}
					elseif($selisih_hari < 0){
						$row[] = $peminjaman->getField($aColumns[$i]);
					}
					else{
						$row[] = $peminjaman->getField($aColumns[$i]);
					}
				}
				elseif ($aColumns[$i] == "LOKASI_SIMPAN_NOMOR_FOLDER") {
					$row[] = "[Rak - ".$peminjaman->getField("LOKASI_SIMPAN_LEMARI")."][B. ".$peminjaman->getField("LOKASI_SIMPAN_NOMOR_BOKS")."][F - ".$peminjaman->getField("LOKASI_SIMPAN_NOMOR_FOLDER")."]";
				} 
				else {
					$row[] = $peminjaman->getField($aColumns[$i]);
				}
			}
			
			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}


	function json_daftar_pinjam_pegawai()
	{
		$this->load->library("crfs_protect"); 
		$csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false){
			exit();
		}

		$this->load->model("Peminjaman");
		$peminjaman_berkas = new Peminjaman();

		$STATUS = $this->input->get("STATUS");

		$aColumns		= array("PEMINJAMAN_BERKAS_ID","PEMINJAMAN_ID","DOKUMEN","KLASIFIKASI_KODE","NOMOR_DOKUMEN","NOMOR_ALTERNATIF",
			"KETERANGAN","KURUN_WAKTU","TAHUN_PINDAH","JUMLAH_BERKAS","TANGGAL_PINJAM","BATAS_TANGGAL_KEMBALI","TANGGAL_KEMBALI",
			"NAMA_CABANG","NAMA_SATUAN_KERJA");
		$aColumnsAlias	= array("PEMINJAMAN_BERKAS_ID","PEMINJAMAN_ID","DOKUMEN","KLASIFIKASI_KODE","NOMOR_DOKUMEN","NOMOR_ALTERNATIF",
			"KETERANGAN","KURUN_WAKTU","TAHUN_PINDAH","JUMLAH_BERKAS","TANGGAL_PINJAM","BATAS_TANGGAL_KEMBALI","TANGGAL_KEMBALI",
			"NAMA_CABANG","NAMA_SATUAN_KERJA");

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
			if (trim($sOrder) == "ORDER BY PEMINJAMAN_BERKAS_ID asc") {
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY F.KLASIFIKASI_KODE asc";
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

		if($STATUS == 'ALL'){
			$statement .= " AND A.STATUS IN ('DIPINJAM','DIPINJAM_ULANG','DIKEMBALIKAN','SIMPAN') ";
		}
		elseif($STATUS == 'DIKEMBALIKAN'){
			$statement .= " AND A.STATUS IN ('DIKEMBALIKAN','SIMPAN') ";
		}
		else{
			$statement .= " AND A.STATUS = '$STATUS' ";
		}

		$statement .= " AND A.PEGAWAI_ID = '$this->PEGAWAI_ID' ";

		$statement .= "AND (UPPER(F.KLASIFIKASI_KODE) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(F.KLASIFIKASI_NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.KETERANGAN) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.KURUN_WAKTU) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.TAHUN_PINDAH) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.JUMLAH_BERKAS) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.NOMOR_DOKUMEN) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(F.NOMOR_ALTERNATIF) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(C.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(D.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%'
			OR UPPER(E.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%'
		)";

		$allRecord = $peminjaman_berkas->getCountByParamsMonitoringBerkas(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $peminjaman_berkas->getCountByParamsMonitoringBerkas(array(), $statement);
		}

		$peminjaman_berkas->selectByParamsMonitoringBerkas(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $peminjaman_berkas->query;exit;

		if($this->IS_MOBILE == true)
		{
			$arrResult["rowCount"] = $peminjaman_berkas->rowCount;
			$arrResult["rowResult"] = $peminjaman_berkas->rowResult;
			echo json_encode($arrResult);
			return;
		}
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($peminjaman_berkas->nextRow()) {
			// Tanggal awal
			$tanggal_awal = date("Y-m-d");
			// Tanggal akhir
			$tanggal_akhir = $peminjaman_berkas->getField("BATAS_TANGGAL_KEMBALI");
			// Mengonversi tanggal ke format yang dapat dihitung
			$timestamp_awal = strtotime($tanggal_awal);
			$timestamp_akhir = strtotime($tanggal_akhir);
			// Menghitung selisih dalam detik
			$selisih_detik = $timestamp_akhir - $timestamp_awal;
			// Mengonversi selisih detik ke selisih hari
			$selisih_hari = floor($selisih_detik / (60 * 60 * 24));
			// Menggunakan fungsi abs() agar selisih tetap positif
			// $selisih_hari = abs($selisih_hari);

			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "KLASIFIKASI_KODE") {
					$row[] = "<b>".$peminjaman_berkas->getField("KLASIFIKASI_KODE")."</b><br>".$peminjaman_berkas->getField("KLASIFIKASI_NAMA");
				}
				elseif ($aColumns[$i] == "DOKUMEN") {
					if ($peminjaman_berkas->getField("STATUS") == "SIMPAN" || $peminjaman_berkas->getField("STATUS") == "DIKEMBALIKAN") {
						$row[] = "Tidak Tersedia";
					}else {
						if($selisih_hari < 0){
							$row[] = "Tidak Tersedia";
						}
						else{
							$row[] = "<a href='javascript:void(0)' class='btn btn-xs btn-info' onclick=openPopup('uploads/pemindahan/".$peminjaman_berkas->getField("DOKUMEN")."')><i class='fa fa-eye'></i> Dokumen</a>";
						}
					}
				}
				elseif ($aColumns[$i] == "BATAS_TANGGAL_KEMBALI") {

					if($selisih_hari <= 2){
						$row[] = "<p style='color:#ff0000;'>".$peminjaman_berkas->getField($aColumns[$i])."</p>";
					}
					elseif($selisih_hari < 0){
						$row[] = $peminjaman_berkas->getField($aColumns[$i]);
					}
					else{
						$row[] = $peminjaman_berkas->getField($aColumns[$i]);
					}
				}
				else {
					$row[] = $peminjaman_berkas->getField($aColumns[$i]);
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

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$id 		= $this->input->post("id");
		$mode 		= $this->input->post("mode");

		$PERUSAHAAN_ID					= $this->input->post("PERUSAHAAN_ID");
		$CABANG_ID						= $this->input->post("CABANG_ID");
		$SATUAN_KERJA_ID				= $this->input->post("SATUAN_KERJA_ID");
		$NOMOR							= $this->input->post("NOMOR");
		$TANGGAL						= $this->input->post("TANGGAL");
		$PERIHAL						= $this->input->post("PERIHAL");
		$ISI							= setQuote($_POST['ISI']);
		$KETERANGAN						= setQuote($_POST['KETERANGAN']);
		$KEPERLUAN						= $this->input->post("KEPERLUAN");
		
		$PEMINJAMAN_BERKAS_ID			= $this->input->post("PEMINJAMAN_BERKAS_ID");
		$PEMINDAHAN_BERKAS_ID			= $this->input->post("PEMINDAHAN_BERKAS_ID");
		$PEMINDAHAN_ID					= $this->input->post("PEMINDAHAN_ID");
		$PEGAWAI_ID_BERKAS				= $this->input->post("PEGAWAI_ID_BERKAS");
		$ADA_HARDCOPY					= $this->input->post("ADA_HARDCOPY");

		$PEMINJAMAN_TUJUAN_ID_TUJUAN	= $this->input->post("PEMINJAMAN_TUJUAN_ID_TUJUAN");
		$PERUSAHAAN_ID_TUJUAN			= $this->input->post("PERUSAHAAN_ID_TUJUAN");
		$CABANG_ID_TUJUAN				= $this->input->post("CABANG_ID_TUJUAN");
		$SATUAN_KERJA_ID_TUJUAN			= $this->input->post("SATUAN_KERJA_ID_TUJUAN");
		$JENIS_TUJUAN					= $this->input->post("JENIS_TUJUAN");
		$PEGAWAI_ID_TUJUAN				= $this->input->post("PEGAWAI_ID_TUJUAN");
		$KODE_TUJUAN					= $this->input->post("KODE_TUJUAN");
		$NAMA_TUJUAN					= $this->input->post("NAMA_TUJUAN");
		$JABATAN_TUJUAN					= $this->input->post("JABATAN_TUJUAN");

		$PEMINJAMAN_TUJUAN_ID_TEMBUSAN	= $this->input->post("PEMINJAMAN_TUJUAN_ID_TEMBUSAN");
		$PERUSAHAAN_ID_TEMBUSAN			= $this->input->post("PERUSAHAAN_ID_TEMBUSAN");
		$CABANG_ID_TEMBUSAN				= $this->input->post("CABANG_ID_TEMBUSAN");
		$SATUAN_KERJA_ID_TEMBUSAN		= $this->input->post("SATUAN_KERJA_ID_TEMBUSAN");
		$JENIS_TEMBUSAN					= $this->input->post("JENIS_TEMBUSAN");
		$PEGAWAI_ID_TEMBUSAN			= $this->input->post("PEGAWAI_ID_TEMBUSAN");
		$KODE_TEMBUSAN					= $this->input->post("KODE_TEMBUSAN");
		$NAMA_TEMBUSAN					= $this->input->post("NAMA_TEMBUSAN");
		$JABATAN_TEMBUSAN				= $this->input->post("JABATAN_TEMBUSAN");

		$PEMINJAMAN_APPROVAL_ID			= $this->input->post("PEMINJAMAN_APPROVAL_ID");
		$PERUSAHAAN_ID_APPROVAL			= $this->input->post("PERUSAHAAN_ID_APPROVAL");
		$CABANG_ID_APPROVAL				= $this->input->post("CABANG_ID_APPROVAL");
		$SATUAN_KERJA_ID_APPROVAL		= $this->input->post("SATUAN_KERJA_ID_APPROVAL");
		$SEBAGAI_APPROVAL				= $this->input->post("SEBAGAI_APPROVAL");
		$PEGAWAI_ID_APPROVAL			= $this->input->post("PEGAWAI_ID_APPROVAL");
		$URUT_APPROVAL					= $this->input->post("URUT_APPROVAL");
		$KODE_APPROVAL					= $this->input->post("KODE_APPROVAL");
		$NAMA_APPROVAL					= $this->input->post("NAMA_APPROVAL");
		$JABATAN_APPROVAL				= $this->input->post("JABATAN_APPROVAL");

		/******VALIDASI*******/
		if(count(array_filter($PEMINDAHAN_BERKAS_ID)) <= 0){
			echo "GAGAL|Daftar Berkas tidak tersedia. Silahkan pilih DPA/D terlebih dahulu!";
			return;
		}

		if(count(array_filter($PEGAWAI_ID_TUJUAN)) <= 0){
			echo "GAGAL|Tujuan tidak tersedia. Tambahkan Tujuan terlebih dahulu!";
			return;
		}

		if(count(array_filter($PEGAWAI_ID_TUJUAN)) > 1){
			echo "GAGAL|Maks. 1 (satu) data Tujuan yang dapat dipilih!";
			return;
		}

		if(count(array_filter($PEGAWAI_ID_APPROVAL)) <= 0){
			echo "GAGAL|Approval tidak tersedia. Tambahkan Approval terlebih dahulu!";
			return;
		}

		if(count(array_filter($PEGAWAI_ID_APPROVAL)) > 0){
			if(in_array("1", $URUT_APPROVAL)){
			}
			else{
				echo "GAGAL|Urutan ke-1 pada Approval belum ditentukan";
				return;
			}

			for ($i=1; $i<=count(array_filter($URUT_APPROVAL)); $i++) { 
				if(in_array($i, $URUT_APPROVAL)){
				}
				else{
					echo "GAGAL|Urutan pada Approval tidak urut!";
					return;
				}
			}
		}

		if(empty($KEPERLUAN)){
			echo "GAGAL|Keperluan permohonan peminjaman wajib diisi!";
			return;
		}
		/******END VALIDASI*******/


		$KODE_JABATAN = $this->db->query("select kode_jabatan from satuan_kerja 
			where satuan_kerja_id='$SATUAN_KERJA_ID'")->row()->kode_jabatan;

		/******UPLOAD DOKUMEN******/
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$FILE_DIR = "uploads/peminjaman/";

		$DOKUMEN 				= $_FILES['DOKUMEN'];
		$PEMINJAMAN_DOKUMEN_ID 	= $this->input->post('PEMINJAMAN_DOKUMEN_ID');
		$NAMA_DOKUMEN 			= $this->input->post('NAMA_DOKUMEN');
		$TEMP_DOKUMEN 			= $this->input->post('TEMP_DOKUMEN');
		$UKURAN_DOKUMEN 		= $this->input->post('UKURAN_DOKUMEN');

		$DOKUMEN_BERKAS 		= $_FILES['DOKUMEN_BERKAS'];
		$TEMP_DOKUMEN_BERKAS 	= $this->input->post('TEMP_DOKUMEN_BERKAS');
		/******END UPLOAD DOKUMEN******/


		$peminjaman->setField("PEMINJAMAN_ID", $id);
		$peminjaman->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID);
		$peminjaman->setField("CABANG_ID", $CABANG_ID);
		$peminjaman->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID);
		$peminjaman->setField("NOMOR", $NOMOR);
		$peminjaman->setField("TANGGAL", dateToDBCheck($TANGGAL));
		$peminjaman->setField("PERIHAL", $PERIHAL);
		$peminjaman->setField("ISI", $TINGKAT_PERKEMBANGAN_ID);
		$peminjaman->setField("KETERANGAN", $KETERANGAN);
		$peminjaman->setField("KEPERLUAN", $KEPERLUAN);
		$peminjaman->setField("DOKUMEN", $insertFile);
		$peminjaman->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$peminjaman->setField("UPDATED_BY", $this->USER_LOGIN_ID);

		if ($mode == "insert") {
			if($peminjaman->insert()){
				$id = $peminjaman->id;

				/******BERKAS*******/
				for ($i=0; $i < count($PEMINDAHAN_BERKAS_ID); $i++) { 
					$peminjaman_berkas = new Peminjaman();
					$peminjaman_berkas->setField("PEMINJAMAN_ID", $id);
					$peminjaman_berkas->setField("PEMINJAMAN_BERKAS_ID", $PEMINJAMAN_BERKAS_ID[$i]);
					$peminjaman_berkas->setField("PEMINDAHAN_BERKAS_ID", $PEMINDAHAN_BERKAS_ID[$i]);
					$peminjaman_berkas->setField("PEMINDAHAN_ID", $PEMINDAHAN_ID[$i]);
					$peminjaman_berkas->setField("PEGAWAI_ID", $this->PEGAWAI_ID);
					$peminjaman_berkas->setField("ADA_HARDCOPY", $ADA_HARDCOPY[$i]);
					$peminjaman_berkas->setField("KEPERLUAN", $KEPERLUAN);
					$peminjaman_berkas->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$peminjaman_berkas->insertBerkas();
				}
				/******END BERKAS*******/

				/******TUJUAN*******/
				for ($i=0; $i < count($PEMINJAMAN_TUJUAN_ID_TUJUAN); $i++) { 
					$peminjaman_tujuan = new Peminjaman();
					$peminjaman_tujuan->setField("PEMINJAMAN_ID", $id);
					$peminjaman_tujuan->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID_TUJUAN[$i]);
					$peminjaman_tujuan->setField("CABANG_ID", $CABANG_ID_TUJUAN[$i]);
					$peminjaman_tujuan->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID_TUJUAN[$i]);
					$peminjaman_tujuan->setField("PEMINJAMAN_TUJUAN_ID", $PEMINJAMAN_TUJUAN_ID_TUJUAN[$i]);
					$peminjaman_tujuan->setField("JENIS", $JENIS_TUJUAN[$i]);
					$peminjaman_tujuan->setField("PEGAWAI_ID", $PEGAWAI_ID_TUJUAN[$i]);
					$peminjaman_tujuan->setField("KODE", $KODE_TUJUAN[$i]);
					$peminjaman_tujuan->setField("NAMA", setQuote($NAMA_TUJUAN[$i]));
					$peminjaman_tujuan->setField("JABATAN", $JABATAN_TUJUAN[$i]);
					$peminjaman_tujuan->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$peminjaman_tujuan->insertTujuan();
				}
				/******END TUJUAN*******/

				/******TEMBUSAN*******/
				for ($i=0; $i < count($PEMINJAMAN_TUJUAN_ID_TEMBUSAN); $i++) { 
					$peminjaman_tembusan = new Peminjaman();
					$peminjaman_tembusan->setField("PEMINJAMAN_ID", $id);
					$peminjaman_tembusan->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("CABANG_ID", $CABANG_ID_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("PEMINJAMAN_TUJUAN_ID", $PEMINJAMAN_TUJUAN_ID_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("JENIS", $JENIS_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("PEGAWAI_ID", $PEGAWAI_ID_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("KODE", $KODE_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("NAMA", setQuote($NAMA_TEMBUSAN[$i]));
					$peminjaman_tembusan->setField("JABATAN", $JABATAN_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$peminjaman_tembusan->insertTujuan();
				}
				/******END TEMBUSAN*******/

				/******APPROVAL*******/
				for ($i=0; $i < count($PEMINJAMAN_APPROVAL_ID); $i++) {
					$peminjaman_approval = new Peminjaman();
					$peminjaman_approval->setField("PEMINJAMAN_ID", $id);
					$peminjaman_approval->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID_APPROVAL[$i]);
					$peminjaman_approval->setField("CABANG_ID", $CABANG_ID_APPROVAL[$i]);
					$peminjaman_approval->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID_APPROVAL[$i]);
					$peminjaman_approval->setField("PEMINJAMAN_APPROVAL_ID", $PEMINJAMAN_APPROVAL_ID[$i]);
					$peminjaman_approval->setField("PEGAWAI_ID", $PEGAWAI_ID_APPROVAL[$i]);
					$peminjaman_approval->setField("KODE", $KODE_APPROVAL[$i]);
					$peminjaman_approval->setField("NAMA", setQuote($NAMA_APPROVAL[$i]));
					$peminjaman_approval->setField("JABATAN", $JABATAN_APPROVAL[$i]);
					$peminjaman_approval->setField("SEBAGAI", $SEBAGAI_APPROVAL[$i]);
					$peminjaman_approval->setField("URUT", $URUT_APPROVAL[$i]);
					$peminjaman_approval->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$peminjaman_approval->insertApproval();
				}
				/******END APPROVAL*******/

				/******DOKUMEN*******/
				for ($i=0; $i < count($DOKUMEN); $i++) { 
					$renameFile = "DOK_DUKUNG_".md5($i.date("Ymdhis").rand()).".".getExtension($DOKUMEN['name'][$i]);
					if($file->uploadToDirArray('DOKUMEN', $FILE_DIR, $renameFile, $i)){
						$insertLinkSize = $file->uploadedSize;
						$insertLinkTipe = $file->uploadedExtension;
						$insertLinkFile = $renameFile;
						$insertNamaFile = setQuote($DOKUMEN['name'][$i]);
						
						$peminjaman_dokumen = new Peminjaman();
						$peminjaman_dokumen->setField("PEMINJAMAN_ID", $id);
						$peminjaman_dokumen->setField("NAMA", $insertNamaFile);
						$peminjaman_dokumen->setField("DOKUMEN", $insertLinkFile);
						$peminjaman_dokumen->setField("UKURAN_DOKUMEN", $insertLinkSize);
						$peminjaman_dokumen->setField("CREATED_BY", $this->USER_LOGIN_ID);
						$peminjaman_dokumen->insertDokumen();
					}
				}
				/******END DOKUMEN*******/

				/******NOMOR*******/
				$NOMOR = generateZero($id, 6)."/TMJ-PEMINJAMAN/".$KODE_JABATAN."/".date('m')."/".date('Y');

				$peminjaman = new Peminjaman();
				$peminjaman->setField("PEMINJAMAN_ID", $id);
				$peminjaman->setField("FIELD", "NOMOR");
				$peminjaman->setField("FIELD_VALUE", $NOMOR);
				$peminjaman->setField("UPDATED_BY", $this->USER_LOGIN_ID);
				if($peminjaman->updateByField()){
					echo "BERHASIL|Data berhasil disimpan|".$id;
				} 
				else{
					echo "GAGAL|Data gagal disimpan";
				}
				/******END NOMOR*******/
			} 
			else{
				echo "GAGAL|Data gagal disimpan";
			}
		} 
		else {
			if ($peminjaman->update()) {
				/******BERKAS*******/
				for ($i=0; $i < count($PEMINDAHAN_BERKAS_ID); $i++) { 
					$peminjaman_berkas = new Peminjaman();
					$peminjaman_berkas->setField("PEMINJAMAN_ID", $id);
					$peminjaman_berkas->setField("PEMINJAMAN_BERKAS_ID", $PEMINJAMAN_BERKAS_ID[$i]);
					$peminjaman_berkas->setField("PEMINDAHAN_BERKAS_ID", $PEMINDAHAN_BERKAS_ID[$i]);
					$peminjaman_berkas->setField("PEMINDAHAN_ID", $PEMINDAHAN_ID[$i]);
					$peminjaman_berkas->setField("PEGAWAI_ID", $this->PEGAWAI_ID);
					$peminjaman_berkas->setField("ADA_HARDCOPY", $ADA_HARDCOPY[$i]);
					$peminjaman_berkas->setField("KEPERLUAN", $KEPERLUAN);
					$peminjaman_berkas->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$peminjaman_berkas->setField("UPDATED_BY", $this->USER_LOGIN_ID);
					
					if($PEMINJAMAN_BERKAS_ID[$i] == ""){
						$peminjaman_berkas->insertBerkas();
					}
					else{
						$peminjaman_berkas->updateBerkas();
					}
				}
				/******END BERKAS*******/

				/******TUJUAN*******/
				for ($i=0; $i < count($PEMINJAMAN_TUJUAN_ID_TUJUAN); $i++) { 
					$peminjaman_tujuan = new Peminjaman();
					$peminjaman_tujuan->setField("PEMINJAMAN_ID", $id);
					$peminjaman_tujuan->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID_TUJUAN[$i]);
					$peminjaman_tujuan->setField("CABANG_ID", $CABANG_ID_TUJUAN[$i]);
					$peminjaman_tujuan->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID_TUJUAN[$i]);
					$peminjaman_tujuan->setField("PEMINJAMAN_TUJUAN_ID", $PEMINJAMAN_TUJUAN_ID_TUJUAN[$i]);
					$peminjaman_tujuan->setField("JENIS", $JENIS_TUJUAN[$i]);
					$peminjaman_tujuan->setField("PEGAWAI_ID", $PEGAWAI_ID_TUJUAN[$i]);
					$peminjaman_tujuan->setField("KODE", $KODE_TUJUAN[$i]);
					$peminjaman_tujuan->setField("NAMA", setQuote($NAMA_TUJUAN[$i]));
					$peminjaman_tujuan->setField("JABATAN", $JABATAN_TUJUAN[$i]);
					$peminjaman_tujuan->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$peminjaman_tujuan->setField("UPDATED_BY", $this->USER_LOGIN_ID);

					if($PEMINJAMAN_TUJUAN_ID_TUJUAN[$i] == ""){
						$peminjaman_tujuan->insertTujuan();
					}
					else{
						$peminjaman_tujuan->updateTujuan();
					}
				}
				/******END TUJUAN*******/

				/******TEMBUSAN*******/
				for ($i=0; $i < count($PEMINJAMAN_TUJUAN_ID_TEMBUSAN); $i++) { 
					$peminjaman_tembusan = new Peminjaman();
					$peminjaman_tembusan->setField("PEMINJAMAN_ID", $id);
					$peminjaman_tembusan->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("CABANG_ID", $CABANG_ID_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("PEMINJAMAN_TUJUAN_ID", $PEMINJAMAN_TUJUAN_ID_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("JENIS", $JENIS_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("PEGAWAI_ID", $PEGAWAI_ID_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("KODE", $KODE_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("NAMA", setQuote($NAMA_TEMBUSAN[$i]));
					$peminjaman_tembusan->setField("JABATAN", $JABATAN_TEMBUSAN[$i]);
					$peminjaman_tembusan->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$peminjaman_tembusan->setField("UPDATED_BY", $this->USER_LOGIN_ID);
					
					if($PEMINJAMAN_TUJUAN_ID_TEMBUSAN[$i] == ""){
						$peminjaman_tembusan->insertTujuan();
					}
					else{
						$peminjaman_tembusan->updateTujuan();
					}
				}
				/******END TEMBUSAN*******/

				/******APPROVAL*******/
				for ($i=0; $i < count($PEMINJAMAN_APPROVAL_ID); $i++) {
					$peminjaman_approval = new Peminjaman();
					$peminjaman_approval->setField("PEMINJAMAN_ID", $id);
					$peminjaman_approval->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID_APPROVAL[$i]);
					$peminjaman_approval->setField("CABANG_ID", $CABANG_ID_APPROVAL[$i]);
					$peminjaman_approval->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID_APPROVAL[$i]);
					$peminjaman_approval->setField("PEMINJAMAN_APPROVAL_ID", $PEMINJAMAN_APPROVAL_ID[$i]);
					$peminjaman_approval->setField("PEGAWAI_ID", $PEGAWAI_ID_APPROVAL[$i]);
					$peminjaman_approval->setField("KODE", $KODE_APPROVAL[$i]);
					$peminjaman_approval->setField("NAMA", setQuote($NAMA_APPROVAL[$i]));
					$peminjaman_approval->setField("JABATAN", $JABATAN_APPROVAL[$i]);
					$peminjaman_approval->setField("SEBAGAI", $SEBAGAI_APPROVAL[$i]);
					$peminjaman_approval->setField("URUT", $URUT_APPROVAL[$i]);
					$peminjaman_approval->setField("STATUS", "BELUM_APPROVE");
					$peminjaman_approval->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$peminjaman_approval->setField("UPDATED_BY", $this->USER_LOGIN_ID);

					if($PEMINJAMAN_APPROVAL_ID[$i] == ""){
						$peminjaman_approval->insertApproval();
					}
					else{
						$peminjaman_approval->updateApproval();
					}
				}
				/******END APPROVAL*******/

				/******DOKUMEN*******/
				for ($i=0; $i < count($DOKUMEN); $i++) { 
					$renameFile = "DOK_DUKUNG_".md5($i.date("Ymdhis").rand()).".".getExtension($DOKUMEN['name'][$i]);
					if($file->uploadToDirArray('DOKUMEN', $FILE_DIR, $renameFile, $i)){
						$insertLinkSize = $file->uploadedSize;
						$insertLinkTipe = $file->uploadedExtension;
						$insertLinkFile = $renameFile;
						$insertNamaFile = setQuote($DOKUMEN['name'][$i]);
						
						$peminjaman_dokumen = new Peminjaman();
						$peminjaman_dokumen->setField("PEMINJAMAN_ID", $id);
						$peminjaman_dokumen->setField("NAMA", $insertNamaFile);
						$peminjaman_dokumen->setField("DOKUMEN", $insertLinkFile);
						$peminjaman_dokumen->setField("UKURAN_DOKUMEN", $insertLinkSize);
						$peminjaman_dokumen->setField("CREATED_BY", $this->USER_LOGIN_ID);
						$peminjaman_dokumen->insertDokumen();
					}
				}

				for ($i=0; $i < count($TEMP_DOKUMEN); $i++) { 
					$peminjaman_dokumen = new Peminjaman();
					$peminjaman_dokumen->setField("PEMINJAMAN_ID", $id);
					$peminjaman_dokumen->setField("PEMINJAMAN_DOKUMEN_ID", $PEMINJAMAN_DOKUMEN_ID[$i]);
					$peminjaman_dokumen->setField("NAMA", $NAMA_DOKUMEN[$i]);
					$peminjaman_dokumen->setField("DOKUMEN", $TEMP_DOKUMEN[$i]);
					$peminjaman_dokumen->setField("UKURAN_DOKUMEN", $UKURAN_DOKUMEN[$i]);
					$peminjaman_dokumen->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$peminjaman_dokumen->updateDokumen();
				}
				/******END DOKUMEN*******/

				/******NOMOR*******/
				$NOMOR = generateZero($id, 6)."/TMJ-PEMINJAMAN/".$KODE_JABATAN."/".date('m')."/".date('Y');

				$peminjaman = new Peminjaman();
				$peminjaman->setField("PEMINJAMAN_ID", $id);
				$peminjaman->setField("FIELD", "NOMOR");
				$peminjaman->setField("FIELD_VALUE", $NOMOR);
				$peminjaman->setField("UPDATED_BY", $this->USER_LOGIN_ID);
				if($peminjaman->updateByField()){
					echo "BERHASIL|Data berhasil disimpan|".$id;
				} 
				else{
					echo "GAGAL|Data gagal disimpan";
				}
				/******END NOMOR*******/
			} 
			else {
				echo "GAGAL|Data gagal disimpan";
			}
		}
	}

	function delete()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$peminjaman->setField("PEMINJAMAN_ID", $reqId);
		if ($peminjaman->delete()){
			echo "Data berhasil dihapus";
		}
		else{
			echo "Data gagal dihapus. Terdapat relasi dengan data lainnya!";
		}
	}

	function delete_berkas()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$peminjaman->setField("PEMINJAMAN_BERKAS_ID", $reqId);

		if ($peminjaman->deleteBerkas()){
			echo "Data berhasil dihapus";
		}
		else{
			echo "Data gagal dihapus. Terdapat relasi dengan data lainnya!";
		}
	}


	function delete_tujuan()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$peminjaman->setField("PEMINJAMAN_TUJUAN_ID", $reqId);

		if ($peminjaman->deleteTujuan()){
			echo "Data berhasil dihapus";
		}
		else{
			echo "Data gagal dihapus. Terdapat relasi dengan data lainnya!";
		}
	}

	function delete_approval()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$peminjaman->setField("PEMINJAMAN_APPROVAL_ID", $reqId);

		if ($peminjaman->deleteApproval()){
			echo "Data berhasil dihapus";
		}
		else{
			echo "Data gagal dihapus. Terdapat relasi dengan data lainnya!";
		}
	}


	function serah_pinjamkan()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		//BATAS TANGGAL KEMBALI
		$currentDate = new DateTime();
		$currentDate->modify('+5 days');
		$BATAS_TANGGAL_KEMBALI = $currentDate->format('d-m-Y');

		$peminjaman->setField("PEMINJAMAN_ID", $reqId);
		$peminjaman->setField("BATAS_TANGGAL_KEMBALI", dateToDBCheck($BATAS_TANGGAL_KEMBALI));
		$peminjaman->setField("PEGAWAI_ID_ARSIPARIS", $this->PEGAWAI_ID);
		$peminjaman->setField("NAMA_ARSIPARIS", $this->NAMA_PEGAWAI);
		$peminjaman->setField("JABATAN_ARSIPARIS", $this->JABATAN);
		$peminjaman->setField("STATUS", "DIPINJAM");
		$peminjaman->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if ($peminjaman->updateSerahPinjam()){
			$peminjaman_berkas = new Peminjaman();
			$peminjaman_berkas->setField("PEMINJAMAN_ID", $reqId);
			$peminjaman_berkas->setField("BATAS_TANGGAL_KEMBALI", dateToDBCheck($BATAS_TANGGAL_KEMBALI));
			$peminjaman_berkas->setField("PEGAWAI_ID_ARSIPARIS", $this->PEGAWAI_ID);
			$peminjaman_berkas->setField("NAMA_ARSIPARIS", $this->NAMA_PEGAWAI);
			$peminjaman_berkas->setField("JABATAN_ARSIPARIS", $this->JABATAN);
			$peminjaman_berkas->setField("STATUS", "DIPINJAM");
			$peminjaman_berkas->setField("UPDATED_BY", $this->USER_LOGIN_ID);
			if ($peminjaman_berkas->updateSerahPinjamBerkasByPeminjamanId()){
				/********LOG*********/
				$kode = "TERIMA_BERKAS";
				$keterangan = "Menyerahkan dan meminjamkan Dokumen/Berkas Fisik (Hardcopy) yang tercantum pada Daftar Pertelaan Arsip/Dokumen";
				$this->log($reqId, $kode, $keterangan);
				/********END LOG*********/

				echo "Berhasil menyerahkan dan meminjamkan Dokumen/Berkas";
			}
			else{
				echo "Gagal menyerahkan dan meminjamkan Dokumen/Berkas";
			}
		}
		else{
			echo "Gagal menyerahkan dan meminjamkan Dokumen/Berkas";
		}
	}

	function verifikasi_pengembalian()
	{
		$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->post('_crfs_7mj4R5iP')) && $this->IS_MOBILE == false){
			exit();
		}

		$this->load->model("Peminjaman");
		$this->load->model("Pemindahan");
		
		$id						= $this->input->post('id');
		$PEMINJAMAN_ID			= $this->input->post('PEMINJAMAN_ID');
		$PEMINDAHAN_ID			= $this->input->post('PEMINDAHAN_ID');
		$PEMINDAHAN_BERKAS_ID	= $this->input->post('PEMINDAHAN_BERKAS_ID');
		$KONDISI_FISIK_ID		= $this->input->post('KONDISI_FISIK_ID');
		$KONDISI_FISIK_KODE		= $this->input->post('KONDISI_FISIK_KODE');
		$KONDISI_FISIK_NAMA		= $this->input->post('KONDISI_FISIK_NAMA');
		$TANGGAL_KEMBALI		= $this->input->post('TANGGAL_KEMBALI');
		$STATUS					= $this->input->post('STATUS');
		$KETERANGAN_PINJAM_ULANG	= $this->input->post('KETERANGAN_PINJAM_ULANG');

		if($STATUS == "DIPINJAM_ULANG" && trim($KETERANGAN_PINJAM_ULANG) == ""){
			echo "GAGAL|Keterangan Peminjaman Ulang belum diisi";
			return;
		}

		//BATAS TANGGAL KEMBALI
		$currentDate = new DateTime();
		$currentDate->modify('+5 days');
		$BATAS_TANGGAL_KEMBALI = $currentDate->format('d-m-Y');

		$peminjaman_berkas = new Peminjaman();
		$peminjaman_berkas->setField("PEMINJAMAN_BERKAS_ID", $id);
		$peminjaman_berkas->setField("PEMINJAMAN_ID", $PEMINJAMAN_ID);
		$peminjaman_berkas->setField("PEMINDAHAN_ID", $PEMINDAHAN_ID);
		$peminjaman_berkas->setField("PEMINDAHAN_BERKAS_ID", $PEMINDAHAN_BERKAS_ID);
		$peminjaman_berkas->setField("KONDISI_FISIK_ID", $KONDISI_FISIK_ID);
		$peminjaman_berkas->setField("KONDISI_FISIK_KODE", $KONDISI_FISIK_KODE);
		$peminjaman_berkas->setField("KONDISI_FISIK_NAMA", $KONDISI_FISIK_NAMA);
		$peminjaman_berkas->setField("TANGGAL_KEMBALI", dateTimeToDBCheck($TANGGAL_KEMBALI));
		$peminjaman_berkas->setField("TANGGAL_PINJAM", dateToDBCheck($TANGGAL_KEMBALI));
		$peminjaman_berkas->setField("BATAS_TANGGAL_KEMBALI", dateToDBCheck($BATAS_TANGGAL_KEMBALI));
		$peminjaman_berkas->setField("STATUS", $STATUS);
		$peminjaman_berkas->setField("KETERANGAN_PINJAM_ULANG", $KETERANGAN_PINJAM_ULANG);
		$peminjaman_berkas->setField("UPDATED_BY", $this->USER_LOGIN_ID);

		if($STATUS == "SIMPAN"){
			if($peminjaman_berkas->updateVerifikasiPengembalian()){

				//SESUAIKAN JUGA KONDISI FISIK PADA PEMINDAHAN
				$pemindahan_berkas = new Pemindahan();
				$pemindahan_berkas->setField("PEMINDAHAN_BERKAS_ID", $PEMINDAHAN_BERKAS_ID);
				$pemindahan_berkas->setField("KONDISI_FISIK_ID", $KONDISI_FISIK_ID);
				$pemindahan_berkas->setField("KONDISI_FISIK_KODE", $KONDISI_FISIK_KODE);
				$pemindahan_berkas->setField("KONDISI_FISIK_NAMA", $KONDISI_FISIK_NAMA);
				$pemindahan_berkas->setField("UPDATED_BY", $this->USER_LOGIN_ID);
				if($pemindahan_berkas->updateVerifikasiPengembalian()){

					//APABILA SUDAH SEMUA STATUS TELAH SIMPAN PADA PEMINJAMAN BERKAS
					$peminjaman_berkas = new Peminjaman();
					$adaNonSimpan = $peminjaman_berkas->getCountByParamsBerkas(array("A.PEMINJAMAN_ID::VARCHAR"=>$PEMINJAMAN_ID)," AND NOT STATUS='SIMPAN'");
					if ($adaNonSimpan == 0) {
						$peminjaman = new Peminjaman();
						$peminjaman->setField("PEMINJAMAN_ID",$PEMINJAMAN_ID);
						$peminjaman->setField("FIELD", "STATUS");
						$peminjaman->setField("FIELD_VALUE", "SIMPAN");
						$peminjaman->setField("UPDATED_BY", $this->USER_LOGIN_ID);
						if ($peminjaman->updateByField()) {
							echo "BERHASIL|Data berhasil disimpan|".$STATUS;
						} 
						else{
							echo "GAGAL|Data gagal disimpan" ;
						}
					}
					else{
						echo "BERHASIL|Data berhasil disimpan|".$STATUS;
					}
				} 
				else{
					echo "GAGAL|Data gagal disimpan" ;
				}
			} 
			else{
				echo "GAGAL|Data gagal disimpan" ;
			}
		}
		elseif($STATUS == "DIPINJAM_ULANG"){
			if($peminjaman_berkas->updateVerifikasiPengembalianDipinjam()){
				
				$peminjaman = new Peminjaman();
				$peminjaman->setField("PEMINJAMAN_ID",$PEMINJAMAN_ID);
				$peminjaman->setField("STATUS", "DIPINJAM_ULANG");
				$peminjaman->setField("TANGGAL_PINJAM", dateToDBCheck($TANGGAL_KEMBALI));
				$peminjaman->setField("BATAS_TANGGAL_KEMBALI", dateToDBCheck($BATAS_TANGGAL_KEMBALI));
				$peminjaman->setField("UPDATED_BY", $this->USER_LOGIN_ID);
				if ($peminjaman->updateVerifikasiPeminjamanDipinjam()) {
					echo "BERHASIL|Data berhasil disimpan|".$STATUS;
				} 
				else{
					echo "GAGAL|Data gagal disimpan" ;
				}
			}
			else{
				echo "GAGAL|Data gagal disimpan";
			}
		}

		
	}

	function terima_berkas()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$peminjaman->setField("PEMINJAMAN_ID", $reqId);
		$peminjaman->setField("FIELD", "STATUS");
		$peminjaman->setField("FIELD_VALUE", "VERIFIKASI_ARSIPARIS");
		$peminjaman->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if ($peminjaman->updateByField()){
			/********LOG*********/
			$kode = "TERIMA_BERKAS";
			$keterangan = "Menerima Dokumen/Berkas Fisik (Hardcopy) yang tercantum pada Daftar Pertelaan Arsip/Dokumen dari Unit Pengolah";
			$this->log($reqId, $kode, $keterangan);
			/********END LOG*********/

			echo "Data berhasil disimpan. Silahkan melakukan verifikasi pada Daftar Pertelaan Arsip/Dokumen!";
		}
		else{
			echo "Data gagal disimpan" ;
		}
	}


	function verifikasi()
	{
		$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->post('_crfs_7mj4R5iP')) && $this->IS_MOBILE == false){
			exit();
		}

		$this->load->model("Peminjaman");
		$pemindahan = new Peminjaman();

		$id 						= $this->input->post("id");
		$URUT 						= $this->input->post("URUT");
		$PERUSAHAAN_ID				= $this->input->post("PERUSAHAAN_ID");
		$CABANG_ID					= $this->input->post("CABANG_ID");
		$SATUAN_KERJA_ID			= $this->input->post("SATUAN_KERJA_ID");
		$PEMINDAHAN_BERKAS_ID		= $this->input->post("PEMINDAHAN_BERKAS_ID");
		$KLASIFIKASI_ID				= $this->input->post("KLASIFIKASI_ID");
		$NOMOR_DOKUMEN				= $this->input->post("NOMOR_DOKUMEN");
		$NOMOR_ALTERNATIF			= $this->input->post("NOMOR_ALTERNATIF");
		$KETERANGAN_BERKAS			= setQuote($_POST['KETERANGAN_BERKAS']);
		$KURUN_WAKTU				= $this->input->post("KURUN_WAKTU");
		$TINGKAT_PERKEMBANGAN_ID	= $this->input->post("TINGKAT_PERKEMBANGAN_ID");
		$KONDISI_FISIK_ID			= $this->input->post("KONDISI_FISIK_ID");
		$JUMLAH_BERKAS				= $this->input->post("JUMLAH_BERKAS");
		$LOKASI_SIMPAN_RUANG		= $this->input->post("LOKASI_SIMPAN_RUANG");
		$LOKASI_SIMPAN_LEMARI		= $this->input->post("LOKASI_SIMPAN_LEMARI");
		$LOKASI_SIMPAN_NOMOR_BOKS	= $this->input->post("LOKASI_SIMPAN_NOMOR_BOKS");
		$LOKASI_SIMPAN_NOMOR_FOLDER	= $this->input->post("LOKASI_SIMPAN_NOMOR_FOLDER");
		$STATUS_BERKAS				= $this->input->post("STATUS_BERKAS");
		$REVISI_BERKAS				= setQuote($_POST['REVISI_BERKAS']);

		/******VALIDASI*******/
		for($i=0;$i<count(array_filter($PEMINDAHAN_BERKAS_ID));$i++) {
			if($STATUS_BERKAS[$i] == ""){
				echo "GAGAL|Terdapat Status Verifikasi Daftar Pertelaan Arsip/Dokumen belum ditentukan!";
				return;
			}
			elseif($STATUS_BERKAS[$i] == "REVISI" && trim($REVISI_BERKAS[$i]) == ""){
				echo "GAGAL|Terdapat Status Verifikasi Daftar Pertelaan Arsip/Dokumen belum diisi Catatan Revisi!";
				return;
			}
		}
		/******END VALIDASI*******/

		$jumlahRevisi = 0;
		for($i=0;$i<count($PEMINDAHAN_BERKAS_ID);$i++) {
			$pemindahan_berkas = new Peminjaman();
			$pemindahan_berkas->setField("PEMINJAMAN_ID", $id);
			$pemindahan_berkas->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID);
			$pemindahan_berkas->setField("CABANG_ID", $CABANG_ID);
			$pemindahan_berkas->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID);
			$pemindahan_berkas->setField("PEMINDAHAN_BERKAS_ID", $PEMINDAHAN_BERKAS_ID[$i]);
			$pemindahan_berkas->setField("KLASIFIKASI_ID", $KLASIFIKASI_ID[$i]);
			$pemindahan_berkas->setField("KETERANGAN", $KETERANGAN_BERKAS[$i]);
			$pemindahan_berkas->setField("KURUN_WAKTU", $KURUN_WAKTU[$i]);
			$pemindahan_berkas->setField("TINGKAT_PERKEMBANGAN_ID", $TINGKAT_PERKEMBANGAN_ID[$i]);
			$pemindahan_berkas->setField("NOMOR_DOKUMEN", $NOMOR_DOKUMEN[$i]);
			$pemindahan_berkas->setField("NOMOR_ALTERNATIF", $NOMOR_ALTERNATIF[$i]);
			$pemindahan_berkas->setField("KONDISI_FISIK_ID", $KONDISI_FISIK_ID[$i]);
			$pemindahan_berkas->setField("JUMLAH_BERKAS", $JUMLAH_BERKAS[$i]);
			$pemindahan_berkas->setField("LOKASI_SIMPAN_RUANG", $LOKASI_SIMPAN_RUANG[$i]);
			$pemindahan_berkas->setField("LOKASI_SIMPAN_LEMARI", $LOKASI_SIMPAN_LEMARI[$i]);
			$pemindahan_berkas->setField("LOKASI_SIMPAN_NOMOR_BOKS", $LOKASI_SIMPAN_NOMOR_BOKS[$i]);
			$pemindahan_berkas->setField("LOKASI_SIMPAN_NOMOR_FOLDER", $LOKASI_SIMPAN_NOMOR_FOLDER[$i]);
			$pemindahan_berkas->setField("STATUS", $STATUS_BERKAS[$i]);
			$pemindahan_berkas->setField("REVISI", $REVISI_BERKAS[$i]);
			$pemindahan_berkas->setField("SUMBER", $this->USER_GROUP);
			$pemindahan_berkas->setField("CREATED_BY", $this->USER_LOGIN_ID);
			$pemindahan_berkas->setField("UPDATED_BY", $this->USER_LOGIN_ID);
			
			if($PEMINDAHAN_BERKAS_ID[$i] == ""){
				$pemindahan_berkas->insertBerkasVerifikasi();
			}
			else{
				$pemindahan_berkas->updateBerkasVerifikasi();
			}

			if($STATUS_BERKAS[$i] == "REVISI"){
				$jumlahRevisi += 1;
			}
		}

		if($jumlahRevisi == 0){
        	$keterangan = "Permohonan Pemindahan telah diverifikasi oleh Arsiparis dan valid seluruhnya.";
			
			/********LOG*********/
			$kode = "VERIFIKASI_ARSIPARIS";
			$this->log($id, $kode, $keterangan);
			/********END LOG*********/
		}
		else{
			/********LOG*********/
			$kode = "REVISI_ARSIPARIS";
        	$keterangan = "Terdapat revisi Daftar Pertelaan Arsip/Dokumen";
			$this->log($id, $kode, $keterangan);
			/********END LOG*********/

			$this->db->query("update pemindahan set status='REVISI_ARSIPARIS', revisi='$keterangan', 
        		updated_by='$this->USER_LOGIN_ID', updated_date=current_timestamp where PEMINJAMAN_ID='$id' ");

			/***** ambil detil permohonan ******/
			$pemindahan = new Peminjaman();
			$pemindahan->selectByParamsMonitoring(array("A.PEMINJAMAN_ID::varchar"=>$id));
			$pemindahan->firstRow();
			$PERUSAHAAN_ID = $pemindahan->getField("PERUSAHAAN_ID");
			$NOMOR = $pemindahan->getField("NOMOR");
			$PERIHAL = $pemindahan->getField("PERIHAL");
			$TANGGAL = $pemindahan->getField("TANGGAL");
			$CREATED_BY = $pemindahan->getField("CREATED_BY");

			/***** ambil data pegawai ******/
			$this->load->model("UserLogin");
			$user_login = new UserLogin();
			$user_login->selectByParamsMonitoring(array("A.USER_LOGIN_ID::varchar"=>$CREATED_BY));
			$user_login->firstRow();
			$PEGAWAI_ID = $user_login->getField("PEGAWAI_ID");
			$PEGAWAI_NAMA = $user_login->getField("NAMA_PEGAWAI");
			$PEGAWAI_JABATAN = $user_login->getField("JABATAN");
			$PEGAWAI_EMAIL = $user_login->getField("EMAIL");

			/********Notifikasi Lonceng*********/
			$this->load->library("NotifikasiLonceng");
			$notifikasi_lonceng = new NotifikasiLonceng();
			$NOTIFIKASI_KODE = "PEMINDAHAN_REVISI_ARSIPARIS";
			$NOTIFIKASI_PRIMARY_ID = $id;
			$NOTIFIKASI_NAMA = "Revisi Daftar Pertelaan Arsip/Dokumen Permohonan Pemindahan";
			$NOTIFIKASI_KETERANGAN = $PERIHAL." Nomor : ".$NOMOR." Tanggal : ".getFormattedDateView($TANGGAL);
			$NOTIFIKASI_LINK = "app/loadUrl/app/permohonan_pemindahan_add/?id=".$id;
			$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
			$NOTIFIKASI_USER_GROUP = "PEGAWAI";
			$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
			$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
			$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

			$notifikasi_lonceng->insertNotifikasi($NOTIFIKASI_KODE, $NOTIFIKASI_PRIMARY_ID, $NOTIFIKASI_NAMA, 
				$NOTIFIKASI_KETERANGAN, $NOTIFIKASI_LINK, $NOTIFIKASI_PENERIMA, $NOTIFIKASI_USER_GROUP, 
				$NOTIFIKASI_PENGIRIM, $NOTIFIKASI_PENGIRIM_NAMA, $NOTIFIKASI_PENGIRIM_JABATAN);
			/********END Notifikasi Lonceng*********/

			/********Notifikasi Email*********/
			$this->load->library("KMail");
			$mail = new KMail();
			$SUBJECT = "Revisi Daftar Pertelaan Arsip/Dokumen Permohonan Pemindahan dan Penyimpanan Arsip Inaktif";
			$mail->Subject = $SUBJECT;
			$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

			$KONTEN = "email/revisi_berkas_permohonan_pemindahan";
			$arrData = array("reqParse1" => $id);
			$body = $this->load->view($KONTEN, $arrData, true);
			$mail->MsgHTML($body);
			if($mail->Send()){
				$STATUS_EMAIL = "TERKIRIM";
			}
			else{
				$STATUS_EMAIL = "TIDAK_TERKIRIM";
			}

			$this->load->model("LogEmail");
			$log_email = new LogEmail();
			$log_email->setField("KODE", $NOTIFIKASI_KODE); 
			$log_email->setField("PRIMARY_ID", $id); 
			$log_email->setField("JUDUL", $SUBJECT); 
			$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID); 
			$log_email->setField("EMAIL", $PEGAWAI_EMAIL); 
			$log_email->setField("KONTEN", $KONTEN); 
			$log_email->setField("STATUS", $STATUS_EMAIL);
			$log_email->insert();
			/********END Notifikasi Email*********/
		}


		echo "BERHASIL|Permohonan Pemindahan berhasil diverikasi|".$id;
	}

	function lokasi_simpan()
	{
		$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->post('_crfs_7mj4R5iP')) && $this->IS_MOBILE == false){
			exit();
		}

		$this->load->model("Peminjaman");
		$pemindahan_berkas = new Peminjaman();

		$id 							= $this->input->post("id");
		$PEMINJAMAN_ID 					= $this->input->post("PEMINJAMAN_ID");
		$KURUN_WAKTU 					= $this->input->post("KURUN_WAKTU");
		$RETENSI_AKTIF					= $this->input->post("RETENSI_AKTIF");
		$RETENSI_INAKTIF				= $this->input->post("RETENSI_INAKTIF");
		$TAHUN_PINDAH					= $this->input->post("TAHUN_PINDAH");
		$TAHUN_MUSNAH					= $this->input->post("TAHUN_MUSNAH");
		$KLASIFIKASI_ID					= $this->input->post("KLASIFIKASI_ID");
		$KLASIFIKASI_KODE				= $this->input->post("KLASIFIKASI_KODE");
		$KLASIFIKASI_NAMA				= $this->input->post("KLASIFIKASI_NAMA");
		$NOMOR_DOKUMEN					= $this->input->post("NOMOR_DOKUMEN");
		$NOMOR_ALTERNATIF				= $this->input->post("NOMOR_ALTERNATIF");
		$TINGKAT_PERKEMBANGAN_ID		= $this->input->post("TINGKAT_PERKEMBANGAN_ID");
		$TINGKAT_PERKEMBANGAN_KODE		= $this->input->post("TINGKAT_PERKEMBANGAN_KODE");
		$TINGKAT_PERKEMBANGAN_NAMA		= $this->input->post("TINGKAT_PERKEMBANGAN_NAMA");
		$MEDIA_SIMPAN_ID				= $this->input->post("MEDIA_SIMPAN_ID");
		$MEDIA_SIMPAN_KODE				= $this->input->post("MEDIA_SIMPAN_KODE");
		$MEDIA_SIMPAN_NAMA				= $this->input->post("MEDIA_SIMPAN_NAMA");
		$KONDISI_FISIK_ID				= $this->input->post("KONDISI_FISIK_ID");
		$KONDISI_FISIK_KODE				= $this->input->post("KONDISI_FISIK_KODE");
		$KONDISI_FISIK_NAMA				= $this->input->post("KONDISI_FISIK_NAMA");
		$JUMLAH_BERKAS					= $this->input->post("JUMLAH_BERKAS");
		$LOKASI_SIMPAN_RUANG			= $this->input->post("LOKASI_SIMPAN_RUANG");
		$LOKASI_SIMPAN_LEMARI			= $this->input->post("LOKASI_SIMPAN_LEMARI");
		$LOKASI_SIMPAN_NOMOR_BOKS		= $this->input->post("LOKASI_SIMPAN_NOMOR_BOKS");
		$LOKASI_SIMPAN_NOMOR_FOLDER		= $this->input->post("LOKASI_SIMPAN_NOMOR_FOLDER");
		$KETERANGAN						= setQuote($_POST['KETERANGAN']);

		/******UPLOAD DOKUMEN******/
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$FILE_DIR = "uploads/pemindahan/";

		$DOKUMEN 		= $_FILES['DOKUMEN'];
		$TEMP_DOKUMEN 	= $this->input->post('TEMP_DOKUMEN');
		$renameFile 	= "ARSIP_PID_".$PEMINJAMAN_ID."_ID_".$id."_".md5(date("Ymdhis").rand()).".".getExtension($DOKUMEN['name']);
		if($file->uploadToDir('DOKUMEN', $FILE_DIR, $renameFile)){
			$insertLinkSize = $file->uploadedSize;
			$insertLinkTipe = $file->uploadedExtension;
			$insertLinkFile = $renameFile;
			$insertNamaFile = setQuote($DOKUMEN['name']);
		}
		else{
			if($TEMP_DOKUMEN == ""){
				echo "GAGAL|Dokumen gagal diupload";
				return;
			}
			else{
				$insertLinkSize = "0";
				$insertLinkTipe = "pdf";
				$insertLinkFile = $TEMP_DOKUMEN;
				$insertNamaFile = $TEMP_DOKUMEN;
			}
		}
		/******END UPLOAD DOKUMEN******/

		$pemindahan_berkas->setField("PEMINDAHAN_BERKAS_ID", $id);
		$pemindahan_berkas->setField("KURUN_WAKTU", $KURUN_WAKTU);
		$pemindahan_berkas->setField("RETENSI_AKTIF", $RETENSI_AKTIF);
		$pemindahan_berkas->setField("RETENSI_INAKTIF", $RETENSI_INAKTIF);
		$pemindahan_berkas->setField("TAHUN_PINDAH", $TAHUN_PINDAH);
		$pemindahan_berkas->setField("TAHUN_MUSNAH", $TAHUN_MUSNAH);
		$pemindahan_berkas->setField("KLASIFIKASI_ID", $KLASIFIKASI_ID);
		$pemindahan_berkas->setField("KLASIFIKASI_KODE", $KLASIFIKASI_KODE);
		$pemindahan_berkas->setField("KLASIFIKASI_NAMA", $KLASIFIKASI_NAMA);
		$pemindahan_berkas->setField("NOMOR_DOKUMEN", $NOMOR_DOKUMEN);
		$pemindahan_berkas->setField("NOMOR_ALTERNATIF", $NOMOR_ALTERNATIF);
		$pemindahan_berkas->setField("TINGKAT_PERKEMBANGAN_ID", $TINGKAT_PERKEMBANGAN_ID);
		$pemindahan_berkas->setField("TINGKAT_PERKEMBANGAN_KODE", $TINGKAT_PERKEMBANGAN_KODE);
		$pemindahan_berkas->setField("TINGKAT_PERKEMBANGAN_NAMA", $TINGKAT_PERKEMBANGAN_NAMA);
		$pemindahan_berkas->setField("MEDIA_SIMPAN_ID", $MEDIA_SIMPAN_ID);
		$pemindahan_berkas->setField("MEDIA_SIMPAN_KODE", $MEDIA_SIMPAN_KODE);
		$pemindahan_berkas->setField("MEDIA_SIMPAN_NAMA", $MEDIA_SIMPAN_NAMA);
		$pemindahan_berkas->setField("KONDISI_FISIK_ID", $KONDISI_FISIK_ID);
		$pemindahan_berkas->setField("KONDISI_FISIK_KODE", $KONDISI_FISIK_KODE);
		$pemindahan_berkas->setField("KONDISI_FISIK_NAMA", $KONDISI_FISIK_NAMA);
		$pemindahan_berkas->setField("JUMLAH_BERKAS", $JUMLAH_BERKAS);
		$pemindahan_berkas->setField("LOKASI_SIMPAN_RUANG", $LOKASI_SIMPAN_RUANG);
		$pemindahan_berkas->setField("LOKASI_SIMPAN_LEMARI", $LOKASI_SIMPAN_LEMARI);
		$pemindahan_berkas->setField("LOKASI_SIMPAN_NOMOR_BOKS", $LOKASI_SIMPAN_NOMOR_BOKS);
		$pemindahan_berkas->setField("LOKASI_SIMPAN_NOMOR_FOLDER", $LOKASI_SIMPAN_NOMOR_FOLDER);
		$pemindahan_berkas->setField("KETERANGAN", $KETERANGAN);
		$pemindahan_berkas->setField("DOKUMEN", $insertLinkFile);
		$pemindahan_berkas->setField("STATUS", "SIMPAN");
		$pemindahan_berkas->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if($pemindahan_berkas->updateBerkasSimpan()){

			$STATUS_PEMINDAHAN = "VERIFIKASI_ARSIPARIS";

			/********LOG*********/
			$kode = "SIMPAN";
			$keterangan = "Berhasil menyimpan berkas ".$KLASIFIKASI_KODE." - ".$KLASIFIKASI_NAMA;
			$this->log($PEMINJAMAN_ID, $kode, $keterangan);
			/********END LOG*********/

			/************APABILA SUDAH SIMPAN SEMUA, UPDATE STATUS PEMINDAHAN************/
			$jumlahBelumSimpan = $pemindahan_berkas->getCountByParamsBerkas(array("A.PEMINJAMAN_ID::VARCHAR"=>$PEMINJAMAN_ID),
				" AND NOT STATUS='SIMPAN'");
			if($jumlahBelumSimpan == 0){
				/***** ambil detil permohonan ******/
				$pemindahan = new Peminjaman();
				$pemindahan->selectByParamsMonitoring(array("A.PEMINJAMAN_ID::varchar"=>$PEMINJAMAN_ID));
				$pemindahan->firstRow();
				$PERUSAHAAN_ID = $pemindahan->getField("PERUSAHAAN_ID");
				$NOMOR = $pemindahan->getField("NOMOR");
				$PERIHAL = $pemindahan->getField("PERIHAL");
				$TANGGAL = $pemindahan->getField("TANGGAL");
				$CREATED_BY = $pemindahan->getField("CREATED_BY");
				$STATUS_PEMINDAHAN = $pemindahan->getField("STATUS");

				if($STATUS_PEMINDAHAN != "SIMPAN"){ //JIKA BELUM SIMPAN, KIRIMI EMAIL
					$this->db->query("update pemindahan set status='SIMPAN', updated_by='$this->USER_LOGIN_ID', 
					updated_date=current_timestamp where PEMINJAMAN_ID='$PEMINJAMAN_ID' ");

					/********LOG*********/
					$kode = "SELESAI";
					$keterangan = "Proses Permohonan Pemindahan Arsip Inaktif telah selesai";
					$this->log($PEMINJAMAN_ID, $kode, $keterangan);
					/********END LOG*********/

					/***** ambil data pegawai ******/
					$this->load->model("UserLogin");
					$user_login = new UserLogin();
					$user_login->selectByParamsMonitoring(array("A.USER_LOGIN_ID::varchar"=>$CREATED_BY));
					$user_login->firstRow();
					$PEGAWAI_ID = $user_login->getField("PEGAWAI_ID");
					$PEGAWAI_NAMA = $user_login->getField("NAMA_PEGAWAI");
					$PEGAWAI_JABATAN = $user_login->getField("JABATAN");
					$PEGAWAI_EMAIL = $user_login->getField("EMAIL");

					/********Notifikasi Lonceng*********/
					
					$this->load->library("NotifikasiLonceng");
					$notifikasi_lonceng = new NotifikasiLonceng();
					$NOTIFIKASI_KODE = "PEMINDAHAN_SELESAI";
					$NOTIFIKASI_PRIMARY_ID = $PEMINJAMAN_ID;
					$NOTIFIKASI_NAMA = "Permohonan Pemindahan Selesai";
					$NOTIFIKASI_KETERANGAN = $PERIHAL." Nomor : ".$NOMOR." Tanggal : ".getFormattedDateView($TANGGAL);
					$NOTIFIKASI_LINK = "app/loadUrl/app/permohonan_pemindahan_add/?id=".$PEMINJAMAN_ID;
					$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
					$NOTIFIKASI_USER_GROUP = "PEGAWAI";
					$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
					$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
					$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

					$notifikasi_lonceng->insertNotifikasi($NOTIFIKASI_KODE, $NOTIFIKASI_PRIMARY_ID, $NOTIFIKASI_NAMA, 
						$NOTIFIKASI_KETERANGAN, $NOTIFIKASI_LINK, $NOTIFIKASI_PENERIMA, $NOTIFIKASI_USER_GROUP, 
						$NOTIFIKASI_PENGIRIM, $NOTIFIKASI_PENGIRIM_NAMA, $NOTIFIKASI_PENGIRIM_JABATAN);
					/********END Notifikasi Lonceng*********/

					/********Notifikasi Email*********/
					$this->load->library("KMail");
					$mail = new KMail();
					$SUBJECT = "Permohonan Pemindahan dan Penyimpanan Arsip Inaktif Selesai";
					$mail->Subject = $SUBJECT;
					$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

					$KONTEN = "email/selesai_permohonan_pemindahan";
					$arrData = array("reqParse1" => $PEMINJAMAN_ID);
					$body = $this->load->view($KONTEN, $arrData, true);
					$mail->MsgHTML($body);
					if($mail->Send()){
						$STATUS_EMAIL = "TERKIRIM";
					}
					else{
						$STATUS_EMAIL = "TIDAK_TERKIRIM";
					}

					$this->load->model("LogEmail");
					$log_email = new LogEmail();
					$log_email->setField("KODE", $NOTIFIKASI_KODE); 
					$log_email->setField("PRIMARY_ID", $PEMINJAMAN_ID); 
					$log_email->setField("JUDUL", $SUBJECT); 
					$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID); 
					$log_email->setField("EMAIL", $PEGAWAI_EMAIL); 
					$log_email->setField("KONTEN", $KONTEN); 
					$log_email->setField("STATUS", $STATUS_EMAIL);
					$log_email->insert();
					/********END Notifikasi Email*********/
				}
				
				$STATUS_PEMINDAHAN = "SIMPAN";
			}
			/************END APABILA SUDAH SIMPAN SEMUA, UPDATE STATUS PEMINDAHAN************/

			echo "BERHASIL|Berhasil menyimpan data|".$STATUS_PEMINDAHAN;
		}
		else{
			echo "GAGAL|Gagal menyimpan data";
		}
	}


	function aktif()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$peminjaman->setField("PEMINJAMAN_ID", $reqId);
		$peminjaman->setField("FIELD", "STATUS");
		$peminjaman->setField("FIELD_VALUE", "AKTIF");
		$peminjaman->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if ($peminjaman->updateByField()){
			echo "Data berhasil diaktifkan";
		}
		else{
			echo "Data gagal diaktifkan" ;
		}
	}

	function non_aktif()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$peminjaman->setField("PEMINJAMAN_ID", $reqId);
		$peminjaman->setField("FIELD", "STATUS");
		$peminjaman->setField("FIELD_VALUE", "TIDAK_AKTIF");
		$peminjaman->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if ($peminjaman->updateByField()){
			echo "Data berhasil dinonaktifkan";
		}
		else{
			echo "Data gagal dinonaktifkan" ;
		}
	}


	function posting()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$peminjaman->setField("PEMINJAMAN_ID", $reqId);
		$peminjaman->setField("FIELD", "STATUS");
		$peminjaman->setField("FIELD_VALUE", "POSTING");
		$peminjaman->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if ($peminjaman->updateByField())
		{
			//UPDATE PERMOHONAN_UPROVAL, SET KE BELUM_APPROVE (KEMUNGKINAN POSTING ULANG KARENA REVISI)
			$peminjaman_approval = new Peminjaman();
			$peminjaman_approval->setField("PEMINJAMAN_ID", $reqId);
			$peminjaman_approval->setField("FIELD", "STATUS");
			$peminjaman_approval->setField("FIELD_VALUE", "BELUM_APPROVE");
			$peminjaman_approval->setField("UPDATED_BY", $this->USER_LOGIN_ID);
			if ($peminjaman_approval->updateByFieldApprovalByPermohonanId())
			{
				/***** ambil detil permohonan ******/
				$peminjaman = new Peminjaman();
				$peminjaman->selectByParamsMonitoring(array("A.PEMINJAMAN_ID::varchar"=>$reqId));
				$peminjaman->firstRow();
				$PERUSAHAAN_ID = $peminjaman->getField("PERUSAHAAN_ID");
				$NOMOR = $peminjaman->getField("NOMOR");
				$PERIHAL = $peminjaman->getField("PERIHAL");
				$TANGGAL = $peminjaman->getField("TANGGAL");

				/***** ambil approval urut ke-1 ******/
				$peminjaman_approval = new Peminjaman();
				$peminjaman_approval->selectByParamsApprovalEmail(array("A.PEMINJAMAN_ID::varchar"=>$reqId,"A.URUT"=>"1"));
				$peminjaman_approval->firstRow();
				$PEMINJAMAN_APPROVAL_ID = $peminjaman_approval->getField("PEMINJAMAN_APPROVAL_ID");
				$PEGAWAI_ID = $peminjaman_approval->getField("PEGAWAI_ID");
				$PEGAWAI_NAMA = $peminjaman_approval->getField("NAMA");
				$PEGAWAI_JABATAN = $peminjaman_approval->getField("JABATAN");
				$PEGAWAI_EMAIL = $peminjaman_approval->getField("EMAIL");

				/********LOG*********/
				$kode = "POSTING";
				$keterangan = "Memposting Permohonan Peminjaman Arsip Inaktif kepada Bapak/Ibu ".$PEGAWAI_NAMA." (".$PEGAWAI_JABATAN.")";
				$this->log($reqId, $kode, $keterangan);
				/********END LOG*********/

				/********Notifikasi Lonceng*********/
				$this->load->library("NotifikasiLonceng");
				$notifikasi_lonceng = new NotifikasiLonceng();
				$NOTIFIKASI_KODE = "PEMINJAMAN_POSTING";
				$NOTIFIKASI_PRIMARY_ID = $reqId;
				$NOTIFIKASI_NAMA = "Permohonan Approval";
				$NOTIFIKASI_KETERANGAN = $PERIHAL." Nomor : ".$NOMOR." Tanggal : ".getFormattedDateView($TANGGAL);
				$NOTIFIKASI_LINK = "app/loadUrl/app/approval_permohonan_peminjaman_detil/?id=".$PEMINJAMAN_APPROVAL_ID;
				$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
				$NOTIFIKASI_USER_GROUP = "PEGAWAI";
				$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
				$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
				$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

				$notifikasi_lonceng->insertNotifikasi($NOTIFIKASI_KODE, $NOTIFIKASI_PRIMARY_ID, $NOTIFIKASI_NAMA, 
					$NOTIFIKASI_KETERANGAN, $NOTIFIKASI_LINK, $NOTIFIKASI_PENERIMA, $NOTIFIKASI_USER_GROUP, 
					$NOTIFIKASI_PENGIRIM, $NOTIFIKASI_PENGIRIM_NAMA, $NOTIFIKASI_PENGIRIM_JABATAN);
				/********END Notifikasi Lonceng*********/

				/********Notifikasi Email*********/
				$this->load->library("KMail");
				$mail = new KMail();
				$SUBJECT = "Approval/Persetujuan Permohonan Peminjaman Arsip Inaktif";
				$mail->Subject = $SUBJECT;
				$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

				$KONTEN = "email/approval_permohonan_peminjaman";
				$arrData = array("reqParse1" => $PEMINJAMAN_APPROVAL_ID);
				$body = $this->load->view($KONTEN, $arrData, true);
				$mail->MsgHTML($body);
				if($mail->Send()){
					$STATUS_EMAIL = "TERKIRIM";
				}
				else{
					$STATUS_EMAIL = "TIDAK_TERKIRIM";
				}

				$this->load->model("LogEmail");
				$log_email = new LogEmail();
				$log_email->setField("KODE", $NOTIFIKASI_KODE); 
				$log_email->setField("PRIMARY_ID", $reqId); 
				$log_email->setField("JUDUL", $SUBJECT); 
				$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID); 
				$log_email->setField("EMAIL", $PEGAWAI_EMAIL); 
				$log_email->setField("KONTEN", $KONTEN); 
				$log_email->setField("STATUS", $STATUS_EMAIL);
				$log_email->insert();
				/********END Notifikasi Email*********/

				echo "Berhasil Memposting Permohonan Peminjaman Arsip Inaktif";
			}
			else{
				echo "Gagal Memposting Permohonan Peminjaman Arsip Inaktif";
			}
		}
		else{
			echo "Gagal Memposting Permohonan Peminjaman Arsip Inaktif";
		}
	}

	function kembalikan()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$peminjaman->setField("PEMINJAMAN_ID", $reqId);
		$peminjaman->setField("FIELD", "STATUS");
		$peminjaman->setField("FIELD_VALUE", "DIKEMBALIKAN");
		$peminjaman->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if ($peminjaman->updateByField())
		{
			//UPDATE PERMOHONAN_UPROVAL, SET KE BELUM_APPROVE (KEMUNGKINAN POSTING ULANG KARENA REVISI)
			$peminjaman_berkas = new Peminjaman();
			$peminjaman_berkas->setField("PEMINJAMAN_ID", $reqId);
			$peminjaman_berkas->setField("UPDATED_BY", $this->USER_LOGIN_ID);
			if ($peminjaman_berkas->updatePermohonanBerkasDikembalikan())
			{
				/***** ambil detil permohonan ******/
				$peminjaman = new Peminjaman();
				$peminjaman->selectByParamsMonitoring(array("A.PEMINJAMAN_ID::varchar"=>$reqId));
				$peminjaman->firstRow();
				$PERUSAHAAN_ID = $peminjaman->getField("PERUSAHAAN_ID");
				$NOMOR = $peminjaman->getField("NOMOR");
				$PERIHAL = $peminjaman->getField("PERIHAL");
				$TANGGAL = $peminjaman->getField("TANGGAL");
				$PEGAWAI_ID = $peminjaman->getField("PEGAWAI_ID_ARSIPARIS");
				$PEGAWAI_NAMA = $peminjaman->getField("NAMA_ARSIPARIS");
				$PEGAWAI_JABATAN = $peminjaman->getField("JABATAN_ARSIPARIS");
				$PEGAWAI_EMAIL = $peminjaman->getField("EMAIL");

				/********LOG*********/
				$kode = "DIKEMBALIKAN";
				$keterangan = "Mengembalikan Peminjaman Arsip Inaktif kepada Unit Kearsipan";
				$this->log($reqId, $kode, $keterangan);
				/********END LOG*********/

				/********Notifikasi Lonceng*********/
				$this->load->library("NotifikasiLonceng");
				$notifikasi_lonceng = new NotifikasiLonceng();
				$NOTIFIKASI_KODE = "PEMINJAMAN_DIKEMBALIKAN";
				$NOTIFIKASI_PRIMARY_ID = $reqId;
				$NOTIFIKASI_NAMA = "Pengembalian Pinjaman Berkas/Dokumen";
				$NOTIFIKASI_KETERANGAN = $PERIHAL." Nomor : ".$NOMOR." Tanggal : ".getFormattedDateView($TANGGAL);
				$NOTIFIKASI_LINK = "app/loadUrl/app/verifikasi_permohonan_peminjaman_add/?id=".$reqId;
				$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
				$NOTIFIKASI_USER_GROUP = "PEGAWAI";
				$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
				$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
				$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

				$notifikasi_lonceng->insertNotifikasi($NOTIFIKASI_KODE, $NOTIFIKASI_PRIMARY_ID, $NOTIFIKASI_NAMA, 
					$NOTIFIKASI_KETERANGAN, $NOTIFIKASI_LINK, $NOTIFIKASI_PENERIMA, $NOTIFIKASI_USER_GROUP, 
					$NOTIFIKASI_PENGIRIM, $NOTIFIKASI_PENGIRIM_NAMA, $NOTIFIKASI_PENGIRIM_JABATAN);
				/********END Notifikasi Lonceng*********/

				/********Notifikasi Email*********/
				$this->load->library("KMail");
				$mail = new KMail();
				$SUBJECT = "Pengembalian Peminjaman Arsip Inaktif";
				$mail->Subject = $SUBJECT;
				$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

				$KONTEN = "email/pengembalian_pinjaman";
				$arrData = array("reqParse1" => $reqId);
				$body = $this->load->view($KONTEN, $arrData, true);
				$mail->MsgHTML($body);
				if($mail->Send()){
					$STATUS_EMAIL = "TERKIRIM";
				}
				else{
					$STATUS_EMAIL = "TIDAK_TERKIRIM";
				}

				$this->load->model("LogEmail");
				$log_email = new LogEmail();
				$log_email->setField("KODE", $NOTIFIKASI_KODE); 
				$log_email->setField("PRIMARY_ID", $reqId); 
				$log_email->setField("JUDUL", $SUBJECT); 
				$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID); 
				$log_email->setField("EMAIL", $PEGAWAI_EMAIL); 
				$log_email->setField("KONTEN", $KONTEN); 
				$log_email->setField("STATUS", $STATUS_EMAIL);
				$log_email->insert();
				/********END Notifikasi Email*********/

				echo "Berhasil Mengembalikan Peminjaman Arsip Inaktif";
			}
			else{
				echo "Gagal Mengembalikan Peminjaman Arsip Inaktif";
			}
		}
		else{
			echo "Gagal Mengembalikan Peminjaman Arsip Inaktif";
		}
	}

	function approval()
	{	
		$this->load->model("Peminjaman");

		$id = $this->input->get("id");
		$PEMINJAMAN_ID = $this->input->get("PEMINJAMAN_ID");
		$URUT = $this->input->get("URUT");

		//UPDATE PERMOHONAN_UPROVAL
		$peminjaman_approval = new Peminjaman();
		$peminjaman_approval->setField("PEMINJAMAN_APPROVAL_ID", $id);
		$peminjaman_approval->setField("STATUS", "APPROVE");
		$peminjaman_approval->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if($peminjaman_approval->setujuiPermohonanApproval()){
			//CEK APAKAH MASIH ADA YG BELUM APPROVE
			$peminjaman_approval = new Peminjaman();
			$adaBelumApprove = $peminjaman_approval->getCountByParamsApproval(array("PEMINJAMAN_ID"=>$PEMINJAMAN_ID,"STATUS"=>"BELUM_APPROVE"));

			if($adaBelumApprove > 0){ //JIKA ADA YG BELUM APPROVE, MAKA POSTING KE APPROVAL SELANJUTNYA
				/***** ambil detil permohonan ******/
				$peminjaman = new Peminjaman();
				$peminjaman->selectByParamsMonitoring(array("A.PEMINJAMAN_ID::varchar"=>$PEMINJAMAN_ID));
				$peminjaman->firstRow();
				$PERUSAHAAN_ID = $peminjaman->getField("PERUSAHAAN_ID");
				$NOMOR = $peminjaman->getField("NOMOR");
				$PERIHAL = $peminjaman->getField("PERIHAL");
				$TANGGAL = $peminjaman->getField("TANGGAL");

				/***** ambil approval urut selanjutnya ******/
				$URUT += 1;
				$peminjaman_approval = new Peminjaman();
				$peminjaman_approval->selectByParamsApprovalEmail(array("A.PEMINJAMAN_ID::varchar"=>$PEMINJAMAN_ID,"A.URUT"=>$URUT));
				$peminjaman_approval->firstRow();
				$PEMINJAMAN_APPROVAL_ID = $peminjaman_approval->getField("PEMINJAMAN_APPROVAL_ID");
				$PEGAWAI_ID = $peminjaman_approval->getField("PEGAWAI_ID");
				$PEGAWAI_NAMA = $peminjaman_approval->getField("NAMA");
				$PEGAWAI_JABATAN = $peminjaman_approval->getField("JABATAN");
				$PEGAWAI_EMAIL = $peminjaman_approval->getField("EMAIL");

				/********LOG*********/
				$kode = "APPROVE";
				$keterangan = "Menyetujui & memposting Permohonan Peminjaman Arsip Inaktif kepada Bapak/Ibu ".$PEGAWAI_NAMA." (".$PEGAWAI_JABATAN.")";
				$this->log($PEMINJAMAN_ID, $kode, $keterangan);
				/********END LOG*********/

				/********Notifikasi Lonceng*********/
				$this->load->library("NotifikasiLonceng");
				$notifikasi_lonceng = new NotifikasiLonceng();
				$NOTIFIKASI_KODE = "PEMINJAMAN_POSTING";
				$NOTIFIKASI_PRIMARY_ID = $PEMINJAMAN_ID;
				$NOTIFIKASI_NAMA = "Permohonan Approval";
				$NOTIFIKASI_KETERANGAN = $PERIHAL." Nomor : ".$NOMOR." Tanggal : ".getFormattedDateView($TANGGAL);
				$NOTIFIKASI_LINK = "app/loadUrl/app/approval_permohonan_peminjaman_detil/?id=".$PEMINJAMAN_APPROVAL_ID;
				$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
				$NOTIFIKASI_USER_GROUP = "PEGAWAI";
				$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
				$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
				$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

				$notifikasi_lonceng->insertNotifikasi($NOTIFIKASI_KODE, $NOTIFIKASI_PRIMARY_ID, $NOTIFIKASI_NAMA, 
					$NOTIFIKASI_KETERANGAN, $NOTIFIKASI_LINK, $NOTIFIKASI_PENERIMA, $NOTIFIKASI_USER_GROUP, 
					$NOTIFIKASI_PENGIRIM, $NOTIFIKASI_PENGIRIM_NAMA, $NOTIFIKASI_PENGIRIM_JABATAN);
				/********END Notifikasi Lonceng*********/

				/********Notifikasi Email*********/
				$this->load->library("KMail");
				$mail = new KMail();
				$SUBJECT = "Approval/Persetujuan Permohonan Peminjaman Arsip Inaktif";
				$mail->Subject = $SUBJECT;
				$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

				$KONTEN = "email/approval_permohonan_peminjaman";
				$arrData = array("reqParse1" => $PEMINJAMAN_APPROVAL_ID);
				$body = $this->load->view($KONTEN, $arrData, true);
				$mail->MsgHTML($body);
				if($mail->Send()){
					$STATUS_EMAIL = "TERKIRIM";
				}
				else{
					$STATUS_EMAIL = "TIDAK_TERKIRIM";
				}

				$this->load->model("LogEmail");
				$log_email = new LogEmail();
				$log_email->setField("KODE", $NOTIFIKASI_KODE); 
				$log_email->setField("PRIMARY_ID", $PEMINJAMAN_ID); 
				$log_email->setField("JUDUL", $SUBJECT); 
				$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID); 
				$log_email->setField("EMAIL", $PEGAWAI_EMAIL); 
				$log_email->setField("KONTEN", $KONTEN); 
				$log_email->setField("STATUS", $STATUS_EMAIL);
				$log_email->insert();
				/********END Notifikasi Email*********/

				echo "Berhasil Menyetujui & Memposting Permohonan Peminjaman Arsip Inaktif";
			}
			else{
				 //JIKA SUDAH APPROVE SEMUA, KIRIM KE TUJUAN
				$peminjaman = new Peminjaman();
				$peminjaman->setField("PEMINJAMAN_ID", $PEMINJAMAN_ID);
				$peminjaman->setField("FIELD", "STATUS");
				$peminjaman->setField("FIELD_VALUE", "TERKIRIM");
				$peminjaman->setField("UPDATED_BY", $this->USER_LOGIN_ID);
				if ($peminjaman->updateByField())
				{
					/********LOG*********/
					$kode = "APPROVE";
					$keterangan = "Menyetujui & mengirim Permohonan Peminjaman Arsip Inaktif kepada Unit Kearsipan";
					$this->log($PEMINJAMAN_ID, $kode, $keterangan);
					/********END LOG*********/

					/***** ambil detil permohonan ******/
					$peminjaman = new Peminjaman();
					$peminjaman->selectByParamsMonitoring(array("A.PEMINJAMAN_ID::varchar"=>$PEMINJAMAN_ID));
					$peminjaman->firstRow();
					$PERUSAHAAN_ID = $peminjaman->getField("PERUSAHAAN_ID");
					$NOMOR = $peminjaman->getField("NOMOR");
					$PERIHAL = $peminjaman->getField("PERIHAL");
					$TANGGAL = $peminjaman->getField("TANGGAL");

					/***** kirim ke tujuan/tembusan ******/
					$peminjaman_tujuan = new Peminjaman();
					$peminjaman_tujuan->selectByParamsTujuanEmail(array("A.PEMINJAMAN_ID::varchar"=>$PEMINJAMAN_ID));
					while ($peminjaman_tujuan->nextRow()) {
						$PEMINJAMAN_TUJUAN_ID = $peminjaman_tujuan->getField("PEMINJAMAN_TUJUAN_ID");
						$PEGAWAI_ID = $peminjaman_tujuan->getField("PEGAWAI_ID");
						$PEGAWAI_NAMA = $peminjaman_tujuan->getField("NAMA");
						$PEGAWAI_JABATAN = $peminjaman_tujuan->getField("JABATAN");
						$PEGAWAI_EMAIL = $peminjaman_tujuan->getField("EMAIL");

						//UPDATE PERMOHONAN_TUJUAN
						$peminjaman_tujuan_disposisi = new Peminjaman();
						$peminjaman_tujuan_disposisi->setField("PEMINJAMAN_TUJUAN_ID", $PEMINJAMAN_TUJUAN_ID);
						$peminjaman_tujuan_disposisi->setField("PERUSAHAAN_ID_DISPOSISI", $this->PERUSAHAAN_ID);
						$peminjaman_tujuan_disposisi->setField("CABANG_ID_DISPOSISI", $this->CABANG_ID);
						$peminjaman_tujuan_disposisi->setField("SATUAN_KERJA_ID_DISPOSISI", $this->SATUAN_KERJA_ID);
						$peminjaman_tujuan_disposisi->setField("PEGAWAI_ID_DISPOSISI", $this->PEGAWAI_ID);
						$peminjaman_tujuan_disposisi->setField("KODE_DISPOSISI", $this->KODE_PEGAWAI);
						$peminjaman_tujuan_disposisi->setField("NAMA_DISPOSISI", $this->NAMA_PEGAWAI);
						$peminjaman_tujuan_disposisi->setField("JABATAN_DISPOSISI", $this->JABATAN);
						$peminjaman_tujuan_disposisi->setField("TERDISPOSISI", "TIDAK");
						$peminjaman_tujuan_disposisi->setField("TERBACA", "TIDAK");
						$peminjaman_tujuan_disposisi->setField("PESAN_DISPOSISI", setQuote($PESAN_DISPOSISI));
						$peminjaman_tujuan_disposisi->setField("PEMINJAMAN_TUJUAN_ID_PARENT", coalesce($PEMINJAMAN_TUJUAN_ID_PARENT,$PEMINJAMAN_TUJUAN_ID));
						$peminjaman_tujuan_disposisi->setField("UPDATED_BY", $this->USER_LOGIN_ID);
						$peminjaman_tujuan_disposisi->updateTujuanDisposisi();

						/********Notifikasi Lonceng*********/
						$this->load->library("NotifikasiLonceng");
						$notifikasi_lonceng = new NotifikasiLonceng();
						$NOTIFIKASI_KODE = "PEMINJAMAN_TERKIRIM";
						$NOTIFIKASI_PRIMARY_ID = $PEMINJAMAN_ID;
						$NOTIFIKASI_NAMA = "Permohonan Peminjaman";
						$NOTIFIKASI_KETERANGAN = $PERIHAL." Nomor : ".$NOMOR." Tanggal : ".getFormattedDateView($TANGGAL);
						$NOTIFIKASI_LINK = "app/loadUrl/app/inbox_permohonan_peminjaman_detil/?id=".$PEMINJAMAN_TUJUAN_ID;
						$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
						$NOTIFIKASI_USER_GROUP = "PEGAWAI";
						$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
						$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
						$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

						$notifikasi_lonceng->insertNotifikasi($NOTIFIKASI_KODE, $NOTIFIKASI_PRIMARY_ID, $NOTIFIKASI_NAMA, 
							$NOTIFIKASI_KETERANGAN, $NOTIFIKASI_LINK, $NOTIFIKASI_PENERIMA, $NOTIFIKASI_USER_GROUP, 
							$NOTIFIKASI_PENGIRIM, $NOTIFIKASI_PENGIRIM_NAMA, $NOTIFIKASI_PENGIRIM_JABATAN);
						/********END Notifikasi Lonceng*********/

						/********Notifikasi Email*********/
						$this->load->library("KMail");
						$mail = new KMail();
						$SUBJECT = "Permohonan Peminjaman Arsip Inaktif";
						$mail->Subject = $SUBJECT;
						$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

						$KONTEN = "email/inbox_permohonan_peminjaman";
						$arrData = array("reqParse1" => $PEMINJAMAN_TUJUAN_ID);
						$body = $this->load->view($KONTEN, $arrData, true);
						$mail->MsgHTML($body);
						if($mail->Send()){
							$STATUS_EMAIL = "TERKIRIM";
						}
						else{
							$STATUS_EMAIL = "TIDAK_TERKIRIM";
						}

						$this->load->model("LogEmail");
						$log_email = new LogEmail();
						$log_email->setField("KODE", $NOTIFIKASI_KODE); 
						$log_email->setField("PRIMARY_ID", $PEMINJAMAN_ID); 
						$log_email->setField("JUDUL", $SUBJECT); 
						$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID); 
						$log_email->setField("EMAIL", $PEGAWAI_EMAIL); 
						$log_email->setField("KONTEN", $KONTEN); 
						$log_email->setField("STATUS", $STATUS_EMAIL);
						$log_email->insert();
						/********END Notifikasi Email*********/
					}

					echo "Berhasil Menyetujui & Mengirim Permohonan Peminjaman Arsip Inaktif kepada Unit Kearsipan";
				}
				else{
					echo "GAGAL|Gagal menyetujui permohonan";
				}
			}
		}
		else{
			echo "GAGAL|Gagal menyetujui permohonan";
		}
	}

	function revisi()
	{
		$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->post('_crfs_7mj4R5iP')) && $this->IS_MOBILE == false){
			exit();
		}
		
		$this->load->model("Peminjaman");

		$id = $this->input->post("id");
		$PEMINJAMAN_ID = $this->input->post("PEMINJAMAN_ID");
		$REVISI = setQuote($_POST['REVISI']);

		if(trim($REVISI) == ""){
			echo "GAGAL|Ketikkan Catatan Revisi terlebih dahulu!";
		}
		else{
			//UPDATE PERMOHONAN_UPROVAL
			$peminjaman_approval = new Peminjaman();
			$peminjaman_approval->setField("PEMINJAMAN_APPROVAL_ID", $id);
			$peminjaman_approval->setField("STATUS", "REVISI");
			$peminjaman_approval->setField("REVISI", $REVISI); 
			$peminjaman_approval->setField("UPDATED_BY", $this->USER_LOGIN_ID);
			if($peminjaman_approval->revisiPermohonanApproval()){

				//UPDATE PERMOHONAN
				$peminjaman = new Peminjaman();
				$peminjaman->setField("PEMINJAMAN_ID", $PEMINJAMAN_ID);
				$peminjaman->setField("STATUS", "REVISI");
				$peminjaman->setField("REVISI", $REVISI); 
				$peminjaman->setField("UPDATED_BY", $this->USER_LOGIN_ID);
				if($peminjaman->revisiPermohonan()){

					/********LOG*********/
					$kode = "REVISI";
					$keterangan = "Revisi, dengan catatan sebagai berikut : ".$REVISI;
					$this->log($PEMINJAMAN_ID, $kode, $keterangan);
					/********END LOG*********/

					/***** ambil detil permohonan ******/
					$peminjaman = new Peminjaman();
					$peminjaman->selectByParamsMonitoring(array("A.PEMINJAMAN_ID::varchar"=>$PEMINJAMAN_ID));
					$peminjaman->firstRow();
					$PERUSAHAAN_ID = $peminjaman->getField("PERUSAHAAN_ID");
					$NOMOR = $peminjaman->getField("NOMOR");
					$PERIHAL = $peminjaman->getField("PERIHAL");
					$TANGGAL = $peminjaman->getField("TANGGAL");
					$CREATED_BY = $peminjaman->getField("CREATED_BY");

					/***** ambil data pegawai ******/
					$this->load->model("UserLogin");
					$user_login = new UserLogin();
					$user_login->selectByParamsMonitoring(array("A.USER_LOGIN_ID::varchar"=>$CREATED_BY));
					$user_login->firstRow();
					$PEGAWAI_ID = $user_login->getField("PEGAWAI_ID");
					$PEGAWAI_NAMA = $user_login->getField("NAMA_PEGAWAI");
					$PEGAWAI_JABATAN = $user_login->getField("JABATAN");
					$PEGAWAI_EMAIL = $user_login->getField("EMAIL");

					/********Notifikasi Lonceng*********/
					$this->load->library("NotifikasiLonceng");
					$notifikasi_lonceng = new NotifikasiLonceng();
					$NOTIFIKASI_KODE = "PEMINJAMAN_REVISI";
					$NOTIFIKASI_PRIMARY_ID = $PEMINJAMAN_ID;
					$NOTIFIKASI_NAMA = "Revisi Permohonan Peminjaman";
					$NOTIFIKASI_KETERANGAN = $PERIHAL." Nomor : ".$NOMOR." Tanggal : ".getFormattedDateView($TANGGAL);
					$NOTIFIKASI_LINK = "app/loadUrl/app/permohonan_peminjaman_add/?id=".$PEMINJAMAN_ID;
					$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
					$NOTIFIKASI_USER_GROUP = "PEGAWAI";
					$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
					$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
					$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

					$notifikasi_lonceng->insertNotifikasi($NOTIFIKASI_KODE, $NOTIFIKASI_PRIMARY_ID, $NOTIFIKASI_NAMA, 
						$NOTIFIKASI_KETERANGAN, $NOTIFIKASI_LINK, $NOTIFIKASI_PENERIMA, $NOTIFIKASI_USER_GROUP, 
						$NOTIFIKASI_PENGIRIM, $NOTIFIKASI_PENGIRIM_NAMA, $NOTIFIKASI_PENGIRIM_JABATAN);
					/********END Notifikasi Lonceng*********/

					/********Notifikasi Email*********/
					$this->load->library("KMail");
					$mail = new KMail();
					$SUBJECT = "Revisi Permohonan Peminjaman Arsip Inaktif";
					$mail->Subject = $SUBJECT;
					$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

					$KONTEN = "email/revisi_permohonan_peminjaman";
					$arrData = array("reqParse1" => $id);
					$body = $this->load->view($KONTEN, $arrData, true);
					$mail->MsgHTML($body);
					if($mail->Send()){
						$STATUS_EMAIL = "TERKIRIM";
					}
					else{
						$STATUS_EMAIL = "TIDAK_TERKIRIM";
					}

					$this->load->model("LogEmail");
					$log_email = new LogEmail();
					$log_email->setField("KODE", $NOTIFIKASI_KODE); 
					$log_email->setField("PRIMARY_ID", $PEMINJAMAN_ID); 
					$log_email->setField("JUDUL", $SUBJECT); 
					$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID); 
					$log_email->setField("EMAIL", $PEGAWAI_EMAIL); 
					$log_email->setField("KONTEN", $KONTEN); 
					$log_email->setField("STATUS", $STATUS_EMAIL);
					$log_email->insert();
					/********END Notifikasi Email*********/

					echo "BERHASIL|Berhasil merevisi permohonan";
				}
				else{
					echo "GAGAL|Gagal merevisi permohonan";
				}
			}
			else{
				echo "GAGAL|Gagal merevisi permohonan";
			}
		}
	}


	function disposisi()
	{
		$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->post('_crfs_7mj4R5iP')) && $this->IS_MOBILE == false){
			exit();
		}

		$this->load->model("Peminjaman");

		$id 	= $this->input->post("id");
		$mode 	= $this->input->post("mode");

		$PEMINJAMAN_ID 		= $this->input->post("PEMINJAMAN_ID");
		$PESAN_DISPOSISI	= setQuote($_POST['PESAN_DISPOSISI']);

		$PEMINJAMAN_TUJUAN_ID_DISPOSISI	= $this->input->post("PEMINJAMAN_TUJUAN_ID_DISPOSISI");
		$PERUSAHAAN_ID_DISPOSISI		= $this->input->post("PERUSAHAAN_ID_DISPOSISI");
		$CABANG_ID_DISPOSISI			= $this->input->post("CABANG_ID_DISPOSISI");
		$SATUAN_KERJA_ID_DISPOSISI		= $this->input->post("SATUAN_KERJA_ID_DISPOSISI");
		$JENIS_DISPOSISI				= $this->input->post("JENIS_DISPOSISI");
		$PEGAWAI_ID_DISPOSISI			= $this->input->post("PEGAWAI_ID_DISPOSISI");
		$KODE_DISPOSISI					= $this->input->post("KODE_DISPOSISI");
		$NAMA_DISPOSISI					= $this->input->post("NAMA_DISPOSISI");
		$JABATAN_DISPOSISI				= $this->input->post("JABATAN_DISPOSISI");

		/******VALIDASI*******/
		if(count(array_filter($PEGAWAI_ID_DISPOSISI)) <= 0){
			echo "GAGAL|Tujuan Disposisi kosong";
			return;
		}

		if(trim($PESAN_DISPOSISI) == ""){
			echo "GAGAL|Catatan Disposisi kosong";
			return;
		}

		for ($i=0; $i < count($PEGAWAI_ID_DISPOSISI); $i++) { 
			$peminjaman_tujuan = new Peminjaman();
			$adaDisposisi = $peminjaman_tujuan->getCountByParamsTujuan(array("A.PEMINJAMAN_ID"=>$PEMINJAMAN_ID,"A.PEGAWAI_ID"=>$PEGAWAI_ID_DISPOSISI[$i]));

			if($adaDisposisi > 0)
			{
				echo "GAGAL|Terdapat Pegawai pada kolom <b>Tujuan Disposisi</b> telah dikirimkan Permohonan Pemindahan sebelumnya. Silahkan cek pada Log Tujuan/Disposisi";
				return;
			}
		}
		/******END VALIDASI*******/

		$arrNamaDisposisi = "";
		for ($i=0; $i < count($PEGAWAI_ID_DISPOSISI); $i++) { 
			$peminjaman_tujuan = new Peminjaman();
			$peminjaman_tujuan->setField("PEMINJAMAN_TUJUAN_ID", $PEMINJAMAN_TUJUAN_ID_DISPOSISI[$i]);
			$peminjaman_tujuan->setField("PEMINJAMAN_TUJUAN_ID_PARENT", $id);
			$peminjaman_tujuan->setField("PEMINJAMAN_ID", $PEMINJAMAN_ID);
			$peminjaman_tujuan->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID_DISPOSISI[$i]);
			$peminjaman_tujuan->setField("CABANG_ID", $CABANG_ID_DISPOSISI[$i]);
			$peminjaman_tujuan->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID_DISPOSISI[$i]);
			$peminjaman_tujuan->setField("PEGAWAI_ID", $PEGAWAI_ID_DISPOSISI[$i]);
			$peminjaman_tujuan->setField("KODE", $KODE_DISPOSISI[$i]);
			$peminjaman_tujuan->setField("NAMA", setQuote($NAMA_DISPOSISI[$i]));
			$peminjaman_tujuan->setField("JABATAN", $JABATAN_DISPOSISI[$i]);
			$peminjaman_tujuan->setField("JENIS", "DISPOSISI");
			$peminjaman_tujuan->setField("TERDISPOSISI", "TIDAK");
			$peminjaman_tujuan->setField("TERBACA", "TIDAK");
			$peminjaman_tujuan->setField("PESAN_DISPOSISI", $PESAN_DISPOSISI);
			$peminjaman_tujuan->setField("PEGAWAI_ID_DISPOSISI", $this->PEGAWAI_ID);
			$peminjaman_tujuan->setField("KODE_DISPOSISI", $this->KODE_PEGAWAI);
			$peminjaman_tujuan->setField("NAMA_DISPOSISI", $this->NAMA_PEGAWAI);
			$peminjaman_tujuan->setField("JABATAN_DISPOSISI", $this->JABATAN);
			$peminjaman_tujuan->setField("PERUSAHAAN_ID_DISPOSISI", $this->PERUSAHAAN_ID);
			$peminjaman_tujuan->setField("CABANG_ID_DISPOSISI", $this->CABANG_ID);
			$peminjaman_tujuan->setField("SATUAN_KERJA_ID_DISPOSISI", $this->SATUAN_KERJA_ID);
			$peminjaman_tujuan->setField("CREATED_BY", $this->USER_LOGIN_ID);
			if($peminjaman_tujuan->insertDisposisi()){
				$PEMINJAMAN_TUJUAN_ID = $peminjaman_tujuan->id;
				/***** ambil detil permohonan ******/
				$peminjaman = new Peminjaman();
				$peminjaman->selectByParamsMonitoring(array("A.PEMINJAMAN_ID::varchar"=>$PEMINJAMAN_ID));
				$peminjaman->firstRow();
				$PERUSAHAAN_ID = $peminjaman->getField("PERUSAHAAN_ID");
				$NOMOR = $peminjaman->getField("NOMOR");
				$PERIHAL = $peminjaman->getField("PERIHAL");
				$TANGGAL = $peminjaman->getField("TANGGAL");

				/***** kirim ke tujuan/tembusan ******/
				$peminjaman_tujuan = new Peminjaman();
				$peminjaman_tujuan->selectByParamsTujuanEmail(array("A.PEMINJAMAN_TUJUAN_ID::varchar"=>$PEMINJAMAN_TUJUAN_ID));
				while ($peminjaman_tujuan->nextRow()) {
					$PEGAWAI_ID = $peminjaman_tujuan->getField("PEGAWAI_ID");
					$PEGAWAI_NAMA = $peminjaman_tujuan->getField("NAMA");
					$PEGAWAI_JABATAN = $peminjaman_tujuan->getField("JABATAN");
					$PEGAWAI_EMAIL = $peminjaman_tujuan->getField("EMAIL");

					/********Notifikasi Lonceng*********/
					$this->load->library("NotifikasiLonceng");
					$notifikasi_lonceng = new NotifikasiLonceng();
					$NOTIFIKASI_KODE = "PEMINJAMAN_DISPOSISI";
					$NOTIFIKASI_PRIMARY_ID = $PEMINJAMAN_ID;
					$NOTIFIKASI_NAMA = "Permohonan Peminjaman";
					$NOTIFIKASI_KETERANGAN = $PERIHAL." Nomor : ".$NOMOR." Tanggal : ".getFormattedDateView($TANGGAL);
					$NOTIFIKASI_LINK = "app/loadUrl/app/inbox_permohonan_peminjaman_detil/?id=".$PEMINJAMAN_TUJUAN_ID;
					$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
					$NOTIFIKASI_USER_GROUP = "PEGAWAI";
					$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
					$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
					$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

					$notifikasi_lonceng->insertNotifikasi($NOTIFIKASI_KODE, $NOTIFIKASI_PRIMARY_ID, $NOTIFIKASI_NAMA, 
						$NOTIFIKASI_KETERANGAN, $NOTIFIKASI_LINK, $NOTIFIKASI_PENERIMA, $NOTIFIKASI_USER_GROUP, 
						$NOTIFIKASI_PENGIRIM, $NOTIFIKASI_PENGIRIM_NAMA, $NOTIFIKASI_PENGIRIM_JABATAN);
					/********END Notifikasi Lonceng*********/

					/********Notifikasi Email*********/
					$this->load->library("KMail");
					$mail = new KMail();
					$SUBJECT = "Permohonan Peminjaman Arsip Inaktif";
					$mail->Subject = $SUBJECT;
					$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

					$KONTEN = "email/inbox_permohonan_peminjaman";
					$arrData = array("reqParse1" => $PEMINJAMAN_TUJUAN_ID);
					$body = $this->load->view($KONTEN, $arrData, true);
					$mail->MsgHTML($body);
					if($mail->Send()){
						$STATUS_EMAIL = "TERKIRIM";
					}
					else{
						$STATUS_EMAIL = "TIDAK_TERKIRIM";
					}

					$this->load->model("LogEmail");
					$log_email = new LogEmail();
					$log_email->setField("KODE", $NOTIFIKASI_KODE); 
					$log_email->setField("PRIMARY_ID", $PEMINJAMAN_ID); 
					$log_email->setField("JUDUL", $SUBJECT); 
					$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID); 
					$log_email->setField("EMAIL", $PEGAWAI_EMAIL); 
					$log_email->setField("KONTEN", $KONTEN); 
					$log_email->setField("STATUS", $STATUS_EMAIL);
					$log_email->insert();
					/********END Notifikasi Email*********/
				}
			}

			$arrNamaDisposisi .= setQuote($NAMA_DISPOSISI[$i]).", ";
		}

		$peminjaman_tujuan = new Peminjaman();
		$peminjaman_tujuan->setField("PEMINJAMAN_TUJUAN_ID", $id);
		$peminjaman_tujuan->setField("TERDISPOSISI", "YA");
		if($peminjaman_tujuan->updateDisposisi()){
			/********LOG*********/
			$kode = "DISPOSISI";
			$keterangan = "Mendisposisikan Permohonan Peminjaman Arsip Inaktif kepada ".$arrNamaDisposisi;
			$this->log($PEMINJAMAN_ID, $kode, $keterangan);
			/********END LOG*********/

			echo "BERHASIL|Berhasil mendisposisikan permohonan peminjaman|".$id;
		} 
		else{
			echo "GAGAL|Gagal mendisposisikan permohonan peminjaman";
		}
	}


	function log($id, $kode, $keterangan)
	{
		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$peminjaman->setField("PEMINJAMAN_ID", $id);
		$peminjaman->setField("KODE", $kode);
		$peminjaman->setField("KETERANGAN", setQuote($keterangan));
		$peminjaman->setField("PEGAWAI_ID", $this->PEGAWAI_ID);
		$peminjaman->setField("PEGAWAI_KODE", $this->KODE_PEGAWAI);
		$peminjaman->setField("PEGAWAI_NAMA", $this->NAMA_PEGAWAI);
		$peminjaman->setField("PEGAWAI_JABATAN", $this->JABATAN);
		$peminjaman->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$peminjaman->insertLog();
	}


	function combo()
	{
		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$i = 0;
		$peminjaman->selectByParams(array("A.STATUS" => "AKTIF"));
		while ($peminjaman->nextRow()) {
			$arr_json[$i]['id']		= $peminjaman->getField("PEMINJAMAN_ID");
			$arr_json[$i]['text']	= $peminjaman->getField("RUANG")."/".$peminjaman->getField("LEMARI")."/".
										$peminjaman->getField("NOMOR_BOKS")."/".$peminjaman->getField("NOMOR_FOLDER");
			$i++;
		}
		echo json_encode($arr_json);
	}


	function daftar_berkas_simpan() 
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$rows = isset($_GET['rows']) ? intval($_GET['rows']) : 50;
		$id   = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$offset = ($page-1)*$rows;
		
		$reqPencarian = trim($this->input->get("reqPencarian"));
		$reqJenisRegister = $this->input->get("reqJenisRegister");
		$reqMode = $this->input->get("reqMode");

		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$statement .= " AND A.PERUSAHAAN_ID='$this->PERUSAHAAN_ID' ";
		$statement .= " AND A.CABANG_ID='$this->CABANG_ID' ";

		if($reqPencarian != ""){
			$statement .= " AND (UPPER(A.KLASIFIKASI_KODE) LIKE '%".strtoupper($reqPencarian)."%' 
			OR UPPER(A.KLASIFIKASI_NAMA) LIKE '%".strtoupper($reqPencarian)."%'
			OR UPPER(A.KETERANGAN) LIKE '%".strtoupper($reqPencarian)."%' ";
		}

		$rowCount = $peminjaman->getCountByParamsBerkas(array("A.STATUS"=>"SIMPAN"), $statement);
		$peminjaman->selectByParamsBerkas(array("A.STATUS"=>"SIMPAN"), $rows, $offset, $statement, " ORDER BY A.KLASIFIKASI_KODE ASC ");
		// echo $peminjaman->query;exit;
		$i = 0;
		$items = array();
		while($peminjaman->nextRow())
		{
			$row['id']							= $peminjaman->getField("PEMINJAMAN_BERKAS_ID");
			$row['text']						= $peminjaman->getField("KLASIFIKASI_KODE")." - ".$peminjaman->getField("KLASIFIKASI_NAMA");
			$row['KLASIFIKASI_ID']				= $peminjaman->getField("KLASIFIKASI_ID");
			$row['KLASIFIKASI_KODE']			= $peminjaman->getField("KLASIFIKASI_KODE");
			$row['KLASIFIKASI_NAMA']			= $peminjaman->getField("KLASIFIKASI_NAMA");
			$row['KLASIFIKASI']					= $peminjaman->getField("KLASIFIKASI_KODE")." - ".$peminjaman->getField("KLASIFIKASI_NAMA");
			$row['KETERANGAN']					= $peminjaman->getField("KETERANGAN");
			$row['KURUN_WAKTU']					= $peminjaman->getField("KURUN_WAKTU");
			$row['TINGKAT_PERKEMBANGAN_ID']		= $peminjaman->getField("TINGKAT_PERKEMBANGAN_ID");
			$row['TINGKAT_PERKEMBANGAN_KODE']	= $peminjaman->getField("TINGKAT_PERKEMBANGAN_KODE");
			$row['TINGKAT_PERKEMBANGAN_NAMA']	= $peminjaman->getField("TINGKAT_PERKEMBANGAN_NAMA");

			$this->TREETABLE_COUNT++;

			array_push($items, $row);
			unset($row);
		}

		$result["rows"] = $items;
		echo json_encode($result);
	}


	function ambil_berkas()
	{
		$id	= $this->input->get('id');

		$this->load->model("Peminjaman");

		$peminjaman_berkas = new Peminjaman();
		$peminjaman_berkas->selectByParamsBerkas(array("A.PEMINJAMAN_BERKAS_ID::VARCHAR"=>$id));
		$peminjaman_berkas->firstRow();
		$result["JUMLAH_BERKAS"] = $peminjaman_berkas->getField("JUMLAH_BERKAS");
		$result["DOKUMEN"] = $peminjaman_berkas->getField("DOKUMEN");
        $result["LOKASI_SIMPAN_RUANG"] = $peminjaman_berkas->getField("LOKASI_SIMPAN_RUANG");
        $result["LOKASI_SIMPAN_LEMARI"] = $peminjaman_berkas->getField("LOKASI_SIMPAN_LEMARI");
        $result["LOKASI_SIMPAN_NOMOR_BOKS"] = $peminjaman_berkas->getField("LOKASI_SIMPAN_NOMOR_BOKS");
        $result["LOKASI_SIMPAN_NOMOR_FOLDER"] = $peminjaman_berkas->getField("LOKASI_SIMPAN_NOMOR_FOLDER");

        if($peminjaman_berkas->getField("LOKASI_SIMPAN_LEMARI") != ""){
            $result["LOKASI_SIMPAN"] = "[Rak-".$peminjaman_berkas->getField("LOKASI_SIMPAN_LEMARI")."][B.".$peminjaman_berkas->getField("LOKASI_SIMPAN_NOMOR_BOKS")."][F-".$peminjaman_berkas->getField("LOKASI_SIMPAN_NOMOR_FOLDER")."]";
        }

        echo json_encode($result);
	}

}
