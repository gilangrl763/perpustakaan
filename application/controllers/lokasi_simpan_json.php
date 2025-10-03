<?php
defined('BASEPATH') or exit('No direct script access allowed');

include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");

class lokasi_simpan_json extends CI_Controller
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

		$this->load->model("LokasiSimpan");
		$lokasi_simpan = new LokasiSimpan();

		$PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");
		$CABANG_ID = $this->input->get("CABANG_ID");
		$STATUS = $this->input->get("STATUS");

		$aColumns		= array("LOKASI_SIMPAN_ID","PERUSAHAAN_ID","CABANG_ID","STATUS","KODE","RUANG","LEMARI","NOMOR_BOKS",
								"NOMOR_FOLDER","NAMA_PERUSAHAAN","NAMA_CABANG","KETERANGAN","STATUS_KET");
		$aColumnsAlias	= array("LOKASI_SIMPAN_ID","PERUSAHAAN_ID","CABANG_ID","STATUS","KODE","RUANG","LEMARI","NOMOR_BOKS",
								"NOMOR_FOLDER","NAMA_PERUSAHAAN","NAMA_CABANG","KETERANGAN","STATUS_KET");

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
			if (trim($sOrder) == "ORDER BY LOKASI_SIMPAN_ID asc") {
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

		if($STATUS != 'ALL'){
			$statement .= " AND A.STATUS = '$STATUS' ";
		}

		$statement .= "AND (UPPER(A.RUANG) LIKE '%" . strtoupper($_GET['sSearch']) . "%'
					OR UPPER(A.NOMOR_BOKS) LIKE '%".strtoupper($_GET['sSearch'])."%'
		)";

		$allRecord = $lokasi_simpan->getCountByParamsMonitoring(array(), $statement_privacy . $statement);

		if ($_GET['sSearch'] == "") {
			$allRecordFilter = $allRecord;
		} else {
			$allRecordFilter =  $lokasi_simpan->getCountByParamsMonitoring(array(), $statement);
		}

		$lokasi_simpan->selectByParamsMonitoring(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $lokasi_simpan->query;exit;

		if($this->IS_MOBILE == true)
		{
			$arrResult["rowCount"] = $lokasi_simpan->rowCount;
			$arrResult["rowResult"] = $lokasi_simpan->rowResult;
			echo json_encode($arrResult);
			return;
		}
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while ($lokasi_simpan->nextRow()) {
			$row = array();
			for ($i = 0; $i < count($aColumns); $i++) {
				if ($aColumns[$i] == "STATUS_KET") {
					if($lokasi_simpan->getField("STATUS") == "AKTIF"){
						$badge_color = "badge-success";
					}
					else{
						$badge_color = "badge-danger";
					}
					$row[] = "<span class='badge ".$badge_color."'>".str_replace("_", " ", $lokasi_simpan->getField("STATUS"))."</span>";
				} else {
					$row[] = $lokasi_simpan->getField($aColumns[$i]);
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

		$this->load->model("LokasiSimpan");
		$lokasi_simpan = new LokasiSimpan();

		$id 		= $this->input->post("id");
		$mode 		= $this->input->post("mode");

		$PERUSAHAAN_ID		= $this->input->post("PERUSAHAAN_ID");
		$CABANG_ID			= $this->input->post("CABANG_ID");
		$KODE				= $this->input->post("KODE");
		$RUANG				= $this->input->post("RUANG");
		$LEMARI				= $this->input->post("LEMARI");
		$NOMOR_BOKS			= $this->input->post("NOMOR_BOKS");
		$NOMOR_FOLDER		= $this->input->post("NOMOR_FOLDER");
		$KETERANGAN			= $this->input->post("KETERANGAN");

		/******* START VALIDASI ******/
		if($mode == "insert") {
			$adaKode = $lokasi_simpan->getCountByParams(array("KODE"=>$KODE));

			if($adaKode > 0){
				echo "GAGAL|Kode telah tersedia";
				return;
			}
		}
		else{
			$adaKode = $lokasi_simpan->getCountByParams(array("KODE"=>$KODE,"NOT LOKASI_SIMPAN_ID"=>$id));

			if($adaKode > 0){
				echo "GAGAL|Kode telah tersedia";
				return;
			}
		}
		/******* END VALIDASI ******/


		$lokasi_simpan->setField("LOKASI_SIMPAN_ID", $id);
		$lokasi_simpan->setField("PERUSAHAAN_ID", $PERUSAHAAN_ID);
		$lokasi_simpan->setField("CABANG_ID", $CABANG_ID);
		$lokasi_simpan->setField("KODE", $KODE);
		$lokasi_simpan->setField("RUANG", $RUANG);
		$lokasi_simpan->setField("LEMARI", $LEMARI);
		$lokasi_simpan->setField("NOMOR_BOKS", $NOMOR_BOKS);
		$lokasi_simpan->setField("NOMOR_FOLDER", $NOMOR_FOLDER);
		$lokasi_simpan->setField("KETERANGAN", $KETERANGAN);
		$lokasi_simpan->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$lokasi_simpan->setField("UPDATED_BY", $this->USER_LOGIN_ID);

		if ($mode == "insert") {
			if($lokasi_simpan->insert()){
				$id = $lokasi_simpan->id;

				$KODE = "LS".generateZero($id, 6);

				$lokasi_simpan = new LokasiSimpan();
				$lokasi_simpan->setField("LOKASI_SIMPAN_ID", $id);
				$lokasi_simpan->setField("FIELD", "KODE");
				$lokasi_simpan->setField("FIELD_VALUE", $KODE);
				if($lokasi_simpan->updateByField()){
					echo "BERHASIL|Data berhasil disimpan|".$id;
				} 
				else{
					echo "GAGAL|Data gagal disimpan";
				}
			} 
			else{
				echo "GAGAL|Data gagal disimpan";
			}
		} else {
			if ($lokasi_simpan->update()) {
				echo "BERHASIL|Data berhasil disimpan|" . $id;
			} else {
				echo "GAGAL|Data gagal disimpan";
			}
		}
	}

	function delete()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("LokasiSimpan");
		$lokasi_simpan = new LokasiSimpan();

		$lokasi_simpan->setField("LOKASI_SIMPAN_ID", $reqId);

		if ($lokasi_simpan->delete()){
			echo "Data berhasil dihapus";
		}
		else{
			echo "Data gagal dihapus. Terdapat relasi dengan data lainnya!";
		}
	}

	function aktif()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("LokasiSimpan");
		$lokasi_simpan = new LokasiSimpan();

		$lokasi_simpan->setField("LOKASI_SIMPAN_ID", $reqId);
		$lokasi_simpan->setField("FIELD", "STATUS");
		$lokasi_simpan->setField("FIELD_VALUE", "AKTIF");
		if ($lokasi_simpan->updateByField()){
			echo "Data berhasil diaktifkan";
		}
		else{
			echo "Data gagal diaktifkan" ;
		}
	}

	function non_aktif()
	{
		$reqId	= $this->input->get('reqId');

		$this->load->model("LokasiSimpan");
		$lokasi_simpan = new LokasiSimpan();

		$lokasi_simpan->setField("LOKASI_SIMPAN_ID", $reqId);
		$lokasi_simpan->setField("FIELD", "STATUS");
		$lokasi_simpan->setField("FIELD_VALUE", "TIDAK_AKTIF");
		if ($lokasi_simpan->updateByField()){
			echo "Data berhasil dinonaktifkan";
		}
		else{
			echo "Data gagal dinonaktifkan" ;
		}
	}


	function combo()
	{
		$this->load->model("LokasiSimpan");
		$lokasi_simpan = new LokasiSimpan();

		$i = 0;
		$lokasi_simpan->selectByParams(array("A.STATUS" => "AKTIF"));
		while ($lokasi_simpan->nextRow()) {
			$arr_json[$i]['id']		= $lokasi_simpan->getField("LOKASI_SIMPAN_ID");
			$arr_json[$i]['text']	= $lokasi_simpan->getField("RUANG")."/".$lokasi_simpan->getField("LEMARI")."/".
										$lokasi_simpan->getField("NOMOR_BOKS")."/".$lokasi_simpan->getField("NOMOR_FOLDER");
			$i++;
		}
		echo json_encode($arr_json);
	}


	function treetable() 
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$rows = isset($_GET['rows']) ? intval($_GET['rows']) : 50;
		$id   = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$offset = ($page-1)*$rows;
		
		$reqPencarian = trim($this->input->get("reqPencarian"));
		$reqJenisRegister = $this->input->get("reqJenisRegister");
		$reqMode = $this->input->get("reqMode");

		$this->load->model("Pegawai");
		$lokasi_simpan = new Pegawai();

		$statement .= " AND PERUSAHAAN_ID='$this->PERUSAHAAN_ID' ";
		$statement .= " AND CABANG_ID='$this->CABANG_ID' ";

		if($reqPencarian != ""){
			$statement .= " AND (UPPER(A.RUANG) LIKE '%".strtoupper($reqPencarian)."%' 
			OR UPPER(A.LEMARI) LIKE '%".strtoupper($reqPencarian)."%'
			OR UPPER(A.NOMOR_BOKS) LIKE '%".strtoupper($reqPencarian)."%'
			OR UPPER(A.NOMOR_FOLDER) LIKE '%".strtoupper($reqPencarian)."%') ";
		}

		$rowCount = $lokasi_simpan->getCountByParams(array("A.STATUS" => "AKTIF"), $statement);
		$lokasi_simpan->selectByParams(array("A.STATUS" => "AKTIF"), $rows, $offset, $statement, " ORDER BY A.NAMA ASC ");
		// echo $lokasi_simpan->query;exit;
		$i = 0;
		$items = array();
		while($lokasi_simpan->selectByParamsMonitoring())
		{
			$row['id']					= $lokasi_simpan->getField("PEGAWAI_ID");
			$row['text']				= $lokasi_simpan->getField("NAMA");
			$row['PERUSAHAAN_ID']		= $lokasi_simpan->getField("PERUSAHAAN_ID");
			$row['CABANG_ID']			= $lokasi_simpan->getField("CABANG_ID");
			$row['KODE']				= $lokasi_simpan->getField("KODE");
			$row['RUANG']				= $lokasi_simpan->getField("RUANG");
			$row['LEMARI']				= $lokasi_simpan->getField("LEMARI");
			$row['NOMOR_BOKS']			= $lokasi_simpan->getField("NOMOR_BOKS");
			$row['NOMOR_FOLDER']		= $lokasi_simpan->getField("NOMOR_FOLDER");
			$row['NAMA']				= $lokasi_simpan->getField("RUANG")."/".$lokasi_simpan->getField("LEMARI")
											."/".$lokasi_simpan->getField("NOMOR_BOKS")."/".$lokasi_simpan->getField("NOMOR_FOLDER");
			$row['KETERANGAN']			= $lokasi_simpan->getField("KETERANGAN");
			$row['state']				= "closed";
			$row['children']			= false;

			$this->TREETABLE_COUNT++;
		}
	}


}
