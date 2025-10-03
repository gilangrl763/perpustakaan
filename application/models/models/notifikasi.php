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
  include_once(APPPATH.'/models/Entity.php');

  class Notifikasi extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function Notifikasi()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("NOTIFIKASI_ID", $this->getNextId("NOTIFIKASI_ID","NOTIFIKASI")); 
		$str = "
				INSERT INTO NOTIFIKASI (
				   NOTIFIKASI_ID, NOTIFIKASI_ID_PARENT, NAMA, KETERANGAN, KODE, 
				   RETENSI_AKTIF, RETENSI_INAKTIF, PENYUSUTAN_AKHIR_ID, 
				   LAST_CREATE_USER, LAST_CREATE_DATE) 
				VALUES(
				  '".$this->getField("NOTIFIKASI_ID")."',
				  '".$this->getField("NOTIFIKASI_ID_PARENT")."',
				  '".$this->getField("NAMA")."',
				  '".$this->getField("KETERANGAN")."',
				  '".$this->getField("KODE")."',
				  ".$this->getField("RETENSI_AKTIF").",
				  ".$this->getField("RETENSI_INAKTIF").",
				  ".$this->getField("PENYUSUTAN_AKHIR_ID").",
				  '".$this->getField("LAST_CREATE_USER")."',
				  CURRENT_DATE
				)"; 
		$this->id = $this->getField("NOTIFIKASI_ID");
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE NOTIFIKASI SET
				  NAMA 					= '".$this->getField("NAMA")."',
				  KETERANGAN 			= '".$this->getField("KETERANGAN")."',
				  KODE 					= '".$this->getField("KODE")."',
				  RETENSI_AKTIF 		= ".$this->getField("RETENSI_AKTIF").",
				  RETENSI_INAKTIF 		= ".$this->getField("RETENSI_INAKTIF").",
				  PENYUSUTAN_AKHIR_ID 	= ".$this->getField("PENYUSUTAN_AKHIR_ID").",
				  LAST_UPDATE_USER		= '".$this->getField("LAST_UPDATE_USER")."',
				  LAST_UPDATE_DATE		= CURRENT_DATE
				WHERE NOTIFIKASI_ID 	= '".$this->getField("NOTIFIKASI_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	

    function updateStatus()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE NOTIFIKASI SET
				  STATUS_AKTIF 			= '".$this->getField("STATUS_AKTIF")."'
				WHERE NOTIFIKASI_ID 	= '".$this->getField("NOTIFIKASI_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	
	function delete()
	{
        $str = "DELETE FROM NOTIFIKASI
                WHERE NOTIFIKASI_ID = '".$this->getField("NOTIFIKASI_ID")."'"; 
				  
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
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1,$statement="", $order=" ORDER BY NOTIFIKASI_ID ASC")
	{
		$str = "SELECT NOTIFIKASI_ID, KODE, NAMA, KETERANGAN, LINK, PEGAWAI_ID, PEGAWAI_ID_DARI, NAMA_DARI, JABATAN_DARI, 
					STATUS, CREATED_DATE, READ_DATE, PRIMARY_ID 
				FROM NOTIFIKASI A
				WHERE 1=1
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		$this->query = $str;
		// echo $str;exit;
		return $this->selectLimit($str,$limit,$from); 
  }


  function selectByParamsMonitoring($paramsArray=array(),$limit=-1,$from=-1,$statement="", $order=" ORDER BY NOTIFIKASI_ID ASC")
	{
		$str = "SELECT NOTIFIKASI_ID, KODE, NAMA, KETERANGAN, LINK, PEGAWAI_ID, PEGAWAI_ID_DARI, NAMA_DARI, JABATAN_DARI, 
					NAMA_DARI || ' (' || JABATAN_DARI || ')' AS DARI,
					STATUS, CASE WHEN STATUS='1' THEN 'Terbaca' ELSE 'Belum Dibaca' END STATUS_KET, 
					CREATED_DATE, READ_DATE, PRIMARY_ID 
				FROM NOTIFIKASI A
				WHERE 1=1
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		$this->query = $str;
		// echo $str;exit;
		return $this->selectLimit($str,$limit,$from); 
  }
    
    function selectByParamsGenerateArsip($satuanKerjaId, $paramsArray=array(),$limit=-1,$from=-1,$statement="", $order="")
	{
		$str = "SELECT 
				A.NOTIFIKASI_ID, NOTIFIKASI_ID_PARENT, A.NAMA, A.KETERANGAN, NO_URUT, A.KODE, 
				A.RETENSI_AKTIF, A.RETENSI_INAKTIF, A.PENYUSUTAN_AKHIR_ID, A.LAST_CREATE_DATE,
				(SELECT COUNT(1) + 1 FROM ARSIP X WHERE X.SATUAN_KERJA_ID = '".$satuanKerjaId."' AND X.NOTIFIKASI_ID = A.NOTIFIKASI_ID ) KODE_TERAKHIR
				FROM NOTIFIKASI A 
				WHERE 1 = 1
			"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		$this->query = $str;
		// echo $str;exit;
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement="")
	{
		$str = "
				SELECT 
				NOTIFIKASI_ID, NOTIFIKASI_ID_PARENT, NAMA, KETERANGAN, NO_URUT, KODE, 
				RETENSI_AKTIF, RETENSI_INAKTIF
				FROM NOTIFIKASI A
				WHERE 1 = 1
				"; 
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$str .= $statement." ORDER BY NOTIFIKASI_ID DESC";
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
		$str = "SELECT COUNT(1) AS ROWCOUNT 
			FROM NOTIFIKASI A WHERE 1 = 1 ".$statement; 

		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
  }

  function getCountByParamsMonitoring($paramsArray=array(), $statement="")
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT 
			FROM NOTIFIKASI A WHERE 1 = 1 ".$statement; 

		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
  }
	
    function generateNOTIFIKASIKode($reqParent)
	{
		$str = "SELECT LPAD(COALESCE((RIGHT(MAX(KODE), 2)::INT + 1), 1)::VARCHAR, 2, '0')  ROWCOUNT 
					FROM NOTIFIKASI A WHERE NOTIFIKASI_ID_PARENT = '".$reqParent."' ";
					
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return "01"; 
    }
	
	

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(NOTIFIKASI_ID) AS ROWCOUNT FROM NOTIFIKASI WHERE 1 = 1 "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }	
  } 
?>