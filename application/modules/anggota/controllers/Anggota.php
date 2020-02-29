<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Anggota extends MY_Controller {
	protected $jenisKelamin = ['L' => 'Laki-laki', 'P' => 'Perempuan'];

	public function __construct() {
		parent::__construct();
		$this->load->model('m_anggota');
		$this->pesanAddSuccess = "Data Berhasil Ditambahkan";
		$this->pesanAddError = "Data Tidak Berhasil Ditambahkan";
		$this->pesanEditSuccess = "Data Berhasil Diubah";
		$this->pesanEditError = "Data Tidak Berhasil Diubah";
		$this->pesanDeleteSuccess = "Data Berhasil Dihapus";
		$this->pesanDeleteError = "Data Tidak Berhasil Dihapus";
		$this->pesanIconSuccess = "success";
		$this->pesanIconError = "danger";
	}

	private function data_construct(){
		$this->load->model('ref_pendidikan/m_ref_pendidikan');
		$this->load->model('ref_pekerjaan/m_ref_pekerjaan');

		$data['list_pendidikan'] = $this->m_ref_pendidikan->getCombo();
		$data['list_pekerjaan'] = $this->m_ref_pekerjaan->getCombo();
		$data['list_jenis_kelamin'] = $this->jenisKelamin;
		$data['msg'] = $this->session->flashdata('msg');
		return $data;
	}

	public function index() {
		$msg = $this->session->userdata('pesan');
		$data = $this->data_construct();
		$data['msg'] = (!empty($msg)) ? $msg : ['', '', ''];
		$data['title'] = 'Data Anggota';
		$data['url_get_json'] = site_url('anggota/get_data_json');
		$data['url_add'] = site_url('anggota/add');
		$this->layout->set_layout('anggota/view_anggota', $data);
	}

	public function get_data_json(){
		ob_start();
		$data = array();
		$requestData= $_REQUEST;
		$order = $this->input->post('order');
		$columns = $this->input->post('columns');
		$options['order'] = !empty($order) && !empty($columns) ? $columns[$order[0]['column']]['data'] : 'agtLastUpdate';

		$options['mode'] = !empty($order) ? $order[0]['dir']: 'desc';
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$options['offset'] = empty($start) ? 0 : $start;
		$options['limit'] = empty($length) ? 10 : $length;
		$where_like = array();

		if (!empty($requestData['search']['value'])){
				$options['where_like'] = array(
				"agtNama LIKE '%".$requestData['search']['value']."%'
				OR noKta LIKE '%".$requestData['search']['value']."%'
				OR wilNama LIkE '%".$requestData['search']['value']."%'"
			);
		}

		if (!empty($requestData['agtJnsKelamin'])){
			$options['where_like'][] = "agtJnsKelamin = '".$requestData['agtJnsKelamin']."'";
		}

		if (!empty($requestData['agtIdPendidikan'])){
			$options['where_like'][] = "agtIdPendidikan = '".$requestData['agtIdPendidikan']."'";
		}

		if (!empty($requestData['agtIdPekerjaan'])){
			$options['where_like'][] = "agtIdPekerjaan = '".$requestData['agtIdPekerjaan']."'";
		}

		if (!empty($requestData['filter_pekerjaan'])){
			$options['where_like'][] = "pkjNama = '".$requestData['filter_pekerjaan']."'";
		}

		if (!empty($requestData['filter_jk'])){
			$plod = explode('@@@', $requestData['filter_jk']);
			$jk = ($plod[0] == 'Perempuan') ? 'P' : 'L';
			$bln = $this->monthFormat($plod[1]);
			$thn = $plod[2];
			$options['where_like'][] = "agtJnsKelamin = '".$jk."' AND MONTH(agtTglInsert) = '".$bln."' AND YEAR(agtTglInsert) = '".$thn."'";
		}

		$options['condition'] = $this->db_condition;
		$dataOutput = $this->m_anggota->getListData($options);
		$totalFiltered = $this->m_anggota->getTotalData($options);
		$totalData = $this->m_anggota->getTotal($this->db_condition);
		$no = $options['offset'] + 1;

		if (!empty($dataOutput)){
			foreach ($dataOutput as $key => $value) {
				$value->no = $no;
				$dataNavbar ="";
				if (count($dataOutput) > 3 && $key >= (count($dataOutput) - 2)){
					$dataNavbar = ", pos:'top-left'";
				}
				$value->noKta = (!empty($value->noKta)) ? $value->noKta : "-";
				$value->aksi = '<a href="'.site_url('anggota/edit/'.$value->noKta).'" class="btn btn-warning btn-circle btn-sm" title="Edit Data"><i class="fas fa-pencil-alt"></i></a> <a href="'.site_url('anggota/delete/'.$value->noKta).'" class="btn btn-danger btn-circle btn-sm" title="Hapus Data"><i class="fas fa-trash"></i></a>';
				$value->agtNama = '<a href="'.site_url('anggota/detil/'.$value->noKta).'" title="Detil Data" class="ajax dest-page_content_inner">'.strtoupper($value->agtNama).'</a>';
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

	public function detil(){
		$id = $this->uri->segment(3);
		$data = $this->m_anggota->getDataById($id);
		$data = array_merge($data, $this->data_construct());
		$data['title'] = 'Detil Anggota';

		if (empty($data)){
			$params = array('danger', $this->pesanIconError, 'Maaf, Data Anggota Tidak Ditemukan');
			$this->session->set_flashdata('pesan',$params);
			redirect('anggota');
			return;
		}

		$this->layout->set_layout('anggota/detil_anggota', $data);
	}
}
