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

class PejabatPengganti extends Entity
{

	var $query;
	/**
	 * Class constructor.
	 **/
	function PejabatPengganti()
	{
		$this->Entity();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PEJABAT_PENGGANTI_ID", $this->getNextId("PEJABAT_PENGGANTI_ID", "PEJABAT_PENGGANTI"));
		$str = "INSERT INTO PEJABAT_PENGGANTI(
				PEJABAT_PENGGANTI_ID, 
				PERUSAHAAN_ID, 
				CABANG_ID, 
				SATUAN_KERJA_ID,
				SATUAN_KERJA,
				PEGAWAI_ID,  
				PEGAWAI,  
				JENIS, 
				TANGGAL_MULAI, 
				TANGGAL_SELESAI, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES ('" . $this->getField("PEJABAT_PENGGANTI_ID") . "', 
				'" . $this->getField("PERUSAHAAN_ID") . "', 
				'" . $this->getField("CABANG_ID") . "', 
				'" . $this->getField("SATUAN_KERJA_ID") . "', 
				'" . $this->getField("SATUAN_KERJA") . "', 
				" . $this->getField("PEGAWAI_ID") . ", 
				'" . $this->getField("PEGAWAI") . "', 
				'" . $this->getField("JENIS") . "', 
				" . $this->getField("TANGGAL_MULAI") . ", 
				" . $this->getField("TANGGAL_SELESAI") . ", 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		// echo "GAGAL|".$str;exit;
		$this->id = $this->getField("PEJABAT_PENGGANTI_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}


	function update()
	{
		$str = "UPDATE PEJABAT_PENGGANTI
				SET 
				PERUSAHAAN_ID			= '" . $this->getField("PERUSAHAAN_ID") . "', 
				CABANG_ID				= '" . $this->getField("CABANG_ID") . "', 
				SATUAN_KERJA_ID			= '" . $this->getField("SATUAN_KERJA_ID") . "', 
				SATUAN_KERJA			= '" . $this->getField("SATUAN_KERJA") . "', 
				PEGAWAI_ID				= " . $this->getField("PEGAWAI_ID") . ", 
				PEGAWAI					= '" . $this->getField("PEGAWAI") . "', 
				JENIS					= '" . $this->getField("JENIS") . "', 
				TANGGAL_MULAI			= " . $this->getField("TANGGAL_MULAI") . ", 
				TANGGAL_SELESAI			= " . $this->getField("TANGGAL_SELESAI") . ", 
				UPDATED_BY				= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE			= CURRENT_TIMESTAMP
			WHERE PEJABAT_PENGGANTI_ID 	= '" . $this->getField("PEJABAT_PENGGANTI_ID") . "'
		";

		// echo "GAGAL|".$str;exit();
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE PEJABAT_PENGGANTI A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "'
			WHERE PEJABAT_PENGGANTI_ID = " . $this->getField("PEJABAT_PENGGANTI_ID");

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}


	function delete()
	{
		$str = "DELETE FROM PEJABAT_PENGGANTI
			WHERE PEJABAT_PENGGANTI_ID = '" . $this->getField("PEJABAT_PENGGANTI_ID") . "'";

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
	function selectByParams($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEJABAT_PENGGANTI_ID ASC")
	{
		$str = "SELECT PEJABAT_PENGGANTI_ID, PERUSAHAAN_ID, CABANG_ID, SATUAN_KERJA_ID, SATUAN_KERJA, 
				PEGAWAI_ID, PEGAWAI, JENIS, TANGGAL_MULAI, TANGGAL_SELESAI,
				CREATED_BY, CREATED_DATE, UPDATED_BY,  UPDATED_DATE
			FROM PEJABAT_PENGGANTI A
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


	function selectByParamsMonitoring($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEJABAT_PENGGANTI_ID ASC")
	{
		$str = "SELECT A.PEJABAT_PENGGANTI_ID, A.PERUSAHAAN_ID, B.NAMA NAMA_PERUSAHAAN, A.CABANG_ID, C.NAMA NAMA_CABANG, 
				A.SATUAN_KERJA_ID, A.SATUAN_KERJA, A.PEGAWAI_ID, A.PEGAWAI, A.JENIS, A.TANGGAL_MULAI, A.TANGGAL_SELESAI,
				A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY,  A.UPDATED_DATE
			FROM PEJABAT_PENGGANTI A
			LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID=A.PERUSAHAAN_ID
			LEFT JOIN CABANG C ON C.CABANG_ID=A.CABANG_ID
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
		$str = "SELECT PEJABAT_PENGGANTI_ID, PERUSAHAAN_ID, CABANG_ID, SATUAN_KERJA_ID, SATUAN_KERJA, 
				PEGAWAI_ID, PEGAWAI, JENIS, TANGGAL_MULAI, TANGGAL_SELESAI,
				CREATED_BY, CREATED_DATE, UPDATED_BY,  UPDATED_DATE
			FROM PEJABAT_PENGGANTI A
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement . " ORDER BY PEJABAT_PENGGANTI_ID DESC";
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
		$str = "SELECT COUNT(1) AS ROWCOUNT 
			FROM PEJABAT_PENGGANTI A 
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
		$str = "SELECT COUNT(1) AS ROWCOUNT 
			FROM PEJABAT_PENGGANTI A 
			LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID=A.PERUSAHAAN_ID
			LEFT JOIN CABANG C ON C.CABANG_ID=A.CABANG_ID
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
