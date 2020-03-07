<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pendataan extends MX_Controller {
	protected $jenisKelamin = ['L' => 'Laki-laki', 'P' => 'Perempuan'];

	public function __construct() {
		parent::__construct();
		$this->load->model('anggota/m_anggota');
		// message
		$this->pesanAddSuccess = "Data Berhasil Disimpan, Terima Kasih";
		$this->pesanColorSuccess = "success";
		$this->pesanAddWarning = "Pastikan Data Sudah Terisi Dengan Benar";
		$this->pesanColorWarning = "warning";
	}

	private function data_construct() {
		$this->load->model('pekerjaan/m_pekerjaan');
		$this->load->model('pendidikan/m_pendidikan');
		$this->load->model('wilayah/m_wilayah');

		$data['list_jenis_kelamin'] = $this->jenisKelamin;
		$data['list_pekerjaan'] = $this->m_pekerjaan->getCombo();
		$data['list_pendidikan'] = $this->m_pendidikan->getCombo();
		$data['list_wilayah'] = $this->m_wilayah->getCombo();
		return $data;
	}

	public function index() {
		$data = $this->data_construct();
		$data['msg'] = $this->session->userdata('pesan');
		$data['title'] = 'Pendataan';
		$this->load->view('pendataan/view_pendataan', $data);
	}

	public function addAction() {
		$this->form_validation->set_rules('agtIdWilayah', 'Koordinator Wilayah', 'required');
		$this->form_validation->set_rules('agtNoKta', 'No. KTA', 'required|min_length[2]');
		$this->form_validation->set_rules('agtNama', 'Nama Lengkap', 'required|min_length[2]');
		$this->form_validation->set_rules('agtNmPendek', 'Nama Panggilan', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtTmptLahir', 'Tempat Lahir', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtTglLahir', 'Tanggal Lahir', 'required');
		$this->form_validation->set_rules('agtJnsKelamin', 'Jenis Kelamin', 'required');
		$this->form_validation->set_rules('agtIdPendidikan', 'Pendidikan Terakhir', 'required');
		$this->form_validation->set_rules('agtIdPekerjaan', 'Pekerjaan', 'required');
		$this->form_validation->set_rules('agtKecamatan', 'Kecamatan', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtAlamatJalan', 'Alamat Jalan', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtKdPos', 'Kode Pos', 'required|trim|max_length[5]');
		$this->form_validation->set_rules('agtNoTelp', 'No. Telp/HP', 'required|trim|max_length[13]');
		$this->form_validation->set_rules('agtEmail', 'Alamat Email', 'required|trim|valid_email');
		$this->form_validation->set_rules('agtUkrnKaos', 'Ukuran Kaos', 'required');
		$this->form_validation->set_rules('agtBrlkDari', 'Berlaku Dari', 'required|min_length[2]');
		$this->form_validation->set_rules('agtBrlkSampai', 'Berlaku Sampai', 'required|min_length[2]');
		if (empty($_FILES['agtFoto']['name'])) {
    	$this->form_validation->set_rules('agtFoto', 'Foto Pas (2x3cm)', 'required');
		}

		$agtNoKta = $this->input->post('agtNoKta');
		$validasi_nokta = $this->m_anggota->cekDuplicateNoKta($agtNoKta);
		if(sizeof($validasi_nokta) > 0){
			$result = 1;
			$params = array($result, $this->pesanColorWarning, 'No. KTA Sudah Ada. Silahkan Coba Lagi.', '');
			$this->session->set_userdata('pesan',$params); 
			redirect('pendataan');
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
			'agtStatusKta' => (!empty($this->input->post('agtStatusKta'))) ? $this->input->post('agtStatusKta') : '1',
			'agtBrlkDari' => (!empty($this->input->post('agtBrlkDari'))) ? $this->input->post('agtBrlkDari') : NULL,
			'agtBrlkSampai' => (!empty($this->input->post('agtBrlkSampai'))) ? $this->input->post('agtBrlkSampai') : NULL,
			'agtTglInsert' => date('Y-m-d'),
		];

		if($this->form_validation->run()==FALSE){
			$result = 1;
			$params = array($result, $this->pesanColorWarning, $this->pesanAddWarning, '');
			$this->session->set_userdata('pesan',$params); 
			redirect('pendataan');
		}else{
			$insertId = $this->m_anggota->addDataAction($data);
		}
		
		$add = true;
		if($add){
			$this->db->trans_commit();
		}else{
			$this->db->trans_rollback();
		}

		if($add){
			$result = 1;
			$back = base_url();
			$params = array($result, $this->pesanColorSuccess, $this->pesanAddSuccess, "window.location='$back'");
			$this->session->set_userdata('pesan', $params);
			redirect('pendataan');
		}else{
			redirect('pendataan');
		}
	}
}
