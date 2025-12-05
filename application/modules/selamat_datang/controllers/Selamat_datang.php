<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Selamat_datang extends MX_Controller {
	public function __construct() {
		parent::__construct();

		// load model
		$this->load->model('selamat_datang/m_selamat_datang');

		// message
		$this->pesanTokenWarning = "Pastikan Jenis Pengisian dan Token Sudah Sesuai";
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
		$this->form_validation->set_rules('jenis', 'Jenis', 'required');
		$this->form_validation->set_rules('token', 'Token', 'required|trim');

		// jika form yang di isi kosong
		if ($this->form_validation->run() == FALSE) {
			$result = 1;
			$params = array($result, $this->pesanColorWarning, $this->pesanTokenWarning, '');
			$this->session->set_userdata('pesan', $params); 
			redirect('/');
		} else {
			// jika form yang di isi benar
			$jenis = $this->input->post('jenis');
			$token = $this->input->post('token');
			$cek = $this->m_selamat_datang->validate($token);

			if (!empty($cek)) {
				$this->session->set_userdata('isToken', TRUE);
				$this->session->set_userdata('tokenId', $cek->tokenId);  
				$this->session->set_userdata('token', $token);  

				// arahkan ke halaman sesuai jenis pengisian
				if ($jenis == 'pendaftaran') {
					redirect('pendaftaran');
				} else if ($jenis == 'pendataan') {
					redirect('pendataan');
				} else {
					redirect('perpanjangan');
				}
			} else {
				// jika form yang di isi salah
				$result = 1;
				$params = array($result, $this->pesanColorError, $this->pesanTokenError, '');
				$this->session->set_userdata('pesan', $params); 
				redirect('/');
			}
		}
	}
}
