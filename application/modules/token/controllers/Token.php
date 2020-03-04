<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Token extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('token/m_token');
		$this->pesanAddSuccess = "Data Berhasil Disimpan";
		$this->pesanDeleteSuccess = "Data Berhasil Dihapus";
		$this->pesanColorSuccess = "success";
	}

	private function data_construct() {
		$data['msg'] = $this->session->flashdata('msg');
		return $data;
	}

	public function index() {
		$msg = $this->session->userdata('pesan');
		$data = $this->data_construct();
		$data['msg'] = (!empty($msg)) ? $msg : ['', '', ''];
		$data['title'] = 'Data Wilayah';
		$data['token'] = $this->m_token->getToken()->result();
		$this->layout->set_layout('token/view_token', $data);
	}

	public function addAction() {
		$this->form_validation->set_rules('token', 'Token', 'required|min_length[2]');

		if($this->form_validation->run()==FALSE){
			$this->session->set_flashdata('msg', array('1', 'warning', 'Lengkapi data terlebih dulu'));
			redirect('token');
		}

		$data = [
			'token' => (!empty($this->input->post('token'))) ? $this->input->post('token') : NULL,
		];

		$insertId = $this->m_token->addDataAction($data);

		$add = true;
		if($add){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}

		if($add){
			$params = array($add, $this->pesanColorSuccess, $this->pesanAddSuccess);
			$this->session->set_userdata('pesan', $params);
			redirect('token');
		}else{
			$params = array($add, 'danger', 'Data Tidak Berhasil Disimpan');
			$this->session->set_userdata('pesan', $params);
			redirect('token');
		}
	}

	public function delete($id){
		$delete = $this->m_token->delete(['tokenId' => $id]);
		if($delete){
			$params = array($delete, $this->pesanColorSuccess, $this->pesanDeleteSuccess);
			$this->session->set_userdata('pesan', $params);
			redirect('token');
		}else{
			redirect('token');
		}
	}
}
