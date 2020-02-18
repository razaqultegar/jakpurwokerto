<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MX_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('m_login');
		// message
		$this->pesanTokenWarning = "Username dan Password Tidak Boleh Kosong";
		$this->pesanColorWarning = "warning";
		$this->pesanTokenError = "Login Gagal, Username atau Password Tidak Sesuai";
		$this->pesanColorError = "error";
	}

	public function index() {
		$data['msg'] = $this->session->userdata('pesan');
		$data['title'] = 'Login';
		$this->load->view('login/view_login', $data);
	}

	public function validate_login() {
		$this->form_validation->set_rules('username', 'Username', 'required|trim');
		$this->form_validation->set_rules('password', 'Password', 'required|md5');
		// jika form yang di isi kosong
		if($this->form_validation->run()==FALSE){
			$result = 1;
			$params = array($result, $this->pesanColorWarning, $this->pesanTokenWarning, '');
			$this->session->set_userdata('pesan', $params); 
			redirect('login');
		}else{
			// jika form yang di isi benar
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			  
			$cek = $this->m_login->validate($username, $password);
			if(!empty($cek)){
				$this->session->set_userdata('isLogin', TRUE);
				$this->session->set_userdata('userId', $cek->userId);  
				$this->session->set_userdata('username', $username);  
				$this->session->set_userdata('password', $password);
				
				$result = 1;
				redirect('beranda/view');
			}else{
				// jika form yang di isi salah
				$result = 1;
				$params = array($result, $this->pesanColorError, $this->pesanTokenError, '');
				$this->session->set_userdata('pesan', $params); 
				redirect('login');
			}
		}
  }
  
  function logout(){
    $this->session->sess_destroy();
    redirect('login');
  }
}
