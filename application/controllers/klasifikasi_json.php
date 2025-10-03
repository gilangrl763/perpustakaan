<?php
defined('BASEPATH') or exit('No direct script access allowed');

include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");

class klasifikasi_json extends CI_Controller
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

		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		$STATUS = $this->input->get("STATUS");

		$aColumns		= array(
			"KLASIFIKASI_ID", "PERUSAHAAN_ID", "CABANG_ID", "PEGAWAI_ID", "STATUS", "KODE", "NAMA", "KLASIFIKASI_ID_PARENT",
			"NAMA_PERUSAHAAN", "NAMA_CABANG", "JENIS_PEGAWAI", "STATUS_KET"
		);
		$aColumnsAlias	= array(
			"KLASIFIKASI_ID", "PERUSAHAAN_ID", "CABANG_ID", "PEGAWAI_ID", "STATUS", "KODE", "NAMA", "KLASIFIKASI_ID_PARENT",
			"NAMA_PERUSAHAAN", "NAMA_CABANG", "JENIS_PEGAWAI", "STATUS_KET"
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
			if (trim($sOrder) == "ORDER BY KLASIFIKASI_ID asc") {
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


		if ($STATUS != 'ALL') {
			$statement .= " AND A.STATUS = '$STATUS' ";
		}

		$statement .= " AND (UPPER(A.NAMA) LIKE '%" . strtoupper($_GET['sSearch']) . "%')";

		$allRecord = $klasifikasi->getCountByParamsMonitoring(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $klasifikasi->getCountByParamsMonitoring(array(), $statement);
		}

		$klasifikasi->selectByParamsMonitoring(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
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

		while ($klasifikasi->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "STATUS_KET") {
					if ($klasifikasi->getField("STATUS") == "AKTIF") {
						$badge_color = "badge-success";
					} else {
						$badge_color = "badge-danger";
					}
					$row[] = "<span class='badge " . $badge_color . "'>" . str_replace("_", " ", $klasifikasi->getField("STATUS")) . "</span>";
				} else {
					$row[] = $klasifikasi->getField($aColumns[$i]);
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

		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		$id 		= $this->input->post("id");
		$mode 		= $this->input->post("mode");

		$KLASIFIKASI_ID_PARENT		= $this->input->post("KLASIFIKASI_ID_PARENT");
		$KODE						= $this->input->post("KODE");
		$NAMA						= $this->input->post("NAMA");
		$PERUSAHAAN_ID				= $this->input->post("PERUSAHAAN_ID");
		$RETENSI_AKTIF				= $this->input->post("RETENSI_AKTIF");
		$RETENSI_INAKTIF			= $this->input->post("RETENSI_INAKTIF");
		$PENYUSUTAN_AKHIR_ID		= $this->input->post("PENYUSUTAN_AKHIR_ID");
		$KETERANGAN					= setQuote($_POST['KETERANGAN']);


		/******* START VALIDASI ******/
		if ($mode == "insert") {
			$adaKode = $klasifikasi->getCountByParams(array("KODE" => $KODE));

			if ($adaKode > 0) {
				echo "GAGAL|Kode telah tersedia";
				return;
			}
		} else {
			$adaKode = $klasifikasi->getCountByParams(array("KODE" => $KODE, "NOT KLASIFIKASI_ID" => $id));

			if ($adaKode > 0) {
				echo "GAGAL|Kode telah tersedia";
				return;
			}
		}
		/******* END VALIDASI ******/


		$klasifikasi->setField("KLASIFIKASI_ID", $id);
		$klasifikasi->setField("KLASIFIKASI_ID_PARENT", $KLASIFIKASI_ID_PARENT);
		$klasifikasi->setField("KODE", $KODE);
		$klasifikasi->setField("NAMA", $NAMA);
		$klasifikasi->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID);
		$klasifikasi->setField("RETENSI_AKTIF", $RETENSI_AKTIF);
		$klasifikasi->setField("RETENSI_INAKTIF", $RETENSI_INAKTIF);
		$klasifikasi->setField("PENYUSUTAN_AKHIR_ID", $PENYUSUTAN_AKHIR_ID);
		$klasifikasi->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$klasifikasi->setField("UPDATED_BY", $this->USER_LOGIN_ID);

		if ($mode == "insert") {
			if ($klasifikasi->insert()) {
				$id = $klasifikasi->id;

				echo "BERHASIL|Data berhasil disimpan|" . $id;
			} else {
				echo "GAGAL|Data gagal disimpan";
			}
		} else {
			if ($klasifikasi->update()) {
				echo "BERHASIL|Data berhasil disimpan|" . $id;
			} else {
				echo "GAGAL|Data gagal disimpan";
			}
		}
	}

	function delete()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		$klasifikasi->setField("KLASIFIKASI_ID", $reqId);

		if ($klasifikasi->delete()) {
			echo "Data berhasil dihapus";
		} else {
			echo "Data gagal dihapus. Terdapat relasi dengan data lainnya!";
		}
	}

	function aktif()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		$klasifikasi->setField("KLASIFIKASI_ID", $reqId);
		$klasifikasi->setField("FIELD", "STATUS");
		$klasifikasi->setField("FIELD_VALUE", "AKTIF");
		if ($klasifikasi->updateByField()) {
			echo "Data berhasil diaktifkan";
		} else {
			echo "Data gagal diaktifkan";
		}
	}

	function non_aktif()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		$klasifikasi->setField("KLASIFIKASI_ID", $reqId);
		$klasifikasi->setField("FIELD", "STATUS");
		$klasifikasi->setField("FIELD_VALUE", "TIDAK_AKTIF");
		if ($klasifikasi->updateByField()) {
			echo "Data berhasil dinonaktifkan";
		} else {
			echo "Data gagal dinonaktifkan";
		}
	}


	function combo()
	{
		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		$i = 0;
		$klasifikasi->selectByParams(array("STATUS" => "AKTIF"));
		while ($klasifikasi->nextRow()) {
			$arr_json[$i]['id']		= $klasifikasi->getField("KLASIFIKASI_ID");
			$arr_json[$i]['text']	= $klasifikasi->getField("NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function treetable_master()
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$rows = isset($_GET['rows']) ? intval($_GET['rows']) : 50;
		$id   = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$offset = ($page - 1) * $rows;

		$PERUSAHAAN_ID 	= $this->input->get("PERUSAHAAN_ID");
		$STATUS 		= $this->input->get("STATUS");
		$PENCARIAN 		= $this->input->get("PENCARIAN");

		if ($PERUSAHAAN_ID  == "") {
			$PERUSAHAAN_ID = $this->PERUSAHAAN_ID;
		}

		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		if ($PENCARIAN == "") {
			if ($STATUS == "AKTIF") {
				$arrStatement = array("A.KLASIFIKASI_ID_PARENT" => "0", "A.STATUS" => "AKTIF", "A.PERUSAHAAN_ID" => $PERUSAHAAN_ID);
			} else {
				$arrStatement = array("A.STATUS" => "NON_AKTIF", "A.PERUSAHAAN_ID" => $PERUSAHAAN_ID);
			}
		} else {
			$arrStatement .= array("A.PERUSAHAAN_ID" => $PERUSAHAAN_ID, "A.STATUS" => "AKTIF");
			$statement = " AND (UPPER(A.KODE) LIKE '%" . strtoupper($PENCARIAN) . "%' OR UPPER(A.NAMA) LIKE '%" . strtoupper($PENCARIAN) . "%') ";
		}


		$i = 0;
		$items = array();
		$rowCount = $klasifikasi->getCountByParamsMonitoring($arrStatement, $statement);
		$klasifikasi->selectByParamsMonitoring($arrStatement, $rows, $offset, $statement, " ORDER BY A.KLASIFIKASI_ID ASC ");
		// echo $klasifikasi->query;exit;
		while ($klasifikasi->nextRow()) {
			$row['id']						= $klasifikasi->getField("KLASIFIKASI_ID");
			$row['parentId']				= $klasifikasi->getField("KLASIFIKASI_ID_PARENT");
			$row['text']					= $klasifikasi->getField("NAMA");
			$row['KLASIFIKASI_ID']			= $klasifikasi->getField("KLASIFIKASI_ID");
			$row['KLASIFIKASI_ID_PARENT']	= $klasifikasi->getField("KLASIFIKASI_ID_PARENT");
			$row['PERUSAHAAN_ID']			= $klasifikasi->getField("PERUSAHAAN_ID");
			$row['KODE']					= $klasifikasi->getField("KODE");
			$row['NAMA']					= $klasifikasi->getField("NAMA");
			$row['KETERANGAN']				= $klasifikasi->getField("KETERANGAN");
			$row['RETENSI_AKTIF']			= $klasifikasi->getField("RETENSI_AKTIF");
			$row['RETENSI_INAKTIF']			= $klasifikasi->getField("RETENSI_INAKTIF");
			$row['PENYUSUTAN_AKHIR_ID']		= $klasifikasi->getField("PENYUSUTAN_AKHIR_ID");
			$row['PENYUSUTAN_AKHIR']		= $klasifikasi->getField("PENYUSUTAN_AKHIR");
			$row['STATUS']					= $klasifikasi->getField("STATUS");

			if (trim($PENCARIAN) == "") {
				$row['state'] = $this->has_child($klasifikasi->getField("KLASIFIKASI_ID"));
				$row['children'] = $this->treetable_children_master($klasifikasi->getField("KLASIFIKASI_ID"), $STATUS);
			}

			$i++;

			array_push($items, $row);
			unset($row);
		}

		$result["rows"] = $items;
		$result["total"] = $this->TREETABLE_COUNT;
		echo json_encode($result);
	}

	function treetable_children_master($PARENT_ID, $STATUS = "AKTIF")
	{
		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		$arrStatement = array("A.KLASIFIKASI_ID_PARENT" => $PARENT_ID, "A.STATUS" => $STATUS);

		$i = 0;
		$items = array();
		$rowCount = $klasifikasi->getCountByParamsMonitoring($arrStatement, $statement);
		$klasifikasi->selectByParamsMonitoring($arrStatement, $rows, $offset, $statement, " ORDER BY A.KLASIFIKASI_ID ASC ");
		// echo $klasifikasi->query;exit;
		while ($klasifikasi->nextRow()) {
			$row['id']						= $klasifikasi->getField("KLASIFIKASI_ID");
			$row['parentId']				= $klasifikasi->getField("KLASIFIKASI_ID_PARENT");
			$row['text']					= $klasifikasi->getField("NAMA");
			$row['KLASIFIKASI_ID']			= $klasifikasi->getField("KLASIFIKASI_ID");
			$row['KLASIFIKASI_ID_PARENT']	= $klasifikasi->getField("KLASIFIKASI_ID_PARENT");
			$row['PERUSAHAAN_ID']			= $klasifikasi->getField("PERUSAHAAN_ID");
			$row['KODE']					= $klasifikasi->getField("KODE");
			$row['NAMA']					= $klasifikasi->getField("NAMA");
			$row['KETERANGAN']				= $klasifikasi->getField("KETERANGAN");
			$row['RETENSI_AKTIF']			= $klasifikasi->getField("RETENSI_AKTIF");
			$row['RETENSI_INAKTIF']			= $klasifikasi->getField("RETENSI_INAKTIF");
			$row['PENYUSUTAN_AKHIR_ID']		= $klasifikasi->getField("PENYUSUTAN_AKHIR_ID");
			$row['PENYUSUTAN_AKHIR']		= $klasifikasi->getField("PENYUSUTAN_AKHIR");
			$row['STATUS']					= $klasifikasi->getField("STATUS");

			$state = $this->has_child($klasifikasi->getField("KLASIFIKASI_ID"));
			$row['state'] = $state;

			if ($state) {
				$row['children'] = $this->treetable_children_master($klasifikasi->getField("KLASIFIKASI_ID"), $STATUS);
			}

			$i++;
			array_push($items, $row);
			unset($row);
		}

		return $items;
	}


	function combotree()
	{
		$PENCARIAN = $this->input->get("q");
		$PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");

		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		if ($PERUSAHAAN_ID == "") {
			$PERUSAHAAN_ID = $this->PERUSAHAAN_ID;
		}

		$i = 0;
		$arr_json[$i]['id']						= "0";
		$arr_json[$i]['text']					= "--- Induk ---";
		$arr_json[$i]['KLASIFIKASI_ID']		= "0";
		$arr_json[$i]['KLASIFIKASI_ID_PARENT']	= "0";
		$arr_json[$i]['KODE']					= "0";
		$arr_json[$i]['NAMA']					= "--- Induk ---";
		$arr_json[$i]['state']					= "close";
		$arr_json[$i]['children'] 				= "";
		$i++;

		$klasifikasi->selectByParams(array("KLASIFIKASI_ID_PARENT" => "0", "STATUS" => "AKTIF", "PERUSAHAAN_ID" => $PERUSAHAAN_ID));
		// echo $klasifikasi->query;exit;
		while ($klasifikasi->nextRow()) {
			$arr_json[$i]['id']						= $klasifikasi->getField("KLASIFIKASI_ID");
			$arr_json[$i]['text']					= $klasifikasi->getField("NAMA");
			$arr_json[$i]['KLASIFIKASI_ID']			= $klasifikasi->getField("KLASIFIKASI_ID");
			$arr_json[$i]['KLASIFIKASI_ID_PARENT']	= $klasifikasi->getField("KLASIFIKASI_ID_PARENT");
			$arr_json[$i]['KODE']					= $klasifikasi->getField("KODE");
			$arr_json[$i]['NAMA']					= $klasifikasi->getField("NAMA");
			$arr_json[$i]['RETENSI_AKTIF']			= $klasifikasi->getField("RETENSI_AKTIF");
			$arr_json[$i]['RETENSI_INAKTIF']		= $klasifikasi->getField("RETENSI_INAKTIF");
			$arr_json[$i]['state']					= $this->has_child($klasifikasi->getField("KLASIFIKASI_ID"));
			$arr_json[$i]['children'] 				= $this->combotree_children($klasifikasi->getField("KLASIFIKASI_ID"));
			$i++;
		}

		echo json_encode($arr_json);
	}

	function combotree_filter()
	{
		$PENCARIAN = $this->input->get("q");
		$PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");

		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		if ($PERUSAHAAN_ID == "") {
			$PERUSAHAAN_ID = $this->PERUSAHAAN_ID;
		}

		$i = 0;
		$arr_json[$i]['id']						= "ALL";
		$arr_json[$i]['text']					= "Seluruhnya";
		$arr_json[$i]['KLASIFIKASI_ID']			= "ALL";
		$arr_json[$i]['KLASIFIKASI_ID_PARENT']	= "ALL";
		$arr_json[$i]['KODE']					= "ALL";
		$arr_json[$i]['NAMA']					= "Seluruhnya";
		$arr_json[$i]['state']					= "close";
		$arr_json[$i]['children'] 				= "";
		$i++;

		$klasifikasi->selectByParams(array("KLASIFIKASI_ID_PARENT" => "0", "STATUS" => "AKTIF", "PERUSAHAAN_ID" => $PERUSAHAAN_ID));
		// echo $klasifikasi->query;exit;
		while ($klasifikasi->nextRow()) {
			$arr_json[$i]['id']						= $klasifikasi->getField("KLASIFIKASI_ID");
			$arr_json[$i]['text']					= $klasifikasi->getField("NAMA");
			$arr_json[$i]['KLASIFIKASI_ID']			= $klasifikasi->getField("KLASIFIKASI_ID");
			$arr_json[$i]['KLASIFIKASI_ID_PARENT']	= $klasifikasi->getField("KLASIFIKASI_ID_PARENT");
			$arr_json[$i]['KODE']					= $klasifikasi->getField("KODE");
			$arr_json[$i]['NAMA']					= $klasifikasi->getField("NAMA");
			$arr_json[$i]['RETENSI_AKTIF']			= $klasifikasi->getField("RETENSI_AKTIF");
			$arr_json[$i]['RETENSI_INAKTIF']		= $klasifikasi->getField("RETENSI_INAKTIF");
			$arr_json[$i]['state']					= $this->has_child($klasifikasi->getField("KLASIFIKASI_ID"));
			$arr_json[$i]['children'] 				= $this->combotree_children($klasifikasi->getField("KLASIFIKASI_ID"));
			$i++;
		}

		echo json_encode($arr_json);
	}

	function combotree_pemindahan()
	{
		$PENCARIAN = $this->input->get("q");
		$PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");

		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		if ($PERUSAHAAN_ID == "") {
			$PERUSAHAAN_ID = $this->PERUSAHAAN_ID;
		}

		$i = 0;
		$klasifikasi->selectByParams(array("KLASIFIKASI_ID_PARENT" => "0", "STATUS" => "AKTIF", "PERUSAHAAN_ID" => $PERUSAHAAN_ID));
		// echo $klasifikasi->query;exit;
		while ($klasifikasi->nextRow()) {
			$arr_json[$i]['id']						= $klasifikasi->getField("KLASIFIKASI_ID");
			$arr_json[$i]['text']					= $klasifikasi->getField("KODE")." - ".$klasifikasi->getField("NAMA");
			$arr_json[$i]['KLASIFIKASI_ID']			= $klasifikasi->getField("KLASIFIKASI_ID");
			$arr_json[$i]['KLASIFIKASI_ID_PARENT']	= $klasifikasi->getField("KLASIFIKASI_ID_PARENT");
			$arr_json[$i]['KODE']					= $klasifikasi->getField("KODE");
			$arr_json[$i]['NAMA']					= $klasifikasi->getField("NAMA");
			$arr_json[$i]['RETENSI_AKTIF']			= $klasifikasi->getField("RETENSI_AKTIF");
			$arr_json[$i]['RETENSI_INAKTIF']		= $klasifikasi->getField("RETENSI_INAKTIF");
			$arr_json[$i]['state']					= $this->has_child($klasifikasi->getField("KLASIFIKASI_ID"));
			$arr_json[$i]['children'] 				= $this->combotree_children($klasifikasi->getField("KLASIFIKASI_ID"));
			$i++;
		}

		echo json_encode($arr_json);
	}

	function combotree_form()
	{
		$PENCARIAN = $this->input->get("q");
		$PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");
		$CABANG_ID = $this->input->get("CABANG_ID");

		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		if ($PERUSAHAAN_ID == "") {
			$PERUSAHAAN_ID = $this->PERUSAHAAN_ID;
		}

		if ($CABANG_ID == "") {
			$CABANG_ID = $this->CABANG_ID;
		}

		$i = 0;
		$arr_json[$i]['id']						= "0";
		$arr_json[$i]['text']					= "--- Induk ---";
		$arr_json[$i]['KLASIFIKASI_ID']		= "0";
		$arr_json[$i]['KLASIFIKASI_ID_PARENT']	= "0";
		$arr_json[$i]['KODE']					= "0";
		$arr_json[$i]['NAMA']					= "--- Induk ---";
		$arr_json[$i]['state']					= "close";
		$arr_json[$i]['children'] 				= "";
		$i++;

		$klasifikasi->selectByParams(array("KLASIFIKASI_ID_PARENT" => "0", "STATUS" => "AKTIF", "PERUSAHAAN_ID" => $PERUSAHAAN_ID, "CABANG_ID" => $CABANG_ID));
		// echo $klasifikasi->query;exit;
		while ($klasifikasi->nextRow()) {
			$arr_json[$i]['id']						= $klasifikasi->getField("KLASIFIKASI_ID");
			$arr_json[$i]['text']					= $klasifikasi->getField("NAMA");
			$arr_json[$i]['KLASIFIKASI_ID']			= $klasifikasi->getField("KLASIFIKASI_ID");
			$arr_json[$i]['KLASIFIKASI_ID_PARENT']	= $klasifikasi->getField("KLASIFIKASI_ID_PARENT");
			$arr_json[$i]['KODE']					= $klasifikasi->getField("KODE");
			$arr_json[$i]['NAMA']					= $klasifikasi->getField("NAMA");
			$arr_json[$i]['RETENSI_AKTIF']			= $klasifikasi->getField("RETENSI_AKTIF");
			$arr_json[$i]['RETENSI_INAKTIF']		= $klasifikasi->getField("RETENSI_INAKTIF");
			$arr_json[$i]['state']					= $this->has_child($klasifikasi->getField("KLASIFIKASI_ID"));
			$arr_json[$i]['children'] 				= $this->combotree_children($klasifikasi->getField("KLASIFIKASI_ID"));
			$i++;
		}

		echo json_encode($arr_json);
	}

	function combotree_children($id)
	{
		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		$i = 0;
		$arr_json = array();
		$klasifikasi->selectByParams(array("KLASIFIKASI_ID_PARENT" => $id, "STATUS" => "AKTIF"));
		//echo $klasifikasi->query;exit;
		while ($klasifikasi->nextRow()) {
			$arr_json[$i]['id']						= $klasifikasi->getField("KLASIFIKASI_ID");
			$arr_json[$i]['text']					= $klasifikasi->getField("KODE")." - ".$klasifikasi->getField("NAMA");
			$arr_json[$i]['KLASIFIKASI_ID']			= $klasifikasi->getField("KLASIFIKASI_ID");
			$arr_json[$i]['KLASIFIKASI_ID_PARENT']	= $klasifikasi->getField("KLASIFIKASI_ID_PARENT");
			$arr_json[$i]['KODE']					= $klasifikasi->getField("KODE");
			$arr_json[$i]['NAMA']					= $klasifikasi->getField("NAMA");
			$arr_json[$i]['RETENSI_AKTIF']			= $klasifikasi->getField("RETENSI_AKTIF");
			$arr_json[$i]['RETENSI_INAKTIF']		= $klasifikasi->getField("RETENSI_INAKTIF");

			$state = $this->has_child($klasifikasi->getField("KLASIFIKASI_ID"));
			$arr_json[$i]['state'] = $state;

			if ($state) {
				$arr_json[$i]['children'] = $this->combotree_children($klasifikasi->getField("KLASIFIKASI_ID"));
			}

			$i++;
		}

		return $arr_json;
	}

	function combotree_nonparent()
	{
		$PENCARIAN = $this->input->get("q");
		$id = $this->input->get("id");

		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		if ($id == "") {
			$id = $this->PERUSAHAAN_ID;
		}

		$i = 0;
		$klasifikasi->selectByParams(array("KLASIFIKASI_ID_PARENT" => "0", "STATUS" => "AKTIF", "PERUSAHAAN_ID" => $id));
		// echo $klasifikasi->query;exit;
		while ($klasifikasi->nextRow()) {
			$arr_json[$i]['id']						= $klasifikasi->getField("KLASIFIKASI_ID");
			$arr_json[$i]['text']					= $klasifikasi->getField("NAMA");
			$arr_json[$i]['KLASIFIKASI_ID']		= $klasifikasi->getField("KLASIFIKASI_ID");
			$arr_json[$i]['KLASIFIKASI_ID_PARENT']	= $klasifikasi->getField("KLASIFIKASI_ID_PARENT");
			$arr_json[$i]['KODE']					= $klasifikasi->getField("KODE");
			$arr_json[$i]['NAMA']					= $klasifikasi->getField("NAMA");
			$arr_json[$i]['state']					= $this->has_child($klasifikasi->getField("KLASIFIKASI_ID"));
			$arr_json[$i]['children'] 				= $this->combotree_children($klasifikasi->getField("KLASIFIKASI_ID"));
			$i++;
		}

		echo json_encode($arr_json);
	}


	function has_child($id)
	{
		$this->load->model("Klasifikasi");
		$klasifikasi = new Klasifikasi();

		$adaChild = $klasifikasi->getCountByParams(array("KLASIFIKASI_ID_PARENT" => $id));
		return $adaChild > 0 ? true : false;
	}
}
