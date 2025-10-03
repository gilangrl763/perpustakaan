<?php
defined('BASEPATH') or exit('No direct script access allowed');

include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");

class pemindahan_json extends CI_Controller
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
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false) {
			exit();
		}

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");
		$CABANG_ID = $this->input->get("CABANG_ID");
		$SATUAN_KERJA_ID = $this->input->get("SATUAN_KERJA_ID");
		$STATUS = $this->input->get("STATUS");

		$aColumns		= array(
			"PEMINDAHAN_ID",
			"STATUS",
			"NOMOR",
			"TANGGAL",
			"PERIHAL",
			"KETERANGAN",
			"NAMA_PERUSAHAAN",
			"NAMA_CABANG",
			"NAMA_SATUAN_KERJA",
			"STATUS_KET"
		);

		$aColumnsAlias	= array(
			"PEMINDAHAN_ID",
			"STATUS",
			"NOMOR",
			"TANGGAL",
			"PERIHAL",
			"KETERANGAN",
			"NAMA_PERUSAHAAN",
			"NAMA_CABANG",
			"NAMA_SATUAN_KERJA",
			"STATUS_KET"
		);

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
			if (trim($sOrder) == "ORDER BY PEMINDAHAN_ID asc") {
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

		if ($PERUSAHAAN_ID != 'ALL') {
			$statement .= " AND A.PERUSAHAAN_ID = '$PERUSAHAAN_ID' ";
		}

		if ($CABANG_ID != 'ALL') {
			$statement .= " AND A.CABANG_ID = '$CABANG_ID' ";
		}

		if ($SATUAN_KERJA_ID != 'ALL') {
			$statement .= " AND A.SATUAN_KERJA_ID = '$SATUAN_KERJA_ID' ";
		}

		if ($STATUS != 'ALL') {
			$statement .= " AND A.STATUS = '$STATUS' ";
		}

		if ($this->USER_GROUP == "PEGAWAI") {
			$statement .= " AND A.CREATED_BY = '$this->USER_LOGIN_ID' ";
		}


		$statement .= "AND (UPPER(A.NOMOR) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.PERIHAL) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
		)";

		$allRecord = $pemindahan->getCountByParamsMonitoring(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $pemindahan->getCountByParamsMonitoring(array(), $statement);
		}

		$pemindahan->selectByParamsMonitoring(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $pemindahan->query;exit;

		if ($this->IS_MOBILE == true) {
			$arrResult["rowCount"] = $pemindahan->rowCount;
			$arrResult["rowResult"] = $pemindahan->rowResult;
			echo json_encode($arrResult);
			return;
		}

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($pemindahan->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "STATUS_KET") {
					if ($pemindahan->getField("STATUS") == "ENTRI") {
						$badge_color = "badge-info";
					} elseif ($pemindahan->getField("STATUS") == "POSTING") {
						$badge_color = "badge-secondary";
					} elseif ($pemindahan->getField("STATUS") == "VERIFIKASI_ARSIPARIS") {
						$badge_color = "badge-success";
					} else {
						$badge_color = "badge-danger";
					}

					$row[] = "<span class='badge " . $badge_color . "'>" . str_replace("_", " ", $pemindahan->getField("STATUS")) . "</span>";
				} else {
					$row[] = $pemindahan->getField($aColumns[$i]);
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
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false) {
			exit();
		}

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$STATUS = $this->input->get("STATUS");

		$aColumns		= array(
			"PEMINDAHAN_APPROVAL_ID",
			"PEMINDAHAN_ID",
			"STATUS",
			"STATUS_PEMINDAHAN",
			"NOMOR",
			"TANGGAL",
			"PERIHAL",
			"KETERANGAN",
			"NAMA_PERUSAHAAN",
			"NAMA_CABANG",
			"NAMA_SATUAN_KERJA",
			"STATUS_KET",
			"TANGGAL_APPROVE"
		);

		$aColumnsAlias	= array(
			"PEMINDAHAN_APPROVAL_ID",
			"PEMINDAHAN_ID",
			"STATUS",
			"STATUS_PEMINDAHAN",
			"NOMOR",
			"TANGGAL",
			"PERIHAL",
			"KETERANGAN",
			"NAMA_PERUSAHAAN",
			"NAMA_CABANG",
			"NAMA_SATUAN_KERJA",
			"STATUS_KET",
			"TANGGAL_APPROVE"
		);

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
			if (trim($sOrder) == "ORDER BY PEMINDAHAN_ID asc") {
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
		$statement .= " AND B.STATUS IN ('POSTING','TERKIRIM','VERIFIKASI_ARSIPARIS','SIMPAN','REVISI','REVISI_ARSIPARIS') ";

		if ($STATUS != 'ALL') {
			$statement .= " AND A.STATUS = '$STATUS' ";
		}

		$statement .= "AND (UPPER(B.NOMOR) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(B.PERIHAL) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
		)";

		$allRecord = $pemindahan->getCountByParamsMonitoringApproval(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $pemindahan->getCountByParamsMonitoringApproval(array(), $statement);
		}

		$pemindahan->selectByParamsMonitoringApproval(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $pemindahan->query;exit;

		if ($this->IS_MOBILE == true) {
			$arrResult["rowCount"] = $pemindahan->rowCount;
			$arrResult["rowResult"] = $pemindahan->rowResult;
			echo json_encode($arrResult);
			return;
		}

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($pemindahan->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "STATUS_KET") {
					if ($pemindahan->getField("STATUS") == "ENTRI") {
						$badge_color = "badge-info";
					} elseif ($pemindahan->getField("STATUS") == "POSTING") {
						$badge_color = "badge-secondary";
					} elseif ($pemindahan->getField("STATUS") == "VERIFIKASI_ARSIPARIS") {
						$badge_color = "badge-success";
					} else {
						$badge_color = "badge-danger";
					}

					$row[] = "<span class='badge " . $badge_color . "'>" . str_replace("_", " ", $pemindahan->getField("STATUS")) . "</span>";
				} else {
					$row[] = $pemindahan->getField($aColumns[$i]);
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
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false) {
			exit();
		}

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$STATUS = $this->input->get("STATUS");
		$TANGGAL_AWAL = $this->input->get("TANGGAL_AWAL");
		$TANGGAL_AKHIR = $this->input->get("TANGGAL_AKHIR");

		$aColumns		= array("PEMINDAHAN_TUJUAN_ID", "PEMINDAHAN_ID", "TERBACA", "KODE", "NOMOR", "TERDISPOSISI", "TANGGAL_KIRIM");
		$aColumnsAlias	= array("PEMINDAHAN_TUJUAN_ID", "PEMINDAHAN_ID", "TERBACA", "KODE", "NOMOR", "TERDISPOSISI", "TANGGAL_KIRIM");

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
			if (trim($sOrder) == "ORDER BY PEMINDAHAN_TUJUAN_ID asc") {
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
		$statement .= " AND B.STATUS IN ('TERKIRIM','VERIFIKASI_ARSIPARIS','SIMPAN','REVISI_ARSIPARIS') ";

		if ($STATUS != 'ALL') {
			$statement .= " AND A.TERDISPOSISI = '$STATUS' ";
		}

		$statement .= " AND TO_CHAR(A.TANGGAL_KIRIM,'YYYY-MM-DD') 
			BETWEEN '" . dateToDB($TANGGAL_AWAL) . "' AND '" . dateToDB($TANGGAL_AKHIR) . "' ";

		$statement .= "AND (UPPER(B.NOMOR) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(B.PERIHAL) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.KODE_DISPOSISI) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.NAMA_DISPOSISI) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
		)";

		$allRecord = $pemindahan->getCountByParamsMonitoringTujuan(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $pemindahan->getCountByParamsMonitoringTujuan(array(), $statement);
		}

		$pemindahan->selectByParamsMonitoringTujuan(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $pemindahan->query;exit;

		if ($this->IS_MOBILE == true) {
			$arrResult["rowCount"] = $pemindahan->rowCount;
			$arrResult["rowResult"] = $pemindahan->rowResult;
			echo json_encode($arrResult);
			return;
		}

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($pemindahan->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "KODE") {
					$row[] = "<b>" . $pemindahan->getField("NAMA") . "</b><br>" . $pemindahan->getField("JABATAN");
				} elseif ($aColumns[$i] == "NOMOR") {
					$row[] = "<b>" . $pemindahan->getField("NOMOR") . "</b><br>" . $pemindahan->getField("PERIHAL");
				} elseif ($aColumns[$i] == "TERBACA") {
					if ($pemindahan->getField("TERBACA") == "YA") {
						$row[] = "<i class='fa fa-envelope-open' style='color: #2e8201;font-size: 36px;'></i>";
					} elseif ($pemindahan->getField("TERBACA") == "TIDAK") {
						$row[] = "<i class='fa fa-envelope' style='color: #2b9ef3;font-size: 36px;'></i>";
					}
				} elseif ($aColumns[$i] == "TERDISPOSISI") {
					if ($pemindahan->getField("TERDISPOSISI") == "YA") {
						$row[] = "<span class='badge badge-success'>Terdisposisikan</span>";
					} elseif ($pemindahan->getField("TERDISPOSISI") == "TIDAK") {
						$row[] = "<span class='badge badge-danger'>Belum Didisposisikan</span>";
					}
				} elseif ($aColumns[$i] == "TANGGAL_KIRIM") {
					$row[] = getFormattedDateTime2($pemindahan->getField($aColumns[$i]));
				} else {
					$row[] = $pemindahan->getField($aColumns[$i]);
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
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false) {
			exit();
		}

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$PEMINDAHAN_ID = $this->input->get("PEMINDAHAN_ID");

		$aColumns		= array(
			"PEMINDAHAN_TUJUAN_ID",
			"PEMINDAHAN_ID",
			"TANGGAL_KIRIM",
			"NAMA_DISPOSISI",
			"NAMA",
			"JENIS",
			"PESAN_DISPOSISI",
			"TERBACA",
			"TERDISPOSISI"
		);
		$aColumnsAlias	= array(
			"PEMINDAHAN_TUJUAN_ID",
			"PEMINDAHAN_ID",
			"TANGGAL_KIRIM",
			"NAMA_DISPOSISI",
			"NAMA",
			"JENIS",
			"PESAN_DISPOSISI",
			"TERBACA",
			"TERDISPOSISI"
		);

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
			if (trim($sOrder) == "ORDER BY PEMINDAHAN_TUJUAN_ID asc") {
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

		$statement .= " AND A.PEMINDAHAN_ID='$PEMINDAHAN_ID' ";

		$statement .= "AND (UPPER(A.KODE) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.NAMA) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.KODE_DISPOSISI) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.NAMA_DISPOSISI) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
		)";

		$allRecord = $pemindahan->getCountByParamsTujuan(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $pemindahan->getCountByParamsTujuan(array(), $statement);
		}

		$pemindahan->selectByParamsTujuan(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $pemindahan->query;exit;

		if ($this->IS_MOBILE == true) {
			$arrResult["rowCount"] = $pemindahan->rowCount;
			$arrResult["rowResult"] = $pemindahan->rowResult;
			echo json_encode($arrResult);
			return;
		}

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($pemindahan->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "NAMA_DISPOSISI") {
					$row[] = "<b>" . $pemindahan->getField("NAMA_DISPOSISI") . "</b><br>" . $pemindahan->getField("JABATAN_DISPOSISI");
				} elseif ($aColumns[$i] == "NAMA") {
					$row[] = "<b>" . $pemindahan->getField("NAMA") . "</b><br>" . $pemindahan->getField("JABATAN");
				} elseif ($aColumns[$i] == "TERBACA") {
					if ($pemindahan->getField("TERBACA") == "YA") {
						$row[] = "<i class='fa fa-check-square-o' style='color: #00a62c;' title=' Terbaca pada tanggal " . getFormattedDateTime2($pemindahan->getField("TERBACA_TANGGAL")) . "'></i>";
					} elseif ($pemindahan->getField("TERBACA") == "TIDAK") {
						$row[] = "<i class='fa fa-times' style='color: #ba0013;' title='Belum dibaca'></i>";
					}
				} elseif ($aColumns[$i] == "TERDISPOSISI") {
					if ($pemindahan->getField("TERDISPOSISI") == "YA") {
						$row[] = "<i class='fa fa-check-square-o' style='color: #00a62c;' title='Terdisposisikan pada tanggal " . getFormattedDateTime2($pemindahan->getField("TANGGAL_DISPOSISI")) . "'></i>";
					} elseif ($pemindahan->getField("TERDISPOSISI") == "TIDAK") {
						$row[] = "<i class='fa fa-times' style='color: #ba0013;'></i>";
					}
				} elseif ($aColumns[$i] == "TANGGAL_KIRIM") {
					$row[] = getFormattedDateTime3($pemindahan->getField($aColumns[$i]));
				} else {
					$row[] = $pemindahan->getField($aColumns[$i]);
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
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false) {
			exit();
		}

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");
		$CABANG_ID = $this->input->get("CABANG_ID");
		$SATUAN_KERJA_ID = $this->input->get("SATUAN_KERJA_ID");
		$STATUS = $this->input->get("STATUS");

		$aColumns		= array(
			"PEMINDAHAN_ID",
			"STATUS",
			"NOMOR",
			"TANGGAL",
			"PERIHAL",
			"KETERANGAN",
			"NAMA_PERUSAHAAN",
			"NAMA_CABANG",
			"NAMA_SATUAN_KERJA",
			"STATUS_KET"
		);

		$aColumnsAlias	= array(
			"PEMINDAHAN_ID",
			"STATUS",
			"NOMOR",
			"TANGGAL",
			"PERIHAL",
			"KETERANGAN",
			"NAMA_PERUSAHAAN",
			"NAMA_CABANG",
			"NAMA_SATUAN_KERJA",
			"STATUS_KET"
		);

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
			if (trim($sOrder) == "ORDER BY PEMINDAHAN_ID asc") {
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

		if ($PERUSAHAAN_ID != 'ALL') {
			$statement .= " AND A.PERUSAHAAN_ID = '$PERUSAHAAN_ID' ";
		}

		if ($CABANG_ID != 'ALL') {
			$statement .= " AND A.CABANG_ID = '$CABANG_ID' ";
		}

		if ($SATUAN_KERJA_ID != 'ALL') {
			$statement .= " AND A.SATUAN_KERJA_ID = '$SATUAN_KERJA_ID' ";
		}

		if ($STATUS == 'ALL') {
			$statement .= " AND A.STATUS IN ('TERKIRIM','VERIFIKASI_ARSIPARIS','SIMPAN','REVISI_ARSIPARIS') ";
		} else {
			$statement .= " AND A.STATUS = '$STATUS' ";
		}


		//CEK APAKAH SUDAH MENDAPATKAN DISPOSISI 
		$statement .= " AND EXISTS(SELECT 1 FROM PEMINDAHAN_TUJUAN X WHERE X.PEMINDAHAN_ID=A.PEMINDAHAN_ID 
			AND X.JENIS='DISPOSISI' AND X.PEGAWAI_ID='$this->PEGAWAI_ID')";


		$statement .= "AND (UPPER(A.NOMOR) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.PERIHAL) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
		)";

		$allRecord = $pemindahan->getCountByParamsMonitoring(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $pemindahan->getCountByParamsMonitoring(array(), $statement);
		}

		$pemindahan->selectByParamsMonitoring(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $pemindahan->query;exit;

		if ($this->IS_MOBILE == true) {
			$arrResult["rowCount"] = $pemindahan->rowCount;
			$arrResult["rowResult"] = $pemindahan->rowResult;
			echo json_encode($arrResult);
			return;
		}

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($pemindahan->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "STATUS_KET") {
					if ($pemindahan->getField("STATUS") == "TERKIRIM") {
						$label = "Penerimaan Berkas";
						$badge_color = "badge-warning";
					} elseif ($pemindahan->getField("STATUS") == "VERIFIKASI_ARSIPARIS") {
						$label = "Verifikasi & Pemindaian";
						$badge_color = "badge-info";
					} elseif ($pemindahan->getField("STATUS") == "SIMPAN") {
						$label = "Tersimpan";
						$badge_color = "badge-success";
					}

					$row[] = "<span class='badge " . $badge_color . "'>" . $label . "</span>";
				} else {
					$row[] = $pemindahan->getField($aColumns[$i]);
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
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false) {
			exit();
		}

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");
		$CABANG_ID = $this->input->get("CABANG_ID");
		$SATUAN_KERJA_ID = $this->input->get("SATUAN_KERJA_ID");
		$STATUS = $this->input->get("STATUS");

		$aColumns		= array(
			"PEMINDAHAN_ID",
			"STATUS",
			"NOMOR",
			"TANGGAL",
			"PERIHAL",
			"KETERANGAN",
			"NAMA_PERUSAHAAN",
			"NAMA_CABANG",
			"NAMA_SATUAN_KERJA",
			"STATUS_KET"
		);

		$aColumnsAlias	= array(
			"PEMINDAHAN_ID",
			"STATUS",
			"NOMOR",
			"TANGGAL",
			"PERIHAL",
			"KETERANGAN",
			"NAMA_PERUSAHAAN",
			"NAMA_CABANG",
			"NAMA_SATUAN_KERJA",
			"STATUS_KET"
		);

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
			if (trim($sOrder) == "ORDER BY PEMINDAHAN_ID asc") {
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

		if ($this->USER_GROUP == "PEGAWAI") {
			$statement .= " AND A.SATUAN_KERJA_ID = '$this->SATUAN_KERJA_ID' ";
			$statement .= " AND A.CREATED_BY = '$this->USER_LOGIN_ID' ";
		} elseif ($this->USER_GROUP == "ARSIPARIS") {
			$statement .= " AND A.STATUS IN ('TERKIRIM','VERIFIKASI_ARSIPARIS','SIMPAN','REVISI_ARSIPARIS') ";
			$statement .= " AND EXISTS(SELECT 1 FROM PEMINDAHAN_TUJUAN X WHERE X.PEMINDAHAN_ID=A.PEMINDAHAN_ID AND X.JENIS='DISPOSISI' AND X.PEGAWAI_ID='$this->PEGAWAI_ID')";
		}

		$statement .= "AND (UPPER(A.NOMOR) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.PERIHAL) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
		)";

		$allRecord = $pemindahan->getCountByParamsMonitoring(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $pemindahan->getCountByParamsMonitoring(array(), $statement);
		}

		$pemindahan->selectByParamsMonitoring(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $pemindahan->query;exit;

		if ($this->IS_MOBILE == true) {
			$arrResult["rowCount"] = $pemindahan->rowCount;
			$arrResult["rowResult"] = $pemindahan->rowResult;
			echo json_encode($arrResult);
			return;
		}

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($pemindahan->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "STATUS_KET") {
					if ($pemindahan->getField("STATUS") == "TERKIRIM") {
						$label = "Penerimaan Berkas";
						$badge_color = "badge-warning";
					} elseif ($pemindahan->getField("STATUS") == "VERIFIKASI_ARSIPARIS") {
						$label = "Verifikasi & Pemindaian";
						$badge_color = "badge-info";
					} elseif ($pemindahan->getField("STATUS") == "SIMPAN") {
						$label = "Tersimpan";
						$badge_color = "badge-success";
					}

					$row[] = "<span class='badge " . $badge_color . "'>" . $label . "</span>";
				} else {
					$row[] = $pemindahan->getField($aColumns[$i]);
				}
			}
			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}


	function json_pencarian()
	{
		$this->load->library("crfs_protect");
		$csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->get("reqToken")) && $this->IS_MOBILE == false) {
			exit();
		}

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");
		$CABANG_ID = $this->input->get("CABANG_ID");
		$SATUAN_KERJA_ID = $this->input->get("SATUAN_KERJA_ID");
		$KLASIFIKASI_ID = $this->input->get("KLASIFIKASI_ID");
		$KURUN_WAKTU = $this->input->get("KURUN_WAKTU");
		$TAHUN_PINDAH = $this->input->get("TAHUN_PINDAH");
		$TAHUN_MUSNAH = $this->input->get("TAHUN_MUSNAH");
		$TINGKAT_PERKEMBANGAN_ID = $this->input->get("TINGKAT_PERKEMBANGAN_ID");
		$KONDISI_FISIK_ID = $this->input->get("KONDISI_FISIK_ID");
		$MEDIA_SIMPAN_ID = $this->input->get("MEDIA_SIMPAN_ID");
		$NOMOR_DOKUMEN = $this->input->get("NOMOR_DOKUMEN");
		$NOMOR_ALTERNATIF = $this->input->get("NOMOR_ALTERNATIF");
		$LOKASI_SIMPAN_RUANG = $this->input->get("LOKASI_SIMPAN_RUANG");
		$LOKASI_SIMPAN_LEMARI = $this->input->get("LOKASI_SIMPAN_LEMARI");
		$LOKASI_SIMPAN_NOMOR_BOKS = $this->input->get("LOKASI_SIMPAN_NOMOR_BOKS");
		$LOKASI_SIMPAN_NOMOR_FOLDER = $this->input->get("LOKASI_SIMPAN_NOMOR_FOLDER");

		$aColumns		= array(
			"PEMINDAHAN_BERKAS_ID",
			"PEMINDAHAN_ID",
			"DOKUMEN",
			"KLASIFIKASI_KODE",
			"NOMOR_SURAT",
			"NOMOR_ALTERNATIF",
			"KETERANGAN",
			"PERIHAL",
			"KURUN_WAKTU",
			"TINGKAT_PERKEMBANGAN_NAMA",
			"KONDISI_FISIK_NAMA",
			"JUMLAH_BERKAS",
			"LOKASI_SIMPAN_NOMOR_FOLDER",
			"NAMA_SATUAN_KERJA",
			"LAMPIRAN"
		);
		$aColumnsAlias	= array(
			"PEMINDAHAN_BERKAS_ID",
			"PEMINDAHAN_ID",
			"DOKUMEN",
			"KLASIFIKASI_KODE",
			"NOMOR_DOKUMEN",
			"NOMOR_ALTERNATIF",
			"KETERANGAN",
			"PERIHAL",
			"KURUN_WAKTU",
			"TINGKAT_PERKEMBANGAN_NAMA",
			"KONDISI_FISIK_NAMA",
			"JUMLAH_BERKAS",
			"LOKASI_SIMPAN_NOMOR_FOLDER",
			"NAMA_SATUAN_KERJA",
			"LAMPIRAN"
		);

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
			if (trim($sOrder) == "ORDER BY PEMINDAHAN_BERKAS_ID asc") {
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY A.KLASIFIKASI_KODE asc";
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

		$statement .= " AND A.STATUS = 'SIMPAN' ";

		if ($PERUSAHAAN_ID != 'ALL') {
			$statement .= " AND A.PERUSAHAAN_ID = '$PERUSAHAAN_ID' ";
		}

		if ($CABANG_ID != 'ALL') {
			$statement .= " AND A.CABANG_ID = '$CABANG_ID' ";
		}

		if ($SATUAN_KERJA_ID != 'ALL') {
			$statement .= " AND A.SATUAN_KERJA_ID = '$SATUAN_KERJA_ID' ";
		}

		if ($KLASIFIKASI_ID != 'ALL') {
			$statement .= " AND A.KLASIFIKASI_ID = '$KLASIFIKASI_ID' ";
		}

		if ($KURUN_WAKTU != 'ALL') {
			$statement .= " AND A.KURUN_WAKTU = '$KURUN_WAKTU' ";
		}

		if ($TAHUN_PINDAH != 'ALL') {
			$statement .= " AND A.TAHUN_PINDAH = '$TAHUN_PINDAH' ";
		}

		if ($TAHUN_MUSNAH != 'ALL') {
			$statement .= " AND A.TAHUN_MUSNAH = '$TAHUN_MUSNAH' ";
		}

		if ($TINGKAT_PERKEMBANGAN_ID != 'ALL') {
			$statement .= " AND A.TINGKAT_PERKEMBANGAN_ID = '$TINGKAT_PERKEMBANGAN_ID' ";
		}

		if ($KONDISI_FISIK_ID != 'ALL') {
			$statement .= " AND A.KONDISI_FISIK_ID = '$KONDISI_FISIK_ID' ";
		}

		if ($MEDIA_SIMPAN_ID != 'ALL') {
			$statement .= " AND A.MEDIA_SIMPAN_ID = '$MEDIA_SIMPAN_ID' ";
		}

		if ($NOMOR_DOKUMEN != '') {
			$statement .= " AND A.NOMOR_DOKUMEN = '$NOMOR_DOKUMEN' ";
		}

		if ($NOMOR_ALTERNATIF != '') {
			$statement .= " AND A.NOMOR_ALTERNATIF = '$NOMOR_ALTERNATIF' ";
		}

		if ($LOKASI_SIMPAN_RUANG != 'ALL') {
			$statement .= " AND A.LOKASI_SIMPAN_RUANG = '$LOKASI_SIMPAN_RUANG' ";
		}

		if ($LOKASI_SIMPAN_LEMARI != 'ALL') {
			$statement .= " AND A.LOKASI_SIMPAN_LEMARI = '$LOKASI_SIMPAN_LEMARI' ";
		}

		if ($LOKASI_SIMPAN_NOMOR_BOKS != 'ALL') {
			$statement .= " AND A.LOKASI_SIMPAN_NOMOR_BOKS = '$LOKASI_SIMPAN_NOMOR_BOKS' ";
		}

		if ($LOKASI_SIMPAN_NOMOR_FOLDER != 'ALL') {
			$statement .= " AND A.LOKASI_SIMPAN_NOMOR_FOLDER = '$LOKASI_SIMPAN_NOMOR_FOLDER' ";
		}

		// SEARCHING
		$statement .= "AND (UPPER(A.KLASIFIKASI_KODE) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.KLASIFIKASI_NAMA) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.KETERANGAN) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.KURUN_WAKTU) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.TINGKAT_PERKEMBANGAN_NAMA) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.TAHUN_PINDAH) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.TAHUN_MUSNAH) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.MEDIA_SIMPAN_NAMA) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.LOKASI_SIMPAN_RUANG) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.LOKASI_SIMPAN_LEMARI) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.LOKASI_SIMPAN_NOMOR_BOKS) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.LOKASI_SIMPAN_NOMOR_FOLDER) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.KONDISI_FISIK_NAMA) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.JUMLAH_BERKAS) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.NOMOR_DOKUMEN) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(A.NOMOR_ALTERNATIF) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(C.NAMA) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(D.NAMA) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(E.NAMA) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
			OR UPPER(F.DOKUMEN_TEXT) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
		)";

		$allRecord = $pemindahan->getCountByParamsMonitoringBerkas(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $pemindahan->getCountByParamsMonitoringBerkas(array(), $statement);
		}

		$pemindahan->selectByParamsMonitoringBerkas(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $pemindahan->query;exit;

		if ($this->IS_MOBILE == true) {
			$arrResult["rowCount"] = $pemindahan->rowCount;
			$arrResult["rowResult"] = $pemindahan->rowResult;
			echo json_encode($arrResult);
			return;
		}

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($pemindahan->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "KLASIFIKASI_KODE") {
					$row[] = "<b>" . $pemindahan->getField("KLASIFIKASI_KODE") . "</b><br>" . $pemindahan->getField("KLASIFIKASI_NAMA");
				} elseif ($aColumns[$i] == "DOKUMEN") {
					$row[] = "<a href='javascript:void(0)' class='btn btn-xs btn-info' onclick=openPopup('uploads/pemindahan/" . $pemindahan->getField("DOKUMEN") . "')><i class='fa fa-eye'></i> Dokumen</a>";
				} elseif ($aColumns[$i] == "LOKASI_SIMPAN_NOMOR_FOLDER") {
					$row[] = "[Rak - " . $pemindahan->getField("LOKASI_SIMPAN_LEMARI") . "][B. " . $pemindahan->getField("LOKASI_SIMPAN_NOMOR_BOKS") . "][F - " . $pemindahan->getField("LOKASI_SIMPAN_NOMOR_FOLDER") . "]";
				} else {
					$row[] = $pemindahan->getField($aColumns[$i]);
				}
			}
			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}


	function add()
	{
		$this->load->library("crfs_protect");
		$csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->post('_crfs_7mj4R5iP')) && $this->IS_MOBILE == false) {
			exit();
		}

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

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

		$PEMINDAHAN_BERKAS_ID			= $this->input->post("PEMINDAHAN_BERKAS_ID");
		$KLASIFIKASI_ID					= $this->input->post("KLASIFIKASI_ID");
		$NOMOR_DOKUMEN					= $this->input->post("NOMOR_DOKUMEN");
		$NOMOR_ALTERNATIF				= $this->input->post("NOMOR_ALTERNATIF");
		$KETERANGAN_BERKAS				= setQuote($_POST['KETERANGAN_BERKAS']);
		$KURUN_WAKTU					= $this->input->post("KURUN_WAKTU");
		$TINGKAT_PERKEMBANGAN_ID		= $this->input->post("TINGKAT_PERKEMBANGAN_ID");
		$KONDISI_FISIK_ID				= $this->input->post("KONDISI_FISIK_ID");

		$PEMINDAHAN_TUJUAN_ID_TUJUAN	= $this->input->post("PEMINDAHAN_TUJUAN_ID_TUJUAN");
		$PERUSAHAAN_ID_TUJUAN			= $this->input->post("PERUSAHAAN_ID_TUJUAN");
		$CABANG_ID_TUJUAN				= $this->input->post("CABANG_ID_TUJUAN");
		$SATUAN_KERJA_ID_TUJUAN			= $this->input->post("SATUAN_KERJA_ID_TUJUAN");
		$JENIS_TUJUAN					= $this->input->post("JENIS_TUJUAN");
		$PEGAWAI_ID_TUJUAN				= $this->input->post("PEGAWAI_ID_TUJUAN");
		$KODE_TUJUAN					= $this->input->post("KODE_TUJUAN");
		$NAMA_TUJUAN					= $this->input->post("NAMA_TUJUAN");
		$JABATAN_TUJUAN					= $this->input->post("JABATAN_TUJUAN");

		$PEMINDAHAN_TUJUAN_ID_TEMBUSAN	= $this->input->post("PEMINDAHAN_TUJUAN_ID_TEMBUSAN");
		$PERUSAHAAN_ID_TEMBUSAN			= $this->input->post("PERUSAHAAN_ID_TEMBUSAN");
		$CABANG_ID_TEMBUSAN				= $this->input->post("CABANG_ID_TEMBUSAN");
		$SATUAN_KERJA_ID_TEMBUSAN		= $this->input->post("SATUAN_KERJA_ID_TEMBUSAN");
		$JENIS_TEMBUSAN					= $this->input->post("JENIS_TEMBUSAN");
		$PEGAWAI_ID_TEMBUSAN			= $this->input->post("PEGAWAI_ID_TEMBUSAN");
		$KODE_TEMBUSAN					= $this->input->post("KODE_TEMBUSAN");
		$NAMA_TEMBUSAN					= $this->input->post("NAMA_TEMBUSAN");
		$JABATAN_TEMBUSAN				= $this->input->post("JABATAN_TEMBUSAN");

		$PEMINDAHAN_APPROVAL_ID			= $this->input->post("PEMINDAHAN_APPROVAL_ID");
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
		if (count(array_filter($KLASIFIKASI_ID)) <= 0) {
			echo "GAGAL|Daftar Berkas tidak tersedia. Silahkan Entri atau Upload DPA/D terlebih dahulu!";
			return;
		}

		if (count(array_filter($PEGAWAI_ID_TUJUAN)) <= 0) {
			echo "GAGAL|Tujuan tidak tersedia. Tambahkan Tujuan terlebih dahulu!";
			return;
		}

		if (count(array_filter($PEGAWAI_ID_TUJUAN)) > 1) {
			echo "GAGAL|Maks. 1 (satu) data Tujuan yang dapat dipilih!";
			return;
		}

		if (count(array_filter($PEGAWAI_ID_APPROVAL)) <= 0) {
			echo "GAGAL|Approval tidak tersedia. Tambahkan Approval terlebih dahulu!";
			return;
		}

		if (count(array_filter($PEGAWAI_ID_APPROVAL)) > 0) {
			if (in_array("1", $URUT_APPROVAL)) {
			} else {
				echo "GAGAL|Urutan ke-1 pada Approval belum ditentukan";
				return;
			}

			for ($i = 1; $i <= count(array_filter($URUT_APPROVAL)); $i++) {
				if (in_array($i, $URUT_APPROVAL)) {
				} else {
					echo "GAGAL|Urutan pada Approval tidak urut!";
					return;
				}
			}
		}
		/******END VALIDASI*******/


		$KODE_JABATAN = $this->db->query("select kode_jabatan from satuan_kerja 
			where satuan_kerja_id='$SATUAN_KERJA_ID'")->row()->kode_jabatan;

		/******UPLOAD DOKUMEN******/
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$FILE_DIR = "uploads/pemindahan/";

		$DOKUMEN 				= $_FILES['DOKUMEN'];
		$PEMINDAHAN_DOKUMEN_ID 	= $this->input->post('PEMINDAHAN_DOKUMEN_ID');
		$NAMA_DOKUMEN 			= $this->input->post('NAMA_DOKUMEN');
		$TEMP_DOKUMEN 			= $this->input->post('TEMP_DOKUMEN');
		$UKURAN_DOKUMEN 		= $this->input->post('UKURAN_DOKUMEN');

		$DOKUMEN_BERKAS 		= $_FILES['DOKUMEN_BERKAS'];
		$TEMP_DOKUMEN_BERKAS 	= $this->input->post('TEMP_DOKUMEN_BERKAS');
		/******END UPLOAD DOKUMEN******/

		$pemindahan->setField("PEMINDAHAN_ID", $id);
		$pemindahan->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID);
		$pemindahan->setField("CABANG_ID", $CABANG_ID);
		$pemindahan->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID);
		$pemindahan->setField("NOMOR", $NOMOR);
		$pemindahan->setField("TANGGAL", dateToDBCheck($TANGGAL));
		$pemindahan->setField("PERIHAL", $PERIHAL);
		$pemindahan->setField("ISI", $TINGKAT_PERKEMBANGAN_ID);
		$pemindahan->setField("KETERANGAN", $KETERANGAN);
		$pemindahan->setField("DOKUMEN", $insertFile);
		$pemindahan->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$pemindahan->setField("UPDATED_BY", $this->USER_LOGIN_ID);

		if ($mode == "insert") {
			if ($pemindahan->insert()) {
				$id = $pemindahan->id;

				/******BERKAS*******/
				for ($i = 0; $i < count($PEMINDAHAN_BERKAS_ID); $i++) {
					$pemindahan_berkas = new Pemindahan();
					$pemindahan_berkas->setField("PEMINDAHAN_ID", $id);
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
					$pemindahan_berkas->setField("CREATED_BY", $this->USER_LOGIN_ID);
					if ($pemindahan_berkas->insertBerkas()) {
						$PEMINDAHAN_BERKAS_ID = $pemindahan_berkas->PEMINDAHAN_BERKAS_ID;
						$PEMINDAHAN_ID = $pemindahan_berkas->PEMINDAHAN_ID;

						$renameFile = "ARSIP_PID_" . $PEMINDAHAN_ID . "_ID_" . $PEMINDAHAN_BERKAS_ID . "_" . md5($i . date("Ymdhis") . rand()) . "." . getExtension($DOKUMEN_BERKAS['name'][$i]);
						if ($file->uploadToDirArray('DOKUMEN_BERKAS', $FILE_DIR, $renameFile, $i)) {
							$insertLinkSize = $file->uploadedSize;
							$insertLinkTipe = $file->uploadedExtension;
							$insertLinkFile = $renameFile;
							$insertNamaFile = setQuote($DOKUMEN_BERKAS['name'][$i]);

							$pemindahan_berkas_dokumen = new Pemindahan();
							$pemindahan_berkas_dokumen->setField("PEMINDAHAN_BERKAS_ID", $PEMINDAHAN_BERKAS_ID);
							$pemindahan_berkas_dokumen->setField("FIELD", "DOKUMEN");
							$pemindahan_berkas_dokumen->setField("FIELD_VALUE", $insertLinkFile);
							$pemindahan_berkas_dokumen->setField("UPDATED_BY", $this->USER_LOGIN_ID);
							$pemindahan_berkas_dokumen->updateByFieldBerkas();
						}
					}
				}
				/******END BERKAS*******/

				/******TUJUAN*******/
				for ($i = 0; $i < count($PEMINDAHAN_TUJUAN_ID_TUJUAN); $i++) {
					$pemindahan_tujuan = new Pemindahan();
					$pemindahan_tujuan->setField("PEMINDAHAN_ID", $id);
					$pemindahan_tujuan->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID_TUJUAN[$i]);
					$pemindahan_tujuan->setField("CABANG_ID", $CABANG_ID_TUJUAN[$i]);
					$pemindahan_tujuan->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID_TUJUAN[$i]);
					$pemindahan_tujuan->setField("PEMINDAHAN_TUJUAN_ID", $PEMINDAHAN_TUJUAN_ID_TUJUAN[$i]);
					$pemindahan_tujuan->setField("JENIS", $JENIS_TUJUAN[$i]);
					$pemindahan_tujuan->setField("PEGAWAI_ID", $PEGAWAI_ID_TUJUAN[$i]);
					$pemindahan_tujuan->setField("KODE", $KODE_TUJUAN[$i]);
					$pemindahan_tujuan->setField("NAMA", setQuote($NAMA_TUJUAN[$i]));
					$pemindahan_tujuan->setField("JABATAN", $JABATAN_TUJUAN[$i]);
					$pemindahan_tujuan->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$pemindahan_tujuan->insertTujuan();
				}
				/******END TUJUAN*******/

				/******TEMBUSAN*******/
				for ($i = 0; $i < count($PEMINDAHAN_TUJUAN_ID_TEMBUSAN); $i++) {
					$pemindahan_tembusan = new Pemindahan();
					$pemindahan_tembusan->setField("PEMINDAHAN_ID", $id);
					$pemindahan_tembusan->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("CABANG_ID", $CABANG_ID_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("PEMINDAHAN_TUJUAN_ID", $PEMINDAHAN_TUJUAN_ID_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("JENIS", $JENIS_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("PEGAWAI_ID", $PEGAWAI_ID_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("KODE", $KODE_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("NAMA", setQuote($NAMA_TEMBUSAN[$i]));
					$pemindahan_tembusan->setField("JABATAN", $JABATAN_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$pemindahan_tembusan->insertTujuan();
				}
				/******END TEMBUSAN*******/

				/******APPROVAL*******/
				for ($i = 0; $i < count($PEMINDAHAN_APPROVAL_ID); $i++) {
					$pemindahan_approval = new Pemindahan();
					$pemindahan_approval->setField("PEMINDAHAN_ID", $id);
					$pemindahan_approval->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID_APPROVAL[$i]);
					$pemindahan_approval->setField("CABANG_ID", $CABANG_ID_APPROVAL[$i]);
					$pemindahan_approval->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID_APPROVAL[$i]);
					$pemindahan_approval->setField("PEMINDAHAN_APPROVAL_ID", $PEMINDAHAN_APPROVAL_ID[$i]);
					$pemindahan_approval->setField("PEGAWAI_ID", $PEGAWAI_ID_APPROVAL[$i]);
					$pemindahan_approval->setField("KODE", $KODE_APPROVAL[$i]);
					$pemindahan_approval->setField("NAMA", setQuote($NAMA_APPROVAL[$i]));
					$pemindahan_approval->setField("JABATAN", $JABATAN_APPROVAL[$i]);
					$pemindahan_approval->setField("SEBAGAI", $SEBAGAI_APPROVAL[$i]);
					$pemindahan_approval->setField("URUT", $URUT_APPROVAL[$i]);
					$pemindahan_approval->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$pemindahan_approval->insertApproval();
				}
				/******END APPROVAL*******/

				/******DOKUMEN*******/
				for ($i = 0; $i < count($DOKUMEN); $i++) {
					$renameFile = "DOK_DUKUNG_" . md5($i . date("Ymdhis") . rand()) . "." . getExtension($DOKUMEN['name'][$i]);
					if ($file->uploadToDirArray('DOKUMEN', $FILE_DIR, $renameFile, $i)) {
						$insertLinkSize = $file->uploadedSize;
						$insertLinkTipe = $file->uploadedExtension;
						$insertLinkFile = $renameFile;
						$insertNamaFile = setQuote($DOKUMEN['name'][$i]);

						$pemindahan_dokumen = new Pemindahan();
						$pemindahan_dokumen->setField("PEMINDAHAN_ID", $id);
						$pemindahan_dokumen->setField("NAMA", $insertNamaFile);
						$pemindahan_dokumen->setField("DOKUMEN", $insertLinkFile);
						$pemindahan_dokumen->setField("UKURAN_DOKUMEN", $insertLinkSize);
						$pemindahan_dokumen->setField("CREATED_BY", $this->USER_LOGIN_ID);
						$pemindahan_dokumen->insertDokumen();
					}
				}
				/******END DOKUMEN*******/

				/******NOMOR*******/
				$NOMOR = generateZero($id, 6) . "/TMJ-PEMINDAHAN/" . $KODE_JABATAN . "/" . date('m') . "/" . date('Y');

				$pemindahan = new Pemindahan();
				$pemindahan->setField("PEMINDAHAN_ID", $id);
				$pemindahan->setField("FIELD", "NOMOR");
				$pemindahan->setField("FIELD_VALUE", $NOMOR);
				$pemindahan->setField("UPDATED_BY", $this->USER_LOGIN_ID);
				if ($pemindahan->updateByField()) {
					echo "BERHASIL|Data berhasil disimpan|" . $id;
				} else {
					echo "GAGAL|Data gagal disimpan";
				}
				/******END NOMOR*******/
			} else {
				echo "GAGAL|Data gagal disimpan";
			}
		} else {
			if ($pemindahan->update()) {
				/******BERKAS*******/
				for ($i = 0; $i < count($PEMINDAHAN_BERKAS_ID); $i++) {
					$pemindahan_berkas = new Pemindahan();
					$pemindahan_berkas->setField("PEMINDAHAN_ID", $id);
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
					$pemindahan_berkas->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$pemindahan_berkas->setField("UPDATED_BY", $this->USER_LOGIN_ID);

					if ($PEMINDAHAN_BERKAS_ID[$i] == "") {
						if ($pemindahan_berkas->insertBerkas()) {
							$PBID = $pemindahan_berkas->PEMINDAHAN_BERKAS_ID;

							$renameFile = "ARSIP_PID_" . $id . "_ID_" . $PBID . "_" . md5($i . date("Ymdhis") . rand()) . "." . getExtension($DOKUMEN_BERKAS['name'][$i]);
							if ($file->uploadToDirArray('DOKUMEN_BERKAS', $FILE_DIR, $renameFile, $i)) {
								$insertLinkSize = $file->uploadedSize;
								$insertLinkTipe = $file->uploadedExtension;
								$insertLinkFile = $renameFile;
								$insertNamaFile = setQuote($DOKUMEN_BERKAS['name'][$i]);

								$pemindahan_berkas_dokumen = new Pemindahan();
								$pemindahan_berkas_dokumen->setField("PEMINDAHAN_BERKAS_ID", $PBID);
								$pemindahan_berkas_dokumen->setField("FIELD", "DOKUMEN");
								$pemindahan_berkas_dokumen->setField("FIELD_VALUE", $insertLinkFile);
								$pemindahan_berkas_dokumen->setField("UPDATED_BY", $this->USER_LOGIN_ID);
								$pemindahan_berkas_dokumen->updateByFieldBerkas();
							}
						}
					} else {
						if ($pemindahan_berkas->updateBerkas()) {
							$renameFile = "ARSIP_PID_" . $id . "_ID_" . $PEMINDAHAN_BERKAS_ID[$i] . "_" . md5($i . date("Ymdhis") . rand()) . "." . getExtension($DOKUMEN_BERKAS['name'][$i]);
							if ($file->uploadToDirArray('DOKUMEN_BERKAS', $FILE_DIR, $renameFile, $i)) {
								$insertLinkSize = $file->uploadedSize;
								$insertLinkTipe = $file->uploadedExtension;
								$insertLinkFile = $renameFile;
								$insertNamaFile = setQuote($DOKUMEN_BERKAS['name'][$i]);

								$pemindahan_berkas_dokumen = new Pemindahan();
								$pemindahan_berkas_dokumen->setField("PEMINDAHAN_BERKAS_ID", $PEMINDAHAN_BERKAS_ID[$i]);
								$pemindahan_berkas_dokumen->setField("FIELD", "DOKUMEN");
								$pemindahan_berkas_dokumen->setField("FIELD_VALUE", $insertLinkFile);
								$pemindahan_berkas_dokumen->setField("UPDATED_BY", $this->USER_LOGIN_ID);
								$pemindahan_berkas_dokumen->updateByFieldBerkas();
							}
						}
					}
				}
				/******END BERKAS*******/

				/******TUJUAN*******/
				for ($i = 0; $i < count($PEMINDAHAN_TUJUAN_ID_TUJUAN); $i++) {
					$pemindahan_tujuan = new Pemindahan();
					$pemindahan_tujuan->setField("PEMINDAHAN_ID", $id);
					$pemindahan_tujuan->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID_TUJUAN[$i]);
					$pemindahan_tujuan->setField("CABANG_ID", $CABANG_ID_TUJUAN[$i]);
					$pemindahan_tujuan->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID_TUJUAN[$i]);
					$pemindahan_tujuan->setField("PEMINDAHAN_TUJUAN_ID", $PEMINDAHAN_TUJUAN_ID_TUJUAN[$i]);
					$pemindahan_tujuan->setField("JENIS", $JENIS_TUJUAN[$i]);
					$pemindahan_tujuan->setField("PEGAWAI_ID", $PEGAWAI_ID_TUJUAN[$i]);
					$pemindahan_tujuan->setField("KODE", $KODE_TUJUAN[$i]);
					$pemindahan_tujuan->setField("NAMA", setQuote($NAMA_TUJUAN[$i]));
					$pemindahan_tujuan->setField("JABATAN", $JABATAN_TUJUAN[$i]);
					$pemindahan_tujuan->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$pemindahan_tujuan->setField("UPDATED_BY", $this->USER_LOGIN_ID);

					if ($PEMINDAHAN_TUJUAN_ID_TUJUAN[$i] == "") {
						$pemindahan_tujuan->insertTujuan();
					} else {
						$pemindahan_tujuan->updateTujuan();
					}
				}
				/******END TUJUAN*******/

				/******TEMBUSAN*******/
				for ($i = 0; $i < count($PEMINDAHAN_TUJUAN_ID_TEMBUSAN); $i++) {
					$pemindahan_tembusan = new Pemindahan();
					$pemindahan_tembusan->setField("PEMINDAHAN_ID", $id);
					$pemindahan_tembusan->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("CABANG_ID", $CABANG_ID_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("PEMINDAHAN_TUJUAN_ID", $PEMINDAHAN_TUJUAN_ID_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("JENIS", $JENIS_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("PEGAWAI_ID", $PEGAWAI_ID_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("KODE", $KODE_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("NAMA", setQuote($NAMA_TEMBUSAN[$i]));
					$pemindahan_tembusan->setField("JABATAN", $JABATAN_TEMBUSAN[$i]);
					$pemindahan_tembusan->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$pemindahan_tembusan->setField("UPDATED_BY", $this->USER_LOGIN_ID);

					if ($PEMINDAHAN_TUJUAN_ID_TEMBUSAN[$i] == "") {
						$pemindahan_tembusan->insertTujuan();
					} else {
						$pemindahan_tembusan->updateTujuan();
					}
				}
				/******END TEMBUSAN*******/

				/******APPROVAL*******/
				for ($i = 0; $i < count($PEMINDAHAN_APPROVAL_ID); $i++) {
					$pemindahan_approval = new Pemindahan();
					$pemindahan_approval->setField("PEMINDAHAN_ID", $id);
					$pemindahan_approval->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID_APPROVAL[$i]);
					$pemindahan_approval->setField("CABANG_ID", $CABANG_ID_APPROVAL[$i]);
					$pemindahan_approval->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID_APPROVAL[$i]);
					$pemindahan_approval->setField("PEMINDAHAN_APPROVAL_ID", $PEMINDAHAN_APPROVAL_ID[$i]);
					$pemindahan_approval->setField("PEGAWAI_ID", $PEGAWAI_ID_APPROVAL[$i]);
					$pemindahan_approval->setField("KODE", $KODE_APPROVAL[$i]);
					$pemindahan_approval->setField("NAMA", setQuote($NAMA_APPROVAL[$i]));
					$pemindahan_approval->setField("JABATAN", $JABATAN_APPROVAL[$i]);
					$pemindahan_approval->setField("SEBAGAI", $SEBAGAI_APPROVAL[$i]);
					$pemindahan_approval->setField("URUT", $URUT_APPROVAL[$i]);
					$pemindahan_approval->setField("STATUS", "BELUM_APPROVE");
					$pemindahan_approval->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$pemindahan_approval->setField("UPDATED_BY", $this->USER_LOGIN_ID);

					if ($PEMINDAHAN_APPROVAL_ID[$i] == "") {
						$pemindahan_approval->insertApproval();
					} else {
						$pemindahan_approval->updateApproval();
					}
				}
				/******END APPROVAL*******/

				/******DOKUMEN*******/
				for ($i = 0; $i < count($DOKUMEN); $i++) {
					$renameFile = "DOK_DUKUNG_" . md5($i . date("Ymdhis") . rand()) . "." . getExtension($DOKUMEN['name'][$i]);
					if ($file->uploadToDirArray('DOKUMEN', $FILE_DIR, $renameFile, $i)) {
						$insertLinkSize = $file->uploadedSize;
						$insertLinkTipe = $file->uploadedExtension;
						$insertLinkFile = $renameFile;
						$insertNamaFile = setQuote($DOKUMEN['name'][$i]);

						$pemindahan_dokumen = new Pemindahan();
						$pemindahan_dokumen->setField("PEMINDAHAN_ID", $id);
						$pemindahan_dokumen->setField("NAMA", $insertNamaFile);
						$pemindahan_dokumen->setField("DOKUMEN", $insertLinkFile);
						$pemindahan_dokumen->setField("UKURAN_DOKUMEN", $insertLinkSize);
						$pemindahan_dokumen->setField("CREATED_BY", $this->USER_LOGIN_ID);
						$pemindahan_dokumen->insertDokumen();
					}
				}

				for ($i = 0; $i < count($TEMP_DOKUMEN); $i++) {
					$pemindahan_dokumen = new Pemindahan();
					$pemindahan_dokumen->setField("PEMINDAHAN_ID", $id);
					$pemindahan_dokumen->setField("PEMINDAHAN_DOKUMEN_ID", $PEMINDAHAN_DOKUMEN_ID[$i]);
					$pemindahan_dokumen->setField("NAMA", $NAMA_DOKUMEN[$i]);
					$pemindahan_dokumen->setField("DOKUMEN", $TEMP_DOKUMEN[$i]);
					$pemindahan_dokumen->setField("UKURAN_DOKUMEN", $UKURAN_DOKUMEN[$i]);
					$pemindahan_dokumen->setField("CREATED_BY", $this->USER_LOGIN_ID);
					$pemindahan_dokumen->updateDokumen();
				}
				/******END DOKUMEN*******/

				/******NOMOR*******/
				$NOMOR = generateZero($id, 6) . "/TMJ-PEMINDAHAN/" . $KODE_JABATAN . "/" . date('m') . "/" . date('Y');

				$pemindahan = new Pemindahan();
				$pemindahan->setField("PEMINDAHAN_ID", $id);
				$pemindahan->setField("FIELD", "NOMOR");
				$pemindahan->setField("FIELD_VALUE", $NOMOR);
				$pemindahan->setField("UPDATED_BY", $this->USER_LOGIN_ID);
				if ($pemindahan->updateByField()) {
					echo "BERHASIL|Data berhasil disimpan|" . $id;
				} else {
					echo "GAGAL|Data gagal disimpan";
				}
				/******END NOMOR*******/
			} else {
				echo "GAGAL|Data gagal disimpan";
			}
		}
	}

	function delete()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$pemindahan->setField("PEMINDAHAN_ID", $reqId);
		if ($pemindahan->delete()) {
			echo "Data berhasil dihapus";
		} else {
			echo "Data gagal dihapus. Terdapat relasi dengan data lainnya!";
		}
	}

	function delete_berkas()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$pemindahan->setField("PEMINDAHAN_BERKAS_ID", $reqId);

		if ($pemindahan->deleteBerkas()) {
			echo "Data berhasil dihapus";
		} else {
			echo "Data gagal dihapus. Terdapat relasi dengan data lainnya!";
		}
	}


	function delete_tujuan()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$pemindahan->setField("PEMINDAHAN_TUJUAN_ID", $reqId);

		if ($pemindahan->deleteTujuan()) {
			echo "Data berhasil dihapus";
		} else {
			echo "Data gagal dihapus. Terdapat relasi dengan data lainnya!";
		}
	}

	function delete_approval()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$pemindahan->setField("PEMINDAHAN_APPROVAL_ID", $reqId);

		if ($pemindahan->deleteApproval()) {
			echo "Data berhasil dihapus";
		} else {
			echo "Data gagal dihapus. Terdapat relasi dengan data lainnya!";
		}
	}


	function terima_berkas()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$pemindahan->setField("PEMINDAHAN_ID", $reqId);
		$pemindahan->setField("FIELD", "STATUS");
		$pemindahan->setField("FIELD_VALUE", "VERIFIKASI_ARSIPARIS");
		$pemindahan->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if ($pemindahan->updateByField()) {
			/********LOG*********/
			$kode = "TERIMA_BERKAS";
			$keterangan = "Menerima Dokumen/Berkas Fisik (Hardcopy) yang tercantum pada Daftar Pertelaan Arsip/Dokumen dari Unit Pengolah";
			$this->log($reqId, $kode, $keterangan);
			/********END LOG*********/

			echo "Data berhasil disimpan. Silahkan melakukan verifikasi pada Daftar Pertelaan Arsip/Dokumen!";
		} else {
			echo "Data gagal disimpan";
		}
	}


	function verifikasi()
	{
		$this->load->library("crfs_protect");
		$csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->post('_crfs_7mj4R5iP')) && $this->IS_MOBILE == false) {
			exit();
		}

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

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
		for ($i = 0; $i < count(array_filter($PEMINDAHAN_BERKAS_ID)); $i++) {
			if ($STATUS_BERKAS[$i] == "") {
				echo "GAGAL|Terdapat Status Verifikasi Daftar Pertelaan Arsip/Dokumen belum ditentukan!";
				return;
			} elseif ($STATUS_BERKAS[$i] == "REVISI" && trim($REVISI_BERKAS[$i]) == "") {
				echo "GAGAL|Terdapat Status Verifikasi Daftar Pertelaan Arsip/Dokumen belum diisi Catatan Revisi!";
				return;
			}
		}
		/******END VALIDASI*******/

		$jumlahRevisi = 0;
		for ($i = 0; $i < count($PEMINDAHAN_BERKAS_ID); $i++) {
			$pemindahan_berkas = new Pemindahan();
			$pemindahan_berkas->setField("PEMINDAHAN_ID", $id);
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

			if ($PEMINDAHAN_BERKAS_ID[$i] == "") {
				$pemindahan_berkas->insertBerkasVerifikasi();
			} else {
				$pemindahan_berkas->updateBerkasVerifikasi();
			}

			if ($STATUS_BERKAS[$i] == "REVISI") {
				$jumlahRevisi += 1;
			}
		}

		if ($jumlahRevisi == 0) {
			$keterangan = "Permohonan Pemindahan telah diverifikasi oleh Arsiparis dan valid seluruhnya.";

			/********LOG*********/
			$kode = "VERIFIKASI_ARSIPARIS";
			$this->log($id, $kode, $keterangan);
			/********END LOG*********/
		} else {
			/********LOG*********/
			$kode = "REVISI_ARSIPARIS";
			$keterangan = "Terdapat revisi Daftar Pertelaan Arsip/Dokumen";
			$this->log($id, $kode, $keterangan);
			/********END LOG*********/

			$this->db->query("update pemindahan set status='REVISI_ARSIPARIS', revisi='$keterangan', 
        		updated_by='$this->USER_LOGIN_ID', updated_date=current_timestamp where pemindahan_id='$id' ");

			/***** ambil detil permohonan ******/
			$pemindahan = new Pemindahan();
			$pemindahan->selectByParamsMonitoring(array("A.PEMINDAHAN_ID::varchar" => $id));
			$pemindahan->firstRow();
			$PERUSAHAAN_ID = $pemindahan->getField("PERUSAHAAN_ID");
			$NOMOR = $pemindahan->getField("NOMOR");
			$PERIHAL = $pemindahan->getField("PERIHAL");
			$TANGGAL = $pemindahan->getField("TANGGAL");
			$CREATED_BY = $pemindahan->getField("CREATED_BY");

			/***** ambil data pegawai ******/
			$this->load->model("UserLogin");
			$user_login = new UserLogin();
			$user_login->selectByParamsMonitoring(array("A.USER_LOGIN_ID::varchar" => $CREATED_BY));
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
			$NOTIFIKASI_KETERANGAN = $PERIHAL . " Nomor : " . $NOMOR . " Tanggal : " . getFormattedDateView($TANGGAL);
			$NOTIFIKASI_LINK = "app/loadUrl/app/permohonan_pemindahan_add/?id=" . $id;
			$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
			$NOTIFIKASI_USER_GROUP = "PEGAWAI";
			$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
			$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
			$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

			$notifikasi_lonceng->insertNotifikasi(
				$NOTIFIKASI_KODE,
				$NOTIFIKASI_PRIMARY_ID,
				$NOTIFIKASI_NAMA,
				$NOTIFIKASI_KETERANGAN,
				$NOTIFIKASI_LINK,
				$NOTIFIKASI_PENERIMA,
				$NOTIFIKASI_USER_GROUP,
				$NOTIFIKASI_PENGIRIM,
				$NOTIFIKASI_PENGIRIM_NAMA,
				$NOTIFIKASI_PENGIRIM_JABATAN
			);
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
			if ($mail->Send()) {
				$STATUS_EMAIL = "TERKIRIM";
			} else {
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


		echo "BERHASIL|Permohonan Pemindahan berhasil diverikasi|" . $id;
	}

	function lokasi_simpan()
	{
		$this->load->library("crfs_protect");
		$csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->post('_crfs_7mj4R5iP')) && $this->IS_MOBILE == false) {
			exit();
		}

		$this->load->model("Pemindahan");
		$pemindahan_berkas = new Pemindahan();

		$id 							= $this->input->post("id");
		$PEMINDAHAN_ID 					= $this->input->post("PEMINDAHAN_ID");
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
		$ADA_HARDCOPY					= $this->input->post("ADA_HARDCOPY");
		$KETERANGAN						= setQuote($_POST['KETERANGAN']);

		/******UPLOAD DOKUMEN******/
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$FILE_DIR = "uploads/pemindahan/";

		$DOKUMEN = $_FILES['DOKUMEN'];
		$TEMP_DOKUMEN = $this->input->post('TEMP_DOKUMEN');
		$DOKUMEN_TEXT = isset($_POST['DOKUMEN_TEXT']) ? setQuote($_POST['DOKUMEN_TEXT']) : '';
		$extension = strtolower(getExtension($DOKUMEN['name']));
		$renameFile = "ARSIP_PID_" . $PEMINDAHAN_ID . "_ID_" . $id . "_" . md5(date("Ymdhis") . rand()) . "." . $extension;

		if ($file->uploadToDir('DOKUMEN', $FILE_DIR, $renameFile)) {
			$insertLinkSize = $file->uploadedSize;
			$insertLinkTipe = $file->uploadedExtension;
			$insertLinkFile = $renameFile;
			$insertNamaFile = setQuote($DOKUMEN['name']);

			// Ekstraksi teks berdasarkan format file
			if ($extension == 'pdf') {
				include 'vendor/autoload.php';
				$pdfParser = new \Smalot\PdfParser\Parser();
				$pdf = $pdfParser->parseFile($FILE_DIR . $renameFile);
				$text = $pdf->getText();
			} elseif (in_array($extension, ['png', 'jpg', 'jpeg'])) {
				// Ekstraksi teks dari gambar menggunakan Tesseract OCR
				include 'vendor/autoload.php';
				$tesseract = new \thiagoalessio\TesseractOCR\TesseractOCR($FILE_DIR . $renameFile);
				$text = $tesseract->run();
			} else {
				echo "GAGAL|Format file tidak didukung";
				return;
			}

			//Function untuk proses memecah isi
			function deteksiIsi($text)
			{
				$result = [
					'PERIHAL' => 'Tidak Ditemukan',
					'NOMOR' => 'Tidak Ditemukan',
					'LAMPIRAN' => 'Tidak Ditemukan',
					'TANDA_TANGAN' => 'Tidak Ditemukan',
				];

				// format teks
				$perihalPattern = '/Hal\s*:(.*)/';
				$nomorPattern = '/Nomor\s*[:\s]+([A-Z0-9\-\.\/\s]+(?:\/\s*\d{4})?)/';
				$lampiranPattern = '/Lampiran\s*:(.*)/';

				// Deteksi perihal
				if (preg_match($perihalPattern, $text, $matches)) {
					$result['PERIHAL'] = trim($matches[1]);
				}

				// Deteksi nomor
				if (preg_match($nomorPattern, $text, $matches)) {
					$result['NOMOR'] = trim($matches[1]);
				}

				// Deteksi lampiran
				if (preg_match($lampiranPattern, $text, $matches)) {
					$result['LAMPIRAN'] = trim($matches[1]);
				}

				// Deteksi tanda tangan
				// Mencari nama dengan format huruf kapital di setiap kata pada beberapa baris terakhir
				$lines = explode("\n", trim($text));
				$lastLines = array_slice($lines, -5);  // Cek 5 baris terakhir

				foreach ($lastLines as $line) {
					// Regex untuk mendeteksi nama yang terdiri dari dua kata atau lebih, dengan huruf kapital di awal setiap kata
					if (preg_match('/\b([A-Z][a-z]+(?:\s[A-Z][a-z]+)*(?:,\s*[A-Z]{2,})?)\b/', $line, $matches)) {
						$result['TANDA_TANGAN'] = trim($matches[1]);
						break;
					}
				}

				return $result;
			}


			// penggunaan dengan OCR
			$result 	= deteksiIsi($text);
			$PERIHAL 	= $result['PERIHAL'];
			$NOMOR 		= $result['NOMOR'];
			$LAMPIRAN 	= $result['LAMPIRAN'];
			$TANDA_TANGAN = $result['TANDA_TANGAN'];


			// Simpan teks yang diekstrak
			// $DOKUMEN_TEXT = setQuote($text);
			// echo 'SUKSES|'.$DOKUMEN_TEXT; exit;

		} else {
			if ($TEMP_DOKUMEN == "") {
				echo "GAGAL|Dokumen gagal diupload";
				return;
			} else {
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
		// $pemindahan_berkas->setField("DOKUMEN_TEXT", $DOKUMEN_TEXT);
		$pemindahan_berkas->setField("ADA_HARDCOPY", $ADA_HARDCOPY);
		$pemindahan_berkas->setField("STATUS", "SIMPAN");
		$pemindahan_berkas->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if ($pemindahan_berkas->updateBerkasSimpan()) {

			$STATUS_PEMINDAHAN = "VERIFIKASI_ARSIPARIS";

			/********SPLIT DOKUMEN OCR*********/
			$panjang_karakter = strlen($text);
			$split = round($panjang_karakter / 5000);

			$text_start = 0;
			$text_lenght = 5000;

			$pemindahan_berkas_ocr = new Pemindahan();
			$pemindahan_berkas_ocr->setField("PEMINDAHAN_BERKAS_ID", $id);
			$pemindahan_berkas_ocr->deleteBerkasOcr();  // HAPUS DULU

			for ($i = 0; $i <= $split; $i++) {
				// Ambil potongan teks
				$DOKUMEN_TEXT = substr($text, $text_start, $text_lenght);

				$pemindahan_berkas_ocr = new Pemindahan();
				$pemindahan_berkas_ocr->setField("PEMINDAHAN_BERKAS_ID", $id);
				$pemindahan_berkas_ocr->setField("DOKUMEN_TEXT", setQuote($DOKUMEN_TEXT));
				$pemindahan_berkas_ocr->setField("PERIHAL", setQuote($PERIHAL));
				$pemindahan_berkas_ocr->setField("NOMOR_SURAT", setQuote($NOMOR));
				$pemindahan_berkas_ocr->setField("LAMPIRAN", $LAMPIRAN);
				$pemindahan_berkas_ocr->setField("TANDA_TANGAN", $TANDA_TANGAN);
				$pemindahan_berkas_ocr->setField("CREATED_BY", $this->USER_LOGIN_ID);
				$pemindahan_berkas_ocr->insertBerkasOcr();
				unset($pemindahan_berkas_ocr);

				$text_start = $text_start + $text_lenght + 1;
			}

			/********END SPLIT DOKUMEN OCR*********/

			/********LOG*********/
			$kode = "SIMPAN";
			$keterangan = "Berhasil menyimpan berkas " . $KLASIFIKASI_KODE . " - " . $KLASIFIKASI_NAMA;
			$this->log($PEMINDAHAN_ID, $kode, $keterangan);
			/********END LOG*********/

			/************APABILA SUDAH SIMPAN SEMUA, UPDATE STATUS PEMINDAHAN************/
			$jumlahBelumSimpan = $pemindahan_berkas->getCountByParamsBerkas(
				array("A.PEMINDAHAN_ID::VARCHAR" => $PEMINDAHAN_ID),
				" AND NOT STATUS='SIMPAN'"
			);
			if ($jumlahBelumSimpan == 0) {
				/***** ambil detil permohonan ******/
				$pemindahan = new Pemindahan();
				$pemindahan->selectByParamsMonitoring(array("A.PEMINDAHAN_ID::varchar" => $PEMINDAHAN_ID));
				$pemindahan->firstRow();
				$PERUSAHAAN_ID = $pemindahan->getField("PERUSAHAAN_ID");
				$NOMOR = $pemindahan->getField("NOMOR");
				$PERIHAL = $pemindahan->getField("PERIHAL");
				$TANGGAL = $pemindahan->getField("TANGGAL");
				$CREATED_BY = $pemindahan->getField("CREATED_BY");
				$STATUS_PEMINDAHAN = $pemindahan->getField("STATUS");

				if ($STATUS_PEMINDAHAN != "SIMPAN") { //JIKA BELUM SIMPAN, KIRIMI EMAIL
					$this->db->query("update pemindahan set status='SIMPAN', updated_by='$this->USER_LOGIN_ID', 
					updated_date=current_timestamp where pemindahan_id='$PEMINDAHAN_ID' ");

					/********LOG*********/
					$kode = "SELESAI";
					$keterangan = "Proses Permohonan Pemindahan Arsip Inaktif telah selesai";
					$this->log($PEMINDAHAN_ID, $kode, $keterangan);
					/********END LOG*********/

					/***** ambil data pegawai ******/
					$this->load->model("UserLogin");
					$user_login = new UserLogin();
					$user_login->selectByParamsMonitoring(array("A.USER_LOGIN_ID::varchar" => $CREATED_BY));
					$user_login->firstRow();
					$PEGAWAI_ID = $user_login->getField("PEGAWAI_ID");
					$PEGAWAI_NAMA = $user_login->getField("NAMA_PEGAWAI");
					$PEGAWAI_JABATAN = $user_login->getField("JABATAN");
					$PEGAWAI_EMAIL = $user_login->getField("EMAIL");

					/********Notifikasi Lonceng*********/
					$this->load->library("NotifikasiLonceng");
					$notifikasi_lonceng = new NotifikasiLonceng();
					$NOTIFIKASI_KODE = "PEMINDAHAN_SELESAI";
					$NOTIFIKASI_PRIMARY_ID = $PEMINDAHAN_ID;
					$NOTIFIKASI_NAMA = "Permohonan Pemindahan Selesai";
					$NOTIFIKASI_KETERANGAN = $PERIHAL . " Nomor : " . $NOMOR . " Tanggal : " . getFormattedDateView($TANGGAL);
					$NOTIFIKASI_LINK = "app/loadUrl/app/permohonan_pemindahan_add/?id=" . $PEMINDAHAN_ID;
					$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
					$NOTIFIKASI_USER_GROUP = "PEGAWAI";
					$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
					$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
					$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

					$notifikasi_lonceng->insertNotifikasi(
						$NOTIFIKASI_KODE,
						$NOTIFIKASI_PRIMARY_ID,
						$NOTIFIKASI_NAMA,
						$NOTIFIKASI_KETERANGAN,
						$NOTIFIKASI_LINK,
						$NOTIFIKASI_PENERIMA,
						$NOTIFIKASI_USER_GROUP,
						$NOTIFIKASI_PENGIRIM,
						$NOTIFIKASI_PENGIRIM_NAMA,
						$NOTIFIKASI_PENGIRIM_JABATAN
					);
					/********END Notifikasi Lonceng*********/

					/********Notifikasi Email*********/
					$this->load->library("KMail");
					$mail = new KMail();
					$SUBJECT = "Permohonan Pemindahan dan Penyimpanan Arsip Inaktif Selesai";
					$mail->Subject = $SUBJECT;
					$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

					$KONTEN = "email/selesai_permohonan_pemindahan";
					$arrData = array("reqParse1" => $PEMINDAHAN_ID);
					$body = $this->load->view($KONTEN, $arrData, true);
					$mail->MsgHTML($body);
					if ($mail->Send()) {
						$STATUS_EMAIL = "TERKIRIM";
					} else {
						$STATUS_EMAIL = "TIDAK_TERKIRIM";
					}

					$this->load->model("LogEmail");
					$log_email = new LogEmail();
					$log_email->setField("KODE", $NOTIFIKASI_KODE);
					$log_email->setField("PRIMARY_ID", $PEMINDAHAN_ID);
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

			echo "BERHASIL|Berhasil menyimpan data|" . $STATUS_PEMINDAHAN;
		} else {
			echo "GAGAL|Gagal menyimpan data";
		}
	}


	function aktif()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$pemindahan->setField("PEMINDAHAN_ID", $reqId);
		$pemindahan->setField("FIELD", "STATUS");
		$pemindahan->setField("FIELD_VALUE", "AKTIF");
		$pemindahan->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if ($pemindahan->updateByField()) {
			echo "Data berhasil diaktifkan";
		} else {
			echo "Data gagal diaktifkan";
		}
	}

	function non_aktif()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$pemindahan->setField("PEMINDAHAN_ID", $reqId);
		$pemindahan->setField("FIELD", "STATUS");
		$pemindahan->setField("FIELD_VALUE", "TIDAK_AKTIF");
		$pemindahan->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if ($pemindahan->updateByField()) {
			echo "Data berhasil dinonaktifkan";
		} else {
			echo "Data gagal dinonaktifkan";
		}
	}


	function posting()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$pemindahan->setField("PEMINDAHAN_ID", $reqId);
		$pemindahan->setField("FIELD", "STATUS");
		$pemindahan->setField("FIELD_VALUE", "POSTING");
		$pemindahan->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if ($pemindahan->updateByField()) {
			//UPDATE PERMOHONAN_UPROVAL, SET KE BELUM_APPROVE (KEMUNGKINAN POSTING ULANG KARENA REVISI)
			$pemindahan_approval = new Pemindahan();
			$pemindahan_approval->setField("PEMINDAHAN_ID", $reqId);
			$pemindahan_approval->setField("FIELD", "STATUS");
			$pemindahan_approval->setField("FIELD_VALUE", "BELUM_APPROVE");
			$pemindahan_approval->setField("UPDATED_BY", $this->USER_LOGIN_ID);
			if ($pemindahan_approval->updateByFieldApprovalByPermohonanId()) {
				/***** ambil detil permohonan ******/
				$pemindahan = new Pemindahan();
				$pemindahan->selectByParamsMonitoring(array("A.PEMINDAHAN_ID::varchar" => $reqId));
				$pemindahan->firstRow();
				$PERUSAHAAN_ID = $pemindahan->getField("PERUSAHAAN_ID");
				$NOMOR = $pemindahan->getField("NOMOR");
				$PERIHAL = $pemindahan->getField("PERIHAL");
				$TANGGAL = $pemindahan->getField("TANGGAL");

				/***** ambil approval urut ke-1 ******/
				$pemindahan_approval = new Pemindahan();
				$pemindahan_approval->selectByParamsApprovalEmail(array("A.PEMINDAHAN_ID::varchar" => $reqId, "A.URUT" => "1"));
				$pemindahan_approval->firstRow();
				$PEMINDAHAN_APPROVAL_ID = $pemindahan_approval->getField("PEMINDAHAN_APPROVAL_ID");
				$PEGAWAI_ID = $pemindahan_approval->getField("PEGAWAI_ID");
				$PEGAWAI_NAMA = $pemindahan_approval->getField("NAMA");
				$PEGAWAI_JABATAN = $pemindahan_approval->getField("JABATAN");
				$PEGAWAI_EMAIL = $pemindahan_approval->getField("EMAIL");

				/********LOG*********/
				$kode = "POSTING";
				$keterangan = "Memposting Permohonan Pemindahan Arsip Inaktif kepada Bapak/Ibu " . $PEGAWAI_NAMA . " (" . $PEGAWAI_JABATAN . ")";
				$this->log($reqId, $kode, $keterangan);
				/********END LOG*********/

				/********Notifikasi Lonceng*********/
				$this->load->library("NotifikasiLonceng");
				$notifikasi_lonceng = new NotifikasiLonceng();
				$NOTIFIKASI_KODE = "PEMINDAHAN_POSTING";
				$NOTIFIKASI_PRIMARY_ID = $reqId;
				$NOTIFIKASI_NAMA = "Permohonan Approval";
				$NOTIFIKASI_KETERANGAN = $PERIHAL . " Nomor : " . $NOMOR . " Tanggal : " . getFormattedDateView($TANGGAL);
				$NOTIFIKASI_LINK = "app/loadUrl/app/approval_permohonan_pemindahan_detil/?id=" . $PEMINDAHAN_APPROVAL_ID;
				$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
				$NOTIFIKASI_USER_GROUP = "PEGAWAI";
				$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
				$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
				$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

				$notifikasi_lonceng->insertNotifikasi(
					$NOTIFIKASI_KODE,
					$NOTIFIKASI_PRIMARY_ID,
					$NOTIFIKASI_NAMA,
					$NOTIFIKASI_KETERANGAN,
					$NOTIFIKASI_LINK,
					$NOTIFIKASI_PENERIMA,
					$NOTIFIKASI_USER_GROUP,
					$NOTIFIKASI_PENGIRIM,
					$NOTIFIKASI_PENGIRIM_NAMA,
					$NOTIFIKASI_PENGIRIM_JABATAN
				);
				/********END Notifikasi Lonceng*********/

				/********Notifikasi Email*********/
				$this->load->library("KMail");
				$mail = new KMail();
				$SUBJECT = "Approval/Persetujuan Permohonan Pemindahan dan Penyimpanan Arsip Inaktif";
				$mail->Subject = $SUBJECT;
				$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

				$KONTEN = "email/approval_permohonan_pemindahan";
				$arrData = array("reqParse1" => $PEMINDAHAN_APPROVAL_ID);
				$body = $this->load->view($KONTEN, $arrData, true);
				$mail->MsgHTML($body);
				if ($mail->Send()) {
					$STATUS_EMAIL = "TERKIRIM";
				} else {
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

				echo "Berhasil Memposting Permohonan Pemindahan Arsip Inaktif";
			} else {
				echo "Gagal Memposting Permohonan Pemindahan Arsip Inaktif";
			}
		} else {
			echo "Gagal Memposting Permohonan Pemindahan Arsip Inaktif";
		}
	}

	function approval()
	{
		$this->load->model("Pemindahan");

		$id = $this->input->get("id");
		$PEMINDAHAN_ID = $this->input->get("PEMINDAHAN_ID");
		$URUT = $this->input->get("URUT");

		//UPDATE PERMOHONAN_UPROVAL
		$pemindahan_approval = new Pemindahan();
		$pemindahan_approval->setField("PEMINDAHAN_APPROVAL_ID", $id);
		$pemindahan_approval->setField("STATUS", "APPROVE");
		$pemindahan_approval->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if ($pemindahan_approval->setujuiPermohonanApproval()) {
			//CEK APAKAH MASIH ADA YG BELUM APPROVE
			$pemindahan_approval = new Pemindahan();
			$adaBelumApprove = $pemindahan_approval->getCountByParamsApproval(array("PEMINDAHAN_ID" => $PEMINDAHAN_ID, "STATUS" => "BELUM_APPROVE"));

			if ($adaBelumApprove > 0) { //JIKA ADA YG BELUM APPROVE, MAKA POSTING KE APPROVAL SELANJUTNYA
				/***** ambil detil permohonan ******/
				$pemindahan = new Pemindahan();
				$pemindahan->selectByParamsMonitoring(array("A.PEMINDAHAN_ID::varchar" => $PEMINDAHAN_ID));
				$pemindahan->firstRow();
				$PERUSAHAAN_ID = $pemindahan->getField("PERUSAHAAN_ID");
				$NOMOR = $pemindahan->getField("NOMOR");
				$PERIHAL = $pemindahan->getField("PERIHAL");
				$TANGGAL = $pemindahan->getField("TANGGAL");

				/***** ambil approval urut selanjutnya ******/
				$URUT += 1;
				$pemindahan_approval = new Pemindahan();
				$pemindahan_approval->selectByParamsApprovalEmail(array("A.PEMINDAHAN_ID::varchar" => $PEMINDAHAN_ID, "A.URUT" => $URUT));
				$pemindahan_approval->firstRow();
				$PEMINDAHAN_APPROVAL_ID = $pemindahan_approval->getField("PEMINDAHAN_APPROVAL_ID");
				$PEGAWAI_ID = $pemindahan_approval->getField("PEGAWAI_ID");
				$PEGAWAI_NAMA = $pemindahan_approval->getField("NAMA");
				$PEGAWAI_JABATAN = $pemindahan_approval->getField("JABATAN");
				$PEGAWAI_EMAIL = $pemindahan_approval->getField("EMAIL");

				/********LOG*********/
				$kode = "APPROVE";
				$keterangan = "Menyetujui & memposting Permohonan Pemindahan Arsip Inaktif kepada Bapak/Ibu " . $PEGAWAI_NAMA . " (" . $PEGAWAI_JABATAN . ")";
				$this->log($PEMINDAHAN_ID, $kode, $keterangan);
				/********END LOG*********/

				/********Notifikasi Lonceng*********/
				$this->load->library("NotifikasiLonceng");
				$notifikasi_lonceng = new NotifikasiLonceng();
				$NOTIFIKASI_KODE = "PEMINDAHAN_POSTING";
				$NOTIFIKASI_PRIMARY_ID = $PEMINDAHAN_ID;
				$NOTIFIKASI_NAMA = "Permohonan Approval";
				$NOTIFIKASI_KETERANGAN = $PERIHAL . " Nomor : " . $NOMOR . " Tanggal : " . getFormattedDateView($TANGGAL);
				$NOTIFIKASI_LINK = "app/loadUrl/app/approval_permohonan_pemindahan_detil/?id=" . $PEMINDAHAN_APPROVAL_ID;
				$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
				$NOTIFIKASI_USER_GROUP = "PEGAWAI";
				$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
				$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
				$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

				$notifikasi_lonceng->insertNotifikasi(
					$NOTIFIKASI_KODE,
					$NOTIFIKASI_PRIMARY_ID,
					$NOTIFIKASI_NAMA,
					$NOTIFIKASI_KETERANGAN,
					$NOTIFIKASI_LINK,
					$NOTIFIKASI_PENERIMA,
					$NOTIFIKASI_USER_GROUP,
					$NOTIFIKASI_PENGIRIM,
					$NOTIFIKASI_PENGIRIM_NAMA,
					$NOTIFIKASI_PENGIRIM_JABATAN
				);
				/********END Notifikasi Lonceng*********/

				/********Notifikasi Email*********/
				$this->load->library("KMail");
				$mail = new KMail();
				$SUBJECT = "Approval/Persetujuan Permohonan Pemindahan dan Penyimpanan Arsip Inaktif";
				$mail->Subject = $SUBJECT;
				$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

				$KONTEN = "email/approval_permohonan_pemindahan";
				$arrData = array("reqParse1" => $PEMINDAHAN_APPROVAL_ID);
				$body = $this->load->view($KONTEN, $arrData, true);
				$mail->MsgHTML($body);
				if ($mail->Send()) {
					$STATUS_EMAIL = "TERKIRIM";
				} else {
					$STATUS_EMAIL = "TIDAK_TERKIRIM";
				}

				$this->load->model("LogEmail");
				$log_email = new LogEmail();
				$log_email->setField("KODE", $NOTIFIKASI_KODE);
				$log_email->setField("PRIMARY_ID", $PEMINDAHAN_ID);
				$log_email->setField("JUDUL", $SUBJECT);
				$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID);
				$log_email->setField("EMAIL", $PEGAWAI_EMAIL);
				$log_email->setField("KONTEN", $KONTEN);
				$log_email->setField("STATUS", $STATUS_EMAIL);
				$log_email->insert();
				/********END Notifikasi Email*********/

				echo "Berhasil Menyetujui & Memposting Permohonan Pemindahan Arsip Inaktif";
			} else {
				//JIKA SUDAH APPROVE SEMUA, KIRIM KE TUJUAN
				$pemindahan = new Pemindahan();
				$pemindahan->setField("PEMINDAHAN_ID", $PEMINDAHAN_ID);
				$pemindahan->setField("FIELD", "STATUS");
				$pemindahan->setField("FIELD_VALUE", "TERKIRIM");
				$pemindahan->setField("UPDATED_BY", $this->USER_LOGIN_ID);
				if ($pemindahan->updateByField()) {
					/********LOG*********/
					$kode = "APPROVE";
					$keterangan = "Menyetujui & mengirim Permohonan Pemindahan Arsip Inaktif kepada Unit Kearsipan";
					$this->log($PEMINDAHAN_ID, $kode, $keterangan);
					/********END LOG*********/

					/***** ambil detil permohonan ******/
					$pemindahan = new Pemindahan();
					$pemindahan->selectByParamsMonitoring(array("A.PEMINDAHAN_ID::varchar" => $PEMINDAHAN_ID));
					$pemindahan->firstRow();
					$PERUSAHAAN_ID = $pemindahan->getField("PERUSAHAAN_ID");
					$NOMOR = $pemindahan->getField("NOMOR");
					$PERIHAL = $pemindahan->getField("PERIHAL");
					$TANGGAL = $pemindahan->getField("TANGGAL");

					/***** kirim ke tujuan/tembusan ******/
					$pemindahan_tujuan = new Pemindahan();
					$pemindahan_tujuan->selectByParamsTujuanEmail(array("A.PEMINDAHAN_ID::varchar" => $PEMINDAHAN_ID));
					while ($pemindahan_tujuan->nextRow()) {
						$PEMINDAHAN_TUJUAN_ID = $pemindahan_tujuan->getField("PEMINDAHAN_TUJUAN_ID");
						$PEGAWAI_ID = $pemindahan_tujuan->getField("PEGAWAI_ID");
						$PEGAWAI_NAMA = $pemindahan_tujuan->getField("NAMA");
						$PEGAWAI_JABATAN = $pemindahan_tujuan->getField("JABATAN");
						$PEGAWAI_EMAIL = $pemindahan_tujuan->getField("EMAIL");

						//UPDATE PERMOHONAN_TUJUAN
						$pemindahan_tujuan_disposisi = new Pemindahan();
						$pemindahan_tujuan_disposisi->setField("PEMINDAHAN_TUJUAN_ID", $PEMINDAHAN_TUJUAN_ID);
						$pemindahan_tujuan_disposisi->setField("PERUSAHAAN_ID_DISPOSISI", $this->PERUSAHAAN_ID);
						$pemindahan_tujuan_disposisi->setField("CABANG_ID_DISPOSISI", $this->CABANG_ID);
						$pemindahan_tujuan_disposisi->setField("SATUAN_KERJA_ID_DISPOSISI", $this->SATUAN_KERJA_ID);
						$pemindahan_tujuan_disposisi->setField("PEGAWAI_ID_DISPOSISI", $this->PEGAWAI_ID);
						$pemindahan_tujuan_disposisi->setField("KODE_DISPOSISI", $this->KODE_PEGAWAI);
						$pemindahan_tujuan_disposisi->setField("NAMA_DISPOSISI", $this->NAMA_PEGAWAI);
						$pemindahan_tujuan_disposisi->setField("JABATAN_DISPOSISI", $this->JABATAN);
						$pemindahan_tujuan_disposisi->setField("TERDISPOSISI", "TIDAK");
						$pemindahan_tujuan_disposisi->setField("TERBACA", "TIDAK");
						$pemindahan_tujuan_disposisi->setField("PESAN_DISPOSISI", setQuote($PESAN_DISPOSISI));
						$pemindahan_tujuan_disposisi->setField("PEMINDAHAN_TUJUAN_ID_PARENT", coalesce($PEMINDAHAN_TUJUAN_ID_PARENT, $PEMINDAHAN_TUJUAN_ID));
						$pemindahan_tujuan_disposisi->setField("UPDATED_BY", $this->USER_LOGIN_ID);
						$pemindahan_tujuan_disposisi->updateTujuanDisposisi();

						/********Notifikasi Lonceng*********/
						$this->load->library("NotifikasiLonceng");
						$notifikasi_lonceng = new NotifikasiLonceng();
						$NOTIFIKASI_KODE = "PEMINDAHAN_TERKIRIM";
						$NOTIFIKASI_PRIMARY_ID = $PEMINDAHAN_ID;
						$NOTIFIKASI_NAMA = "Permohonan Pemindahan";
						$NOTIFIKASI_KETERANGAN = $PERIHAL . " Nomor : " . $NOMOR . " Tanggal : " . getFormattedDateView($TANGGAL);
						$NOTIFIKASI_LINK = "app/loadUrl/app/inbox_permohonan_pemindahan_detil/?id=" . $PEMINDAHAN_TUJUAN_ID;
						$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
						$NOTIFIKASI_USER_GROUP = "PEGAWAI";
						$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
						$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
						$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

						$notifikasi_lonceng->insertNotifikasi(
							$NOTIFIKASI_KODE,
							$NOTIFIKASI_PRIMARY_ID,
							$NOTIFIKASI_NAMA,
							$NOTIFIKASI_KETERANGAN,
							$NOTIFIKASI_LINK,
							$NOTIFIKASI_PENERIMA,
							$NOTIFIKASI_USER_GROUP,
							$NOTIFIKASI_PENGIRIM,
							$NOTIFIKASI_PENGIRIM_NAMA,
							$NOTIFIKASI_PENGIRIM_JABATAN
						);
						/********END Notifikasi Lonceng*********/

						/********Notifikasi Email*********/
						$this->load->library("KMail");
						$mail = new KMail();
						$SUBJECT = "Permohonan Pemindahan dan Penyimpanan Arsip Inaktif";
						$mail->Subject = $SUBJECT;
						$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

						$KONTEN = "email/inbox_permohonan_pemindahan";
						$arrData = array("reqParse1" => $PEMINDAHAN_TUJUAN_ID);
						$body = $this->load->view($KONTEN, $arrData, true);
						$mail->MsgHTML($body);
						if ($mail->Send()) {
							$STATUS_EMAIL = "TERKIRIM";
						} else {
							$STATUS_EMAIL = "TIDAK_TERKIRIM";
						}

						$this->load->model("LogEmail");
						$log_email = new LogEmail();
						$log_email->setField("KODE", $NOTIFIKASI_KODE);
						$log_email->setField("PRIMARY_ID", $PEMINDAHAN_ID);
						$log_email->setField("JUDUL", $SUBJECT);
						$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID);
						$log_email->setField("EMAIL", $PEGAWAI_EMAIL);
						$log_email->setField("KONTEN", $KONTEN);
						$log_email->setField("STATUS", $STATUS_EMAIL);
						$log_email->insert();
						/********END Notifikasi Email*********/
					}

					echo "Berhasil Menyetujui & Mengirim Permohonan Pemindahan Arsip Inaktif kepada Unit Kearsipan";
				} else {
					echo "GAGAL|Gagal menyetujui permohonan";
				}
			}
		} else {
			echo "GAGAL|Gagal menyetujui permohonan";
		}
	}

	function revisi()
	{
		$this->load->library("crfs_protect");
		$csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->post('_crfs_7mj4R5iP')) && $this->IS_MOBILE == false) {
			exit();
		}

		$this->load->model("Pemindahan");

		$id = $this->input->post("id");
		$PEMINDAHAN_ID = $this->input->post("PEMINDAHAN_ID");
		$REVISI = setQuote($_POST['REVISI']);

		if (trim($REVISI) == "") {
			echo "GAGAL|Ketikkan Catatan Revisi terlebih dahulu!";
		} else {
			//UPDATE PERMOHONAN_UPROVAL
			$pemindahan_approval = new Pemindahan();
			$pemindahan_approval->setField("PEMINDAHAN_APPROVAL_ID", $id);
			$pemindahan_approval->setField("STATUS", "REVISI");
			$pemindahan_approval->setField("REVISI", $REVISI);
			$pemindahan_approval->setField("UPDATED_BY", $this->USER_LOGIN_ID);
			if ($pemindahan_approval->revisiPermohonanApproval()) {

				//UPDATE PERMOHONAN
				$pemindahan = new Pemindahan();
				$pemindahan->setField("PEMINDAHAN_ID", $PEMINDAHAN_ID);
				$pemindahan->setField("STATUS", "REVISI");
				$pemindahan->setField("REVISI", $REVISI);
				$pemindahan->setField("UPDATED_BY", $this->USER_LOGIN_ID);
				if ($pemindahan->revisiPermohonan()) {

					/********LOG*********/
					$kode = "REVISI";
					$keterangan = "Revisi, dengan catatan sebagai berikut : " . $REVISI;
					$this->log($PEMINDAHAN_ID, $kode, $keterangan);
					/********END LOG*********/

					/***** ambil detil permohonan ******/
					$pemindahan = new Pemindahan();
					$pemindahan->selectByParamsMonitoring(array("A.PEMINDAHAN_ID::varchar" => $PEMINDAHAN_ID));
					$pemindahan->firstRow();
					$PERUSAHAAN_ID = $pemindahan->getField("PERUSAHAAN_ID");
					$NOMOR = $pemindahan->getField("NOMOR");
					$PERIHAL = $pemindahan->getField("PERIHAL");
					$TANGGAL = $pemindahan->getField("TANGGAL");
					$CREATED_BY = $pemindahan->getField("CREATED_BY");

					/***** ambil data pegawai ******/
					$this->load->model("UserLogin");
					$user_login = new UserLogin();
					$user_login->selectByParamsMonitoring(array("A.USER_LOGIN_ID::varchar" => $CREATED_BY));
					$user_login->firstRow();
					$PEGAWAI_ID = $user_login->getField("PEGAWAI_ID");
					$PEGAWAI_NAMA = $user_login->getField("NAMA_PEGAWAI");
					$PEGAWAI_JABATAN = $user_login->getField("JABATAN");
					$PEGAWAI_EMAIL = $user_login->getField("EMAIL");

					/********Notifikasi Lonceng*********/
					$this->load->library("NotifikasiLonceng");
					$notifikasi_lonceng = new NotifikasiLonceng();
					$NOTIFIKASI_KODE = "PEMINDAHAN_REVISI";
					$NOTIFIKASI_PRIMARY_ID = $PEMINDAHAN_ID;
					$NOTIFIKASI_NAMA = "Revisi Permohonan Pemindahan";
					$NOTIFIKASI_KETERANGAN = $PERIHAL . " Nomor : " . $NOMOR . " Tanggal : " . getFormattedDateView($TANGGAL);
					$NOTIFIKASI_LINK = "app/loadUrl/app/permohonan_pemindahan_add/?id=" . $PEMINDAHAN_ID;
					$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
					$NOTIFIKASI_USER_GROUP = "PEGAWAI";
					$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
					$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
					$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

					$notifikasi_lonceng->insertNotifikasi(
						$NOTIFIKASI_KODE,
						$NOTIFIKASI_PRIMARY_ID,
						$NOTIFIKASI_NAMA,
						$NOTIFIKASI_KETERANGAN,
						$NOTIFIKASI_LINK,
						$NOTIFIKASI_PENERIMA,
						$NOTIFIKASI_USER_GROUP,
						$NOTIFIKASI_PENGIRIM,
						$NOTIFIKASI_PENGIRIM_NAMA,
						$NOTIFIKASI_PENGIRIM_JABATAN
					);
					/********END Notifikasi Lonceng*********/

					/********Notifikasi Email*********/
					$this->load->library("KMail");
					$mail = new KMail();
					$SUBJECT = "Revisi Permohonan Pemindahan dan Penyimpanan Arsip Inaktif";
					$mail->Subject = $SUBJECT;
					$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

					$KONTEN = "email/revisi_permohonan_pemindahan";
					$arrData = array("reqParse1" => $id);
					$body = $this->load->view($KONTEN, $arrData, true);
					$mail->MsgHTML($body);
					if ($mail->Send()) {
						$STATUS_EMAIL = "TERKIRIM";
					} else {
						$STATUS_EMAIL = "TIDAK_TERKIRIM";
					}

					$this->load->model("LogEmail");
					$log_email = new LogEmail();
					$log_email->setField("KODE", $NOTIFIKASI_KODE);
					$log_email->setField("PRIMARY_ID", $PEMINDAHAN_ID);
					$log_email->setField("JUDUL", $SUBJECT);
					$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID);
					$log_email->setField("EMAIL", $PEGAWAI_EMAIL);
					$log_email->setField("KONTEN", $KONTEN);
					$log_email->setField("STATUS", $STATUS_EMAIL);
					$log_email->insert();
					/********END Notifikasi Email*********/

					echo "BERHASIL|Berhasil merevisi permohonan";
				} else {
					echo "GAGAL|Gagal merevisi permohonan";
				}
			} else {
				echo "GAGAL|Gagal merevisi permohonan";
			}
		}
	}


	function disposisi()
	{
		$this->load->library("crfs_protect");
		$csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->post('_crfs_7mj4R5iP')) && $this->IS_MOBILE == false) {
			exit();
		}

		$this->load->model("Pemindahan");

		$id 	= $this->input->post("id");
		$mode 	= $this->input->post("mode");

		$PEMINDAHAN_ID 		= $this->input->post("PEMINDAHAN_ID");
		$PESAN_DISPOSISI	= setQuote($_POST['PESAN_DISPOSISI']);

		$PEMINDAHAN_TUJUAN_ID_DISPOSISI	= $this->input->post("PEMINDAHAN_TUJUAN_ID_DISPOSISI");
		$PERUSAHAAN_ID_DISPOSISI		= $this->input->post("PERUSAHAAN_ID_DISPOSISI");
		$CABANG_ID_DISPOSISI			= $this->input->post("CABANG_ID_DISPOSISI");
		$SATUAN_KERJA_ID_DISPOSISI		= $this->input->post("SATUAN_KERJA_ID_DISPOSISI");
		$JENIS_DISPOSISI				= $this->input->post("JENIS_DISPOSISI");
		$PEGAWAI_ID_DISPOSISI			= $this->input->post("PEGAWAI_ID_DISPOSISI");
		$KODE_DISPOSISI					= $this->input->post("KODE_DISPOSISI");
		$NAMA_DISPOSISI					= $this->input->post("NAMA_DISPOSISI");
		$JABATAN_DISPOSISI				= $this->input->post("JABATAN_DISPOSISI");

		/******VALIDASI*******/
		if (count(array_filter($PEGAWAI_ID_DISPOSISI)) <= 0) {
			echo "GAGAL|Tujuan Disposisi kosong";
			return;
		}

		if (trim($PESAN_DISPOSISI) == "") {
			echo "GAGAL|Catatan Disposisi kosong";
			return;
		}

		for ($i = 0; $i < count($PEGAWAI_ID_DISPOSISI); $i++) {
			$pemindahan_tujuan = new Pemindahan();
			$adaDisposisi = $pemindahan_tujuan->getCountByParamsTujuan(array("A.PEMINDAHAN_ID" => $PEMINDAHAN_ID, "A.PEGAWAI_ID" => $PEGAWAI_ID_DISPOSISI[$i]));

			if ($adaDisposisi > 0) {
				echo "GAGAL|Terdapat Pegawai pada kolom <b>Tujuan Disposisi</b> telah dikirimkan Permohonan Pemindahan sebelumnya. Silahkan cek pada Log Tujuan/Disposisi";
				return;
			}
		}
		/******END VALIDASI*******/

		$arrNamaDisposisi = "";
		for ($i = 0; $i < count($PEGAWAI_ID_DISPOSISI); $i++) {
			$pemindahan_tujuan = new Pemindahan();
			$pemindahan_tujuan->setField("PEMINDAHAN_TUJUAN_ID", $PEMINDAHAN_TUJUAN_ID_DISPOSISI[$i]);
			$pemindahan_tujuan->setField("PEMINDAHAN_TUJUAN_ID_PARENT", $id);
			$pemindahan_tujuan->setField("PEMINDAHAN_ID", $PEMINDAHAN_ID);
			$pemindahan_tujuan->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID_DISPOSISI[$i]);
			$pemindahan_tujuan->setField("CABANG_ID", $CABANG_ID_DISPOSISI[$i]);
			$pemindahan_tujuan->setField("SATUAN_KERJA_ID", $SATUAN_KERJA_ID_DISPOSISI[$i]);
			$pemindahan_tujuan->setField("PEGAWAI_ID", $PEGAWAI_ID_DISPOSISI[$i]);
			$pemindahan_tujuan->setField("KODE", $KODE_DISPOSISI[$i]);
			$pemindahan_tujuan->setField("NAMA", setQuote($NAMA_DISPOSISI[$i]));
			$pemindahan_tujuan->setField("JABATAN", $JABATAN_DISPOSISI[$i]);
			$pemindahan_tujuan->setField("JENIS", "DISPOSISI");
			$pemindahan_tujuan->setField("TERDISPOSISI", "TIDAK");
			$pemindahan_tujuan->setField("TERBACA", "TIDAK");
			$pemindahan_tujuan->setField("PESAN_DISPOSISI", $PESAN_DISPOSISI);
			$pemindahan_tujuan->setField("PEGAWAI_ID_DISPOSISI", $this->PEGAWAI_ID);
			$pemindahan_tujuan->setField("KODE_DISPOSISI", $this->KODE_PEGAWAI);
			$pemindahan_tujuan->setField("NAMA_DISPOSISI", $this->NAMA_PEGAWAI);
			$pemindahan_tujuan->setField("JABATAN_DISPOSISI", $this->JABATAN);
			$pemindahan_tujuan->setField("PERUSAHAAN_ID_DISPOSISI", $this->PERUSAHAAN_ID);
			$pemindahan_tujuan->setField("CABANG_ID_DISPOSISI", $this->CABANG_ID);
			$pemindahan_tujuan->setField("SATUAN_KERJA_ID_DISPOSISI", $this->SATUAN_KERJA_ID);
			$pemindahan_tujuan->setField("CREATED_BY", $this->USER_LOGIN_ID);
			if ($pemindahan_tujuan->insertDisposisi()) {
				$PEMINDAHAN_TUJUAN_ID = $pemindahan_tujuan->id;
				/***** ambil detil permohonan ******/
				$pemindahan = new Pemindahan();
				$pemindahan->selectByParamsMonitoring(array("A.PEMINDAHAN_ID::varchar" => $PEMINDAHAN_ID));
				$pemindahan->firstRow();
				$PERUSAHAAN_ID = $pemindahan->getField("PERUSAHAAN_ID");
				$NOMOR = $pemindahan->getField("NOMOR");
				$PERIHAL = $pemindahan->getField("PERIHAL");
				$TANGGAL = $pemindahan->getField("TANGGAL");

				/***** kirim ke tujuan/tembusan ******/
				$pemindahan_tujuan = new Pemindahan();
				$pemindahan_tujuan->selectByParamsTujuanEmail(array("A.PEMINDAHAN_TUJUAN_ID::varchar" => $PEMINDAHAN_TUJUAN_ID));
				while ($pemindahan_tujuan->nextRow()) {
					$PEGAWAI_ID = $pemindahan_tujuan->getField("PEGAWAI_ID");
					$PEGAWAI_NAMA = $pemindahan_tujuan->getField("NAMA");
					$PEGAWAI_JABATAN = $pemindahan_tujuan->getField("JABATAN");
					$PEGAWAI_EMAIL = $pemindahan_tujuan->getField("EMAIL");

					/********Notifikasi Lonceng*********/
					$this->load->library("NotifikasiLonceng");
					$notifikasi_lonceng = new NotifikasiLonceng();
					$NOTIFIKASI_KODE = "PEMINDAHAN_DISPOSISI";
					$NOTIFIKASI_PRIMARY_ID = $PEMINDAHAN_ID;
					$NOTIFIKASI_NAMA = "Permohonan Pemindahan";
					$NOTIFIKASI_KETERANGAN = $PERIHAL . " Nomor : " . $NOMOR . " Tanggal : " . getFormattedDateView($TANGGAL);
					$NOTIFIKASI_LINK = "app/loadUrl/app/inbox_permohonan_pemindahan_detil/?id=" . $PEMINDAHAN_TUJUAN_ID;
					$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
					$NOTIFIKASI_USER_GROUP = "PEGAWAI";
					$NOTIFIKASI_PENGIRIM = $this->USER_LOGIN_ID;
					$NOTIFIKASI_PENGIRIM_NAMA = $this->NAMA_PEGAWAI;
					$NOTIFIKASI_PENGIRIM_JABATAN = $this->JABATAN;

					$notifikasi_lonceng->insertNotifikasi(
						$NOTIFIKASI_KODE,
						$NOTIFIKASI_PRIMARY_ID,
						$NOTIFIKASI_NAMA,
						$NOTIFIKASI_KETERANGAN,
						$NOTIFIKASI_LINK,
						$NOTIFIKASI_PENERIMA,
						$NOTIFIKASI_USER_GROUP,
						$NOTIFIKASI_PENGIRIM,
						$NOTIFIKASI_PENGIRIM_NAMA,
						$NOTIFIKASI_PENGIRIM_JABATAN
					);
					/********END Notifikasi Lonceng*********/

					/********Notifikasi Email*********/
					$this->load->library("KMail");
					$mail = new KMail();
					$SUBJECT = "Permohonan Pemindahan dan Penyimpanan Arsip Inaktif";
					$mail->Subject = $SUBJECT;
					$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

					$KONTEN = "email/inbox_permohonan_pemindahan";
					$arrData = array("reqParse1" => $PEMINDAHAN_TUJUAN_ID);
					$body = $this->load->view($KONTEN, $arrData, true);
					$mail->MsgHTML($body);
					if ($mail->Send()) {
						$STATUS_EMAIL = "TERKIRIM";
					} else {
						$STATUS_EMAIL = "TIDAK_TERKIRIM";
					}

					$this->load->model("LogEmail");
					$log_email = new LogEmail();
					$log_email->setField("KODE", $NOTIFIKASI_KODE);
					$log_email->setField("PRIMARY_ID", $PEMINDAHAN_ID);
					$log_email->setField("JUDUL", $SUBJECT);
					$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID);
					$log_email->setField("EMAIL", $PEGAWAI_EMAIL);
					$log_email->setField("KONTEN", $KONTEN);
					$log_email->setField("STATUS", $STATUS_EMAIL);
					$log_email->insert();
					/********END Notifikasi Email*********/
				}
			}

			$arrNamaDisposisi .= setQuote($NAMA_DISPOSISI[$i]) . ", ";
		}

		$pemindahan_tujuan = new Pemindahan();
		$pemindahan_tujuan->setField("PEMINDAHAN_TUJUAN_ID", $id);
		$pemindahan_tujuan->setField("TERDISPOSISI", "YA");
		if ($pemindahan_tujuan->updateDisposisi()) {
			/********LOG*********/
			$kode = "DISPOSISI";
			$keterangan = "Mendisposisikan Permohonan Pemindahan Arsip Inaktif kepada " . $arrNamaDisposisi;
			$this->log($PEMINDAHAN_ID, $kode, $keterangan);
			/********END LOG*********/

			echo "BERHASIL|Berhasil mendisposisikan permohonan pemindahan|" . $id;
		} else {
			echo "GAGAL|Gagal mendisposisikan permohonan pemindahan";
		}
	}


	function log($id, $kode, $keterangan)
	{
		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$pemindahan->setField("PEMINDAHAN_ID", $id);
		$pemindahan->setField("KODE", $kode);
		$pemindahan->setField("KETERANGAN", setQuote($keterangan));
		$pemindahan->setField("PEGAWAI_ID", $this->PEGAWAI_ID);
		$pemindahan->setField("PEGAWAI_KODE", $this->KODE_PEGAWAI);
		$pemindahan->setField("PEGAWAI_NAMA", $this->NAMA_PEGAWAI);
		$pemindahan->setField("PEGAWAI_JABATAN", $this->JABATAN);
		$pemindahan->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$pemindahan->insertLog();
	}


	function combo()
	{
		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$i = 0;
		$pemindahan->selectByParams(array("A.STATUS" => "AKTIF"));
		while ($pemindahan->nextRow()) {
			$arr_json[$i]['id']		= $pemindahan->getField("PEMINDAHAN_ID");
			$arr_json[$i]['text']	= $pemindahan->getField("RUANG") . "/" . $pemindahan->getField("LEMARI") . "/" .
				$pemindahan->getField("NOMOR_BOKS") . "/" . $pemindahan->getField("NOMOR_FOLDER");
			$i++;
		}
		echo json_encode($arr_json);
	}


	function daftar_berkas_simpan()
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$rows = isset($_GET['rows']) ? intval($_GET['rows']) : 50;
		$id   = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$offset = ($page - 1) * $rows;

		$reqPencarian = trim($this->input->get("reqPencarian"));
		$reqJenisRegister = $this->input->get("reqJenisRegister");
		$reqMode = $this->input->get("reqMode");

		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$statement .= " AND A.PERUSAHAAN_ID='$this->PERUSAHAAN_ID' ";
		$statement .= " AND A.CABANG_ID='$this->CABANG_ID' ";

		if ($reqPencarian != "") {
			$statement .= " AND (UPPER(A.KLASIFIKASI_KODE) LIKE '%" . strtoupper($reqPencarian) . "%' 
			OR UPPER(A.KLASIFIKASI_NAMA) LIKE '%" . strtoupper($reqPencarian) . "%'
			OR UPPER(A.NOMOR_DOKUMEN) LIKE '%" . strtoupper($reqPencarian) . "%'
			OR UPPER(A.NOMOR_ALTERNATIF) LIKE '%" . strtoupper($reqPencarian) . "%'
			OR UPPER(A.KETERANGAN) LIKE '%" . strtoupper($reqPencarian) . "%' ";
		}

		$rowCount = $pemindahan->getCountByParamsMonitoringBerkas(array("A.STATUS" => "SIMPAN"), $statement);
		$pemindahan->selectByParamsMonitoringBerkas(array("A.STATUS" => "SIMPAN"), $rows, $offset, $statement, " ORDER BY A.KLASIFIKASI_KODE ASC ");
		// echo $pemindahan->query;exit;
		$i = 0;
		$items = array();
		while ($pemindahan->nextRow()) {
			$row['id']							= $pemindahan->getField("PEMINDAHAN_BERKAS_ID");
			$row['text']						= $pemindahan->getField("KLASIFIKASI_KODE") . " - " . $pemindahan->getField("KLASIFIKASI_NAMA");
			$row['PEMINDAHAN_BERKAS_ID']		= $pemindahan->getField("PEMINDAHAN_BERKAS_ID");
			$row['KLASIFIKASI_ID']				= $pemindahan->getField("KLASIFIKASI_ID");
			$row['KLASIFIKASI_KODE']			= $pemindahan->getField("KLASIFIKASI_KODE");
			$row['KLASIFIKASI_NAMA']			= $pemindahan->getField("KLASIFIKASI_NAMA");
			$row['KLASIFIKASI']					= $pemindahan->getField("KLASIFIKASI_KODE") . " - " . $pemindahan->getField("KLASIFIKASI_NAMA");
			$row['KETERANGAN']					= $pemindahan->getField("KETERANGAN");
			$row['KURUN_WAKTU']					= $pemindahan->getField("KURUN_WAKTU");
			$row['TAHUN_PINDAH']				= $pemindahan->getField("TAHUN_PINDAH");
			$row['TINGKAT_PERKEMBANGAN_ID']		= $pemindahan->getField("TINGKAT_PERKEMBANGAN_ID");
			$row['TINGKAT_PERKEMBANGAN_KODE']	= $pemindahan->getField("TINGKAT_PERKEMBANGAN_KODE");
			$row['TINGKAT_PERKEMBANGAN_NAMA']	= $pemindahan->getField("TINGKAT_PERKEMBANGAN_NAMA");
			$row['NOMOR_DOKUMEN']				= $pemindahan->getField("NOMOR_DOKUMEN");
			$row['NOMOR_ALTERNATIF']			= $pemindahan->getField("NOMOR_ALTERNATIF");
			$row['NAMA_SATUAN_KERJA']			= $pemindahan->getField("NAMA_SATUAN_KERJA");
			$row['NAMA_CABANG']					= $pemindahan->getField("NAMA_CABANG");
			$row['ADA_HARDCOPY']				= $pemindahan->getField("ADA_HARDCOPY");

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

		$this->load->model("Pemindahan");

		$pemindahan_berkas = new Pemindahan();
		$pemindahan_berkas->selectByParamsBerkas(array("A.PEMINDAHAN_BERKAS_ID::VARCHAR" => $id));
		$pemindahan_berkas->firstRow();
		$result["JUMLAH_BERKAS"] = $pemindahan_berkas->getField("JUMLAH_BERKAS");
		$result["DOKUMEN"] = $pemindahan_berkas->getField("DOKUMEN");
		$result["LOKASI_SIMPAN_RUANG"] = $pemindahan_berkas->getField("LOKASI_SIMPAN_RUANG");
		$result["LOKASI_SIMPAN_LEMARI"] = $pemindahan_berkas->getField("LOKASI_SIMPAN_LEMARI");
		$result["LOKASI_SIMPAN_NOMOR_BOKS"] = $pemindahan_berkas->getField("LOKASI_SIMPAN_NOMOR_BOKS");
		$result["LOKASI_SIMPAN_NOMOR_FOLDER"] = $pemindahan_berkas->getField("LOKASI_SIMPAN_NOMOR_FOLDER");

		if ($pemindahan_berkas->getField("LOKASI_SIMPAN_LEMARI") != "") {
			$result["LOKASI_SIMPAN"] = "[Rak-" . $pemindahan_berkas->getField("LOKASI_SIMPAN_LEMARI") . "][B." . $pemindahan_berkas->getField("LOKASI_SIMPAN_NOMOR_BOKS") . "][F-" . $pemindahan_berkas->getField("LOKASI_SIMPAN_NOMOR_FOLDER") . "]";
		}

		echo json_encode($result);
	}
}
