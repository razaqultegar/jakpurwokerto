<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Masuk extends MX_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('masuk/m_masuk');
		// message
		$this->pesanMasukWarning = "Username dan Password Tidak Boleh Kosong";
		$this->pesanColorWarning = "warning";
		$this->pesanMasukError = "Masuk Gagal, Username atau Password Tidak Sesuai";
		$this->pesanColorError = "error";
	}

	public function index() {
		$data['msg'] = $this->session->userdata('pesan');
		$data['title'] = 'Masuk';
		$this->load->view('masuk/view_masuk', $data);
	}

	public function validate_masuk() {
		$this->form_validation->set_rules('username', 'Username', 'required|trim');
		$this->form_validation->set_rules('password', 'Password', 'required|md5');
		// jika form yang di isi kosong
		if($this->form_validation->run()==FALSE){
			$result = 1;
			$params = array($result, $this->pesanColorWarning, $this->pesanMasukWarning, '');
			$this->session->set_userdata('pesan', $params); 
			redirect('masuk');
		}else{
			// jika form yang di isi benar
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			  
			$cek = $this->m_masuk->validate($username, $password);
			if(!empty($cek)){
				$this->session->set_userdata('isLogin', TRUE);
				$this->session->set_userdata('userId', $cek->userId);  
				$this->session->set_userdata('username', $username);  
				$this->session->set_userdata('password', $password);
				
				$result = 1;
				redirect('beranda');
			}else{
				// jika form yang di isi salah
				$result = 1;
				$params = array($result, $this->pesanColorError, $this->pesanMasukError, '');
				$this->session->set_userdata('pesan', $params); 
				redirect('masuk');
			}
		}
	}
  
  function keluar(){
    $this->session->sess_destroy();
    redirect('masuk');
  }
}
