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

class SatuanKerja extends Entity
{

	var $query;
	/**
	 * Class constructor.
	 **/
	function SatuanKerja()
	{
		$this->Entity();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("SATUAN_KERJA_ID", $this->getNextId("SATUAN_KERJA_ID", "SATUAN_KERJA"));
		$str = " INSERT INTO SATUAN_KERJA(
				SATUAN_KERJA_ID, 
				SATUAN_KERJA_ID_PARENT, 
				KODE, 
				NAMA, 
				KODE_JABATAN, 
				JABATAN, 
				CABANG_ID, 
				PERUSAHAAN_ID, 
				PEGAWAI_ID, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES ('" . $this->getField("SATUAN_KERJA_ID") . "', 
				'" . $this->getField("SATUAN_KERJA_ID_PARENT") . "', 
				'" . $this->getField("KODE") . "', 
				'" . $this->getField("NAMA") . "', 
				'" . $this->getField("KODE_JABATAN") . "', 
				'" . $this->getField("JABATAN") . "', 
				'" . $this->getField("CABANG_ID") . "', 
				'" . $this->getField("PERUSAHAAN_ID") . "', 
				" . $this->getField("PEGAWAI_ID") . ", 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";
		// echo "GAGAL|".$str;exit;
		$this->id = $this->getField("SATUAN_KERJA_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function update()
	{
		$str = " UPDATE SATUAN_KERJA
				SET 
				SATUAN_KERJA_ID_PARENT		= '" . $this->getField("SATUAN_KERJA_ID_PARENT") . "', 
				KODE						= '" . $this->getField("KODE") . "', 
				NAMA						= '" . $this->getField("NAMA") . "', 
				KODE_JABATAN				= '" . $this->getField("KODE_JABATAN") . "', 
				JABATAN						= '" . $this->getField("JABATAN") . "', 
				PERUSAHAAN_ID				= '" . $this->getField("PERUSAHAAN_ID") . "', 
				CABANG_ID					= '" . $this->getField("CABANG_ID") . "', 
				PEGAWAI_ID					= " . $this->getField("PEGAWAI_ID") . ", 
				UPDATED_BY					= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE				= CURRENT_TIMESTAMP
			WHERE SATUAN_KERJA_ID 			= '" . $this->getField("SATUAN_KERJA_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE SATUAN_KERJA A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "'
			WHERE SATUAN_KERJA_ID = " . $this->getField("SATUAN_KERJA_ID");

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function delete()
	{
		$str = "DELETE FROM SATUAN_KERJA
			WHERE SATUAN_KERJA_ID = '" . $this->getField("SATUAN_KERJA_ID") . "'";

		// echo $str;exit();
		$this->query = $str;
		return $this->execQuery($str);
	}

	/** 
	 * Cari record berdasarkan array parameter dan limit tampilan 
	 * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","JAWABAN"=>"yyy") 
	 * @param int limit Jumlah maksimal record yang akan diambil 
	 * @param int from Awal record yang diambil 
	 * @return boolean True jika sukses, false jika tidak 
	 **/
	function selectByParams($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY SATUAN_KERJA_ID ASC")
	{
		$str = "  SELECT SATUAN_KERJA_ID, SATUAN_KERJA_ID_PARENT, KODE, NAMA, PEGAWAI_ID, KODE_JABATAN, JABATAN, STATUS, PERUSAHAAN_ID, CABANG_ID,
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM SATUAN_KERJA A
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


	function selectByParamsMonitoring($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY SATUAN_KERJA_ID ASC")
	{
		$str = "  SELECT A.SATUAN_KERJA_ID, A.SATUAN_KERJA_ID_PARENT, A.KODE, A.NAMA, A.KODE_JABATAN, A.JABATAN, A.STATUS, 
					A.PERUSAHAAN_ID, B.NAMA NAMA_PERUSAHAAN, A.CABANG_ID, C.NAMA NAMA_CABANG, A.PEGAWAI_ID, D.NAMA NAMA_PEJABAT, 
					D.JABATAN JABATAN_PEJABAT, D.JENIS_PEGAWAI JENIS_PEGAWAI, 
					A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
				FROM SATUAN_KERJA A
				LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID = A.PERUSAHAAN_ID
				LEFT JOIN CABANG C ON C.CABANG_ID = A.CABANG_ID
				LEFT JOIN PEGAWAI D ON D.PEGAWAI_ID = A.PEGAWAI_ID
				WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit();
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsLike($paramsArray = array(), $limit = -1, $from = -1, $statement = "")
	{
		$str = "  SELECT A.SATUAN_KERJA_ID, A.SATUAN_KERJA_ID_PARENT, A.KODE, A.NAMA, A.STATUS, 
				A.PERUSAHAAN_ID, B.NAMA NAMA_PERUSAHAAN, A.CABANG_ID, C.NAMA NAMA_CABANG, 
				A.PEGAWAI_ID, D.JENIS_PEGAWAI JENIS_PEGAWAI, A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
			FROM SATUAN_KERJA A
			LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID = A.PERUSAHAAN_ID
			LEFT JOIN CABANG C ON C.CABANG_ID = A.CABANG_ID
			LEFT JOIN PEGAWAI D ON D.PEGAWAI_ID = A.PEGAWAI_ID
			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement . " ORDER BY SATUAN_KERJA_ID DESC";
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
			FROM SATUAN_KERJA A 
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
		$str = " SELECT COUNT(1) AS ROWCOUNT 
			FROM SATUAN_KERJA A 
			LEFT JOIN PERUSAHAAN B ON B.PERUSAHAAN_ID = A.PERUSAHAAN_ID
			LEFT JOIN CABANG C ON C.CABANG_ID = A.CABANG_ID
			LEFT JOIN PEGAWAI D ON D.PEGAWAI_ID = A.PEGAWAI_ID
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
