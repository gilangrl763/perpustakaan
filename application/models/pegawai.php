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

class Pegawai extends Entity
{

	var $query;
	/**
	 * Class constructor.
	 **/
	function Pegawai()
	{
		$this->Entity();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PEGAWAI_ID", $this->getNextId("PEGAWAI_ID", "PEGAWAI"));
		$str = "INSERT INTO PEGAWAI(
				PEGAWAI_ID, 
				KODE, 
				NAMA, 
				JABATAN, 
				JENIS_PEGAWAI, 
				JENIS_KELAMIN, 
				TEMPAT_LAHIR, 
				TANGGAL_LAHIR, 
				ALAMAT, 
				TELEPON, 
				EMAIL, 
				PERUSAHAAN_ID, 
				CABANG_ID, 
				SATUAN_KERJA_ID, 
				KATEGORI_PEGAWAI, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES ('" . $this->getField("PEGAWAI_ID") . "', 
				'" . $this->getField("KODE") . "', 
				'" . $this->getField("NAMA") . "', 
				'" . $this->getField("JABATAN") . "', 
				'" . $this->getField("JENIS_PEGAWAI") . "', 
				'" . $this->getField("JENIS_KELAMIN") . "', 
				'" . $this->getField("TEMPAT_LAHIR") . "', 
				" . $this->getField("TANGGAL_LAHIR") . ", 
				'" . $this->getField("ALAMAT") . "', 
				'" . $this->getField("TELEPON") . "', 
				'" . $this->getField("EMAIL") . "', 
				'" . $this->getField("PERUSAHAAN_ID") . "', 
				'" . $this->getField("CABANG_ID") . "', 
				'" . $this->getField("SATUAN_KERJA_ID") . "', 
				'" . $this->getField("KATEGORI_PEGAWAI"). "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		// echo "GAGAL|".$str;exit;
		$this->id = $this->getField("PEGAWAI_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}


	function update()
	{
		$str = "UPDATE PEGAWAI
				SET 
				KODE				= '" . $this->getField("KODE") . "', 
				NAMA				= '" . $this->getField("NAMA") . "', 
				JABATAN				= '" . $this->getField("JABATAN") . "', 
				JENIS_PEGAWAI		= '" . $this->getField("JENIS_PEGAWAI") . "', 
				JENIS_KELAMIN		= '" . $this->getField("JENIS_KELAMIN") . "', 
				TEMPAT_LAHIR		= '" . $this->getField("TEMPAT_LAHIR") . "', 
				TANGGAL_LAHIR		= " . $this->getField("TANGGAL_LAHIR") . ", 
				ALAMAT				= '" . $this->getField("ALAMAT") . "', 
				TELEPON				= '" . $this->getField("TELEPON") . "', 
				EMAIL				= '" . $this->getField("EMAIL") . "', 
				PERUSAHAAN_ID		= '" . $this->getField("PERUSAHAAN_ID") . "', 
				CABANG_ID			= '" . $this->getField("CABANG_ID") . "', 
				SATUAN_KERJA_ID		= '" . $this->getField("SATUAN_KERJA_ID") . "', 
				KATEGORI_PEGAWAI	= '" . $this->getField("KATEGORI_PEGAWAI") . "', 
				UPDATED_BY			= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE		= CURRENT_TIMESTAMP
			WHERE PEGAWAI_ID 		= '" . $this->getField("PEGAWAI_ID") . "'
		";

		// echo "GAGAL|".$str;exit();
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE PEGAWAI A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "'
			WHERE PEGAWAI_ID = " . $this->getField("PEGAWAI_ID");

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}


	function delete()
	{
		$str = "DELETE FROM PEGAWAI
			WHERE PEGAWAI_ID = '" . $this->getField("PEGAWAI_ID") . "'";

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
	function selectByParams($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEGAWAI_ID ASC")
	{
		$str = "SELECT A.PEGAWAI_ID,A.KODE,A.NAMA,A.JABATAN,A.JENIS_PEGAWAI,A.JENIS_KELAMIN,A.TEMPAT_LAHIR, 
				A.TANGGAL_LAHIR,A.ALAMAT,A.TELEPON,A.EMAIL,A.STATUS,A.PERUSAHAAN_ID,A.CABANG_ID,A.SATUAN_KERJA_ID,A.KATEGORI_PEGAWAI,
				A.CREATED_BY,A.CREATED_DATE,A.UPDATED_BY, A.UPDATED_DATE
			FROM PEGAWAI A
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


	function selectByParamsMonitoring($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEGAWAI_ID ASC")
	{
		$str = "SELECT A.PEGAWAI_ID,A.KODE,A.NAMA,A.JABATAN,A.JENIS_PEGAWAI,A.JENIS_KELAMIN,A.TEMPAT_LAHIR, 
				A.TANGGAL_LAHIR,A.ALAMAT,A.TELEPON,A.EMAIL,A.STATUS,A.PERUSAHAAN_ID,B.NAMA NAMA_PERUSAHAAN, 
				A.CABANG_ID,C.NAMA NAMA_CABANG,A.SATUAN_KERJA_ID,D.NAMA NAMA_SATUAN_KERJA,A.KATEGORI_PEGAWAI,A.CREATED_BY,
				A.CREATED_DATE,A.UPDATED_BY,A.UPDATED_DATE
			FROM PEGAWAI A
			LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID = A.PERUSAHAAN_ID
			LEFT JOIN CABANG C ON C.CABANG_ID = A.CABANG_ID
			LEFT JOIN SATUAN_KERJA D ON D.SATUAN_KERJA_ID = A.SATUAN_KERJA_ID
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
		$str = "SELECT A.PEGAWAI_ID,A.KODE,A.NAMA,A.JABATAN,A.JENIS_PEGAWAI,A.JENIS_KELAMIN,A.TEMPAT_LAHIR, 
				A.TANGGAL_LAHIR,A.ALAMAT,A.TELEPON,A.EMAIL,A.STATUS,A.PERUSAHAAN_ID,B.NAMA NAMA_PERUSAHAAN, 
				A.CABANG_ID,C.NAMA NAMA_CABANG,A.SATUAN_KERJA_ID,D.NAMA NAMA_SATUAN_KERJA,A.KATEGORI_PEGAWAI,A.CREATED_BY,
				A.CREATED_DATE,A.UPDATED_BY,A.UPDATED_DATE
			FROM PEGAWAI A
			LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID = A.PERUSAHAAN_ID
			LEFT JOIN CABANG C ON C.CABANG_ID = A.CABANG_ID
			LEFT JOIN SATUAN_KERJA D ON D.SATUAN_KERJA_ID = A.SATUAN_KERJA_ID
			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement . " ORDER BY PEGAWAI_ID DESC";
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
			FROM PEGAWAI A 
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
			FROM PEGAWAI A 
			LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID = A.PERUSAHAAN_ID
			LEFT JOIN CABANG C ON C.CABANG_ID = A.CABANG_ID
			LEFT JOIN SATUAN_KERJA D ON D.SATUAN_KERJA_ID = A.SATUAN_KERJA_ID
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
