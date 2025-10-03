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

class LokasiSimpan extends Entity
{

	var $query;
	/**
	 * Class constructor.
	 **/
	function LokasiSimpan()
	{
		$this->Entity();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("LOKASI_SIMPAN_ID", $this->getNextId("LOKASI_SIMPAN_ID", "LOKASI_SIMPAN"));
		$str = "INSERT INTO LOKASI_SIMPAN(
				LOKASI_SIMPAN_ID, 
				PERUSAHAAN_ID, 
				CABANG_ID, 
				KODE, 
				RUANG, 
				LEMARI, 
				NOMOR_BOKS, 
				NOMOR_FOLDER, 
				KETERANGAN, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES ('" . $this->getField("LOKASI_SIMPAN_ID") . "', 
				'" . $this->getField("PERUSAHAAN_ID") . "', 
				'" . $this->getField("CABANG_ID") . "', 
				'" . $this->getField("KODE") . "', 
				'" . $this->getField("RUANG") . "', 
				'" . $this->getField("LEMARI") . "', 
				'" . $this->getField("NOMOR_BOKS") . "', 
				'" . $this->getField("NOMOR_FOLDER") . "', 
				'" . $this->getField("KETERANGAN") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		$this->id = $this->getField("LOKASI_SIMPAN_ID");
		// echo $str;exit();
		$this->query = $str;
		return $this->execQuery($str);
	}

	function update()
	{
		$str = "UPDATE LOKASI_SIMPAN
				SET 
				PERUSAHAAN_ID		= '" . $this->getField("PERUSAHAAN_ID") . "', 
				CABANG_ID			= '" . $this->getField("CABANG_ID") . "', 
				KODE				= '" . $this->getField("KODE") . "', 
				RUANG				= '" . $this->getField("RUANG") . "', 
				LEMARI				= '" . $this->getField("LEMARI") . "', 
				NOMOR_BOKS			= '" . $this->getField("NOMOR_BOKS") . "', 
				NOMOR_FOLDER		= '" . $this->getField("NOMOR_FOLDER") . "', 
				KETERANGAN			= '" . $this->getField("KETERANGAN") . "', 
				UPDATED_BY			= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE		= CURRENT_TIMESTAMP
			WHERE LOKASI_SIMPAN_ID 	= '" . $this->getField("LOKASI_SIMPAN_ID") . "'
		";

		// echo "GAGAL|".$str;exit();
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE LOKASI_SIMPAN A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "'
			WHERE LOKASI_SIMPAN_ID = " . $this->getField("LOKASI_SIMPAN_ID");

		$this->query = $str;
		// echo $str;exit();
		return $this->execQuery($str);
	}

	function delete()
	{
		$str = "DELETE FROM LOKASI_SIMPAN
			WHERE LOKASI_SIMPAN_ID = '" . $this->getField("LOKASI_SIMPAN_ID") . "'";

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
	function selectByParams($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY LOKASI_SIMPAN_ID ASC")
	{
		$str = "SELECT A.LOKASI_SIMPAN_ID,A.PERUSAHAAN_ID,A.CABANG_ID,A.KODE,A.RUANG,A.LEMARI,A.NOMOR_BOKS,A.NOMOR_FOLDER,A.KETERANGAN, 
				A.STATUS,A.CREATED_BY,A.CREATED_DATE,A.UPDATED_BY, A.UPDATED_DATE
			FROM LOKASI_SIMPAN A
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

	function selectByParamsMonitoring($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY LOKASI_SIMPAN_ID ASC")
	{
		$str = "SELECT A.LOKASI_SIMPAN_ID,A.PERUSAHAAN_ID,B.NAMA NAMA_PERUSAHAAN,A.CABANG_ID,C.NAMA NAMA_CABANG,A.KODE,A.RUANG,A.LEMARI,A.NOMOR_BOKS,A.NOMOR_FOLDER,A.KETERANGAN, 
				A.STATUS,A.CREATED_BY,A.CREATED_DATE,A.UPDATED_BY, A.UPDATED_DATE
			FROM LOKASI_SIMPAN A
			LEFT JOIN PERUSAHAAN B 	ON B.PERUSAHAAN_ID 	= A.PERUSAHAAN_ID
			LEFT JOIN CABANG C 		ON C.CABANG_ID 		= A.CABANG_ID
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
		$str = "SELECT A.LOKASI_SIMPAN_ID,,A.PERUSAHAAN_ID,B.NAMA NAMA_PERUSAHAAN,A.CABANG_ID,C.NAMA NAMA_CABANG,A.KODE,A.RUANG,A.LEMARI,A.NOMOR_BOKS,A.NOMOR_FOLDER,A.KETERANGAN, 
				A.STATUS,A.CREATED_BY,A.CREATED_DATE,A.UPDATED_BY, A.UPDATED_DATE
			FROM LOKASI_SIMPAN A
			LEFT JOIN PERUSAHAAN B 	ON B.PERUSAHAAN_ID 	= A.PERUSAHAAN_ID
			LEFT JOIN CABANG C 		ON C.CABANG_ID 		= A.CABANG_ID
			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement . " ORDER BY LOKASI_SIMPAN_ID DESC";
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
			FROM LOKASI_SIMPAN A 
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
			FROM LOKASI_SIMPAN A 
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
