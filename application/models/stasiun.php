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

class Stasiun extends Entity{ 

	var $query;
	/**
	* Class constructor.
	**/
	function Stasiun()
	{
		$this->Entity(); 
	}

	function insert()
	{
		$str = "
			INSERT INTO STASIUN (
				KODE, NAMA, ALAMAT, KELURAHAN_ID, KECAMATAN_ID, KOTA_ID, PROVINSI_ID, TELEPON, FAX, CREATED_BY, CREATED_DATE) 
			VALUES (
				
				'".$this->getField("KODE")."', 
				'".$this->getField("NAMA")."', 
				'".$this->getField("ALAMAT")."', 
				'".$this->getField("KELURAHAN_ID")."', 
				'".$this->getField("KECAMATAN_ID")."', 
				'".$this->getField("KOTA_ID")."', 
				'".$this->getField("PROVINSI_ID")."', 
				'".$this->getField("TELEPON")."', 
				'".$this->getField("FAX")."', 
				'".$this->getField("CREATED_BY")."', 
				CURRENT_TIMESTAMP
			)
		"; 

		// echo $str;exit;
		$this->id = $this->getField("STASIUN_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function update()
	{
		$str = "
			UPDATE STASIUN
			SET NAMA   		 		= '".$this->getField("NAMA")."',
			KODE    				= '".$this->getField("KODE")."',
			ALAMAT    				= '".$this->getField("ALAMAT")."',
			KELURHAN_ID    			= '".$this->getField("KELURHAN_ID")."',
			KECAMATAN_ID    		= '".$this->getField("KECAMATAN_ID")."',
			KOTA_ID    				= '".$this->getField("KOTA_ID")."',
			PROVINSI_ID    			= '".$this->getField("PROVINSI_ID")."',
			TELEPON    				= '".$this->getField("TELEPON")."',
			FAX    					= '".$this->getField("FAX")."',
			UPDATED_BY    			= '".$this->getField("UPDATED_BY")."',
			UPDATED_DATE    		= CURRENT_TIMESTAMP
			WHERE STASIUN_ID    	= '".$this->getField("STASIUN_ID")."'
		";

		// echo $str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE STASIUN A SET
			".$this->getField("FIELD")." 	= '".$this->getField("FIELD_VALUE")."'
			WHERE STASIUN_ID 			= '".$this->getField("STASIUN_ID")."'";

		// echo $str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}


	function delete()
	{
		$str = "
			DELETE FROM STASIUN
			WHERE STASIUN_ID = '".$this->getField("STASIUN_ID")."'"; 

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

	function selectByParams($paramsArray=array(), $limit=-1, $from=-1, $statement="", $order=" ORDER BY STASIUN_ID ASC")
	{
		$str = "
			SELECT STASIUN_ID, KODE, NAMA, ALAMAT,  KELURAHAN_ID, KECAMATAN_ID, KOTA_ID, PROVINSI_ID, TELEPON, FAX,  
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM STASIUN A
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

	
	function selectByParamsMonitoring($paramsArray=array(), $limit=-1, $from=-1, $statement="", $order=" ORDER BY STASIUN_ID ASC")
	{
		$str = "
			SELECT STASIUN_ID, KODE, NAMA, ALAMAT,  KELURAHAN_ID, KECAMATAN_ID, KOTA_ID, PROVINSI_ID, TELEPON, FAX,  
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM STASIUN A
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
			SELECT STASIUN_ID, KODE, NAMA, ALAMAT,  KELURAHAN_ID, KECAMATAN_ID, KOTA_ID, PROVINSI_ID, TELEPON, FAX,  
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM STASIUN A
			WHERE 1 = 1
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement." ORDER BY STASIUN_ID DESC";
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
			SELECT COUNT(STASIUN_ID) AS ROWCOUNT FROM STASIUN A 
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
			SELECT COUNT(STASIUN_ID) AS ROWCOUNT FROM STASIUN A
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
			SELECT COUNT(STASIUN_ID) AS ROWCOUNT FROM STASIUN A
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