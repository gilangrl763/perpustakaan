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

class UsulMusnah extends Entity
{

	var $query;
	/**
	 * Class constructor.
	 **/
	function UsulMusnah()
	{
		$this->Entity();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("USUL_MUSNAH_ID", $this->getNextId("USUL_MUSNAH_ID", "USUL_MUSNAH"));
		$str = "INSERT INTO USUL_MUSNAH(
				USUL_MUSNAH_ID, 
				PEMINDAHAN_ID, 
				KETERANGAN, 
				DOKUMEN, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES ('" . $this->getField("USUL_MUSNAH_ID") . "', 
				'" . $this->getField("PEMINDAHAN_ID") . "', 
				'" . $this->getField("KETERANGAN") . "', 
				'" . $this->getField("DOKUMEN") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		$this->id = $this->getField("USUL_MUSNAH_ID");
		// echo $str;exit();
		$this->query = $str;
		return $this->execQuery($str);
	}

	function update()
	{
		$str = "UPDATE USUL_MUSNAH
				SET 
				PEMINDAHAN_ID		= '" . $this->getField("PEMINDAHAN_ID") . "', 
				KETERANGAN			= '" . $this->getField("KETERANGAN") . "', 
				DOKUMEN				= '" . $this->getField("DOKUMEN") . "', 
				UPDATED_BY			= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE		= CURRENT_TIMESTAMP
			WHERE USUL_MUSNAH_ID 	= '" . $this->getField("USUL_MUSNAH_ID") . "'
		";

		// echo "GAGAL|".$str;exit();
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE USUL_MUSNAH A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "'
			WHERE USUL_MUSNAH_ID = " . $this->getField("USUL_MUSNAH_ID");

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function delete()
	{
		$str = "DELETE FROM USUL_MUSNAH
			WHERE USUL_MUSNAH_ID = '" . $this->getField("USUL_MUSNAH_ID") . "'";

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}


	/** 
	 * Cari record berdasarkan array parameter dan limit tampilan 
	 * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","JAWABAN"=>"yyy") 
	 * @param int limit Jumlah maksimal record yang akan diambil 
	 * @param int from Awal record yang diambil 
	 * @return boolean True jika sukses, false jika tidak 
	 **/
	function selectByParams($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY USUL_MUSNAH_ID ASC")
	{
		$str = "SELECT A.USUL_MUSNAH_ID,A.PEMINDAHAN_ID,A.KETERANGAN,A.DOKUMEN,A.STATUS,A.CREATED_BY,A.CREATED_DATE,A.UPDATED_BY,A.UPDATED_DATE
			FROM USUL_MUSNAH A
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

	function selectByParamsMonitoring($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY USUL_MUSNAH_ID ASC")
	{
		$str = "SELECT A.USUL_MUSNAH_ID,A.PEMINDAHAN_ID,A.KETERANGAN,A.DOKUMEN,A.STATUS,A.CREATED_BY,A.CREATED_DATE,A.UPDATED_BY,A.UPDATED_DATE
			FROM USUL_MUSNAH A
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
		$str = "SELECT A.USUL_MUSNAH_ID,A.PEMINDAHAN_ID,A.KETERANGAN,A.DOKUMEN,A.STATUS,A.CREATED_BY,A.CREATED_DATE,A.UPDATED_BY,A.UPDATED_DATE
			FROM USUL_MUSNAH A
			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement . " ORDER BY USUL_MUSNAH_ID DESC";
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
			FROM USUL_MUSNAH A 
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
			FROM USUL_MUSNAH A 
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
