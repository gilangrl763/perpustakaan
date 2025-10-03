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
include_once("Entity.php");

class UserLoginMobile extends Entity{ 

	var $query;
	/**
	* Class constructor.
	**/
	function UserLoginMobile()
	{
		$this->Entity(); 
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("USER_LOGIN_MOBILE_ID", $this->getNextId("USER_LOGIN_MOBILE_ID","USER_LOGIN_MOBILE")); 
		$this->setField("TOKEN", $this->getToken($this->getField("USER_LOGIN_MOBILE_ID")));

		$str = "
			INSERT INTO USER_LOGIN_MOBILE (USER_LOGIN_MOBILE_ID, USER_LOGIN_ID, USER_GROUP, PERUSAHAAN_ID, CABANG_ID, 
				SATUAN_KERJA_ID, PEGAWAI_ID, USER_LOGIN, TOKEN, DEVICE_ID, LOGIN_DATE
				) 
			VALUES (
				'".$this->getField("USER_LOGIN_MOBILE_ID")."', 
				'".$this->getField("USER_LOGIN_ID")."', 
				'".$this->getField("USER_GROUP")."',
				'".$this->getField("PERUSAHAAN_ID")."', 
				'".$this->getField("CABANG_ID")."', 
				'".$this->getField("SATUAN_KERJA_ID")."', 
				'".$this->getField("PEGAWAI_ID")."', 
				'".$this->getField("USER_LOGIN")."', 
				'".$this->getField("TOKEN")."', 
				'".$this->getField("DEVICE_ID")."', 
				CURRENT_TIMESTAMP
			)
		"; 

		// echo "GAGAL|".$str;exit;
		$this->id = $this->getField("USER_LOGIN_MOBILE_ID");
		$this->token = $this->getField("TOKEN");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function update()
	{
		$str = "
			UPDATE USER_LOGIN
			SET USER_GROUP   		= '".$this->getField("USER_GROUP")."',
			PERUSAHAAN_ID      		= '".$this->getField("PERUSAHAAN_ID")."',
			CABANG_ID     			= '".$this->getField("CABANG_ID")."',
			SATUAN_KERJA_ID    		= '".$this->getField("SATUAN_KERJA_ID")."',
			PEGAWAI_ID    			= '".$this->getField("PEGAWAI_ID")."',,
			USER_LOGIN    			= '".$this->getField("USER_LOGIN")."',
			UPDATED_BY    			= '".$this->getField("UPDATED_BY")."',
			UPDATED_DATE    		= CURRENT_TIMESTAMP
			WHERE USER_LOGIN_ID    	= '".$this->getField("USER_LOGIN_ID")."'
		";

		// echo $str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE USER_LOGIN A SET
			".$this->getField("FIELD")." 	= '".$this->getField("FIELD_VALUE")."',
			UPDATED_BY    					= '".$this->getField("UPDATED_BY")."',
			UPDATED_DATE    				= CURRENT_TIMESTAMP
			WHERE USER_LOGIN_ID 			= '".$this->getField("USER_LOGIN_ID")."'";

		// echo $str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function resetPassword()
	{
		$str = "
			UPDATE USER_LOGIN
			SET USER_PASSWORD    	= '".$this->getField("USER_PASSWORD")."',
			STATUS    				= '".$this->getField("STATUS")."',
			UPDATED_BY    			= '".$this->getField("UPDATED_BY")."',
			UPDATED_DATE    		= CURRENT_TIMESTAMP
			WHERE USER_LOGIN_ID    	= '".$this->getField("USER_LOGIN_ID")."'
		";

		// echo $str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function delete()
	{
		$str = "
			DELETE FROM USER_LOGIN
			WHERE USER_LOGIN_ID = '".$this->getField("USER_LOGIN_ID")."'"; 

		// echo $str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	/** 
	* Cari record berdasarkan array parameter dan limit tampilan 
	* @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PEGAWAI_ID"=>"yyy") 
	* @param int limit Jumlah maksimal record yang akan diambil 
	* @param int from Awal record yang diambil 
	* @return boolean True jika sukses, false jika tidak 
	**/ 

	function selectByToken($token){
		$str = "SELECT USER_LOGIN_MOBILE_ID, USER_LOGIN_ID, USER_GROUP, PERUSAHAAN_ID, CABANG_ID, SATUAN_KERJA_ID, 
				PEGAWAI_ID, USER_LOGIN, STATUS, LOGIN_DATE, TOKEN, DEVICE_ID, IMEI, TOKEN_FIREBASE, 
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM USER_LOGIN_MOBILE A
			WHERE A.STATUS='AKTIF' AND A.TOKEN ='$token'
		";

		// echo $str;exit;
		$this->query = $str;
		return $this->select($str);
	}

	function selectByParams($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY A.USER_LOGIN_ID ASC")
	{
		$str = " SELECT USER_LOGIN_ID, USER_GROUP, PERUSAHAAN_ID, CABANG_ID, SATUAN_KERJA_ID, PEGAWAI_ID, 
				USER_LOGIN, USER_PASSWORD,STATUS, CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM USER_LOGIN A 
			WHERE 1=1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;

		$this->query = $str;
		// echo $str;exit;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsMonitoring($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY A.USER_LOGIN_ID ASC")
	{
		$str = "SELECT A.USER_LOGIN_ID, A.USER_GROUP, A.PERUSAHAAN_ID, B.NAMA NAMA_PERUSAHAAN, 
				A.CABANG_ID, C.NAMA NAMA_CABANG, A.SATUAN_KERJA_ID,  D.NAMA NAMA_SATUAN_KERJA, 
				A.PEGAWAI_ID, E.NAMA NAMA_PEGAWAI, A.USER_LOGIN, A.USER_PASSWORD, E.EMAIL, A.STATUS, 
				A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
			FROM USER_LOGIN A 
			LEFT JOIN PERUSAHAAN B ON A.PERUSAHAAN_ID=B.PERUSAHAAN_ID
			LEFT JOIN CABANG C ON A.CABANG_ID=C.CABANG_ID
			LEFT JOIN SATUAN_KERJA D ON A.SATUAN_KERJA_ID=D.SATUAN_KERJA_ID
			LEFT JOIN PEGAWAI E ON A.PEGAWAI_ID=E.PEGAWAI_ID
			WHERE 1=1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;

		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsLike($paramsArray = array(), $limit = -1, $from = -1, $statement = "")
	{
		$str = "SELECT USER_LOGIN_ID, USER_LOGIN
			FROM USER_LOGIN A	
			WHERE 1=1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement . " ORDER BY USER_LOGIN_ID ASC";

		$this->query = $str;
		// echo $str;exit;	
		return $this->selectLimit($str, $limit, $from);
	}

	/** 
	* Hitung jumlah record berdasarkan parameter (array). 
	* @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PEGAWAI_ID"=>"yyy") 
	* @return long Jumlah record yang sesuai kriteria 
	**/ 
	function getCountByParams($paramsArray = array(), $statement = "")
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT 
			FROM USER_LOGIN A 
			WHERE 1=1" . $statement;

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		// echo $str;exit;
		$this->select($str);
		if ($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
	}

	function getCountByParamsMonitoring($paramsArray = array(), $statement = "")
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT 
			FROM USER_LOGIN A 
			LEFT JOIN PERUSAHAAN B ON A.PERUSAHAAN_ID=B.PERUSAHAAN_ID
			LEFT JOIN CABANG C ON A.CABANG_ID=C.CABANG_ID
			LEFT JOIN SATUAN_KERJA D ON A.SATUAN_KERJA_ID=D.SATUAN_KERJA_ID
			LEFT JOIN PEGAWAI E ON A.PEGAWAI_ID=E.PEGAWAI_ID
			WHERE 1=1 " . $statement;

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		// echo $str;exit;
		$this->select($str);
		if ($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
	}

	function getCountByParamsLike($paramsArray = array())
	{
		$str = "SELECT COUNT(USER_LOGIN_ID) AS ROWCOUNT 
			FROM USER_LOGIN 
			WHERE 1=1 ";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key LIKE '%$val%' ";
		}

		// echo $str;exit;
		$this->select($str);
		if ($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
	}	
} 
?>