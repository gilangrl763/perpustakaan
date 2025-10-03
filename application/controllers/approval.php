<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");

class Approval extends CI_Controller {

	function __construct() {
		parent::__construct();

		$this->db->query("SET DATESTYLE TO PostgreSQL,European;");  
				
		$this->load->library('kauth');     
	}
	
	public function index()
	{
		$this->load->view('approval/index');
	}
	
	public function loadUrl()
	{
		
		$reqFolder = $this->uri->segment(3, "");
		$reqFilename = $this->uri->segment(4, "");
		$reqParse1 = $this->uri->segment(5, "");
		$reqParse2 = $this->uri->segment(6, "");
		$reqParse3 = $this->uri->segment(7, "");
		$reqParse4 = $this->uri->segment(8, "");
		$reqParse5 = $this->uri->segment(9, "");
		$data = array(
			'reqParse1' => $reqParse1,
			'reqParse2' => $reqParse2,
			'reqParse3' => $reqParse3,
			'reqParse4' => $reqParse4,
			'reqParse5' => urldecode($reqParse5)
		);
		$this->load->view($reqFolder.'/'.$reqFilename, $data);
	}	


	function sesi_pengguna($PEGAWAI_ID)
	{	
		$result = array();
		$this->load->model("UserLogin");
		$user_login = new UserLogin();
		$user_login->selectByParamsMonitoring(array("A.PEGAWAI_ID"=>$PEGAWAI_ID));
		$user_login->firstRow();
		$result["USER_LOGIN_ID"] = $user_login->getField("USER_LOGIN_ID");
		$result["USER_GROUP"] = $user_login->getField("USER_GROUP");
		$result["PERUSAHAAN_ID"] = $user_login->getField("PERUSAHAAN_ID");
		$result["NAMA_PERUSAHAAN"] = $user_login->getField("NAMA_PERUSAHAAN");
		$result["CABANG_ID"] = $user_login->getField("CABANG_ID");
		$result["NAMA_CABANG"] = $user_login->getField("NAMA_CABANG");
		$result["SATUAN_KERJA_ID"] = $user_login->getField("SATUAN_KERJA_ID");
		$result["NAMA_SATUAN_KERJA"] = $user_login->getField("NAMA_SATUAN_KERJA");
		$result["PEGAWAI_ID"] = $user_login->getField("PEGAWAI_ID");
		$result["KODE_PEGAWAI"] = $user_login->getField("KODE_PEGAWAI");
		$result["NAMA_PEGAWAI"] = $user_login->getField("NAMA_PEGAWAI");
		$result["JABATAN"] = $user_login->getField("JABATAN");
		$result["USER_LOGIN"] = $user_login->getField("USER_LOGIN");
		$result["EMAIL"] = $user_login->getField("EMAIL");

		return $result;
	}


