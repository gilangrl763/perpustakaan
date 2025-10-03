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

class Kereta extends Entity{ 

	var $query;
	/**
	* Class constructor.
	**/
	function Kereta()
	{
		$this->Entity(); 
	}

	function insert()
	{
		$str = "
			INSERT INTO KERETA (
				KODE, NAMA, CREATED_BY, CREATED_DATE) 
			VALUES (
				
				'".$this->getField("KODE")."', 
				'".$this->getField("NAMA")."', 
				'".$this->getField("CREATED_BY")."', 
				CURRENT_TIMESTAMP
			)
		"; 

		// echo $str;exit;
		$this->id = $this->getField("KERETA_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function update()
	{
		$str = "
			UPDATE KERETA
			SET NAMA   		 		= '".$this->getField("NAMA")."',
			KODE    				= '".$this->getField("KODE")."',
			UPDATED_BY    			= '".$this->getField("UPDATED_BY")."',
			UPDATED_DATE    		= CURRENT_TIMESTAMP
			WHERE KERETA_ID    		= '".$this->getField("KERETA_ID")."'
		";

		// echo $str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE KERETA A SET
			".$this->getField("FIELD")." 	= '".$this->getField("FIELD_VALUE")."'
			WHERE KERETA_ID 			= '".$this->getField("KERETA_ID")."'";

		// echo $str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}


	function delete()
	{
		$str = "
			DELETE FROM KERETA
			WHERE KERETA_ID = '".$this->getField("KERETA_ID")."'"; 

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

	function selectByParams($paramsArray=array(), $limit=-1, $from=-1, $statement="", $order=" ORDER BY KERETA_ID ASC")
	{
		$str = "
			SELECT KERETA_ID, KODE, NAMA, CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM KERETA A
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

	
	function selectByParamsMonitoring($paramsArray=array(), $limit=-1, $from=-1, $statement="", $order=" ORDER BY KERETA_ID ASC")
	{
		$str = "
			SELECT KERETA_ID, KODE, NAMA, CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM KERETA A
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
			SELECT KERETA_ID, KODE, NAMA, CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM KERETA A
			WHERE 1 = 1
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement." ORDER BY KERETA_ID DESC";
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
			SELECT COUNT(KERETA_ID) AS ROWCOUNT FROM KERETA A 
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
			SELECT COUNT(KERETA_ID) AS ROWCOUNT FROM KERETA A
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
			SELECT COUNT(KERETA_ID) AS ROWCOUNT FROM KERETA A
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