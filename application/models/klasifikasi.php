<?
/* *******************************************************************************************************
MODUL NAME 			: MTSN LAWANG
FILE NAME 			: 
AUTHOR				: 
VERSION				: 1.0
MODIFICATION DOC	:
DESCRIPTION			: 
***************************************************************************************************** */

/***
 * Entity-base class untuk mengimplementasikan tabel kategori.
 * 
 ***/

include_once(APPPATH . '/models/Entity.php');

class Klasifikasi extends Entity
{

	var $query;
	/**
	 * Class constructor.
	 **/
	function Klasifikasi()
	{
		$this->Entity();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("KLASIFIKASI_ID", $this->getNextId("KLASIFIKASI_ID", "KLASIFIKASI"));
		$str = " INSERT INTO KLASIFIKASI(
				KLASIFIKASI_ID, 
				KLASIFIKASI_ID_PARENT, 
				PERUSAHAAN_ID, 
				KODE, 
				NAMA, 
				KETERANGAN, 
				RETENSI_AKTIF, 
				RETENSI_INAKTIF, 
				PENYUSUTAN_AKHIR_ID, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES ('" . $this->getField("KLASIFIKASI_ID") . "', 
				'" . $this->getField("KLASIFIKASI_ID_PARENT") . "', 
				'" . $this->getField("PERUSAHAAN_ID") . "', 
				'" . $this->getField("KODE") . "', 
				'" . $this->getField("NAMA") . "', 
				'" . $this->getField("KETERANGAN") . "', 
				'" . $this->getField("RETENSI_AKTIF") . "', 
				'" . $this->getField("RETENSI_INAKTIF") . "', 
				'" . $this->getField("PENYUSUTAN_AKHIR_ID") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";
		// echo "GAGAL|".$str;exit;
		$this->id = $this->getField("KLASIFIKASI_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function update()
	{
		$str = " UPDATE KLASIFIKASI
				SET 
				KLASIFIKASI_ID_PARENT		= '" . $this->getField("KLASIFIKASI_ID_PARENT") . "', 
				PERUSAHAAN_ID				= '" . $this->getField("PERUSAHAAN_ID") . "', 
				KODE						= '" . $this->getField("KODE") . "', 
				NAMA						= '" . $this->getField("NAMA") . "', 
				KETERANGAN					= '" . $this->getField("KETERANGAN") . "', 
				RETENSI_AKTIF				= '" . $this->getField("RETENSI_AKTIF") . "', 
				RETENSI_INAKTIF				= '" . $this->getField("RETENSI_INAKTIF") . "', 
				PENYUSUTAN_AKHIR_ID			= '" . $this->getField("PENYUSUTAN_AKHIR_ID") . "', 
				UPDATED_BY					= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE				= CURRENT_TIMESTAMP
			WHERE KLASIFIKASI_ID 			= '" . $this->getField("KLASIFIKASI_ID") . "'
		";

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE KLASIFIKASI A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "'
			WHERE KLASIFIKASI_ID = " . $this->getField("KLASIFIKASI_ID");

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function delete()
	{
		$str = "DELETE FROM KLASIFIKASI
			WHERE KLASIFIKASI_ID = '" . $this->getField("KLASIFIKASI_ID") . "'";

		// echo $str;exit();
		$this->query = $str;
		return $this->execQuery($str);
	}

	/** 
	 * Cari record berdasarkan array parameter dan limit tampilan 
	 * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","JAWABAN"=>"yyy") 
	 * @param int limit Jumlah maksimal record yang akan diambil 
	 * @param int from Awal record yang diambil 
	 * @return boolean True jika sukses, false jika tidak 
	 **/
	function selectByParams($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY KLASIFIKASI_ID ASC")
	{
		$str = "  SELECT KLASIFIKASI_ID, KLASIFIKASI_ID_PARENT, PERUSAHAAN_ID, KODE, NAMA, KETERANGAN, RETENSI_AKTIF, RETENSI_INAKTIF, 
				PENYUSUTAN_AKHIR_ID, STATUS, CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM KLASIFIKASI A
			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		$this->query = $str;
		// echo $str;exit();
		return $this->selectLimit($str, $limit, $from);
	}


	function selectByParamsMonitoring($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY KLASIFIKASI_ID ASC")
	{
		$str = "  SELECT KLASIFIKASI_ID, KLASIFIKASI_ID_PARENT, A.PERUSAHAAN_ID, B.NAMA NAMA_PERUSAHAAN, A.KODE, A.NAMA, A.KETERANGAN, 
					RETENSI_AKTIF, RETENSI_INAKTIF, A.PENYUSUTAN_AKHIR_ID, C.NAMA PENYUSUTAN_AKHIR, A.STATUS, 
					A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
				FROM KLASIFIKASI A
				LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID = A.PERUSAHAAN_ID
				LEFT JOIN PENYUSUTAN_AKHIR C ON C.PENYUSUTAN_AKHIR_ID = A.PENYUSUTAN_AKHIR_ID
				WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit();
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsLike($paramsArray = array(), $limit = -1, $from = -1, $statement = "")
	{
		$str = "  SELECT KLASIFIKASI_ID, KLASIFIKASI_ID_PARENT, A.PERUSAHAAN_ID, B.NAMA NAMA_PERUSAHAAN, KODE, NAMA, KETERANGAN, 
					RETENSI_AKTIF, RETENSI_INAKTIF, A.PENYUSUTAN_AKHIR_ID, C.NAMA PENYUSUTAN_AKHIR, A.STATUS, 
					A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
				FROM KLASIFIKASI A
				LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID = A.PERUSAHAAN_ID
				LEFT JOIN PENYUSUTAN_AKHIR C ON C.PENYUSUTAN_AKHIR_ID = A.PENYUSUTAN_AKHIR_ID
				WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement . " ORDER BY KLASIFIKASI_ID DESC";
		$this->query = $str;
		// echo $str;exit();		
		return $this->selectLimit($str, $limit, $from);
	}

	/** 
	 * Hitung jumlah record berdasarkan parameter (array). 
	 * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","JAWABAN"=>"yyy") 
	 * @return long Jumlah record yang sesuai kriteria 
	 **/
	function getCountByParams($paramsArray = array(), $statement = "")
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT 
			FROM KLASIFIKASI A 
			WHERE 1 = 1 " . $statement;

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$this->select($str);
		if ($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
	}

	function getCountByParamsMonitoring($paramsArray = array(), $statement = "")
	{
		$str = " SELECT COUNT(1) AS ROWCOUNT 
			FROM KLASIFIKASI A
			LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID = A.PERUSAHAAN_ID
			LEFT JOIN PENYUSUTAN_AKHIR C ON C.PENYUSUTAN_AKHIR_ID = A.PENYUSUTAN_AKHIR_ID
			WHERE 1 = 1 " . $statement;

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$this->select($str);
		if ($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
	}
}
