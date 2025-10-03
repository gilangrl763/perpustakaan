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

class Peminjaman extends Entity
{

	var $query;
	/**
	 * Class constructor.
	 **/
	function Peminjaman()
	{
		$this->Entity();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PEMINJAMAN_ID", $this->getNextId("PEMINJAMAN_ID", "PEMINJAMAN"));
		$str = "INSERT INTO PEMINJAMAN(
				PEMINJAMAN_ID, 
				PERUSAHAAN_ID, 
				CABANG_ID, 
				SATUAN_KERJA_ID, 
				NOMOR, 
				TANGGAL, 
				PERIHAL, 
				ISI, 
				KETERANGAN, 
				KEPERLUAN, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES (
				'" . $this->getField("PEMINJAMAN_ID") . "', 
				'" . $this->getField("PERUSAHAAN_ID") . "', 
				'" . $this->getField("CABANG_ID") . "', 
				'" . $this->getField("SATUAN_KERJA_ID") . "', 
				'" . $this->getField("NOMOR") . "', 
				" . $this->getField("TANGGAL") . ", 
				'" . $this->getField("PERIHAL") . "', 
				'" . $this->getField("ISI") . "', 
				'" . $this->getField("KETERANGAN") . "', 
				'" . $this->getField("KEPERLUAN") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		// echo "GAGAL|".$str;exit;
		$this->id = $this->getField("PEMINJAMAN_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function insertDokumen()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PEMINJAMAN_DOKUMEN_ID", $this->getNextId("PEMINJAMAN_DOKUMEN_ID", "PEMINJAMAN_DOKUMEN"));
		$str = "INSERT INTO PEMINJAMAN_DOKUMEN(
				PEMINJAMAN_DOKUMEN_ID, 
				PEMINJAMAN_ID, 
				NAMA, 
				DOKUMEN, 
				UKURAN_DOKUMEN, 
				KETERANGAN, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES (
				'" . $this->getField("PEMINJAMAN_DOKUMEN_ID") . "', 
				'" . $this->getField("PEMINJAMAN_ID") . "', 
				'" . $this->getField("NAMA") . "', 
				'" . $this->getField("DOKUMEN") . "', 
				'" . $this->getField("UKURAN_DOKUMEN") . "', 
				'" . $this->getField("KETERANGAN") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		// echo "GAGAL|".$str;exit;
		$this->id = $this->getField("PEMINJAMAN_DOKUMEN_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function insertBerkas()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PEMINJAMAN_BERKAS_ID", $this->getNextId("PEMINJAMAN_BERKAS_ID", "PEMINJAMAN_BERKAS"));
		$str = "INSERT INTO PEMINJAMAN_BERKAS(
				PEMINJAMAN_BERKAS_ID, 
				PEMINJAMAN_ID, 
				PEMINDAHAN_BERKAS_ID, 
				PEMINDAHAN_ID, 
				PEGAWAI_ID, 
				ADA_HARDCOPY, 
				KEPERLUAN, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES (
				'" . $this->getField("PEMINJAMAN_BERKAS_ID") . "', 
				'" . $this->getField("PEMINJAMAN_ID") . "', 
				'" . $this->getField("PEMINDAHAN_BERKAS_ID") . "', 
				'" . $this->getField("PEMINDAHAN_ID") . "', 
				'" . $this->getField("PEGAWAI_ID") . "', 
				'" . $this->getField("ADA_HARDCOPY") . "', 
				'" . $this->getField("KEPERLUAN") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		// echo "GAGAL|".$str;exit;
		$this->id = $this->getField("PEMINJAMAN_BERKAS_ID");
		$this->PEMINJAMAN_BERKAS_ID = $this->getField("PEMINJAMAN_BERKAS_ID");
		$this->PEMINJAMAN_ID = $this->getField("PEMINJAMAN_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function insertBerkasVerifikasi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PEMINJAMAN_BERKAS_ID", $this->getNextId("PEMINJAMAN_BERKAS_ID", "PEMINJAMAN_BERKAS"));
		$str = "INSERT INTO PEMINJAMAN_BERKAS(
				PEMINJAMAN_BERKAS_ID, 
				PEMINJAMAN_ID, 
				PERUSAHAAN_ID, 
				CABANG_ID, 
				SATUAN_KERJA_ID, 
				KLASIFIKASI_ID, 
				KETERANGAN, 
				KURUN_WAKTU, 
				TINGKAT_PERKEMBANGAN_ID, 
				NOMOR_DOKUMEN,
				NOMOR_ALTERNATIF,
				KONDISI_FISIK_ID,
				JUMLAH_BERKAS,
				LOKASI_SIMPAN_RUANG,
				LOKASI_SIMPAN_LEMARI,
				LOKASI_SIMPAN_NOMOR_BOKS,
				LOKASI_SIMPAN_NOMOR_FOLDER,
				SUMBER,
				CREATED_BY, 
				CREATED_DATE)
			VALUES (
				'" . $this->getField("PEMINJAMAN_BERKAS_ID") . "', 
				'" . $this->getField("PEMINJAMAN_ID") . "', 
				'" . $this->getField("PERUSAHAAN_ID") . "', 
				'" . $this->getField("CABANG_ID") . "', 
				'" . $this->getField("SATUAN_KERJA_ID") . "', 
				'" . $this->getField("KLASIFIKASI_ID") . "', 
				'" . $this->getField("KETERANGAN") . "', 
				'" . $this->getField("KURUN_WAKTU") . "', 
				'" . $this->getField("TINGKAT_PERKEMBANGAN_ID") . "', 
				'" . $this->getField("NOMOR_DOKUMEN") . "', 
				'" . $this->getField("NOMOR_ALTERNATIF") . "', 
				'" . $this->getField("KONDISI_FISIK_ID") . "', 
				'" . $this->getField("JUMLAH_BERKAS") . "', 
				'" . $this->getField("LOKASI_SIMPAN_RUANG") . "', 
				'" . $this->getField("LOKASI_SIMPAN_LEMARI") . "', 
				'" . $this->getField("LOKASI_SIMPAN_NOMOR_BOKS") . "', 
				'" . $this->getField("LOKASI_SIMPAN_NOMOR_FOLDER") . "', 
				'" . $this->getField("SUMBER") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		// echo "GAGAL|".$str;exit;
		$this->id = $this->getField("PEMINJAMAN_BERKAS_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}


	function insertTujuan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PEMINJAMAN_TUJUAN_ID", $this->getNextId("PEMINJAMAN_TUJUAN_ID", "PEMINJAMAN_TUJUAN"));
		$str = "INSERT INTO PEMINJAMAN_TUJUAN(
				PEMINJAMAN_TUJUAN_ID, 
				PEMINJAMAN_ID, 
				PERUSAHAAN_ID, 
				CABANG_ID, 
				SATUAN_KERJA_ID, 
				PEGAWAI_ID, 
				KODE, 
				NAMA, 
				JABATAN, 
				JENIS, 
				KETERANGAN, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES (
				'" . $this->getField("PEMINJAMAN_TUJUAN_ID") . "', 
				'" . $this->getField("PEMINJAMAN_ID") . "', 
				'" . $this->getField("PERUSAHAAN_ID") . "', 
				'" . $this->getField("CABANG_ID") . "', 
				'" . $this->getField("SATUAN_KERJA_ID") . "', 
				'" . $this->getField("PEGAWAI_ID") . "', 
				'" . $this->getField("KODE") . "', 
				'" . $this->getField("NAMA") . "', 
				'" . $this->getField("JABATAN") . "', 
				'" . $this->getField("JENIS") . "', 
				'" . $this->getField("KETERANGAN") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		// echo "GAGAL|".$str;exit;
		$this->id = $this->getField("PEMINJAMAN_TUJUAN_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function insertDisposisi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PEMINJAMAN_TUJUAN_ID", $this->getNextId("PEMINJAMAN_TUJUAN_ID", "PEMINJAMAN_TUJUAN"));
		$str = "INSERT INTO PEMINJAMAN_TUJUAN(
				PEMINJAMAN_TUJUAN_ID, 
				PEMINJAMAN_TUJUAN_ID_PARENT, 
				PEMINJAMAN_ID, 
				PERUSAHAAN_ID, 
				CABANG_ID, 
				SATUAN_KERJA_ID, 
				PEGAWAI_ID, 
				KODE, 
				NAMA, 
				JABATAN, 
				JENIS, 
				TERDISPOSISI, 
				TERBACA, 
				PESAN_DISPOSISI, 
				PEGAWAI_ID_DISPOSISI, 
				KODE_DISPOSISI, 
				NAMA_DISPOSISI, 
				JABATAN_DISPOSISI, 
				PERUSAHAAN_ID_DISPOSISI, 
				CABANG_ID_DISPOSISI, 
				SATUAN_KERJA_ID_DISPOSISI, 
				CREATED_BY, 
				CREATED_DATE,
				TANGGAL_KIRIM
				)
			VALUES (
				'" . $this->getField("PEMINJAMAN_TUJUAN_ID") . "', 
				'" . $this->getField("PEMINJAMAN_TUJUAN_ID_PARENT") . "', 
				'" . $this->getField("PEMINJAMAN_ID") . "', 
				'" . $this->getField("PERUSAHAAN_ID") . "', 
				'" . $this->getField("CABANG_ID") . "', 
				'" . $this->getField("SATUAN_KERJA_ID") . "', 
				'" . $this->getField("PEGAWAI_ID") . "', 
				'" . $this->getField("KODE") . "', 
				'" . $this->getField("NAMA") . "', 
				'" . $this->getField("JABATAN") . "', 
				'" . $this->getField("JENIS") . "', 
				'" . $this->getField("TERDISPOSISI") . "', 
				'" . $this->getField("TERBACA") . "', 
				'" . $this->getField("PESAN_DISPOSISI") . "', 
				'" . $this->getField("PEGAWAI_ID_DISPOSISI") . "', 
				'" . $this->getField("KODE_DISPOSISI") . "', 
				'" . $this->getField("NAMA_DISPOSISI") . "', 
				'" . $this->getField("JABATAN_DISPOSISI") . "', 
				'" . $this->getField("PERUSAHAAN_ID_DISPOSISI") . "', 
				'" . $this->getField("CABANG_ID_DISPOSISI") . "', 
				'" . $this->getField("SATUAN_KERJA_ID_DISPOSISI") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP,
				CURRENT_TIMESTAMP
			)
		";

		// echo "GAGAL|".$str;exit;
		$this->id = $this->getField("PEMINJAMAN_TUJUAN_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}
	

	function insertApproval()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PEMINJAMAN_APPROVAL_ID", $this->getNextId("PEMINJAMAN_APPROVAL_ID", "PEMINJAMAN_APPROVAL"));
		$str = "INSERT INTO PEMINJAMAN_APPROVAL(
				PEMINJAMAN_APPROVAL_ID, 
				PEMINJAMAN_ID, 
				PERUSAHAAN_ID, 
				CABANG_ID, 
				SATUAN_KERJA_ID, 
				PEGAWAI_ID, 
				KODE, 
				NAMA, 
				JABATAN, 
				SEBAGAI, 
				URUT, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES (
				'" . $this->getField("PEMINJAMAN_APPROVAL_ID") . "', 
				'" . $this->getField("PEMINJAMAN_ID") . "', 
				'" . $this->getField("PERUSAHAAN_ID") . "', 
				'" . $this->getField("CABANG_ID") . "', 
				'" . $this->getField("SATUAN_KERJA_ID") . "', 
				'" . $this->getField("PEGAWAI_ID") . "', 
				'" . $this->getField("KODE") . "', 
				'" . $this->getField("NAMA") . "', 
				'" . $this->getField("JABATAN") . "', 
				'" . $this->getField("SEBAGAI") . "', 
				'" . $this->getField("URUT") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		// echo "GAGAL|".$str;exit;
		$this->id = $this->getField("PEMINJAMAN_APPROVAL_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}

	function insertLog()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PEMINJAMAN_LOG_ID", $this->getNextId("PEMINJAMAN_LOG_ID", "PEMINJAMAN_LOG"));
		$str = "INSERT INTO PEMINJAMAN_LOG(
				PEMINJAMAN_LOG_ID, 
				PEMINJAMAN_ID, 
				KODE, 
				KETERANGAN, 
				PEGAWAI_ID, 
				PEGAWAI_KODE, 
				PEGAWAI_NAMA, 
				PEGAWAI_JABATAN, 
				CREATED_BY, 
				CREATED_DATE)
			VALUES (
				'" . $this->getField("PEMINJAMAN_LOG_ID") . "', 
				'" . $this->getField("PEMINJAMAN_ID") . "', 
				'" . $this->getField("KODE") . "', 
				'" . $this->getField("KETERANGAN") . "', 
				'" . $this->getField("PEGAWAI_ID") . "', 
				'" . $this->getField("PEGAWAI_KODE") . "', 
				'" . $this->getField("PEGAWAI_NAMA") . "', 
				'" . $this->getField("PEGAWAI_JABATAN") . "', 
				'" . $this->getField("CREATED_BY") . "', 
				CURRENT_TIMESTAMP
			)
		";

		// echo "GAGAL|".$str;exit;
		$this->id = $this->getField("PEMINJAMAN_LOG_ID");
		$this->query = $str;
		return $this->execQuery($str);
	}
	

	function update()
	{
		$str = "UPDATE PEMINJAMAN
				SET 
				PERUSAHAAN_ID		= '" . $this->getField("PERUSAHAAN_ID") . "', 
				CABANG_ID			= '" . $this->getField("CABANG_ID") . "', 
				SATUAN_KERJA_ID		= '" . $this->getField("SATUAN_KERJA_ID") . "', 
				NOMOR				= '" . $this->getField("NOMOR") . "', 
				TANGGAL				= " . $this->getField("TANGGAL") . ", 
				PERIHAL				= '" . $this->getField("PERIHAL") . "', 
				KETERANGAN			= '" . $this->getField("KETERANGAN") . "', 
				ISI					= '" . $this->getField("ISI") . "', 
				DOKUMEN				= '" . $this->getField("DOKUMEN") . "', 
				UPDATED_BY			= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE		= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_ID 	= '" . $this->getField("PEMINJAMAN_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateDokumen()
	{
		$str = "UPDATE PEMINJAMAN_DOKUMEN
				SET 
				PEMINJAMAN_ID		= '" . $this->getField("PEMINJAMAN_ID") . "', 
				NAMA				= '" . $this->getField("NAMA") . "', 
				DOKUMEN				= '" . $this->getField("DOKUMEN") . "', 
				UKURAN_DOKUMEN		= '" . $this->getField("UKURAN_DOKUMEN") . "', 
				KETERANGAN			= '" . $this->getField("KETERANGAN") . "', 
				UPDATED_BY			= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE		= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_DOKUMEN_ID 	= '" . $this->getField("PEMINJAMAN_DOKUMEN_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateBerkas()
	{
		$str = "UPDATE PEMINJAMAN_BERKAS
				SET 
				PEMINJAMAN_ID			= '" . $this->getField("PEMINJAMAN_ID") . "', 
				PEMINJAMAN_BERKAS_ID	= '" . $this->getField("PEMINJAMAN_BERKAS_ID") . "', 
				PEMINDAHAN_ID			= '" . $this->getField("PEMINDAHAN_ID") . "', 
				PEGAWAI_ID				= '" . $this->getField("PEGAWAI_ID") . "', 
				ADA_HARDCOPY			= '" . $this->getField("ADA_HARDCOPY") . "', 
				KEPERLUAN				= '" . $this->getField("KEPERLUAN") . "', 
				UPDATED_BY				= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE			= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_BERKAS_ID 	= '" . $this->getField("PEMINJAMAN_BERKAS_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateBerkasVerifikasi()
	{
		$str = "UPDATE PEMINJAMAN_BERKAS
				SET 
				KLASIFIKASI_ID				= '" . $this->getField("KLASIFIKASI_ID") . "', 
				KETERANGAN					= '" . $this->getField("KETERANGAN") . "', 
				KURUN_WAKTU					= '" . $this->getField("KURUN_WAKTU") . "', 
				TINGKAT_PERKEMBANGAN_ID		= '" . $this->getField("TINGKAT_PERKEMBANGAN_ID") . "', 
				NOMOR_DOKUMEN				= '" . $this->getField("NOMOR_DOKUMEN") . "', 
				NOMOR_ALTERNATIF			= '" . $this->getField("NOMOR_ALTERNATIF") . "', 
				KONDISI_FISIK_ID			= '" . $this->getField("KONDISI_FISIK_ID") . "', 
				JUMLAH_BERKAS				= '" . $this->getField("JUMLAH_BERKAS") . "', 
				LOKASI_SIMPAN_RUANG			= '" . $this->getField("LOKASI_SIMPAN_RUANG") . "', 
				LOKASI_SIMPAN_LEMARI		= '" . $this->getField("LOKASI_SIMPAN_LEMARI") . "', 
				LOKASI_SIMPAN_NOMOR_BOKS	= '" . $this->getField("LOKASI_SIMPAN_NOMOR_BOKS") . "', 
				LOKASI_SIMPAN_NOMOR_FOLDER	= '" . $this->getField("LOKASI_SIMPAN_NOMOR_FOLDER") . "', 
				STATUS						= '" . $this->getField("STATUS") . "', 
				REVISI						= '" . $this->getField("REVISI") . "', 
				UPDATED_BY					= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE				= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_BERKAS_ID 		= '" . $this->getField("PEMINJAMAN_BERKAS_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateBerkasSimpan()
	{
		$str = "UPDATE PEMINJAMAN_BERKAS
				SET 
				KURUN_WAKTU					= '" . $this->getField("KURUN_WAKTU") . "', 
				RETENSI_AKTIF				= '" . $this->getField("RETENSI_AKTIF") . "', 
				RETENSI_INAKTIF				= '" . $this->getField("RETENSI_INAKTIF") . "', 
				TAHUN_PINDAH				= '" . $this->getField("TAHUN_PINDAH") . "', 
				TAHUN_MUSNAH				= '" . $this->getField("TAHUN_MUSNAH") . "', 
				KLASIFIKASI_ID				= '" . $this->getField("KLASIFIKASI_ID") . "', 
				TINGKAT_PERKEMBANGAN_ID		= '" . $this->getField("TINGKAT_PERKEMBANGAN_ID") . "', 
				KONDISI_FISIK_ID			= '" . $this->getField("KONDISI_FISIK_ID") . "',  
				MEDIA_SIMPAN_ID				= '" . $this->getField("MEDIA_SIMPAN_ID") . "', 
				MEDIA_SIMPAN_KODE			= '" . $this->getField("MEDIA_SIMPAN_KODE") . "', 
				MEDIA_SIMPAN_NAMA			= '" . $this->getField("MEDIA_SIMPAN_NAMA") . "', 
				NOMOR_DOKUMEN				= '" . $this->getField("NOMOR_DOKUMEN") . "', 
				NOMOR_ALTERNATIF			= '" . $this->getField("NOMOR_ALTERNATIF") . "', 
				JUMLAH_BERKAS				= '" . $this->getField("JUMLAH_BERKAS") . "', 
				LOKASI_SIMPAN_RUANG			= '" . $this->getField("LOKASI_SIMPAN_RUANG") . "', 
				LOKASI_SIMPAN_LEMARI		= '" . $this->getField("LOKASI_SIMPAN_LEMARI") . "', 
				LOKASI_SIMPAN_NOMOR_BOKS	= '" . $this->getField("LOKASI_SIMPAN_NOMOR_BOKS") . "', 
				LOKASI_SIMPAN_NOMOR_FOLDER	= '" . $this->getField("LOKASI_SIMPAN_NOMOR_FOLDER") . "', 
				KETERANGAN					= '" . $this->getField("KETERANGAN") . "', 
				DOKUMEN						= '" . $this->getField("DOKUMEN") . "', 
				STATUS						= '" . $this->getField("STATUS") . "', 
				UPDATED_BY					= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE				= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_BERKAS_ID 		= '" . $this->getField("PEMINJAMAN_BERKAS_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateTujuan()
	{
		$str = "UPDATE PEMINJAMAN_TUJUAN
				SET 
				PEMINJAMAN_ID			= '" . $this->getField("PEMINJAMAN_ID") . "', 
				PERUSAHAAN_ID			= '" . $this->getField("PERUSAHAAN_ID") . "', 
				CABANG_ID				= '" . $this->getField("CABANG_ID") . "', 
				SATUAN_KERJA_ID			= '" . $this->getField("SATUAN_KERJA_ID") . "', 
				PEGAWAI_ID				= '" . $this->getField("PEGAWAI_ID") . "', 
				KODE					= '" . $this->getField("KODE") . "', 
				NAMA					= '" . $this->getField("NAMA") . "', 
				JABATAN					= '" . $this->getField("JABATAN") . "', 
				JENIS					= '" . $this->getField("JENIS") . "', 
				KETERANGAN				= '" . $this->getField("KETERANGAN") . "', 
				UPDATED_BY				= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE			= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_TUJUAN_ID 	= '" . $this->getField("PEMINJAMAN_TUJUAN_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateDisposisi()
	{
		$str = "UPDATE PEMINJAMAN_TUJUAN
				SET 
				TERDISPOSISI			= '" . $this->getField("TERDISPOSISI") . "', 
				TANGGAL_DISPOSISI		= CURRENT_TIMESTAMP, 
				UPDATED_BY				= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE			= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_TUJUAN_ID 	= '" . $this->getField("PEMINJAMAN_TUJUAN_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateTujuanDisposisi()
	{
		$str = "UPDATE PEMINJAMAN_TUJUAN
				SET 
				PERUSAHAAN_ID_DISPOSISI			= '" . $this->getField("PERUSAHAAN_ID_DISPOSISI") . "', 
				CABANG_ID_DISPOSISI				= '" . $this->getField("CABANG_ID_DISPOSISI") . "', 
				SATUAN_KERJA_ID_DISPOSISI		= '" . $this->getField("SATUAN_KERJA_ID_DISPOSISI") . "', 
				PEGAWAI_ID_DISPOSISI			= '" . $this->getField("PEGAWAI_ID_DISPOSISI") . "', 
				KODE_DISPOSISI					= '" . $this->getField("KODE_DISPOSISI") . "', 
				NAMA_DISPOSISI					= '" . $this->getField("NAMA_DISPOSISI") . "', 
				JABATAN_DISPOSISI				= '" . $this->getField("JABATAN_DISPOSISI") . "', 
				TERDISPOSISI					= '" . $this->getField("TERDISPOSISI") . "', 
				TERBACA							= '" . $this->getField("TERBACA") . "', 
				PESAN_DISPOSISI					= '" . $this->getField("PESAN_DISPOSISI") . "', 
				PEMINJAMAN_TUJUAN_ID_PARENT		= '" . $this->getField("PEMINJAMAN_TUJUAN_ID_PARENT") . "', 
				UPDATED_BY						= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE					= CURRENT_TIMESTAMP,
				TANGGAL_KIRIM					= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_TUJUAN_ID 			= '" . $this->getField("PEMINJAMAN_TUJUAN_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateApproval()
	{
		$str = "UPDATE PEMINJAMAN_APPROVAL
				SET 
				PEMINJAMAN_ID			= '" . $this->getField("PEMINJAMAN_ID") . "', 
				PERUSAHAAN_ID			= '" . $this->getField("PERUSAHAAN_ID") . "', 
				CABANG_ID				= '" . $this->getField("CABANG_ID") . "', 
				SATUAN_KERJA_ID			= '" . $this->getField("SATUAN_KERJA_ID") . "', 
				PEGAWAI_ID				= '" . $this->getField("PEGAWAI_ID") . "', 
				KODE					= '" . $this->getField("KODE") . "', 
				NAMA					= '" . $this->getField("NAMA") . "', 
				JABATAN					= '" . $this->getField("JABATAN") . "', 
				SEBAGAI					= '" . $this->getField("SEBAGAI") . "', 
				STATUS					= '" . $this->getField("STATUS") . "', 
				URUT					= '" . $this->getField("URUT") . "', 
				UPDATED_BY				= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE			= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_APPROVAL_ID 	= '" . $this->getField("PEMINJAMAN_APPROVAL_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateSerahPinjam()
	{
		$str = "UPDATE PEMINJAMAN
				SET 
				TANGGAL_PINJAM			= CURRENT_DATE, 
				BATAS_TANGGAL_KEMBALI	= " . $this->getField("BATAS_TANGGAL_KEMBALI") . ", 
				PEGAWAI_ID_ARSIPARIS	= '" . $this->getField("PEGAWAI_ID_ARSIPARIS") . "', 
				NAMA_ARSIPARIS			= '" . $this->getField("NAMA_ARSIPARIS") . "', 
				JABATAN_ARSIPARIS		= '" . $this->getField("JABATAN_ARSIPARIS") . "', 
				STATUS					= '" . $this->getField("STATUS") . "', 
				UPDATED_BY				= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE			= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_ID 		= '" . $this->getField("PEMINJAMAN_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateVerifikasiPengembalian()
	{
		$str = "UPDATE PEMINJAMAN_BERKAS
				SET 
				KONDISI_FISIK_ID		= '" . $this->getField("KONDISI_FISIK_ID") . "', 
				KONDISI_FISIK_KODE		= '" . $this->getField("KONDISI_FISIK_KODE") . "', 
				KONDISI_FISIK_NAMA		= '" . $this->getField("KONDISI_FISIK_NAMA") . "', 
				STATUS					= '" . $this->getField("STATUS") . "', 
				TANGGAL_KEMBALI			= " . $this->getField("TANGGAL_KEMBALI") . ", 
				UPDATED_BY				= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE			= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_BERKAS_ID 	= '" . $this->getField("PEMINJAMAN_BERKAS_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateVerifikasiPeminjamanDipinjam()
	{
		$str = "UPDATE PEMINJAMAN
				SET 
				BATAS_TANGGAL_KEMBALI	= " . $this->getField("BATAS_TANGGAL_KEMBALI") . ", 
				TANGGAL_PINJAM			= " . $this->getField("TANGGAL_PINJAM") . ", 
				STATUS					= '" . $this->getField("STATUS") . "', 
				UPDATED_BY				= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE			= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_ID 	= '" . $this->getField("PEMINJAMAN_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateVerifikasiPengembalianDipinjam()
	{
		$str = "UPDATE PEMINJAMAN_BERKAS
				SET 
				KONDISI_FISIK_ID		= '" . $this->getField("KONDISI_FISIK_ID") . "', 
				KONDISI_FISIK_KODE		= '" . $this->getField("KONDISI_FISIK_KODE") . "', 
				KONDISI_FISIK_NAMA		= '" . $this->getField("KONDISI_FISIK_NAMA") . "', 
				BATAS_TANGGAL_KEMBALI	= " . $this->getField("BATAS_TANGGAL_KEMBALI") . ", 
				TANGGAL_PINJAM			= " . $this->getField("TANGGAL_PINJAM") . ", 
				STATUS					= '" . $this->getField("STATUS") . "', 
				KETERANGAN_PINJAM_ULANG	= '" . $this->getField("KETERANGAN_PINJAM_ULANG") . "', 
				UPDATED_BY				= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE			= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_BERKAS_ID 	= '" . $this->getField("PEMINJAMAN_BERKAS_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateSerahPinjamBerkasByPeminjamanId()
	{
		$str = "UPDATE PEMINJAMAN_BERKAS
				SET 
				TANGGAL_PINJAM			= CURRENT_DATE, 
				BATAS_TANGGAL_KEMBALI	= " . $this->getField("BATAS_TANGGAL_KEMBALI") . ", 
				PEGAWAI_ID_ARSIPARIS	= '" . $this->getField("PEGAWAI_ID_ARSIPARIS") . "', 
				NAMA_ARSIPARIS			= '" . $this->getField("NAMA_ARSIPARIS") . "', 
				JABATAN_ARSIPARIS		= '" . $this->getField("JABATAN_ARSIPARIS") . "', 
				STATUS					= '" . $this->getField("STATUS") . "', 
				UPDATED_BY				= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE			= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_ID 		= '" . $this->getField("PEMINJAMAN_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByField()
	{
		$str = "UPDATE PEMINJAMAN A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "',
			UPDATED_BY			= '" . $this->getField("UPDATED_BY") . "', 
			UPDATED_DATE		= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_ID = " . $this->getField("PEMINJAMAN_ID");

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByFieldBerkas()
	{
		$str = "UPDATE PEMINJAMAN_BERKAS A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "',
			UPDATED_BY			= '" . $this->getField("UPDATED_BY") . "', 
			UPDATED_DATE		= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_BERKAS_ID = " . $this->getField("PEMINJAMAN_BERKAS_ID");

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByFieldApproval()
	{
		$str = "UPDATE PEMINJAMAN_APPROVAL A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "',
			UPDATED_BY			= '" . $this->getField("UPDATED_BY") . "', 
			UPDATED_DATE		= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_APPROVAL_ID = " . $this->getField("PEMINJAMAN_APPROVAL_ID");

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updateByFieldApprovalByPermohonanId()
	{
		$str = "UPDATE PEMINJAMAN_APPROVAL A SET
			" . $this->getField("FIELD") . "= '" . $this->getField("FIELD_VALUE") . "',
			UPDATED_BY			= '" . $this->getField("UPDATED_BY") . "', 
			UPDATED_DATE		= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_ID = " . $this->getField("PEMINJAMAN_ID");

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function updatePermohonanBerkasDikembalikan()
	{
		$str = "UPDATE PEMINJAMAN_BERKAS
				SET 
				STATUS					= 'DIKEMBALIKAN', 
				UPDATED_BY				= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE			= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_ID 	= '" . $this->getField("PEMINJAMAN_ID") . "' 
				AND STATUS IN ('DIPINJAM','DIPINJAM_ULANG')
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function revisiPermohonan()
	{
		$str = "UPDATE PEMINJAMAN
				SET 
				STATUS					= '" . $this->getField("STATUS") . "', 
				REVISI					= '" . $this->getField("REVISI") . "', 
				UPDATED_BY				= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE			= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_ID 	= '" . $this->getField("PEMINJAMAN_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function revisiPermohonanApproval()
	{
		$str = "UPDATE PEMINJAMAN_APPROVAL
				SET 
				STATUS					= '" . $this->getField("STATUS") . "', 
				REVISI					= '" . $this->getField("REVISI") . "', 
				UPDATED_BY				= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE			= CURRENT_TIMESTAMP,
				TANGGAL_APPROVE			= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_APPROVAL_ID 	= '" . $this->getField("PEMINJAMAN_APPROVAL_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function setujuiPermohonanApproval()
	{
		$str = "UPDATE PEMINJAMAN_APPROVAL
				SET 
				STATUS					= '" . $this->getField("STATUS") . "', 
				UPDATED_BY				= '" . $this->getField("UPDATED_BY") . "', 
				UPDATED_DATE			= CURRENT_TIMESTAMP,
				TANGGAL_APPROVE			= CURRENT_TIMESTAMP
			WHERE PEMINJAMAN_APPROVAL_ID 	= '" . $this->getField("PEMINJAMAN_APPROVAL_ID") . "'
		";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function delete()
	{
		$str = "DELETE FROM PEMINJAMAN
			WHERE PEMINJAMAN_ID = '" . $this->getField("PEMINJAMAN_ID") . "'";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}


	function deleteBerkas()
	{
		$str = "DELETE FROM PEMINJAMAN_BERKAS
			WHERE PEMINJAMAN_BERKAS_ID = '" . $this->getField("PEMINJAMAN_BERKAS_ID") . "'";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function deleteTujuan()
	{
		$str = "DELETE FROM PEMINJAMAN_TUJUAN
			WHERE PEMINJAMAN_TUJUAN_ID = '" . $this->getField("PEMINJAMAN_TUJUAN_ID") . "'";

		// echo "GAGAL|".$str;exit;
		$this->query = $str;
		return $this->execQuery($str);
	}

	function deleteApproval()
	{
		$str = "DELETE FROM PEMINJAMAN_APPROVAL
			WHERE PEMINJAMAN_APPROVAL_ID = '" . $this->getField("PEMINJAMAN_APPROVAL_ID") . "'";

		// echo "GAGAL|".$str;exit;
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
	function selectByParams($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEMINJAMAN_ID ASC")
	{
		$str = "SELECT PEMINJAMAN_ID, PERUSAHAAN_ID, CABANG_ID, SATUAN_KERJA_ID, NOMOR, TANGGAL, PERIHAL, 
				ISI, DOKUMEN, KETERANGAN, STATUS, REVISI, KEPERLUAN, 
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM PEMINJAMAN A
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsMonitoring($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEMINJAMAN_ID ASC")
	{
		$str = "SELECT A.PEMINJAMAN_ID, A.PERUSAHAAN_ID, B.NAMA NAMA_PERUSAHAAN, A.CABANG_ID, C.NAMA NAMA_CABANG, C.KOTA, A.SATUAN_KERJA_ID, 
				D.NAMA NAMA_SATUAN_KERJA, A.NOMOR, A.TANGGAL, A.PERIHAL, A.ISI, A.DOKUMEN, A.KETERANGAN, A.STATUS, A.REVISI, A.KEPERLUAN, 
				F.NAMA NAMA_PEMINJAM, F.JABATAN JABATAN_PEMINJAM, A.TANGGAL_PINJAM, A.BATAS_TANGGAL_KEMBALI, A.TANGGAL_KEMBALI, 
				A.PEGAWAI_ID_ARSIPARIS, A.NAMA_ARSIPARIS, A.JABATAN_ARSIPARIS, F.EMAIL,
				A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
			FROM PEMINJAMAN A
			LEFT JOIN PERUSAHAAN B ON A.PERUSAHAAN_ID=B.PERUSAHAAN_ID
			LEFT JOIN CABANG C ON A.CABANG_ID=C.CABANG_ID
			LEFT JOIN SATUAN_KERJA D ON A.SATUAN_KERJA_ID=D.SATUAN_KERJA_ID
			LEFT JOIN USER_LOGIN E ON A.CREATED_BY::VARCHAR=E.USER_LOGIN_ID::VARCHAR
			LEFT JOIN PEGAWAI F ON F.PEGAWAI_ID=E.PEGAWAI_ID
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsMonitoringApproval($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEMINJAMAN_ID ASC")
	{
		$str = "SELECT A.PEMINJAMAN_APPROVAL_ID, A.PEMINJAMAN_ID, A.PEGAWAI_ID, A.KODE, A.NAMA, A.JABATAN, A.STATUS, A.URUT,
				B.NOMOR, B.TANGGAL, B.PERIHAL, B.ISI, B.DOKUMEN, B.KETERANGAN, B.STATUS STATUS_PEMINJAMAN, 
				B.PERUSAHAAN_ID, C.NAMA NAMA_PERUSAHAAN, B.CABANG_ID, D.NAMA NAMA_CABANG, D.KOTA, 
				B.SATUAN_KERJA_ID, E.NAMA NAMA_SATUAN_KERJA,A.TANGGAL_APPROVE, A.REVISI, B.KEPERLUAN,
				B.CREATED_BY, B.CREATED_DATE, B.UPDATED_BY, B.UPDATED_DATE
			FROM PEMINJAMAN_APPROVAL A
			LEFT JOIN PEMINJAMAN B ON A.PEMINJAMAN_ID=B.PEMINJAMAN_ID
			LEFT JOIN PERUSAHAAN C ON B.PERUSAHAAN_ID=C.PERUSAHAAN_ID
			LEFT JOIN CABANG D ON B.CABANG_ID=D.CABANG_ID
			LEFT JOIN SATUAN_KERJA E ON B.SATUAN_KERJA_ID=E.SATUAN_KERJA_ID
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsMonitoringTujuan($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEMINJAMAN_ID ASC")
	{
		$str = "SELECT A.PEMINJAMAN_TUJUAN_ID, A.PEMINJAMAN_TUJUAN_ID_PARENT, A.PEMINJAMAN_ID, A.JENIS, 
				B.NOMOR, B.PERIHAL, B.ISI, B.KETERANGAN, B.TANGGAL, A.PEGAWAI_ID, A.KODE, A.NAMA, A.JABATAN, A.PERUSAHAAN_ID, 
				A.PEGAWAI_ID_DISPOSISI, A.KODE_DISPOSISI, A.NAMA_DISPOSISI, A.JABATAN_DISPOSISI, A.TANGGAL_KIRIM, 
				A.TERDISPOSISI, A.TANGGAL_DISPOSISI, A.TERBACA, A.TERBACA_TANGGAL, A.PESAN_DISPOSISI, B.STATUS, B.KEPERLUAN,
				A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
			FROM PEMINJAMAN_TUJUAN A
			LEFT JOIN PEMINJAMAN B ON A.PEMINJAMAN_ID=B.PEMINJAMAN_ID
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsDokumen($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEMINJAMAN_DOKUMEN_ID ASC")
	{
		$str = "SELECT PEMINJAMAN_DOKUMEN_ID, PEMINJAMAN_ID, NAMA, DOKUMEN, UKURAN_DOKUMEN, KETERANGAN,
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE 
			FROM PEMINJAMAN_DOKUMEN A
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsBerkas($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEMINJAMAN_BERKAS_ID ASC")
	{
		$str = "SELECT PEMINJAMAN_BERKAS_ID, PEMINJAMAN_ID, PERUSAHAAN_ID, CABANG_ID, SATUAN_KERJA_ID, KLASIFIKASI_ID, 
				KLASIFIKASI_KODE, KLASIFIKASI_NAMA, KETERANGAN, KURUN_WAKTU, TINGKAT_PERKEMBANGAN_ID, TINGKAT_PERKEMBANGAN_KODE, 
				TINGKAT_PERKEMBANGAN_NAMA, RETENSI_AKTIF, RETENSI_INAKTIF, TAHUN_PINDAH, TAHUN_MUSNAH, MEDIA_SIMPAN_ID, 
				MEDIA_SIMPAN_KODE, MEDIA_SIMPAN_NAMA, LOKASI_SIMPAN_ID, LOKASI_SIMPAN_KODE, LOKASI_SIMPAN_RUANG, 
				LOKASI_SIMPAN_LEMARI, LOKASI_SIMPAN_NOMOR_BOKS, LOKASI_SIMPAN_NOMOR_FOLDER, KONDISI_FISIK_ID, KONDISI_FISIK_KODE, 
				KONDISI_FISIK_NAMA, JUMLAH_BERKAS, DOKUMEN, STATUS, REVISI, SUMBER, NOMOR_DOKUMEN, NOMOR_ALTERNATIF,
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE 
			FROM PEMINJAMAN_BERKAS A
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsBerkasCetak($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEMINJAMAN_BERKAS_ID ASC")
	{
		$str = "SELECT A.PEMINJAMAN_BERKAS_ID,A.PEMINJAMAN_ID,E.NAMA SATUAN_KERJA_NAMA,A.KLASIFIKASI_KODE,
				A.LOKASI_SIMPAN_RUANG,A.LOKASI_SIMPAN_LEMARI,A.LOKASI_SIMPAN_NOMOR_BOKS,A.LOKASI_SIMPAN_NOMOR_FOLDER
			FROM PEMINJAMAN_BERKAS A
			LEFT JOIN PEMINJAMAN B ON A.PEMINJAMAN_ID=B.PEMINJAMAN_ID
			LEFT JOIN PERUSAHAAN C ON B.PERUSAHAAN_ID=C.PERUSAHAAN_ID
			LEFT JOIN CABANG D ON B.CABANG_ID=D.CABANG_ID
			LEFT JOIN SATUAN_KERJA E ON B.SATUAN_KERJA_ID=E.SATUAN_KERJA_ID
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsMonitoringBerkas($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEMINJAMAN_BERKAS_ID ASC")
	{
		$str = "SELECT A.PEMINJAMAN_BERKAS_ID, A.PEMINDAHAN_BERKAS_ID, A.PEMINDAHAN_ID, A.PEMINJAMAN_ID, B.PERUSAHAAN_ID, 
				C.NAMA NAMA_PERUSAHAAN, B.CABANG_ID, D.NAMA NAMA_CABANG, B.SATUAN_KERJA_ID, E.NAMA NAMA_SATUAN_KERJA, 
				F.KLASIFIKASI_ID, F.KLASIFIKASI_KODE, F.KLASIFIKASI_NAMA, F.KETERANGAN, A.KEPERLUAN, A.ADA_HARDCOPY, 
				F.ADA_HARDCOPY ADA_HARDCOPY_PEMINDAHAN, A.PEGAWAI_ID, A.PEGAWAI_KODE, A.PEGAWAI_NAMA, A.PEGAWAI_JABATAN, 
				A.TANGGAL_PINJAM, A.BATAS_TANGGAL_KEMBALI, A.TANGGAL_KEMBALI, F.KURUN_WAKTU, A.TINGKAT_PERKEMBANGAN_ID, 
				A.TINGKAT_PERKEMBANGAN_KODE, A.TINGKAT_PERKEMBANGAN_NAMA, A.KONDISI_FISIK_ID, A.KONDISI_FISIK_KODE, 
				A.KONDISI_FISIK_NAMA, F.RETENSI_AKTIF, F.RETENSI_INAKTIF, F.TAHUN_PINDAH, F.TAHUN_MUSNAH, F.MEDIA_SIMPAN_ID, 
				F.MEDIA_SIMPAN_KODE, F.MEDIA_SIMPAN_NAMA, F.LOKASI_SIMPAN_ID, F.LOKASI_SIMPAN_KODE, F.LOKASI_SIMPAN_RUANG, 
				F.LOKASI_SIMPAN_LEMARI, F.LOKASI_SIMPAN_NOMOR_BOKS, F.LOKASI_SIMPAN_NOMOR_FOLDER, F.KONDISI_FISIK_ID, 
				F.KONDISI_FISIK_KODE, F.KONDISI_FISIK_NAMA, A.JUMLAH_BERKAS, F.DOKUMEN, A.STATUS, F.REVISI, A.SUMBER, 
				F.NOMOR_DOKUMEN, F.NOMOR_ALTERNATIF, A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
			FROM PEMINJAMAN_BERKAS A
			LEFT JOIN PEMINJAMAN B ON A.PEMINJAMAN_ID=B.PEMINJAMAN_ID
			LEFT JOIN PERUSAHAAN C ON B.PERUSAHAAN_ID=C.PERUSAHAAN_ID
			LEFT JOIN CABANG D ON B.CABANG_ID=D.CABANG_ID
			LEFT JOIN SATUAN_KERJA E ON B.SATUAN_KERJA_ID=E.SATUAN_KERJA_ID
			LEFT JOIN PEMINDAHAN_BERKAS F ON A.PEMINDAHAN_BERKAS_ID=F.PEMINDAHAN_BERKAS_ID
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsTujuan($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEMINJAMAN_TUJUAN_ID ASC")
	{
		$str = "SELECT PEMINJAMAN_TUJUAN_ID,PEMINJAMAN_ID,PEGAWAI_ID,KODE,NAMA,JABATAN,PERUSAHAAN_ID,CABANG_ID,SATUAN_KERJA_ID,
				KODE_DISPOSISI,NAMA_DISPOSISI,JABATAN_DISPOSISI,PERUSAHAAN_ID_DISPOSISI,CABANG_ID_DISPOSISI,SATUAN_KERJA_ID_DISPOSISI,
				JENIS,KETERANGAN,PESAN_DISPOSISI,TANGGAL_KIRIM,TANGGAL_DISPOSISI,TERDISPOSISI,TERBACA,TANGGAL_DISPOSISI,TERBACA_TANGGAL,
				CREATED_BY,CREATED_DATE,UPDATED_BY,UPDATED_DATE 
			FROM PEMINJAMAN_TUJUAN A
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsApproval($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY URUT ASC")
	{
		$str = "SELECT PEMINJAMAN_APPROVAL_ID, PEMINJAMAN_ID, PEGAWAI_ID, KODE, NAMA, JABATAN, PERUSAHAAN_ID, CABANG_ID, SATUAN_KERJA_ID, 
				STATUS, KETERANGAN, SEBAGAI, URUT, TANGGAL_APPROVE, REVISI, 
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE 
			FROM PEMINJAMAN_APPROVAL A
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsApprovalEmail($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY URUT ASC")
	{
		$str = "SELECT A.PEMINJAMAN_APPROVAL_ID, A.PEMINJAMAN_ID, A.PEGAWAI_ID, A.KODE, A.NAMA, A.JABATAN, B.EMAIL, A.STATUS,
				A.PERUSAHAAN_ID, A.CABANG_ID, A.SATUAN_KERJA_ID
			FROM PEMINJAMAN_APPROVAL A
			LEFT JOIN PEGAWAI B ON B.PEGAWAI_ID=A.PEGAWAI_ID
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsTujuanEmail($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEMINJAMAN_TUJUAN_ID ASC")
	{
		$str = "SELECT A.PEMINJAMAN_TUJUAN_ID, A.PEMINJAMAN_ID, A.PEGAWAI_ID, A.KODE, A.NAMA, A.JABATAN, B.EMAIL, 
				A.PERUSAHAAN_ID, A.CABANG_ID, A.SATUAN_KERJA_ID
			FROM PEMINJAMAN_TUJUAN A
			LEFT JOIN PEGAWAI B ON B.PEGAWAI_ID=A.PEGAWAI_ID
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsLog($paramsArray = array(), $limit = -1, $from = -1, $statement = "", $order = " ORDER BY PEMINJAMAN_LOG_ID DESC")
	{
		$str = "SELECT PEMINJAMAN_LOG_ID, PEMINJAMAN_ID, KODE, PEGAWAI_ID, PEGAWAI_KODE, PEGAWAI_NAMA, PEGAWAI_JABATAN, 
				KETERANGAN, CREATED_BY, CREATED_DATE 
			FROM PEMINJAMAN_LOG A
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement . " " . $order;
		// echo $str;exit;
		$this->query = $str;
		return $this->selectLimit($str, $limit, $from);
	}

	function selectByParamsLike($paramsArray = array(), $limit = -1, $from = -1, $statement = "")
	{
		$str = "SELECT PEMINJAMAN_ID, PERUSAHAAN_ID, CABANG_ID, SATUAN_KERJA_ID, NOMOR, TANGGAL, PERIHAL, ISI, DOKUMEN, KETERANGAN, STATUS,
				CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
			FROM PEMINJAMAN A
 			WHERE 1 = 1
		";

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement . " ORDER BY PEMINJAMAN_ID DESC";
		$this->query = $str;
		// echo $str;exit;		
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
			FROM PEMINJAMAN A 
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
			FROM PEMINJAMAN A
			LEFT JOIN PERUSAHAAN B ON A.PERUSAHAAN_ID=B.PERUSAHAAN_ID
			LEFT JOIN CABANG C ON A.CABANG_ID=C.CABANG_ID
			LEFT JOIN SATUAN_KERJA D ON A.SATUAN_KERJA_ID=D.SATUAN_KERJA_ID
			WHERE 1 = 1 " . $statement;

		while (list($key, $val) = each($paramsArray)) {
			$str .= " AND $key = '$val' ";
		}
		// echo $str;exit;
		$this->select($str);
		if ($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
	}

	function getCountByParamsMonitoringBerkas($paramsArray = array(), $statement = "")
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT 
			FROM PEMINJAMAN_BERKAS A
			LEFT JOIN PEMINJAMAN B ON A.PEMINJAMAN_ID=B.PEMINJAMAN_ID
			LEFT JOIN PERUSAHAAN C ON B.PERUSAHAAN_ID=C.PERUSAHAAN_ID
			LEFT JOIN CABANG D ON B.CABANG_ID=D.CABANG_ID
			LEFT JOIN SATUAN_KERJA E ON B.SATUAN_KERJA_ID=E.SATUAN_KERJA_ID
			LEFT JOIN PEMINDAHAN_BERKAS F ON A.PEMINDAHAN_BERKAS_ID=F.PEMINDAHAN_BERKAS_ID
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

	function getCountByParamsApproval($paramsArray = array(), $statement = "")
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT 
			FROM PEMINJAMAN_APPROVAL A 
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

	function getCountByParamsMonitoringApproval($paramsArray = array(), $statement = "")
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT 
			FROM PEMINJAMAN_APPROVAL A
			LEFT JOIN PEMINJAMAN B ON A.PEMINJAMAN_ID=B.PEMINJAMAN_ID
			LEFT JOIN PERUSAHAAN C ON B.PERUSAHAAN_ID=C.PERUSAHAAN_ID
			LEFT JOIN CABANG D ON B.CABANG_ID=D.CABANG_ID
			LEFT JOIN SATUAN_KERJA E ON B.SATUAN_KERJA_ID=E.SATUAN_KERJA_ID
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

	function getCountByParamsMonitoringTujuan($paramsArray = array(), $statement = "")
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT 
			FROM PEMINJAMAN_TUJUAN A
			LEFT JOIN PEMINJAMAN B ON A.PEMINJAMAN_ID=B.PEMINJAMAN_ID
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

	function getCountByParamsTujuan($paramsArray = array(), $statement = "")
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT 
			FROM PEMINJAMAN_TUJUAN A 
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

	function getCountByParamsBerkas($paramsArray = array(), $statement = "")
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT 
			FROM PEMINJAMAN_BERKAS A 
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
