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

class Banner extends Entity
{

	var $query;
	/**
	 * Class constructor.
	 **/
	function Banner()
	{
		$this->Entity();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("BANNER_ID", $this->getNextId("BANNER_ID", "BANNER"));
		$str = " INSERT INTO BANNER(
				BANNER_ID, 
				NAMA, 
				KETERANGAN, 
				GAMBAR, 
				STATUS, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES ('" . $this->getField("BANNER_ID") . "', 
				'" . $this->getField("NAMA") . "', 
				'" . $this->getField("KETERANGAN") . "', 
				'" . $this->getField("GAMBAR") . "', 
				'" . $this->getField("STATUS") . "', 
				'" . $this->USER_LOGIN_ID . "',
				CURRENT_TIMESTAMP
			)
		";

		$this->id = $this->getField("BANNER_ID");
		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function update()
	{
		$str = " UPDATE BANNER
				SET 
				NAMA			='" . $this->getField("NAMA") . "', 
				KETERANGAN		='" . $this->getField("KETERANGAN") . "', 
				GAMBAR			='" . $this->getField("GAMBAR") . "', 
				STATUS			='" . $this->getField("STATUS") . "', 
				UPDATED_BY		='" . $this->USER_LOGIN_ID . "', 
				UPDATED_DATE	= CURRENT_TIMESTAMP
			WHERE BANNER_ID = '" . $this->getField("BANNER_ID") . "'
		";

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE BANNER A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "'
			WHERE BANNER_ID = " . $this->getField("BANNER_ID");

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function delete()
	{
		$str = " DELETE FROM BANNER
			WHERE BANNER_ID = '" . $this->getField("BANNER_ID") . "'";

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
	function selectByParams($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY BANNER_ID ASC")
	{
		$str = " SELECT A.BANNER_ID, A.NAMA, A.KETERANGAN, A.GAMBAR, A.STATUS, 
				A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
			FROM BANNER A
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


	function selectByParamsMonitoring($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY BANNER_ID ASC")
	{
		$str = " SELECT A.BANNER_ID, A.NAMA, A.KETERANGAN, A.GAMBAR, A.STATUS, 
				A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
			FROM BANNER A
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
		$str = " SELECT A.BANNER_ID, A.NAMA, A.KETERANGAN, A.GAMBAR, A.STATUS, 
				A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
			FROM BANNER A
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement . " ORDER BY BANNER_ID DESC";
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
		$str = " SELECT COUNT(BANNER_ID) AS ROWCOUNT FROM BANNER A 
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

	function getCountByParamsMonitoring($whichUser = 'CREATED_BY', $paramsArray = array(), $statement = "")
	{
		$str = "SELECT COUNT(BANNER_ID) AS ROWCOUNT FROM BANNER A
			LEFT JOIN PEGAWAI B ON A." . $whichUser . "=B.PEGAWAI_ID
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