	function revisi_enkripsi()
	{
		$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->post('_crfs_7mj4R5iP')) && $this->IS_MOBILE == false){
			exit();
		}
		
		$this->load->model("Pemindahan");

		$id = $this->input->post("id");
		$PEMINDAHAN_ID = $this->input->post("PEMINDAHAN_ID");
		$REVISI = setQuote($_POST['REVISI']);
		$PEGAWAI_ID = $this->input->post("PEGAWAI_ID");

		$sesi_pengguna = $this->sesi_pengguna($PEGAWAI_ID);

		if(trim($REVISI) == ""){
			echo "GAGAL|Ketikkan Catatan Revisi terlebih dahulu!";
		}
		else{
			//UPDATE PERMOHONAN_UPROVAL
			$pemindahan_approval = new Pemindahan();
			$pemindahan_approval->setField("PEMINDAHAN_APPROVAL_ID", $id);
			$pemindahan_approval->setField("STATUS", "REVISI");
			$pemindahan_approval->setField("REVISI", $REVISI); 
			$pemindahan_approval->setField("UPDATED_BY", $sesi_pengguna["USER_LOGIN_ID"]);
			if($pemindahan_approval->revisiPermohonanApproval()){

				//UPDATE PERMOHONAN
				$pemindahan = new Pemindahan();
				$pemindahan->setField("PEMINDAHAN_ID", $PEMINDAHAN_ID);
				$pemindahan->setField("STATUS", "REVISI");
				$pemindahan->setField("REVISI", $REVISI); 
				$pemindahan->setField("UPDATED_BY", $sesi_pengguna["USER_LOGIN_ID"]);
				if($pemindahan->revisiPermohonan()){

					/********LOG*********/
					$kode = "REVISI";
					$keterangan = "Revisi, dengan catatan sebagai berikut : ".$REVISI;
					$this->log($PEMINDAHAN_ID, $kode, $keterangan, $PEGAWAI_ID);
					/********END LOG*********/

					/***** ambil detil permohonan ******/
					$pemindahan = new Pemindahan();
					$pemindahan->selectByParamsMonitoring(array("A.PEMINDAHAN_ID::varchar"=>$PEMINDAHAN_ID));
					$pemindahan->firstRow();
					$PERUSAHAAN_ID = $pemindahan->getField("PERUSAHAAN_ID");
					$NOMOR = $pemindahan->getField("NOMOR");
					$PERIHAL = $pemindahan->getField("PERIHAL");
					$TANGGAL = $pemindahan->getField("TANGGAL");
					$CREATED_BY = $pemindahan->getField("CREATED_BY");

					/***** ambil data pegawai ******/
					$this->load->model("UserLogin");
					$user_login = new UserLogin();
					$user_login->selectByParamsMonitoring(array("A.USER_LOGIN_ID::varchar"=>$CREATED_BY));
					$user_login->firstRow();
					$PEGAWAI_ID = $user_login->getField("PEGAWAI_ID");
					$PEGAWAI_NAMA = $user_login->getField("NAMA_PEGAWAI");
					$PEGAWAI_JABATAN = $user_login->getField("JABATAN");
					$PEGAWAI_EMAIL = $user_login->getField("EMAIL");

					/********Notifikasi Lonceng*********/
					$this->load->library("NotifikasiLonceng");
					$notifikasi_lonceng = new NotifikasiLonceng();
					$NOTIFIKASI_KODE = "PEMINDAHAN_REVISI";
					$NOTIFIKASI_PRIMARY_ID = $PEMINDAHAN_ID;
					$NOTIFIKASI_NAMA = "Revisi Permohonan Pemindahan";
					$NOTIFIKASI_KETERANGAN = $PERIHAL." Nomor : ".$NOMOR." Tanggal : ".getFormattedDateView($TANGGAL);
					$NOTIFIKASI_LINK = "app/loadUrl/app/permohonan_pemindahan_add/?id=".$PEMINDAHAN_ID;
					$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
					$NOTIFIKASI_USER_GROUP = "PEGAWAI";
					$NOTIFIKASI_PENGIRIM = $sesi_pengguna["USER_LOGIN_ID"];
					$NOTIFIKASI_PENGIRIM_NAMA = $sesi_pengguna["NAMA_PEGAWAI"];
					$NOTIFIKASI_PENGIRIM_JABATAN = $sesi_pengguna["JABATAN"];

					$notifikasi_lonceng->insertNotifikasi($NOTIFIKASI_KODE, $NOTIFIKASI_PRIMARY_ID, $NOTIFIKASI_NAMA, 
						$NOTIFIKASI_KETERANGAN, $NOTIFIKASI_LINK, $NOTIFIKASI_PENERIMA, $NOTIFIKASI_USER_GROUP, 
						$NOTIFIKASI_PENGIRIM, $NOTIFIKASI_PENGIRIM_NAMA, $NOTIFIKASI_PENGIRIM_JABATAN);
					/********END Notifikasi Lonceng*********/

					/********Notifikasi Email*********/
					$this->load->library("KMail");
					$mail = new KMail();
					$SUBJECT = "Revisi Permohonan Pemindahan dan Penyimpanan Arsip Inaktif";
					$mail->Subject = $SUBJECT;
					$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

					$KONTEN = "email/revisi_permohonan_pemindahan";
					$arrData = array("reqParse1" => $id);
					$body = $this->load->view($KONTEN, $arrData, true);
					$mail->MsgHTML($body);
					if($mail->Send()){
						$STATUS_EMAIL = "TERKIRIM";
					}
					else{
						$STATUS_EMAIL = "TIDAK_TERKIRIM";
					}

					$this->load->model("LogEmail");
					$log_email = new LogEmail();
					$log_email->setField("KODE", $NOTIFIKASI_KODE); 
					$log_email->setField("PRIMARY_ID", $PEMINDAHAN_ID); 
					$log_email->setField("JUDUL", $SUBJECT); 
					$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID); 
					$log_email->setField("EMAIL", $PEGAWAI_EMAIL); 
					$log_email->setField("KONTEN", $KONTEN); 
					$log_email->setField("STATUS", $STATUS_EMAIL);
					$log_email->insert();
					/********END Notifikasi Email*********/

					echo "BERHASIL|Berhasil merevisi permohonan";
				}
				else{
					echo "GAGAL|Gagal merevisi permohonan";
				}
			}
			else{
				echo "GAGAL|Gagal merevisi permohonan";
			}
		}
	}


	function revisi_enkripsi_peminjaman()
	{
		$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_7mj4R5iP');
		if (!$csrf->isTokenValid($this->input->post('_crfs_7mj4R5iP')) && $this->IS_MOBILE == false){
			exit();
		}
		
		$this->load->model("Peminjaman");

		$id = $this->input->post("id");
		$PEMINJAMAN_ID = $this->input->post("PEMINJAMAN_ID");
		$REVISI = setQuote($_POST['REVISI']);
		$PEGAWAI_ID = $this->input->post("PEGAWAI_ID");

		$sesi_pengguna = $this->sesi_pengguna($PEGAWAI_ID);

		if(trim($REVISI) == ""){
			echo "GAGAL|Ketikkan Catatan Revisi terlebih dahulu!";
		}
		else{
			//UPDATE PERMOHONAN_UPROVAL
			$peminjaman_approval = new Peminjaman();
			$peminjaman_approval->setField("PEMINJAMAN_APPROVAL_ID", $id);
			$peminjaman_approval->setField("STATUS", "REVISI");
			$peminjaman_approval->setField("REVISI", $REVISI); 
			$peminjaman_approval->setField("UPDATED_BY", $sesi_pengguna["USER_LOGIN_ID"]);
			if($peminjaman_approval->revisiPermohonanApproval()){

				//UPDATE PERMOHONAN
				$peminjaman = new Peminjaman();
				$peminjaman->setField("PEMINJAMAN_ID", $PEMINJAMAN_ID);
				$peminjaman->setField("STATUS", "REVISI");
				$peminjaman->setField("REVISI", $REVISI); 
				$peminjaman->setField("UPDATED_BY", $sesi_pengguna["USER_LOGIN_ID"]);
				if($peminjaman->revisiPermohonan()){

					/********LOG*********/
					$kode = "REVISI";
					$keterangan = "Revisi, dengan catatan sebagai berikut : ".$REVISI;
					$this->log_peminjaman($PEMINJAMAN_ID, $kode, $keterangan, $PEGAWAI_ID);
					/********END LOG*********/

					/***** ambil detil permohonan ******/
					$peminjaman = new Peminjaman();
					$peminjaman->selectByParamsMonitoring(array("A.PEMINJAMAN_ID::varchar"=>$PEMINJAMAN_ID));
					$peminjaman->firstRow();
					$PERUSAHAAN_ID = $peminjaman->getField("PERUSAHAAN_ID");
					$NOMOR = $peminjaman->getField("NOMOR");
					$PERIHAL = $peminjaman->getField("PERIHAL");
					$TANGGAL = $peminjaman->getField("TANGGAL");
					$CREATED_BY = $peminjaman->getField("CREATED_BY");

					/***** ambil data pegawai ******/
					$this->load->model("UserLogin");
					$user_login = new UserLogin();
					$user_login->selectByParamsMonitoring(array("A.USER_LOGIN_ID::varchar"=>$CREATED_BY));
					$user_login->firstRow();
					$PEGAWAI_ID = $user_login->getField("PEGAWAI_ID");
					$PEGAWAI_NAMA = $user_login->getField("NAMA_PEGAWAI");
					$PEGAWAI_JABATAN = $user_login->getField("JABATAN");
					$PEGAWAI_EMAIL = $user_login->getField("EMAIL");

					/********Notifikasi Lonceng*********/
					$this->load->library("NotifikasiLonceng");
					$notifikasi_lonceng = new NotifikasiLonceng();
					$NOTIFIKASI_KODE = "PEMINJAMAN_REVISI";
					$NOTIFIKASI_PRIMARY_ID = $PEMINJAMAN_ID;
					$NOTIFIKASI_NAMA = "Revisi Permohonan Peminjaman";
					$NOTIFIKASI_KETERANGAN = $PERIHAL." Nomor : ".$NOMOR." Tanggal : ".getFormattedDateView($TANGGAL);
					$NOTIFIKASI_LINK = "app/loadUrl/app/permohonan_peminjaman_add/?id=".$PEMINJAMAN_ID;
					$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
					$NOTIFIKASI_USER_GROUP = "PEGAWAI";
					$NOTIFIKASI_PENGIRIM = $sesi_pengguna["USER_LOGIN_ID"];
					$NOTIFIKASI_PENGIRIM_NAMA = $sesi_pengguna["NAMA_PEGAWAI"];
					$NOTIFIKASI_PENGIRIM_JABATAN = $sesi_pengguna["JABATAN"];

					$notifikasi_lonceng->insertNotifikasi($NOTIFIKASI_KODE, $NOTIFIKASI_PRIMARY_ID, $NOTIFIKASI_NAMA, 
						$NOTIFIKASI_KETERANGAN, $NOTIFIKASI_LINK, $NOTIFIKASI_PENERIMA, $NOTIFIKASI_USER_GROUP, 
						$NOTIFIKASI_PENGIRIM, $NOTIFIKASI_PENGIRIM_NAMA, $NOTIFIKASI_PENGIRIM_JABATAN);
					/********END Notifikasi Lonceng*********/

					/********Notifikasi Email*********/
					$this->load->library("KMail");
					$mail = new KMail();
					$SUBJECT = "Revisi Permohonan Peminjaman Arsip Inaktif";
					$mail->Subject = $SUBJECT;
					$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

					$KONTEN = "email/revisi_permohonan_peminjaman";
					$arrData = array("reqParse1" => $id);
					$body = $this->load->view($KONTEN, $arrData, true);
					$mail->MsgHTML($body);
					if($mail->Send()){
						$STATUS_EMAIL = "TERKIRIM";
					}
					else{
						$STATUS_EMAIL = "TIDAK_TERKIRIM";
					}

					$this->load->model("LogEmail");
					$log_email = new LogEmail();
					$log_email->setField("KODE", $NOTIFIKASI_KODE); 
					$log_email->setField("PRIMARY_ID", $PEMINJAMAN_ID); 
					$log_email->setField("JUDUL", $SUBJECT); 
					$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID); 
					$log_email->setField("EMAIL", $PEGAWAI_EMAIL); 
					$log_email->setField("KONTEN", $KONTEN); 
					$log_email->setField("STATUS", $STATUS_EMAIL);
					$log_email->insert();
					/********END Notifikasi Email*********/

					echo "BERHASIL|Berhasil merevisi permohonan";
				}
				else{
					echo "GAGAL|Gagal merevisi permohonan";
				}
			}
			else{
				echo "GAGAL|Gagal merevisi permohonan";
			}
		}
	}



	function approval_enkripsi()
	{	
		$this->load->model("Pemindahan");

		$id = $this->input->get("id");
		$PEMINDAHAN_ID = $this->input->get("PEMINDAHAN_ID");
		$URUT = $this->input->get("URUT");

		$PEGAWAI_ID = $this->input->get("PEGAWAI_ID");

		$sesi_pengguna = $this->sesi_pengguna($PEGAWAI_ID);

		//UPDATE PERMOHONAN_UPROVAL
		$pemindahan_approval = new Pemindahan();
		$pemindahan_approval->setField("PEMINDAHAN_APPROVAL_ID", $id);
		$pemindahan_approval->setField("STATUS", "APPROVE");
		$pemindahan_approval->setField("UPDATED_BY", $sesi_pengguna["USER_LOGIN_ID"]);
		if($pemindahan_approval->setujuiPermohonanApproval()){
			//CEK APAKAH MASIH ADA YG BELUM APPROVE
			$pemindahan_approval = new Pemindahan();
			$adaBelumApprove = $pemindahan_approval->getCountByParamsApproval(array("PEMINDAHAN_ID"=>$PEMINDAHAN_ID,"STATUS"=>"BELUM_APPROVE"));

			if($adaBelumApprove > 0){ //JIKA ADA YG BELUM APPROVE, MAKA POSTING KE APPROVAL SELANJUTNYA
				/***** ambil detil permohonan ******/
				$pemindahan = new Pemindahan();
				$pemindahan->selectByParamsMonitoring(array("A.PEMINDAHAN_ID::varchar"=>$PEMINDAHAN_ID));
				$pemindahan->firstRow();
				$PERUSAHAAN_ID = $pemindahan->getField("PERUSAHAAN_ID");
				$NOMOR = $pemindahan->getField("NOMOR");
				$PERIHAL = $pemindahan->getField("PERIHAL");
				$TANGGAL = $pemindahan->getField("TANGGAL");

				/***** ambil approval urut selanjutnya ******/
				$URUT += 1;
				$pemindahan_approval = new Pemindahan();
				$pemindahan_approval->selectByParamsApprovalEmail(array("A.PEMINDAHAN_ID::varchar"=>$PEMINDAHAN_ID,"A.URUT"=>$URUT));
				$pemindahan_approval->firstRow();
				$PEMINDAHAN_APPROVAL_ID = $pemindahan_approval->getField("PEMINDAHAN_APPROVAL_ID");
				$PEGAWAI_ID = $pemindahan_approval->getField("PEGAWAI_ID");
				$PEGAWAI_NAMA = $pemindahan_approval->getField("NAMA");
				$PEGAWAI_JABATAN = $pemindahan_approval->getField("JABATAN");
				$PEGAWAI_EMAIL = $pemindahan_approval->getField("EMAIL");

				/********LOG*********/
				$kode = "APPROVE";
				$keterangan = "Menyetujui & memposting Permohonan Pemindahan Arsip Inaktif kepada Bapak/Ibu ".$PEGAWAI_NAMA." (".$PEGAWAI_JABATAN.")";
				$this->log($PEMINDAHAN_ID, $kode, $keterangan, $PEGAWAI_ID);
				/********END LOG*********/

				/********Notifikasi Lonceng*********/
				$this->load->library("NotifikasiLonceng");
				$notifikasi_lonceng = new NotifikasiLonceng();
				$NOTIFIKASI_KODE = "PEMINDAHAN_POSTING";
				$NOTIFIKASI_PRIMARY_ID = $PEMINDAHAN_ID;
				$NOTIFIKASI_NAMA = "Permohonan Approval";
				$NOTIFIKASI_KETERANGAN = $PERIHAL." Nomor : ".$NOMOR." Tanggal : ".getFormattedDateView($TANGGAL);
				$NOTIFIKASI_LINK = "app/loadUrl/app/approval_permohonan_pemindahan_detil/?id=".$PEMINDAHAN_APPROVAL_ID;
				$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
				$NOTIFIKASI_USER_GROUP = "PEGAWAI";
				$NOTIFIKASI_PENGIRIM = $sesi_pengguna["USER_LOGIN_ID"];
				$NOTIFIKASI_PENGIRIM_NAMA = $sesi_pengguna["NAMA_PEGAWAI"];
				$NOTIFIKASI_PENGIRIM_JABATAN = $sesi_pengguna["JABATAN"];

				$notifikasi_lonceng->insertNotifikasi($NOTIFIKASI_KODE, $NOTIFIKASI_PRIMARY_ID, $NOTIFIKASI_NAMA, 
					$NOTIFIKASI_KETERANGAN, $NOTIFIKASI_LINK, $NOTIFIKASI_PENERIMA, $NOTIFIKASI_USER_GROUP, 
					$NOTIFIKASI_PENGIRIM, $NOTIFIKASI_PENGIRIM_NAMA, $NOTIFIKASI_PENGIRIM_JABATAN);
				/********END Notifikasi Lonceng*********/

				/********Notifikasi Email*********/
				$this->load->library("KMail");
				$mail = new KMail();
				$SUBJECT = "Approval/Persetujuan Permohonan Pemindahan dan Penyimpanan Arsip Inaktif";
				$mail->Subject = $SUBJECT;
				$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

				$KONTEN = "email/approval_permohonan_pemindahan";
				$arrData = array("reqParse1" => $PEMINDAHAN_APPROVAL_ID);
				$body = $this->load->view($KONTEN, $arrData, true);
				$mail->MsgHTML($body);
				if($mail->Send()){
					$STATUS_EMAIL = "TERKIRIM";
				}
				else{
					$STATUS_EMAIL = "TIDAK_TERKIRIM";
				}

				$this->load->model("LogEmail");
				$log_email = new LogEmail();
				$log_email->setField("KODE", $NOTIFIKASI_KODE); 
				$log_email->setField("PRIMARY_ID", $PEMINDAHAN_ID); 
				$log_email->setField("JUDUL", $SUBJECT); 
				$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID); 
				$log_email->setField("EMAIL", $PEGAWAI_EMAIL); 
				$log_email->setField("KONTEN", $KONTEN); 
				$log_email->setField("STATUS", $STATUS_EMAIL);
				$log_email->insert();
				/********END Notifikasi Email*********/

				echo "Berhasil Menyetujui & Memposting Permohonan Pemindahan Arsip Inaktif";
			}
			else{
				 //JIKA SUDAH APPROVE SEMUA, KIRIM KE TUJUAN
				$pemindahan = new Pemindahan();
				$pemindahan->setField("PEMINDAHAN_ID", $PEMINDAHAN_ID);
				$pemindahan->setField("FIELD", "STATUS");
				$pemindahan->setField("FIELD_VALUE", "TERKIRIM");
				$pemindahan->setField("UPDATED_BY", $sesi_pengguna["USER_LOGIN_ID"]);
				if ($pemindahan->updateByField())
				{
					/********LOG*********/
					$kode = "APPROVE";
					$keterangan = "Menyetujui & mengirim Permohonan Pemindahan Arsip Inaktif kepada Unit Kearsipan";
					$this->log($PEMINDAHAN_ID, $kode, $keterangan, $PEGAWAI_ID);
					/********END LOG*********/

					/***** ambil detil permohonan ******/
					$pemindahan = new Pemindahan();
					$pemindahan->selectByParamsMonitoring(array("A.PEMINDAHAN_ID::varchar"=>$PEMINDAHAN_ID));
					$pemindahan->firstRow();
					$PERUSAHAAN_ID = $pemindahan->getField("PERUSAHAAN_ID");
					$NOMOR = $pemindahan->getField("NOMOR");
					$PERIHAL = $pemindahan->getField("PERIHAL");
					$TANGGAL = $pemindahan->getField("TANGGAL");

					/***** kirim ke tujuan/tembusan ******/
					$pemindahan_tujuan = new Pemindahan();
					$pemindahan_tujuan->selectByParamsTujuanEmail(array("A.PEMINDAHAN_ID::varchar"=>$PEMINDAHAN_ID));
					while ($pemindahan_tujuan->nextRow()) {
						$PEMINDAHAN_TUJUAN_ID = $pemindahan_tujuan->getField("PEMINDAHAN_TUJUAN_ID");
						$PEGAWAI_ID = $pemindahan_tujuan->getField("PEGAWAI_ID");
						$PEGAWAI_NAMA = $pemindahan_tujuan->getField("NAMA");
						$PEGAWAI_JABATAN = $pemindahan_tujuan->getField("JABATAN");
						$PEGAWAI_EMAIL = $pemindahan_tujuan->getField("EMAIL");

						//UPDATE PERMOHONAN_TUJUAN
						$pemindahan_tujuan_disposisi = new Pemindahan();
						$pemindahan_tujuan_disposisi->setField("PEMINDAHAN_TUJUAN_ID", $PEMINDAHAN_TUJUAN_ID);
						$pemindahan_tujuan_disposisi->setField("PERUSAHAAN_ID_DISPOSISI", $sesi_pengguna["PERUSAHAAN_ID"]);
						$pemindahan_tujuan_disposisi->setField("CABANG_ID_DISPOSISI", $sesi_pengguna["CABANG_ID"]);
						$pemindahan_tujuan_disposisi->setField("SATUAN_KERJA_ID_DISPOSISI", $sesi_pengguna["SATUAN_KERJA_ID"]);
						$pemindahan_tujuan_disposisi->setField("PEGAWAI_ID_DISPOSISI", $sesi_pengguna["PEGAWAI_ID"]);
						$pemindahan_tujuan_disposisi->setField("KODE_DISPOSISI", $sesi_pengguna["KODE_PEGAWAI"]);
						$pemindahan_tujuan_disposisi->setField("NAMA_DISPOSISI", $sesi_pengguna["NAMA_PEGAWAI"]);
						$pemindahan_tujuan_disposisi->setField("JABATAN_DISPOSISI", $sesi_pengguna["JABATAN"]);
						$pemindahan_tujuan_disposisi->setField("TERDISPOSISI", "TIDAK");
						$pemindahan_tujuan_disposisi->setField("TERBACA", "TIDAK");
						$pemindahan_tujuan_disposisi->setField("PESAN_DISPOSISI", setQuote($PESAN_DISPOSISI));
						$pemindahan_tujuan_disposisi->setField("PEMINDAHAN_TUJUAN_ID_PARENT", coalesce($PEMINDAHAN_TUJUAN_ID_PARENT,$PEMINDAHAN_TUJUAN_ID));
						$pemindahan_tujuan_disposisi->setField("UPDATED_BY", $sesi_pengguna["USER_LOGIN_ID"]);
						$pemindahan_tujuan_disposisi->updateTujuanDisposisi();

						/********Notifikasi Lonceng*********/
						$this->load->library("NotifikasiLonceng");
						$notifikasi_lonceng = new NotifikasiLonceng();
						$NOTIFIKASI_KODE = "PEMINDAHAN_TERKIRIM";
						$NOTIFIKASI_PRIMARY_ID = $PEMINDAHAN_ID;
						$NOTIFIKASI_NAMA = "Permohonan Pemindahan";
						$NOTIFIKASI_KETERANGAN = $PERIHAL." Nomor : ".$NOMOR." Tanggal : ".getFormattedDateView($TANGGAL);
						$NOTIFIKASI_LINK = "app/loadUrl/app/inbox_permohonan_pemindahan_detil/?id=".$PEMINDAHAN_TUJUAN_ID;
						$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
						$NOTIFIKASI_USER_GROUP = "PEGAWAI";
						$NOTIFIKASI_PENGIRIM = $sesi_pengguna["USER_LOGIN_ID"];
						$NOTIFIKASI_PENGIRIM_NAMA = $sesi_pengguna["NAMA_PEGAWAI"];
						$NOTIFIKASI_PENGIRIM_JABATAN = $sesi_pengguna["JABATAN"];

						$notifikasi_lonceng->insertNotifikasi($NOTIFIKASI_KODE, $NOTIFIKASI_PRIMARY_ID, $NOTIFIKASI_NAMA, 
							$NOTIFIKASI_KETERANGAN, $NOTIFIKASI_LINK, $NOTIFIKASI_PENERIMA, $NOTIFIKASI_USER_GROUP, 
							$NOTIFIKASI_PENGIRIM, $NOTIFIKASI_PENGIRIM_NAMA, $NOTIFIKASI_PENGIRIM_JABATAN);
						/********END Notifikasi Lonceng*********/

						/********Notifikasi Email*********/
						$this->load->library("KMail");
						$mail = new KMail();
						$SUBJECT = "Permohonan Pemindahan dan Penyimpanan Arsip Inaktif";
						$mail->Subject = $SUBJECT;
						$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

						$KONTEN = "email/inbox_permohonan_pemindahan";
						$arrData = array("reqParse1" => $PEMINDAHAN_TUJUAN_ID);
						$body = $this->load->view($KONTEN, $arrData, true);
						$mail->MsgHTML($body);
						if($mail->Send()){
							$STATUS_EMAIL = "TERKIRIM";
						}
						else{
							$STATUS_EMAIL = "TIDAK_TERKIRIM";
						}

						$this->load->model("LogEmail");
						$log_email = new LogEmail();
						$log_email->setField("KODE", $NOTIFIKASI_KODE); 
						$log_email->setField("PRIMARY_ID", $PEMINDAHAN_ID); 
						$log_email->setField("JUDUL", $SUBJECT); 
						$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID); 
						$log_email->setField("EMAIL", $PEGAWAI_EMAIL); 
						$log_email->setField("KONTEN", $KONTEN); 
						$log_email->setField("STATUS", $STATUS_EMAIL);
						$log_email->insert();
						/********END Notifikasi Email*********/
					}

					echo "Berhasil Menyetujui & Mengirim Permohonan Pemindahan Arsip Inaktif kepada Unit Kearsipan";
				}
				else{
					echo "GAGAL|Gagal menyetujui permohonan";
				}
			}
		}
		else{
			echo "GAGAL|Gagal menyetujui permohonan";
		}
	}


	function approval_enkripsi_peminjaman()
	{	
		$this->load->model("Peminjaman");

		$id = $this->input->get("id");
		$PEMINJAMAN_ID = $this->input->get("PEMINJAMAN_ID");
		$URUT = $this->input->get("URUT");

		$PEGAWAI_ID = $this->input->get("PEGAWAI_ID");

		$sesi_pengguna = $this->sesi_pengguna($PEGAWAI_ID);

		//UPDATE PERMOHONAN_UPROVAL
		$peminjaman_approval = new Peminjaman();
		$peminjaman_approval->setField("PEMINJAMAN_APPROVAL_ID", $id);
		$peminjaman_approval->setField("STATUS", "APPROVE");
		$peminjaman_approval->setField("UPDATED_BY", $sesi_pengguna["USER_LOGIN_ID"]);
		if($peminjaman_approval->setujuiPermohonanApproval()){
			//CEK APAKAH MASIH ADA YG BELUM APPROVE
			$peminjaman_approval = new Peminjaman();
			$adaBelumApprove = $peminjaman_approval->getCountByParamsApproval(array("PEMINJAMAN_ID"=>$PEMINJAMAN_ID,"STATUS"=>"BELUM_APPROVE"));

			if($adaBelumApprove > 0){ //JIKA ADA YG BELUM APPROVE, MAKA POSTING KE APPROVAL SELANJUTNYA
				/***** ambil detil permohonan ******/
				$peminjaman = new Peminjaman();
				$peminjaman->selectByParamsMonitoring(array("A.PEMINJAMAN_ID::varchar"=>$PEMINJAMAN_ID));
				$peminjaman->firstRow();
				$PERUSAHAAN_ID = $peminjaman->getField("PERUSAHAAN_ID");
				$NOMOR = $peminjaman->getField("NOMOR");
				$PERIHAL = $peminjaman->getField("PERIHAL");
				$TANGGAL = $peminjaman->getField("TANGGAL");

				/***** ambil approval urut selanjutnya ******/
				$URUT += 1;
				$peminjaman_approval = new Peminjaman();
				$peminjaman_approval->selectByParamsApprovalEmail(array("A.PEMINJAMAN_ID::varchar"=>$PEMINJAMAN_ID,"A.URUT"=>$URUT));
				$peminjaman_approval->firstRow();
				$PEMINJAMAN_APPROVAL_ID = $peminjaman_approval->getField("PEMINJAMAN_APPROVAL_ID");
				$PEGAWAI_ID = $peminjaman_approval->getField("PEGAWAI_ID");
				$PEGAWAI_NAMA = $peminjaman_approval->getField("NAMA");
				$PEGAWAI_JABATAN = $peminjaman_approval->getField("JABATAN");
				$PEGAWAI_EMAIL = $peminjaman_approval->getField("EMAIL");

				/********LOG*********/
				$kode = "APPROVE";
				$keterangan = "Menyetujui & memposting Permohonan Peminjaman Arsip Inaktif kepada Bapak/Ibu ".$PEGAWAI_NAMA." (".$PEGAWAI_JABATAN.")";
				$this->log_peminjaman($PEMINJAMAN_ID, $kode, $keterangan, $PEGAWAI_ID);
				/********END LOG*********/

				/********Notifikasi Lonceng*********/
				$this->load->library("NotifikasiLonceng");
				$notifikasi_lonceng = new NotifikasiLonceng();
				$NOTIFIKASI_KODE = "PEMINJAMAN_POSTING";
				$NOTIFIKASI_PRIMARY_ID = $PEMINJAMAN_ID;
				$NOTIFIKASI_NAMA = "Permohonan Approval";
				$NOTIFIKASI_KETERANGAN = $PERIHAL." Nomor : ".$NOMOR." Tanggal : ".getFormattedDateView($TANGGAL);
				$NOTIFIKASI_LINK = "app/loadUrl/app/approval_permohonan_peminjaman_detil/?id=".$PEMINJAMAN_APPROVAL_ID;
				$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
				$NOTIFIKASI_USER_GROUP = "PEGAWAI";
				$NOTIFIKASI_PENGIRIM = $sesi_pengguna["USER_LOGIN_ID"];
				$NOTIFIKASI_PENGIRIM_NAMA = $sesi_pengguna["NAMA_PEGAWAI"];
				$NOTIFIKASI_PENGIRIM_JABATAN = $sesi_pengguna["JABATAN"];

				$notifikasi_lonceng->insertNotifikasi($NOTIFIKASI_KODE, $NOTIFIKASI_PRIMARY_ID, $NOTIFIKASI_NAMA, 
					$NOTIFIKASI_KETERANGAN, $NOTIFIKASI_LINK, $NOTIFIKASI_PENERIMA, $NOTIFIKASI_USER_GROUP, 
					$NOTIFIKASI_PENGIRIM, $NOTIFIKASI_PENGIRIM_NAMA, $NOTIFIKASI_PENGIRIM_JABATAN);
				/********END Notifikasi Lonceng*********/

				/********Notifikasi Email*********/
				$this->load->library("KMail");
				$mail = new KMail();
				$SUBJECT = "Approval/Persetujuan Permohonan Peminjaman Arsip Inaktif";
				$mail->Subject = $SUBJECT;
				$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

				$KONTEN = "email/approval_permohonan_peminjaman";
				$arrData = array("reqParse1" => $PEMINJAMAN_APPROVAL_ID);
				$body = $this->load->view($KONTEN, $arrData, true);
				$mail->MsgHTML($body);
				if($mail->Send()){
					$STATUS_EMAIL = "TERKIRIM";
				}
				else{
					$STATUS_EMAIL = "TIDAK_TERKIRIM";
				}

				$this->load->model("LogEmail");
				$log_email = new LogEmail();
				$log_email->setField("KODE", $NOTIFIKASI_KODE); 
				$log_email->setField("PRIMARY_ID", $PEMINJAMAN_ID); 
				$log_email->setField("JUDUL", $SUBJECT); 
				$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID); 
				$log_email->setField("EMAIL", $PEGAWAI_EMAIL); 
				$log_email->setField("KONTEN", $KONTEN); 
				$log_email->setField("STATUS", $STATUS_EMAIL);
				$log_email->insert();
				/********END Notifikasi Email*********/

				echo "Berhasil Menyetujui & Memposting Permohonan Peminjaman Arsip Inaktif";
			}
			else{
				 //JIKA SUDAH APPROVE SEMUA, KIRIM KE TUJUAN
				$peminjaman = new Peminjaman();
				$peminjaman->setField("PEMINJAMAN_ID", $PEMINJAMAN_ID);
				$peminjaman->setField("FIELD", "STATUS");
				$peminjaman->setField("FIELD_VALUE", "TERKIRIM");
				$peminjaman->setField("UPDATED_BY", $sesi_pengguna["USER_LOGIN_ID"]);
				if ($peminjaman->updateByField())
				{
					/********LOG*********/
					$kode = "APPROVE";
					$keterangan = "Menyetujui & mengirim Permohonan Peminjaman Arsip Inaktif kepada Unit Kearsipan";
					$this->log_peminjaman($PEMINJAMAN_ID, $kode, $keterangan, $PEGAWAI_ID);
					/********END LOG*********/

					/***** ambil detil permohonan ******/
					$peminjaman = new Peminjaman();
					$peminjaman->selectByParamsMonitoring(array("A.PEMINJAMAN_ID::varchar"=>$PEMINJAMAN_ID));
					$peminjaman->firstRow();
					$PERUSAHAAN_ID = $peminjaman->getField("PERUSAHAAN_ID");
					$NOMOR = $peminjaman->getField("NOMOR");
					$PERIHAL = $peminjaman->getField("PERIHAL");
					$TANGGAL = $peminjaman->getField("TANGGAL");

					/***** kirim ke tujuan/tembusan ******/
					$peminjaman_tujuan = new Peminjaman();
					$peminjaman_tujuan->selectByParamsTujuanEmail(array("A.PEMINJAMAN_ID::varchar"=>$PEMINJAMAN_ID));
					while ($peminjaman_tujuan->nextRow()) {
						$PEMINJAMAN_TUJUAN_ID = $peminjaman_tujuan->getField("PEMINJAMAN_TUJUAN_ID");
						$PEGAWAI_ID = $peminjaman_tujuan->getField("PEGAWAI_ID");
						$PEGAWAI_NAMA = $peminjaman_tujuan->getField("NAMA");
						$PEGAWAI_JABATAN = $peminjaman_tujuan->getField("JABATAN");
						$PEGAWAI_EMAIL = $peminjaman_tujuan->getField("EMAIL");

						//UPDATE PERMOHONAN_TUJUAN
						$peminjaman_tujuan_disposisi = new Peminjaman();
						$peminjaman_tujuan_disposisi->setField("PEMINJAMAN_TUJUAN_ID", $PEMINJAMAN_TUJUAN_ID);
						$peminjaman_tujuan_disposisi->setField("PERUSAHAAN_ID_DISPOSISI", $sesi_pengguna["PERUSAHAAN_ID"]);
						$peminjaman_tujuan_disposisi->setField("CABANG_ID_DISPOSISI", $sesi_pengguna["CABANG_ID"]);
						$peminjaman_tujuan_disposisi->setField("SATUAN_KERJA_ID_DISPOSISI", $sesi_pengguna["SATUAN_KERJA_ID"]);
						$peminjaman_tujuan_disposisi->setField("PEGAWAI_ID_DISPOSISI", $sesi_pengguna["PEGAWAI_ID"]);
						$peminjaman_tujuan_disposisi->setField("KODE_DISPOSISI", $sesi_pengguna["KODE_PEGAWAI"]);
						$peminjaman_tujuan_disposisi->setField("NAMA_DISPOSISI", $sesi_pengguna["NAMA_PEGAWAI"]);
						$peminjaman_tujuan_disposisi->setField("JABATAN_DISPOSISI", $sesi_pengguna["JABATAN"]);
						$peminjaman_tujuan_disposisi->setField("TERDISPOSISI", "TIDAK");
						$peminjaman_tujuan_disposisi->setField("TERBACA", "TIDAK");
						$peminjaman_tujuan_disposisi->setField("PESAN_DISPOSISI", setQuote($PESAN_DISPOSISI));
						$peminjaman_tujuan_disposisi->setField("PEMINJAMAN_TUJUAN_ID_PARENT", coalesce($PEMINJAMAN_TUJUAN_ID_PARENT,$PEMINJAMAN_TUJUAN_ID));
						$peminjaman_tujuan_disposisi->setField("UPDATED_BY", $sesi_pengguna["USER_LOGIN_ID"]);
						$peminjaman_tujuan_disposisi->updateTujuanDisposisi();

						/********Notifikasi Lonceng*********/
						$this->load->library("NotifikasiLonceng");
						$notifikasi_lonceng = new NotifikasiLonceng();
						$NOTIFIKASI_KODE = "PEMINJAMAN_TERKIRIM";
						$NOTIFIKASI_PRIMARY_ID = $PEMINJAMAN_ID;
						$NOTIFIKASI_NAMA = "Permohonan Peminjaman";
						$NOTIFIKASI_KETERANGAN = $PERIHAL." Nomor : ".$NOMOR." Tanggal : ".getFormattedDateView($TANGGAL);
						$NOTIFIKASI_LINK = "app/loadUrl/app/inbox_permohonan_peminjaman_detil/?id=".$PEMINJAMAN_TUJUAN_ID;
						$NOTIFIKASI_PENERIMA = $PEGAWAI_ID;
						$NOTIFIKASI_USER_GROUP = "PEGAWAI";
						$NOTIFIKASI_PENGIRIM = $sesi_pengguna["USER_LOGIN_ID"];
						$NOTIFIKASI_PENGIRIM_NAMA = $sesi_pengguna["NAMA_PEGAWAI"];
						$NOTIFIKASI_PENGIRIM_JABATAN = $sesi_pengguna["JABATAN"];

						$notifikasi_lonceng->insertNotifikasi($NOTIFIKASI_KODE, $NOTIFIKASI_PRIMARY_ID, $NOTIFIKASI_NAMA, 
							$NOTIFIKASI_KETERANGAN, $NOTIFIKASI_LINK, $NOTIFIKASI_PENERIMA, $NOTIFIKASI_USER_GROUP, 
							$NOTIFIKASI_PENGIRIM, $NOTIFIKASI_PENGIRIM_NAMA, $NOTIFIKASI_PENGIRIM_JABATAN);
						/********END Notifikasi Lonceng*********/

						/********Notifikasi Email*********/
						$this->load->library("KMail");
						$mail = new KMail();
						$SUBJECT = "Permohonan Peminjaman Arsip Inaktif";
						$mail->Subject = $SUBJECT;
						$mail->AddAddress($PEGAWAI_EMAIL, $PEGAWAI_NAMA);

						$KONTEN = "email/inbox_permohonan_peminjaman";
						$arrData = array("reqParse1" => $PEMINJAMAN_TUJUAN_ID);
						$body = $this->load->view($KONTEN, $arrData, true);
						$mail->MsgHTML($body);
						if($mail->Send()){
							$STATUS_EMAIL = "TERKIRIM";
						}
						else{
							$STATUS_EMAIL = "TIDAK_TERKIRIM";
						}

						$this->load->model("LogEmail");
						$log_email = new LogEmail();
						$log_email->setField("KODE", $NOTIFIKASI_KODE); 
						$log_email->setField("PRIMARY_ID", $PEMINJAMAN_ID); 
						$log_email->setField("JUDUL", $SUBJECT); 
						$log_email->setField("PEGAWAI_ID", $PEGAWAI_ID); 
						$log_email->setField("EMAIL", $PEGAWAI_EMAIL); 
						$log_email->setField("KONTEN", $KONTEN); 
						$log_email->setField("STATUS", $STATUS_EMAIL);
						$log_email->insert();
						/********END Notifikasi Email*********/
					}

					echo "Berhasil Menyetujui & Mengirim Permohonan Peminjaman Arsip Inaktif kepada Unit Kearsipan";
				}
				else{
					echo "GAGAL|Gagal menyetujui permohonan";
				}
			}
		}
		else{
			echo "GAGAL|Gagal menyetujui permohonan";
		}
	}


	function log($id, $kode, $keterangan, $pegawaiId)
	{
		$this->load->model("Pemindahan");
		$pemindahan = new Pemindahan();

		$sesi_pengguna = $this->sesi_pengguna($pegawaiId);

		$pemindahan->setField("PEMINDAHAN_ID", $id);
		$pemindahan->setField("KODE", $kode);
		$pemindahan->setField("KETERANGAN", setQuote($keterangan));
		$pemindahan->setField("PEGAWAI_ID", $sesi_pengguna["PEGAWAI_ID"]);
		$pemindahan->setField("PEGAWAI_KODE", $sesi_pengguna["KODE_PEGAWAI"]);
		$pemindahan->setField("PEGAWAI_NAMA", $sesi_pengguna["NAMA_PEGAWAI"]);
		$pemindahan->setField("PEGAWAI_JABATAN", $sesi_pengguna["JABATAN"]);
		$pemindahan->setField("CREATED_BY", $sesi_pengguna["USER_LOGIN_ID"]);
		$pemindahan->insertLog();
	}


	function log_peminjaman($id, $kode, $keterangan, $pegawaiId)
	{
		$this->load->model("Peminjaman");
		$peminjaman = new Peminjaman();

		$sesi_pengguna = $this->sesi_pengguna($pegawaiId);

		$peminjaman->setField("PEMINJAMAN_ID", $id);
		$peminjaman->setField("KODE", $kode);
		$peminjaman->setField("KETERANGAN", setQuote($keterangan));
		$peminjaman->setField("PEGAWAI_ID", $sesi_pengguna["PEGAWAI_ID"]);
		$peminjaman->setField("PEGAWAI_KODE", $sesi_pengguna["KODE_PEGAWAI"]);
		$peminjaman->setField("PEGAWAI_NAMA", $sesi_pengguna["NAMA_PEGAWAI"]);
		$peminjaman->setField("PEGAWAI_JABATAN", $sesi_pengguna["JABATAN"]);
		$peminjaman->setField("CREATED_BY", $sesi_pengguna["USER_LOGIN_ID"]);
		$peminjaman->insertLog();
	}
}