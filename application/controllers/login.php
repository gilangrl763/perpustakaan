<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once("functions/image.func.php");
include_once("functions/string.func.php");

class login extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		/* GLOBAL VARIABLE */
		$this->db->query("SET DATESTYLE TO PostgreSQL,European;");
	}

	public function index()
	{

		$pg = $this->uri->segment(3, "home");
		$reqParse1 = $this->uri->segment(4, "");
		$reqParse2 = $this->uri->segment(5, "");
		$reqParse3 = $this->uri->segment(6, "");
		$reqParse4 = $this->uri->segment(7, "");
		$reqParse5 = $this->uri->segment(5, "");

		$view = array(
			'pg' => $pg,
			'reqParse1' => $reqParse1,
			'reqParse2'	=> $reqParse2,
			'reqParse3'	=> $reqParse3,
			'reqParse4'	=> $reqParse4,
			'reqParse5'	=> $reqParse5
		);

		$data = array(
			'pg' => $pg,
			'reqParse1' => $reqParse1,
			'reqParse2'	=> $reqParse2,
			'reqParse3'	=> $reqParse3,
			'reqParse4'	=> $reqParse4,
			'reqParse5'	=> $reqParse5
		);

		$this->load->view('login/index', $data);
	}

	public function action()
	{
		$reqEmail = $this->input->post("reqEmail");
		$reqPassword = $this->input->post("reqPassword");

		if (!empty($reqEmail) and !empty($reqPassword)) {
			$respon = $this->kauth->localAuthenticate($reqEmail, $reqPassword);
			if ($respon == "1") {
				//JIKA CUSTOMER
				if ($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID == "2") {
					redirect('main/index');
				} else {
					redirect('app/index');
				}
			} elseif ($respon == "MULTIROLE") {
				redirect("role");
			} else {
				$data['pesan'] = translate("Username atau Password salah", "Wrong username or password");
				$this->load->view('login/index', $data);
			}
		} else {
			$data['pesan'] = translate("Masukkan Username dan Password", "Enter a Username and Password");
			$this->load->view('login/index', $data);
		}
	}


	public function multi()
	{
		$this->load->library("crfs_protect");
		$csrf = new crfs_protect('_crfs_role');
		if (!$csrf->isTokenValid($_POST['_crfs_role'])) {
			exit();
		}


		$reqGroupId = $this->input->post("reqGroupId");

		if (trim($this->kauth->getInstance()->getIdentity()->USER_TYPE) == "") {
			redirect("login");
		} else {
			/* CEGAH REKANAN MASUK || YANG GA PUNYA LOGIN */
			if ($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID == "2" || $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID == "") {
				redirect("login");
			}
		}

		$arrHakAkses = explode(",", $this->kauth->getInstance()->getIdentity()->USER_TYPE);

		/* CHECK APAKAH ADA AKSES */
		$adaAkses = 0;
		for ($i = 0; $i < count($arrHakAkses); $i++) {
			if (trim($reqGroupId) == trim($arrHakAkses[$i])) {
				$adaAkses = 1;
			}
		}

		if ($adaAkses == 0) {
			redirect("login");
		}

		$respon = $this->kauth->multiAkses($reqGroupId);

		if ($respon == "1") {
			redirect('cms');
		}
	}


	public function logout()
	{
		$this->kauth->getInstance()->clearIdentity();
		echo "Berhasil Logout";
	}


	public function lupa_password()
	{
		$this->load->model("UserLogin");
		$user_login = new UserLogin();

		$reqUser = $this->input->post("reqUser");
		if (!empty($reqUser)) {
			$statement = "AND (A.USER_LOGIN = '" . $reqUser . "' OR A.EMAIL = '" . $reqUser . "') ";
			$user_login->selectByParams(array(), -1, -1, $statement);
			// echo $user_login->query;exit;
			$user_login->firstRow();
			$reqUserLoginId = $user_login->getField("USER_LOGIN_ID");
			$reqEmail = $user_login->getField("EMAIL");
			$reqKepada = $user_login->getField("NAMA");

			if ($reqUserLoginId != "") {
				function randomPassword()
				{
					$kata = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
					$pass = array();
					$panjangKata = strlen($kata) - 1;
					for ($i = 0; $i < 8; $i++) {
						$n = rand(0, $panjangKata);
						$pass[] = $kata[$n];
					}
					return implode($pass);
				}

				$reqPassword 	= randomPassword();

				$user_login = new UserLogin();
				$user_login->setField("USER_PASSWORD", MD5($reqPassword));
				$user_login->setField("STATUS", "1");
				$user_login->setField("UPDATED_BY", $reqUserLoginId);
				$user_login->setField("USER_LOGIN_ID", $reqUserLoginId);
				if ($user_login->resetPassword()) {
					/* EMAIL */
					$this->load->library("KMail");
					$mail = new KMail();
					$mail->Subject = "Reset Password SIP | PT Angkasa Pura I (Persero)";
					$mail->AddAddress($reqEmail, $reqKepada);

					$arrData = array("reqId" => $reqUserLoginId, "reqPassword" => $reqPassword);
					$body = $this->load->view("email/lupa_password", $arrData, true);
					$mail->MsgHTML($body);

					if (!$mail->Send()) {
						$data['pesan'] = "E-mail gagal dikirimkan, silahkan hubungi administrator";
						$this->load->view('login/lupa_password', $data);
					} else {
						$data['pesan'] = "Reset password berhasil, password terbaru telah kami kirimkan ke e-mail anda";
						$this->load->view('login/lupa_password', $data);
					}
					/* END EMAIL */
				}
			} else {
				$data['pesan'] = "Username/E-mail tidak ditemukan";
				$this->load->view('login/lupa_password', $data);
			}
		} else {
			$data['pesan'] = "Masukkan Username/E-mail terlebih dahulu";
			$this->load->view('login/lupa_password', $data);
		}
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
			'reqParse1' => urldecode($reqParse1),
			'reqParse2' => urldecode($reqParse2),
			'reqParse3' => urldecode($reqParse3),
			'reqParse4' => urldecode($reqParse4),
			'reqParse5' => urldecode($reqParse5)
		);
		$this->load->view($reqFolder . '/' . $reqFilename, $data);
	}

	public function getTokenFirebase()
	{
		$reqToken = $this->kauth->getInstance()->getIdentity()->TOKEN;

		$this->load->model('UserLoginWeb');

		$user_login_web = new UserLoginWeb();

		$reqPegawaiId = $user_login_web->getTokenFirebase(array("TOKEN" => $reqToken, "STATUS" => '1'));

		echo ($reqPegawaiId);
	}

	public function setTokenFirebase()
	{
		$reqToken = $this->kauth->getInstance()->getIdentity()->TOKEN;
		$reqTokenFirebase = $this->input->post('reqTokenFirebase');

		$this->load->model('UserLoginWeb');

		$user_login_web = new UserLoginWeb();

		$user_login_web->setField("TOKEN", $reqToken);
		$user_login_web->setField("TOKEN_FIREBASE", $reqTokenFirebase);
		if ($user_login_web->updateTokenFirebase()) {
			echo ("1");
		} else {
			echo ("0");
		}
	}
}
