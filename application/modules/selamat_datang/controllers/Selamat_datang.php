<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Selamat_datang extends MX_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('m_selamat_datang');
		// message
		$this->pesanTokenWarning = "Silahkan Isi Token Terlebih Dahulu";
		$this->pesanColorWarning = "warning";
		$this->pesanTokenError = "Token Tidak Sesuai, Silahkan Isi Token Kembali";
		$this->pesanColorError = "error";
	}

	public function index() {
		$data['msg'] = $this->session->userdata('pesan');
		$data['title'] = 'Token';
		$this->load->view('selamat_datang/view_selamat_datang', $data);
	}

	public function validate_token() {
		$this->form_validation->set_rules('token', 'Token', 'required|trim');
		// jika form yang di isi kosong
		if($this->form_validation->run()==FALSE){
			$result = 1;
			$params = array($result, $this->pesanColorWarning, $this->pesanTokenWarning, '');
			$this->session->set_userdata('pesan', $params); 
			redirect('/');
		}else{
			// jika form yang di isi benar
			$token = $this->input->post('token');
			  
			$cek = $this->m_selamat_datang->validate($token);
			if(!empty($cek)){
				$this->session->set_userdata('isToken', TRUE);
				$this->session->set_userdata('tokenId', $cek->tokenId);  
				$this->session->set_userdata('token', $token);  
				
				$result = 1;
				redirect('beranda/view');
			}else{
				// jika form yang di isi salah
				$result = 1;
				$params = array($result, $this->pesanColorError, $this->pesanTokenError, '');
				$this->session->set_userdata('pesan', $params); 
				redirect('/');
			}
		}
	}
}
