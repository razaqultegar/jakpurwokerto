<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengguna extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('pengguna/m_pengguna');
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
		$data['title'] = 'Data Pengguna';
		$data['url_get_json'] = site_url('pengguna/get_data_json');
		$data['url_add'] = site_url('pengguna/add');
		$this->layout->set_layout('pengguna/view_pengguna', $data);
	}

	public function get_data_json() {
		ob_start();
		$data = array();
		$requestData= $_REQUEST;
		$order = $this->input->post('order');
		$columns = $this->input->post('columns');
		$options['order'] = !empty($order) && !empty($columns) ? $columns[$order[0]['column']]['data'] : 'realname';

		$options['mode'] = !empty($order) ? $order[0]['dir']: 'asc';
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$options['offset'] = empty($start) ? 0 : $start;
		$options['limit'] = empty($length) ? 10 : $length;
		$where_like = array();

		if (!empty($requestData['search']['value'])){
				$options['where_like'] = array(
				"realname LIKE '%".$requestData['search']['value']."%'
				OR username LIKE '%".$requestData['search']['value']."%'"
			);
		}

		$options['condition'] = $this->db_condition;
		$dataOutput = $this->m_pengguna->getListData($options);
		$totalFiltered = $this->m_pengguna->getTotalData($options);
		$totalData = $this->m_pengguna->getTotal($this->db_condition);
		$no = $options['offset'] + 1;

		if (!empty($dataOutput)){
			foreach ($dataOutput as $key => $value) {
				$value->no = $no;
				$dataNavbar ="";
				if (count($dataOutput) > 3 && $key >= (count($dataOutput) - 2)){
					$dataNavbar = ", pos:'top-left'";
				}
				$value->aksi = '<a href="'.site_url('pengguna/edit/'.$value->userId).'" class="btn btn-warning btn-circle btn-sm" title="Edit Data"><i class="fas fa-pencil-alt"></i></a> <a href="'.site_url('pengguna/delete/'.$value->userId).'" class="btn btn-danger btn-circle btn-sm" title="Hapus Data"><i class="fas fa-trash"></i></a>';
				$no++;
			}
		}

		$response = array(
			"draw"            => isset($requestData['draw']) ? intval( $requestData['draw'] ) : 0,
			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => $dataOutput
		);
		echo json_encode($response);
	}

	public function add() {
		$msg = $this->session->userdata('pesan');
		$data['msg'] = (!empty($msg)) ? $msg : ['', '', ''];
		$post = $this->session->flashdata('post');
		$data = array_merge($data, $this->data_construct());
		$data['title'] = 'Tambah Data Pengguna';
		$this->layout->set_layout('pengguna/add_pengguna', $data);
	}

	public function addAction() {
		$this->form_validation->set_rules('realname', 'Nama Pengguna', 'required|min_length[2]');
		$this->form_validation->set_rules('username', 'Username', 'required|min_length[2]');
		$this->form_validation->set_rules('password', 'Kata Sandi', 'required|min_length[2]');

		if($this->form_validation->run()==FALSE){
			$this->session->set_flashdata('msg', array('1', 'warning', 'Lengkapi data terlebih dulu'));
			redirect('pengguna/add');
		}

		$data = [
			'realname' => (!empty($this->input->post('realname'))) ? $this->input->post('realname') : NULL,
			'username' => (!empty($this->input->post('username'))) ? $this->input->post('username') : NULL,
			'password' => (!empty($this->input->post('password'))) ? $this->input->post('password') : NULL,
		];

		$insertId = $this->m_pengguna->addDataAction($data);

		$add = true;
		if($add){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}

		if($add){
			$params = array($add, $this->pesanColorSuccess, $this->pesanAddSuccess);
			$this->session->set_userdata('pesan', $params);
			redirect('pengguna');
		}else{
			$params = array($add, 'danger', 'Data Tidak Berhasil Disimpan');
			$this->session->set_userdata('pesan', $params);
			redirect('pengguna/add');
		}
	}

	public function edit() {
		$id = $this->uri->segment(3);
		$data = $this->m_pengguna->getDataByIdd($id);
		$data = array_merge($data, $this->data_construct());
		$data['title'] = "Ubah Data Pengguna";
		$msg = $this->session->flashdata('pesan');
		$data['msg'] = (!empty($msg)) ? $msg : ['', '', ''];
		$this->layout->set_layout('pengguna/edit_pengguna', $data);
	}

	public function editAction() {
		$this->form_validation->set_rules('realname', 'Nama Pengguna', 'required|min_length[2]');
		$this->form_validation->set_rules('username', 'Username', 'required|min_length[2]');
		$this->form_validation->set_rules('password', 'Kata Sandi', 'required|min_length[2]');

		if($this->form_validation->run()==FALSE){
			$params = array('1', 'danger', 'Data Tidak Berhasil Disimpan');
			$this->session->set_userdata('pesan', $params);
			redirect('pengguna');
		}

		$data = [
			'realname' => (!empty($this->input->post('realname'))) ? $this->input->post('realname') : NULL,
			'username' => (!empty($this->input->post('username'))) ? $this->input->post('username') : NULL,
			'password' => (!empty($this->input->post('password'))) ? $this->input->post('password') : NULL,
		];

		$updateId = $this->m_pengguna->editDataAction($data, ['userId' => $this->input->post('userId')]);

		$update = true;
		if($update){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}

		if($update){
			$params = array($update, $this->pesanColorSuccess, $this->pesanAddSuccess);
			$this->session->set_userdata('pesan', $params);
			redirect('pengguna');
		}else{
			$params = array($update, 'danger', 'Data Tidak Berhasil Disimpan');
			$this->session->set_userdata('pesan', $params);
			redirect('pengguna');
		}
	}

	public function delete($id){
		$delete = $this->m_pengguna->delete(['userId' => $id]);
		if($delete){
			$params = array($delete, $this->pesanColorSuccess, $this->pesanDeleteSuccess);
			$this->session->set_userdata('pesan', $params);
			redirect('pengguna');
		}else{
			redirect('pengguna');
		}
	}
}
