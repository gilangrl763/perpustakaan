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

class Cabang extends Entity
{

	var $query;
	/**
	 * Class constructor.
	 **/
	function Cabang()
	{
		$this->Entity();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("CABANG_ID", $this->getNextId("CABANG_ID", "CABANG"));
		$str = "
			INSERT INTO CABANG(
				CABANG_ID, 
				PERUSAHAAN_ID, 
				KODE, 
				NAMA, 
				ALAMAT, 
				KOTA, 
				TELEPON, 
				EMAIL, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES ('" . $this->getField("CABANG_ID") . "', 
				'" . $this->getField("PERUSAHAAN_ID") . "', 
				'" . $this->getField("KODE") . "', 
				'" . $this->getField("NAMA") . "', 
				'" . $this->getField("ALAMAT") . "', 
				'" . $this->getField("KOTA") . "', 
				'" . $this->getField("TELEPON") . "', 
				'" . $this->getField("EMAIL") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		$this->id = $this->getField("CABANG_ID");
		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function update()
	{
		$str = "
			UPDATE CABANG
				SET 
				PERUSAHAAN_ID		= '" . $this->getField("PERUSAHAAN_ID") . "', 
				KODE				= '" . $this->getField("KODE") . "', 
				NAMA				= '" . $this->getField("NAMA") . "', 
				ALAMAT				= '" . $this->getField("ALAMAT") . "', 
				KOTA				= '" . $this->getField("KOTA") . "', 
				TELEPON				= '" . $this->getField("TELEPON") . "', 
				EMAIL				= '" . $this->getField("EMAIL") . "', 
				UPDATED_BY			= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE		= CURRENT_TIMESTAMP
			WHERE CABANG_ID 	= '" . $this->getField("CABANG_ID") . "'
		";

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE CABANG A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "'
			WHERE CABANG_ID = " . $this->getField("CABANG_ID");

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function delete()
	{
		$str = "DELETE FROM CABANG
			WHERE CABANG_ID = '" . $this->getField("CABANG_ID") . "'";

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
	function selectByParams($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY CABANG_ID ASC")
	{
		$str = "SELECT A.CABANG_ID, A.KODE, A.NAMA, A.ALAMAT, A.KOTA, A.TELEPON, A.EMAIL, A.STATUS, A.PERUSAHAAN_ID,
				B.NAMA NAMA_PERUSAHAAN, A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
			FROM CABANG A
			LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID = A.PERUSAHAAN_ID
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


	function selectByParamsMonitoring($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PERUSAHAAN_ID ASC")
	{
		$str = "SELECT A.CABANG_ID, A.KODE, A.NAMA, A.ALAMAT, A.TELEPON, A.EMAIL, A.STATUS, A.PERUSAHAAN_ID,
				B.NAMA NAMA_PERUSAHAAN, A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
			FROM CABANG A
			LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID = A.PERUSAHAAN_ID
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

	function selectByParamsLike($paramsArray = array(), $limit = -1, $from = -1, $statement = "")
	{
		$str = "SELECT A.CABANG_ID, A.KODE, A.NAMA, A.ALAMAT, A.TELEPON, A.EMAIL, A.STATUS, A.PERUSAHAAN_ID,
				B.NAMA NAMA_PERUSAHAAN, A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
			FROM CABANG A
			LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID = A.PERUSAHAAN_ID
			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement . " ORDER BY CABANG_ID DESC";
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
			FROM CABANG A 
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
		$str = "SELECT COUNT(1) AS ROWCOUNT 
			FROM CABANG A 
			LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID = A.PERUSAHAAN_ID
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
