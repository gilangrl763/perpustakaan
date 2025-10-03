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

class LogEmail extends Entity
{

	var $query;
	/**
	 * Class constructor.
	 **/
	function LogEmail()
	{
		$this->Entity();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("LOG_EMAIL_ID", $this->getNextId("LOG_EMAIL_ID", "LOG_EMAIL"));
		$str = "INSERT INTO LOG_EMAIL(
				LOG_EMAIL_ID, 
				KODE, 
				PRIMARY_ID, 
				JUDUL, 
				PEGAWAI_ID, 
				EMAIL, 
				KONTEN, 
				STATUS,  
				CREATED_DATE)
			VALUES ('" . $this->getField("LOG_EMAIL_ID") . "', 
				'" . $this->getField("KODE") . "', 
				'" . $this->getField("PRIMARY_ID") . "', 
				'" . $this->getField("JUDUL") . "', 
				'" . $this->getField("PEGAWAI_ID") . "', 
				'" . $this->getField("EMAIL") . "', 
				'" . $this->getField("KONTEN") . "', 
				'" . $this->getField("STATUS") . "', 
				CURRENT_TIMESTAMP
			)
		";

		$this->id = $this->getField("LOG_EMAIL_ID");
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
	function selectByParams($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY LOG_EMAIL_ID ASC")
	{
		$str = "SELECT LOG_EMAIL_ID, KODE, PRIMARY_ID, JUDUL, PEGAWAI_ID, EMAIL, KONTEN, STATUS, CREATED_DATE
			FROM LOG_EMAIL 
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


	function selectByParamsMonitoring($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY LOG_EMAIL_ID ASC")
	{
		$str = "SELECT LOG_EMAIL_ID, KODE, PRIMARY_ID, JUDUL, PEGAWAI_ID, EMAIL, KONTEN, STATUS, CREATED_DATE
			FROM LOG_EMAIL 
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

	/** 
	 * Hitung jumlah record berdasarkan parameter (array). 
	 * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","JAWABAN"=>"yyy") 
	 * @return long Jumlah record yang sesuai kriteria 
	 **/
	function getCountByParams($paramsArray = array(), $statement = "")
	{
		$str = " SELECT COUNT(1) AS ROWCOUNT 
			FROM LOG_EMAIL 
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
