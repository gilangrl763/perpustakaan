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

class Faq extends Entity
{

	var $query;
	/**
	 * Class constructor.
	 **/
	function Faq()
	{
		$this->Entity();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("FAQ_ID", $this->getNextId("FAQ_ID", "FAQ"));
		$str = "INSERT INTO FAQ(
				FAQ_ID, 
				PERTANYAAN, 
				JAWABAN, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES ('" . $this->getField("FAQ_ID") . "', 
				'" . $this->getField("PERTANYAAN") . "', 
				'" . $this->getField("JAWABAN") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		$this->id = $this->getField("FAQ_ID");
		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function update()
	{
		$str = "UPDATE FAQ
				SET 
				PERTANYAAN			= '" . $this->getField("PERTANYAAN") . "', 
				JAWABAN				= '" . $this->getField("JAWABAN") . "', 
				UPDATED_BY			= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE		= CURRENT_TIMESTAMP
			WHERE FAQ_ID 	= '" . $this->getField("FAQ_ID") . "'
		";

		// echo $str;exit();
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE FAQ A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "'
			WHERE FAQ_ID = " . $this->getField("FAQ_ID");

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function delete()
	{
		$str = "DELETE FROM FAQ
			WHERE FAQ_ID = '" . $this->getField("FAQ_ID") . "'";

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
	function selectByParams($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY FAQ_ID ASC")
	{
		$str = "SELECT FAQ_ID,PERTANYAAN,JAWABAN,STATUS,CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM FAQ 
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


	function selectByParamsMonitoring($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY FAQ_ID ASC")
	{
		$str = "SELECT FAQ_ID,PERTANYAAN,JAWABAN,STATUS,CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM FAQ A
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
		$str = "SELECT FAQ_ID,PERTANAYAAN,JAWABAN,STATUS,CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM FAQ A
			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement . " ORDER BY FAQ_ID DESC";
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
			FROM FAQ 
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
			FROM FAQ 
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
