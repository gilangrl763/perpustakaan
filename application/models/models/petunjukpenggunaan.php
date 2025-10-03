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

class PetunjukPenggunaan extends Entity
{

	var $query;
	/**
	 * Class constructor.
	 **/
	function PetunjukPenggunaan()
	{
		$this->Entity();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PETUNJUK_PENGGUNAAN_ID", $this->getNextId("PETUNJUK_PENGGUNAAN_ID", "PETUNJUK_PENGGUNAAN"));
		$str = "INSERT INTO PETUNJUK_PENGGUNAAN(
				PETUNJUK_PENGGUNAAN_ID, 
				NAMA, 
				USER_GROUP, 
				KETERANGAN, 
				DOKUMEN, 
				UKURAN_DOKUMEN, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES ('" . $this->getField("PETUNJUK_PENGGUNAAN_ID") . "', 
				'" . $this->getField("NAMA") . "', 
				'" . $this->getField("USER_GROUP") . "', 
				'" . $this->getField("KETERANGAN") . "', 
				'" . $this->getField("DOKUMEN") . "', 
				'" . $this->getField("UKURAN_DOKUMEN") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		$this->id = $this->getField("PETUNJUK_PENGGUNAAN_ID");
		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function update()
	{
		$str = "UPDATE PETUNJUK_PENGGUNAAN
				SET 
				NAMA				= '" . $this->getField("NAMA") . "', 
				USER_GROUP			= '" . $this->getField("USER_GROUP") . "', 
				KETERANGAN			= '" . $this->getField("KETERANGAN") . "', 
				DOKUMEN				= '" . $this->getField("DOKUMEN") . "', 
				UKURAN_DOKUMEN		= '" . $this->getField("UKURAN_DOKUMEN") . "', 
				UPDATED_BY			= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE		= CURRENT_TIMESTAMP
			WHERE PETUNJUK_PENGGUNAAN_ID 	= '" . $this->getField("PETUNJUK_PENGGUNAAN_ID") . "'
		";

		// echo $str;exit();
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE PETUNJUK_PENGGUNAAN A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "'
			WHERE PETUNJUK_PENGGUNAAN_ID = " . $this->getField("PETUNJUK_PENGGUNAAN_ID");

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function delete()
	{
		$str = "DELETE FROM PETUNJUK_PENGGUNAAN
			WHERE PETUNJUK_PENGGUNAAN_ID = '" . $this->getField("PETUNJUK_PENGGUNAAN_ID") . "'";

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
	function selectByParams($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PETUNJUK_PENGGUNAAN_ID ASC")
	{
		$str = "SELECT PETUNJUK_PENGGUNAAN_ID,NAMA,USER_GROUP,KETERANGAN,DOKUMEN,UKURAN_DOKUMEN,
				STATUS,CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM PETUNJUK_PENGGUNAAN A
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


	function selectByParamsMonitoring($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PETUNJUK_PENGGUNAAN_ID ASC")
	{
		$str = "SELECT PETUNJUK_PENGGUNAAN_ID,NAMA,USER_GROUP,KETERANGAN,DOKUMEN,UKURAN_DOKUMEN,
				STATUS,CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM PETUNJUK_PENGGUNAAN A
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
		$str = "SELECT PETUNJUK_PENGGUNAAN_ID,NAMA,USER_GROUP,KETERANGAN,DOKUMEN,UKURAN_DOKUMEN,
				STATUS,CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM PETUNJUK_PENGGUNAAN 
			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement . " ORDER BY PETUNJUK_PENGGUNAAN_ID DESC";
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
		$str = " SELECT COUNT(1) AS ROWCOUNT 
			FROM PETUNJUK_PENGGUNAAN 
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
			FROM PETUNJUK_PENGGUNAAN 
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
