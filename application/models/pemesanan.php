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

class Pemesanan extends Entity{ 

	var $query;
	/**
	* Class constructor.
	**/
	function Pemesanan()
	{
		$this->Entity(); 
	}

	function insert()
	{
		$str = "
			INSERT INTO PEMESANAN (
				KODE, USER_LOGIN_ID, JADWAL_ID, JUMLAH_PENUMPANG, HARGA, METODE_PEMBAYARAN, STATUS, KETERANGAN, 
				CREATED_BY, CREATED_DATE) 
			VALUES (
				
				'".$this->getField("KODE")."', 
				'".$this->getField("USER_LOGIN_ID")."',
				'".$this->getField("JADWAL_ID")."',
				'".$this->getField("JUMLAH_PENUMPANG")."',
				'".$this->getField("HARGA")."',
				'".$this->getField("METODE_PEMBAYARAN")."',
				'".$this->getField("STATUS")."',
				'".$this->getField("KETERANGAN")."',
				'".$this->getField("CREATED_BY")."', 
				CURRENT_TIMESTAMP
			)
		"; 

		 // echo "GAGAL|".$str;exit;
		$this->id = $this->getField("PEMESANAN_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function insertPenumpang()
	{
		$str = "
			INSERT INTO PENUMPANG (
				PEMESANAN_ID, TITLE_GENDER, NAMA, TIPE_IDENTITAS, NOMOR_IDENTITAS) 
			VALUES (
				
				'".$this->getField("PEMESANAN_ID")."', 
				'".$this->getField("TITLE_GENDER")."',
				'".$this->getField("NAMA")."',
				'".$this->getField("TIPE_IDENTITAS")."',
				'".$this->getField("NOMOR_IDENTITAS")."'
			)
		"; 

		 // echo "GAGAL|".$str;exit;
		$this->id = $this->getField("PEMESANAN_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function update()
	{
		$str = "
			UPDATE PEMESANAN
			SET USER_LOGIN_ID    		= '".$this->getField("USER_LOGIN_ID")."',
			JADWAL_ID    				= '".$this->getField("JADWAL_ID")."',
			JUMLAH_PENUMPANG    		= '".$this->getField("JUMLAH_PENUMPANG")."',
			HARGA    					= '".$this->getField("HARGA")."',
			METODE_PEMBAYARAN    		= '".$this->getField("METODE_PEMBAYARAN")."',
			STATUS    					= '".$this->getField("STATUS")."',
			KETERANGAN    				= '".$this->getField("KETERANGAN")."',
			UPDATED_BY    				= '".$this->getField("UPDATED_BY")."',
			UPDATED_DATE    			= CURRENT_TIMESTAMP
			WHERE 	PEMESANAN_ID    	= '".$this->getField("PEMESANAN_ID")."'
		";

		// echo $str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE PEMESANAN A SET
			".$this->getField("FIELD")." 	= '".$this->getField("FIELD_VALUE")."',
			UPDATED_BY    					= '".$this->getField("UPDATED_BY")."',
			UPDATED_DATE    				= CURRENT_TIMESTAMP
			WHERE 	PEMESANAN_ID 			= '".$this->getField("PEMESANAN_ID")."'";

		// echo $str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}


	function delete()
	{
		$str = "
			DELETE FROM PEMESANAN
			WHERE 	PEMESANAN_ID = '".$this->getField("PEMESANAN_ID")."'"; 

		//echo $str;exit;
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

	function selectByParams($paramsArray=array(), $limit=-1, $from=-1, $statement="", $order=" ORDER BY PEMESANAN_ID ASC")
	{
		$str = "
			SELECT PEMESANAN_ID, KODE, USER_LOGIN_ID, JADWAL_ID, JUMLAH_PENUMPANG, HARGA, METODE_PEMBAYARAN, STATUS, KETERANGAN, 
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM PEMESANAN A
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

	
	function selectByParamsMonitoring($paramsArray=array(), $limit=-1, $from=-1, $statement="", $order=" ORDER BY PEMESANAN_ID ASC")
	{
		$str = "
			SELECT PEMESANAN_ID, KODE, USER_LOGIN_ID, JADWAL_ID, JUMLAH_PENUMPANG, HARGA, METODE_PEMBAYARAN, STATUS, KETERANGAN, 
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM PEMESANAN A
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
			SELECT KODE, USER_LOGIN_ID, JADWAL_ID, JUMLAH_PENUMPANG, HARGA, METODE_PEMBAYARAN, STATUS, KETERANGAN, 
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM PEMESANAN A
			WHERE 1 = 1
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement." ORDER BY PEMESANAN_ID DESC";
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
			SELECT COUNT(PEMESANAN_ID) AS ROWCOUNT FROM PEMESANAN A 
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
			SELECT COUNT(PEMESANAN_ID) AS ROWCOUNT FROM PEMESANAN A
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
			SELECT COUNT(PEMESANAN_ID) AS ROWCOUNT FROM PEMESANAN A
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