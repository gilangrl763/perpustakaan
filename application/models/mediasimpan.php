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

class MediaSimpan extends Entity
{

	var $query;
	/**
	 * Class constructor.
	 **/
	function MediaSimpan()
	{
		$this->Entity();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("MEDIA_SIMPAN_ID", $this->getNextId("MEDIA_SIMPAN_ID", "MEDIA_SIMPAN"));
		$str = "INSERT INTO MEDIA_SIMPAN(
				MEDIA_SIMPAN_ID, 
				KODE, 
				NAMA, 
				KETERANGAN, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES ('" . $this->getField("MEDIA_SIMPAN_ID") . "', 
				'" . $this->getField("KODE") . "', 
				'" . $this->getField("NAMA") . "', 
				'" . $this->getField("KETERANGAN") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		$this->id = $this->getField("MEDIA_SIMPAN_ID");
		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function update()
	{
		$str = "UPDATE MEDIA_SIMPAN
				SET 
				KODE				= '" . $this->getField("KODE") . "', 
				NAMA				= '" . $this->getField("NAMA") . "', 
				KETERANGAN			= '" . $this->getField("KETERANGAN") . "', 
				UPDATED_BY			= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE		= CURRENT_TIMESTAMP
			WHERE MEDIA_SIMPAN_ID 	= '" . $this->getField("MEDIA_SIMPAN_ID") . "'
		";

		// echo $str;exit();
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE MEDIA_SIMPAN A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "'
			WHERE MEDIA_SIMPAN_ID = " . $this->getField("MEDIA_SIMPAN_ID");

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function delete()
	{
		$str = "DELETE FROM MEDIA_SIMPAN
			WHERE MEDIA_SIMPAN_ID = '" . $this->getField("MEDIA_SIMPAN_ID") . "'";

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
	function selectByParams($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY MEDIA_SIMPAN_ID ASC")
	{
		$str = "  SELECT MEDIA_SIMPAN_ID,KODE,NAMA,KETERANGAN,STATUS,CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM MEDIA_SIMPAN 
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


	function selectByParamsMonitoring($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY MEDIA_SIMPAN_ID ASC")
	{
		$str = " SELECT MEDIA_SIMPAN_ID, KODE, NAMA, KETERANGAN,STATUS,CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM MEDIA_SIMPAN A
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
		$str = "  SELECT MEDIA_SIMPAN_ID, KODE, NAMA, KETERANGAN,STATUS,CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
				FROM MEDIA_SIMPAN
				WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement . " ORDER BY MEDIA_SIMPAN_ID DESC";
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
			FROM MEDIA_SIMPAN 
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
			FROM MEDIA_SIMPAN 
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
