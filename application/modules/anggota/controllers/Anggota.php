<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Anggota extends MY_Controller {
	protected $jenisKelamin = ['L' => 'Laki-laki', 'P' => 'Perempuan'];
	protected $statuskta = ['0' => 'Belum', '1' => 'Sudah'];

	public function __construct() {
		parent::__construct();
		$this->load->model('anggota/m_anggota');
		$this->pesanAddSuccess = "Data Berhasil Disimpan";
		$this->pesanDeleteSuccess = "Data Berhasil Dihapus";
		$this->pesanColorSuccess = "success";
	}

	private function data_construct() {
		$this->load->model('ref_pendidikan/m_ref_pendidikan');
		$this->load->model('ref_pekerjaan/m_ref_pekerjaan');
		$this->load->model('ref_wilayah/m_ref_wilayah');

		$data['list_pendidikan'] = $this->m_ref_pendidikan->getCombo();
		$data['list_pekerjaan'] = $this->m_ref_pekerjaan->getCombo();
		$data['list_wilayah'] = $this->m_ref_wilayah->getCombo();
		$data['list_jenis_kelamin'] = $this->jenisKelamin;
		$data['list_status_kta'] = $this->statuskta;
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

	public function get_data_json() {
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
				OR agtNoKta LIKE '%".$requestData['search']['value']."%'
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
				$value->agtNoKta = (!empty($value->agtNoKta)) ? $value->agtNoKta : "-";
				$value->aksi = '<a href="'.site_url('anggota/edit/'.$value->agtId).'" class="btn btn-warning btn-circle btn-sm" title="Edit Data"><i class="fas fa-pencil-alt"></i></a> <a href="'.site_url('anggota/delete/'.$value->agtId).'" class="btn btn-danger btn-circle btn-sm" title="Hapus Data"><i class="fas fa-trash"></i></a>';
				$value->agtNama = '<a href="'.site_url('anggota/detil/'.$value->agtId).'" title="Detil Data">'.strtoupper($value->agtNama).'</a>';
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
		$data['title'] = 'Tambah Data Anggota';
		$this->layout->set_layout('anggota/add_anggota', $data);
	}

	public function addAction() {
		$this->form_validation->set_rules('agtIdWilayah', 'Koordinator Wilayah', 'required');
		$this->form_validation->set_rules('agtNama', 'Nama Lengkap', 'required|min_length[2]');
		$this->form_validation->set_rules('agtNmPendek', 'Nama Panggilan', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtTmptLahir', 'Tempat Lahir', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtTglLahir', 'Tanggal Lahir', 'required');
		$this->form_validation->set_rules('agtJnsKelamin', 'Jenis Kelamin', 'required');
		$this->form_validation->set_rules('agtIdPendidikan', 'Pendidikan Terakhir', 'required');
		$this->form_validation->set_rules('agtIdPekerjaan', 'Pekerjaan', 'required');
		$this->form_validation->set_rules('agtKecamatan', 'Kecamatan', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtAlamatJalan', 'Alamat Lengkap', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtKdPos', 'Kode Pos', 'required|trim|max_length[5]');
		$this->form_validation->set_rules('agtStatusKta', 'Status KTA', 'required');
		$this->form_validation->set_rules('agtNoTelp', 'No. Telp/HP', 'required|trim|max_length[14]');
		$this->form_validation->set_rules('agtEmail', 'Alamat Email', 'required|trim|valid_email');
		$this->form_validation->set_rules('agtUkrnKaos', 'Ukuran Kaos', 'required');

		if($this->form_validation->run()==FALSE){
			$this->session->set_flashdata('msg', array('1', 'warning', 'Lengkapi data terlebih dulu'));
			redirect('anggota/add');
		}

		if (!empty($_FILES['agtFoto']['name'])){
			$file=$_FILES['agtFoto']['name'];
			$tmp_file=$_FILES['agtFoto']['tmp_name'];
			$path = FCPATH.'files/anggota/';
			$random_name= date('dmysi');
			$explode = explode('.',$file);
			$extensi = $explode[count($explode)-1];
			$file_name = $random_name.".".$extensi;
			$upload = move_uploaded_file ($tmp_file, $path.$file_name);
		}else{
			$file_name = $this->input->post('agtFoto');
			$upload = "";
		}

		$tgllhr = explode("/", $_POST['agtTglLahir']);
		$tgllhr2 = explode("-", $_POST['agtTglLahir']);
		if($tgllhr){
			$tgl =  $tgllhr[2]."-".$tgllhr[1]."-".$tgllhr[0];
		}elseif($tgllhr2){
			$tgl =  $tgllhr2[0]."-".$tgllhr2[1]."-".$tgllhr2[2];
		}else{
			$tgl = NULL;
		}

		$biday = new DateTime($tgl);
		$today = new DateTime();
		$umur = $today->diff($biday)->y;

		$data = [
			'agtNoKta' => (!empty($this->input->post('agtNoKta'))) ? $this->input->post('agtNoKta') : NULL,
			'agtIdWilayah' => (!empty($this->input->post('agtIdWilayah'))) ? $this->input->post('agtIdWilayah') : NULL,
			'agtNama' => (!empty($this->input->post('agtNama'))) ? $this->input->post('agtNama') : NULL,
			'agtNmPendek' => (!empty($this->input->post('agtNmPendek'))) ? $this->input->post('agtNmPendek') : NULL,
			'agtTmptLahir' => (!empty($this->input->post('agtTmptLahir'))) ? $this->input->post('agtTmptLahir') : NULL,
			'agtTglLahir' => $tgl,
			'agtUmur' => $umur,
			'agtJnsKelamin' => (!empty($this->input->post('agtJnsKelamin'))) ? $this->input->post('agtJnsKelamin') : NULL,
			'agtIdPendidikan' => (!empty($this->input->post('agtIdPendidikan'))) ? $this->input->post('agtIdPendidikan') : NULL,
			'agtIdPekerjaan' => (!empty($this->input->post('agtIdPekerjaan'))) ? $this->input->post('agtIdPekerjaan') : NULL,
			'agtKelurahan' => (!empty($this->input->post('agtKelurahan'))) ? $this->input->post('agtKelurahan') : NULL,
			'agtKecamatan' => (!empty($this->input->post('agtKecamatan'))) ? $this->input->post('agtKecamatan') : NULL,
			'agtAlamatJalan' => (!empty($this->input->post('agtAlamatJalan'))) ? $this->input->post('agtAlamatJalan') : NULL,
			'agtKdPos' => (!empty($this->input->post('agtKdPos'))) ? $this->input->post('agtKdPos') : NULL,
			'agtNoTelp' => (!empty($this->input->post('agtNoTelp'))) ? $this->input->post('agtNoTelp') : NULL,
			'agtEmail' => (!empty($this->input->post('agtEmail'))) ? $this->input->post('agtEmail') : NULL,
			'agtUkrnKaos' => (!empty($this->input->post('agtUkrnKaos'))) ? $this->input->post('agtUkrnKaos') : NULL,
			'agtFoto' => $file_name,
			'agtStatusKta' => (!empty($this->input->post('agtStatusKta'))) ? $this->input->post('agtStatusKta') : '0',
			'agtBrlkDari' => (!empty($this->input->post('agtBrlkDari'))) ? $this->input->post('agtBrlkDari') : NULL,
			'agtBrlkSampai' => (!empty($this->input->post('agtBrlkSampai'))) ? $this->input->post('agtBrlkSampai') : NULL,
			'agtTglInsert' => date('Y-m-d'),
		];

		$insertId = $this->m_anggota->addDataAction($data);

		$add = true;
		if($add){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}

		if($add){
			$params = array($add, $this->pesanColorSuccess, $this->pesanAddSuccess);
			$this->session->set_userdata('pesan', $params);
			redirect('anggota');
		}else{
			redirect('anggota/add');
		}
	}

	public function detil() {
		$id = $this->uri->segment(3);
		$data = $this->m_anggota->getDataById($id);
		$data = array_merge($data, $this->data_construct());
		$data['title'] = 'Detil Data Anggota';
		$this->layout->set_layout('anggota/detil_anggota', $data);
	}

	public function edit() {
		$id = $this->uri->segment(3);
		$data = $this->m_anggota->getDataById($id);
		$data = array_merge($data, $this->data_construct());
		$data['title'] = "Ubah Data Anggota";
		$msg = $this->session->flashdata('pesan');
		$data['msg'] = (!empty($msg)) ? $msg : ['', '', ''];
		$this->layout->set_layout('anggota/edit_anggota', $data);
	}

	public function editAction() {
		$this->form_validation->set_rules('agtIdWilayah', 'Koordinator Wilayah', 'required');
		$this->form_validation->set_rules('agtNama', 'Nama Lengkap', 'required|min_length[2]');
		$this->form_validation->set_rules('agtNmPendek', 'Nama Panggilan', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtTmptLahir', 'Tempat Lahir', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtTglLahir', 'Tanggal Lahir', 'required');
		$this->form_validation->set_rules('agtJnsKelamin', 'Jenis Kelamin', 'required');
		$this->form_validation->set_rules('agtIdPendidikan', 'Pendidikan Terakhir', 'required');
		$this->form_validation->set_rules('agtIdPekerjaan', 'Pekerjaan', 'required');
		$this->form_validation->set_rules('agtKecamatan', 'Kecamatan', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtAlamatJalan', 'Alamat Lengkap', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtKdPos', 'Kode Pos', 'required|trim|max_length[5]');
		$this->form_validation->set_rules('agtStatusKta', 'Status KTA', 'required');
		$this->form_validation->set_rules('agtNoTelp', 'No. Telp/HP', 'required|trim|max_length[14]');
		$this->form_validation->set_rules('agtEmail', 'Alamat Email', 'required|trim|valid_email');
		$this->form_validation->set_rules('agtUkrnKaos', 'Ukuran Kaos', 'required');

		if($this->form_validation->run()==FALSE){
			$params = array('1', 'danger', 'Data Tidak Berhasil Disimpan');
			$this->session->set_userdata('pesan', $params);
			redirect('anggota');
		}

		if (!empty($_FILES['agtFoto']['name'])){
			$file=$_FILES['agtFoto']['name'];
			$tmp_file=$_FILES['agtFoto']['tmp_name'];
			$path = FCPATH.'files/anggota/';
			unlink($path.$this->input->post('agtFoto'));
			$random_name= date('dmysi');
			$explode = explode('.',$file);
			$extensi = $explode[count($explode)-1];
			$file_name = $random_name.".".$extensi;
			$upload = move_uploaded_file ($tmp_file, $path.$file_name);
		}else{
			$file_name = $this->input->post('agtFoto');
			$upload = "";
		}

		$tgllhr = explode("/", $_POST['agtTglLahir']);
		$tgllhr2 = explode("-", $_POST['agtTglLahir']);
		if($tgllhr){
			$tgl =  $tgllhr[2]."-".$tgllhr[1]."-".$tgllhr[0];
		}elseif($tgllhr2){
			$tgl =  $tgllhr2[0]."-".$tgllhr2[1]."-".$tgllhr2[2];
		}else{
			$tgl = NULL;
		}

		$biday = new DateTime($tgl);
		$today = new DateTime();
		$umur = $today->diff($biday)->y;

		$data = [
			'agtNoKta' => (!empty($this->input->post('agtNoKta'))) ? $this->input->post('agtNoKta') : NULL,
			'agtIdWilayah' => (!empty($this->input->post('agtIdWilayah'))) ? $this->input->post('agtIdWilayah') : NULL,
			'agtNama' => (!empty($this->input->post('agtNama'))) ? $this->input->post('agtNama') : NULL,
			'agtNmPendek' => (!empty($this->input->post('agtNmPendek'))) ? $this->input->post('agtNmPendek') : NULL,
			'agtTmptLahir' => (!empty($this->input->post('agtTmptLahir'))) ? $this->input->post('agtTmptLahir') : NULL,
			'agtTglLahir' => $tgl,
			'agtUmur' => $umur,
			'agtJnsKelamin' => (!empty($this->input->post('agtJnsKelamin'))) ? $this->input->post('agtJnsKelamin') : NULL,
			'agtIdPendidikan' => (!empty($this->input->post('agtIdPendidikan'))) ? $this->input->post('agtIdPendidikan') : NULL,
			'agtIdPekerjaan' => (!empty($this->input->post('agtIdPekerjaan'))) ? $this->input->post('agtIdPekerjaan') : NULL,
			'agtKelurahan' => (!empty($this->input->post('agtKelurahan'))) ? $this->input->post('agtKelurahan') : NULL,
			'agtKecamatan' => (!empty($this->input->post('agtKecamatan'))) ? $this->input->post('agtKecamatan') : NULL,
			'agtAlamatJalan' => (!empty($this->input->post('agtAlamatJalan'))) ? $this->input->post('agtAlamatJalan') : NULL,
			'agtKdPos' => (!empty($this->input->post('agtKdPos'))) ? $this->input->post('agtKdPos') : NULL,
			'agtNoTelp' => (!empty($this->input->post('agtNoTelp'))) ? $this->input->post('agtNoTelp') : NULL,
			'agtEmail' => (!empty($this->input->post('agtEmail'))) ? $this->input->post('agtEmail') : NULL,
			'agtUkrnKaos' => (!empty($this->input->post('agtUkrnKaos'))) ? $this->input->post('agtUkrnKaos') : NULL,
			'agtFoto' => $file_name,
			'agtStatusKta' => (!empty($this->input->post('agtStatusKta'))) ? $this->input->post('agtStatusKta') : '0',
			'agtBrlkDari' => (!empty($this->input->post('agtBrlkDari'))) ? $this->input->post('agtBrlkDari') : NULL,
			'agtBrlkSampai' => (!empty($this->input->post('agtBrlkSampai'))) ? $this->input->post('agtBrlkSampai') : NULL,
			'agtTglInsert' => date('Y-m-d'),
		];

		$updateId = $this->m_anggota->editDataAction($data, ['agtId' => $this->input->post('agtId')]);

		$update = true;
		if($update){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}

		if($update){
			$params = array($update, $this->pesanColorSuccess, $this->pesanAddSuccess);
			$this->session->set_userdata('pesan', $params);
			redirect('anggota');
		}else{
			$params = array($update, 'danger', 'Data Tidak Berhasil Disimpan');
			$this->session->set_userdata('pesan', $params);
			redirect('anggota');
		}
	}

	public function delete($id){
		$data = $this->m_anggota->deleteImage($id)->row();
		unlink("./files/anggota/$data->agtFoto");

		$delete = $this->m_anggota->delete(['agtId' => $id]);
		if($delete){
			$params = array($delete, $this->pesanColorSuccess, $this->pesanDeleteSuccess);
			$this->session->set_userdata('pesan', $params);
			redirect('anggota');
		}else{
			redirect('anggota');
		}
	}
}
