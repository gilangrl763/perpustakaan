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

class jadwal extends Entity{ 

	var $query;
	/**
	* Class constructor.
	**/
	function jadwal()
	{
		$this->Entity(); 
	}

	function insert()
	{
		$str = "
			INSERT INTO JADWAL (
				KODE, KERETA_ID, STASIUN_ID_KEBERANGKATAN, TANGGAL_KEBERANGKATAN, JAM_KEBERANGKATAN, 
				STASIUN_ID_KEDATANGAN, TANGGAL_KEDATANGAN, JAM_KEDATANGAN, KETERANGAN, DURASI, KUOTA, HARGA, KELAS,
				CREATED_BY, CREATED_DATE) 
			VALUES (
				
				'".$this->getField("KODE")."', 
				'".$this->getField("KERETA_ID")."',
				'".$this->getField("STASIUN_ID_KEBERANGKATAN")."',
				'".$this->getField("TANGGAL_KEBERANGKATAN")."',
				'".$this->getField("JAM_KEBERANGKATAN")."',
				'".$this->getField("STASIUN_ID_KEDATANGAN")."',
				'".$this->getField("TANGGAL_KEDATANGAN")."',
				'".$this->getField("JAM_KEDATANGAN")."',
				'".$this->getField("KETERANGAN")."',
				'".$this->getField("DURASI")."',
				'".$this->getField("KUOTA")."',
				'".$this->getField("HARGA")."',
				'".$this->getField("KELAS")."',
				'".$this->getField("CREATED_BY")."', 
				CURRENT_TIMESTAMP
			)
		"; 

		 // echo "GAGAL|".$str;exit;
		$this->id = $this->getField("JADWAL_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function update()
	{
		$str = "
			UPDATE JADWAL
			SET KODE   		 			= '".$this->getField("KODE")."',
			KERETA_ID    				= '".$this->getField("KERETA_ID")."',
			STASIUN_ID_KEBERANGKATAN    = '".$this->getField("STASIUN_ID_KEBERANGKATAN")."',
			TANGGAL_KEBERANGKATAN    	= '".$this->getField("TANGGAL_KEBERANGKATAN")."',
			JAM_KEBERANGKATAN    		= '".$this->getField("JAM_KEBERANGKATAN")."',
			STASIUN_ID_KEDATANGAN    	= '".$this->getField("STASIUN_ID_KEDATANGAN")."',
			TANGGAL_KEDATANGAN    		= '".$this->getField("TANGGAL_KEDATANGAN")."',
			JAM_KEDATANGAN    			= '".$this->getField("JAM_KEDATANGAN")."',
			KETERANGAN    				= '".$this->getField("KETERANGAN")."',
			DURASI    					= '".$this->getField("DURASI")."',
			KUOTA    					= '".$this->getField("KUOTA")."',
			HARGA    					= '".$this->getField("HARGA")."',
			KELAS    					= '".$this->getField("KELAS")."',
			UPDATED_BY    				= '".$this->getField("UPDATED_BY")."',
			UPDATED_DATE    			= CURRENT_TIMESTAMP
			WHERE JADWAL_ID    			= '".$this->getField("JADWAL_ID")."'
		";

		// echo $str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE JADWAL A SET
			".$this->getField("FIELD")." 	= '".$this->getField("FIELD_VALUE")."'
			WHERE JADWAL_ID 				= '".$this->getField("JADWAL_ID")."'";

		// echo $str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}


	function delete()
	{
		$str = "
			DELETE FROM JADWAL
			WHERE JADWAL_ID = '".$this->getField("JADWAL_ID")."'"; 

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

	function selectByParams($paramsArray=array(), $limit=-1, $from=-1, $statement="", $order=" ORDER BY JADWAL_ID ASC")
	{
		$str = "
			SELECT JADWAL_ID, KODE, KERETA_ID, STASIUN_ID_KEBERANGKATAN, TANGGAL_KEBERANGKATAN, JAM_KEBERANGKATAN, 
				STASIUN_ID_KEDATANGAN, TANGGAL_KEDATANGAN, JAM_KEDATANGAN, KETERANGAN, KUOTA, HARGA, KELAS,
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM JADWAL A
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

	
	function selectByParamsMonitoring($paramsArray=array(), $limit=-1, $from=-1, $statement="", $order=" ORDER BY JADWAL_ID ASC")
	{
		$str = "
			SELECT JADWAL_ID, A.KODE, A.KERETA_ID, B.KODE KODE_KERETA, B.NAMA KERETA, A.KELAS, A.STASIUN_ID_KEBERANGKATAN, 
				C.NAMA STASIUN_KEBERANGKATAN, A.TANGGAL_KEBERANGKATAN, A.JAM_KEBERANGKATAN, 
				A.STASIUN_ID_KEDATANGAN, D.NAMA STASIUN_KEDATANGAN, A.TANGGAL_KEDATANGAN, A.JAM_KEDATANGAN, 
				A.KETERANGAN, A.DURASI, A.HARGA, A.KUOTA,
				A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
			FROM JADWAL A
			LEFT JOIN KERETA B ON B.KERETA_ID=A.KERETA_ID
			LEFT JOIN STASIUN C ON C.STASIUN_ID=A.STASIUN_ID_KEBERANGKATAN
			LEFT JOIN STASIUN D ON D.STASIUN_ID=A.STASIUN_ID_KEDATANGAN
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
			SELECT JADWAL_ID, KODE, KERETA_ID, STASIUN_ID_KEBERANGKATAN, TANGGAL_KEBERANGKATAN, JAM_KEBERANGKATAN, STASIUN_ID_KEDATANGAN, TANGGAL_KEDATANGAN, JAM_KEDATANGAN, KETERANGAN, CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM JADWAL A
			WHERE 1 = 1
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement." ORDER BY JADWAL_ID DESC";
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
			SELECT COUNT(JADWAL_ID) AS ROWCOUNT FROM Jadwal A 
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
			SELECT COUNT(JADWAL_ID) AS ROWCOUNT FROM JADWAL A
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
			SELECT COUNT(JADWAL_ID) AS ROWCOUNT FROM JADWAL A
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