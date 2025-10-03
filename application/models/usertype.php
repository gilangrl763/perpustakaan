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

class UserType extends Entity{ 

	var $query;
	/**
	* Class constructor.
	**/
	function UserType()
	{
		$this->Entity(); 
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("USER_TYPE_ID", $this->getNextId("USER_TYPE_ID","USER_TYPE")); 
		$str = "
			INSERT INTO USER_TYPE (
				USER_TYPE_ID, NAMA) 
			VALUES (
				'".$this->getField("USER_TYPE_ID")."', 
				'".$this->getField("NAMA")."'
			)
		"; 

		// echo $str;exit;
		$this->id = $this->getField("USER_TYPE_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function update()
	{
		$str = "
			UPDATE USER_TYPE
			SET NAMA    = '".$this->getField("NAMA")."'
			WHERE USER_TYPE_ID    	= '".$this->getField("USER_TYPE_ID")."'
		";

		// echo $str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE USER_TYPE A SET
			".$this->getField("FIELD")." 	= '".$this->getField("FIELD_VALUE")."'
			WHERE USER_TYPE_ID 			= '".$this->getField("USER_TYPE_ID")."'";

		// echo $str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function delete()
	{
		$str = "
			DELETE FROM USER_TYPE
			WHERE USER_TYPE_ID = '".$this->getField("USER_TYPE_ID")."'"; 

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

	function selectByParams($paramsArray=array(), $limit=-1, $from=-1, $statement="", $order=" ORDER BY USER_TYPE_ID ASC")
	{
		$str = "
			SELECT USER_TYPE_ID, NAMA
			FROM USER_TYPE A
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

	
	function selectByParamsMonitoring($paramsArray=array(), $limit=-1, $from=-1, $statement="", $order=" ORDER BY USER_TYPE_ID ASC")
	{
		$str = "
			SELECT USER_TYPE_ID, NAMA
			FROM USER_TYPE A
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
			SELECT USER_TYPE_ID, NAMA
			FROM USER_TYPE A
			WHERE 1 = 1
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement." ORDER BY USER_TYPE_ID DESC";
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
			SELECT COUNT(USER_TYPE_ID) AS ROWCOUNT FROM USER_TYPE A 
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
			SELECT COUNT(USER_TYPE_ID) AS ROWCOUNT FROM USER_TYPE A
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
	
	function getCountByParamsLike($paramsArray=array())
	{
		$str = "
			SELECT COUNT(USER_TYPE_ID) AS ROWCOUNT FROM USER_TYPE A
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