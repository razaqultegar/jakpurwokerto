<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_wilayah extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('ref_wilayah/m_ref_wilayah');
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
		$data['url_get_json'] = site_url('ref_wilayah/get_data_json');
		$data['url_add'] = site_url('ref_wilayah/add');
		$this->layout->set_layout('ref_wilayah/view_ref_wilayah', $data);
	}

	public function get_data_json() {
		ob_start();
		$data = array();
		$requestData= $_REQUEST;
		$order = $this->input->post('order');
		$columns = $this->input->post('columns');
		$options['order'] = !empty($order) && !empty($columns) ? $columns[$order[0]['column']]['data'] : 'wilNama';

		$options['mode'] = !empty($order) ? $order[0]['dir']: 'asc';
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$options['offset'] = empty($start) ? 0 : $start;
		$options['limit'] = empty($length) ? 10 : $length;
		$where_like = array();

		if (!empty($requestData['search']['value'])){
				$options['where_like'] = array(
				"wilNama LIKE '%".$requestData['search']['value']."%'"
			);
		}

		$options['condition'] = $this->db_condition;
		$dataOutput = $this->m_ref_wilayah->getListData($options);
		$totalFiltered = $this->m_ref_wilayah->getTotalData($options);
		$totalData = $this->m_ref_wilayah->getTotal($this->db_condition);
		$no = $options['offset'] + 1;

		if (!empty($dataOutput)){
			foreach ($dataOutput as $key => $value) {
				$value->no = $no;
				$dataNavbar ="";
				if (count($dataOutput) > 3 && $key >= (count($dataOutput) - 2)){
					$dataNavbar = ", pos:'top-left'";
				}
				$value->aksi = '<a href="'.site_url('ref_wilayah/edit/'.$value->wilIdWilayah).'" class="btn btn-warning btn-circle btn-sm" title="Edit Data"><i class="fas fa-pencil-alt"></i></a> <a href="'.site_url('ref_wilayah/delete/'.$value->wilIdWilayah).'" class="btn btn-danger btn-circle btn-sm" title="Hapus Data"><i class="fas fa-trash"></i></a>';
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
		$data['title'] = 'Tambah Data Wilayah';
		$this->layout->set_layout('ref_wilayah/add_ref_wilayah', $data);
	}

	public function addAction() {
		$this->form_validation->set_rules('wilNama', 'Nama Wilayah', 'required|min_length[2]');

		if($this->form_validation->run()==FALSE){
			$this->session->set_flashdata('msg', array('1', 'warning', 'Lengkapi data terlebih dulu'));
			redirect('ref_wilayah/add');
		}

		$data = [
			'wilNama' => (!empty($this->input->post('wilNama'))) ? $this->input->post('wilNama') : NULL,
		];

		$insertId = $this->m_ref_wilayah->addDataAction($data);

		$add = true;
		if($add){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}

		if($add){
			$params = array($add, $this->pesanColorSuccess, $this->pesanAddSuccess);
			$this->session->set_userdata('pesan', $params);
			redirect('ref_wilayah');
		}else{
			$params = array($add, 'danger', 'Data Tidak Berhasil Disimpan');
			$this->session->set_userdata('pesan', $params);
			redirect('ref_wilayah/add');
		}
	}

	public function edit() {
		$id = $this->uri->segment(3);
		$data = $this->m_ref_wilayah->getDataById($id);
		$data = array_merge($data, $this->data_construct());
		$data['title'] = "Ubah Data Wilayah";
		$msg = $this->session->flashdata('pesan');
		$data['msg'] = (!empty($msg)) ? $msg : ['', '', ''];
		$this->layout->set_layout('ref_wilayah/edit_ref_wilayah', $data);
	}

	public function editAction() {
		$this->form_validation->set_rules('wilNama', 'Nama Wilayah', 'required|min_length[2]');

		if($this->form_validation->run()==FALSE){
			$params = array('1', 'danger', 'Data Tidak Berhasil Disimpan');
			$this->session->set_userdata('pesan', $params);
			redirect('ref_wilayah');
		}

		$data = [
			'wilNama' => (!empty($this->input->post('wilNama'))) ? $this->input->post('wilNama') : NULL,
		];

		$updateId = $this->m_ref_wilayah->editDataAction($data, ['wilIdWilayah' => $this->input->post('wilIdWilayah')]);

		$update = true;
		if($update){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}

		if($update){
			$params = array($update, $this->pesanColorSuccess, $this->pesanAddSuccess);
			$this->session->set_userdata('pesan', $params);
			redirect('ref_wilayah');
		}else{
			$params = array($update, 'danger', 'Data Tidak Berhasil Disimpan');
			$this->session->set_userdata('pesan', $params);
			redirect('ref_wilayah');
		}
	}

	public function delete($id){
		$delete = $this->m_ref_wilayah->delete(['wilIdWilayah' => $id]);
		if($delete){
			$params = array($delete, $this->pesanColorSuccess, $this->pesanDeleteSuccess);
			$this->session->set_userdata('pesan', $params);
			redirect('ref_wilayah');
		}else{
			redirect('ref_wilayah');
		}
	}
}
