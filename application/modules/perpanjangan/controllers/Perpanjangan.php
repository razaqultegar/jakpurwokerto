<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Perpanjangan extends MX_Controller {
	protected $jenisKelamin = ['L' => 'Laki-laki', 'P' => 'Perempuan'];

	public function __construct() {
		parent::__construct();

		// load model
		$this->load->model('anggota/m_anggota');

		// message
		$this->pesanAddSuccess = "Data Berhasil Disimpan, Terima Kasih";
		$this->pesanColorSuccess = "success";
		$this->pesanAddWarning = "Pastikan Data Sudah Terisi Dengan Benar";
		$this->pesanColorWarning = "warning";
	}

	private function data_construct() {
		$this->load->model('wilayah/m_wilayah');

		$data = array();
		$data['list_jenis_kelamin'] = $this->jenisKelamin;
		$data['list_wilayah'] = $this->m_wilayah->getCombo();

		return $data;
	}

	public function index() {
		$data = $this->data_construct();
		$data['msg'] = $this->session->userdata('pesan');
		$data['title'] = 'Perpanjangan';

		$this->load->view('perpanjangan/view_perpanjangan', $data);
	}

	public function addAction() {
		$this->form_validation->set_rules('agtNik', 'Nomor Induk Kependudukan', 'required|min_length[16]|max_length[16]');
		$this->form_validation->set_rules('agtNoKTA', 'Nomor KTA Sebelumnya', 'min_length[6]|max_length[20]');
		$this->form_validation->set_rules('agtNama', 'Nama Lengkap', 'required|min_length[3]');
		$this->form_validation->set_rules('agtJnsKelamin', 'Jenis Kelamin', 'required');
		$this->form_validation->set_rules('agtTmptLahir', 'Tempat Lahir', 'required|trim|min_length[3]');
		$this->form_validation->set_rules('agtTglLahir', 'Tanggal Lahir', 'required');
		$this->form_validation->set_rules('agtProvinsi', 'Provinsi', 'required');
		$this->form_validation->set_rules('agtKabupaten', 'Kabupaten', 'required');
		$this->form_validation->set_rules('agtKecamatan', 'Kecamatan', 'required');
		$this->form_validation->set_rules('agtKelurahan', 'Kelurahan', 'required');
		$this->form_validation->set_rules('agtAlamatJalan', 'Alamat Jalan', 'trim|min_length[10]');
		$this->form_validation->set_rules('agtEmail', 'Alamat Email', 'required|trim|valid_email');
		$this->form_validation->set_rules('agtNoTelp', 'No. Telp/HP', 'required|trim|min_length[10]|max_length[13]');
		$this->form_validation->set_rules('agtUkuranKaos', 'Ukuran Kaos', 'required');
		$this->form_validation->set_rules('agtMetodePembayaran', 'Metode Pembayaran', 'required');

		if (empty($_FILES['agtFoto']['name'])) {
			$this->form_validation->set_rules('agtFoto', 'Foto Pas (2x3cm)', 'required');
		}

		if (empty($_FILES['agtFotoKTA']['name'])) {
			$this->form_validation->set_rules('agtFotoKTA', 'Foto KTA Sebelumnya', 'required');
		}

		$tgllhr = explode("/", $_POST['agtTglLahir']);
		$tgllhr2 = explode("-", $_POST['agtTglLahir']);
		if ($tgllhr) {
			$tgl =  $tgllhr[2] . "-" . $tgllhr[1] . "-" . $tgllhr[0];
		} elseif ($tgllhr2) {
			$tgl =  $tgllhr2[0] . "-" . $tgllhr2[1] . "-" . $tgllhr2[2];
		} else {
			$tgl = NULL;
		}

		if (!empty($_FILES['agtFoto']['name'])) {
			$file = $_FILES['agtFoto']['name'];
			$tmp_file = $_FILES['agtFoto']['tmp_name'];
			$path = FCPATH . 'files/anggota/';
			$random_name= date('dmysi');
			$explode = explode('.', $file);
			$extensi = $explode[count($explode) - 1];
			$agtFoto = $random_name . "." . $extensi;
			$upload = move_uploaded_file ($tmp_file, $path . $agtFoto);
		} else {
			$agtFoto = $this->input->post('agtFoto');
			$upload = "";
		}

		if (!empty($_FILES['agtFotoKTA']['name'])) {
			$file = $_FILES['agtFotoKTA']['name'];
			$tmp_file = $_FILES['agtFotoKTA']['tmp_name'];
			$path = FCPATH . 'files/kta/';
			$random_name= date('dmysi');
			$explode = explode('.', $file);
			$extensi = $explode[count($explode) - 1];
			$agtFotoKTA = $random_name . "." . $extensi;
			$upload = move_uploaded_file ($tmp_file, $path . $agtFotoKTA);
		} else {
			$agtFotoKTA = $this->input->post('agtFoto');
			$upload = "";
		}

		$biday = new DateTime($tgl);
		$today = new DateTime();
		$umur = $today->diff($biday)->y;

		$data = [
			'agtFoto' => $agtFoto,
			'agtFotoKTA' => $agtFotoKTA,
			'agtNik' => $this->input->post('agtNik'),
			'agtNoKTA' => (!empty($this->input->post('agtNoKTA'))) ? $this->input->post('agtNoKTA') : NULL,
			'agtNama' => $this->input->post('agtNama'),
			'agtJnsKelamin' => $this->input->post('agtJnsKelamin'),
			'agtTmptLahir' => $this->input->post('agtTmptLahir'),
			'agtTglLahir' => $tgl,
			'agtUmur' => $umur,
			'agtProvinsi' => $this->input->post('agtProvinsi'),
			'agtKabupaten' => $this->input->post('agtKabupaten'),
			'agtKecamatan' => $this->input->post('agtKecamatan'),
			'agtKelurahan' => $this->input->post('agtKelurahan'),
			'agtAlamatJalan' => $this->input->post('agtAlamatJalan'),
			'agtEmail' => $this->input->post('agtEmail'),
			'agtNoTelp' => $this->input->post('agtNoTelp'),
			'agtUkuranKaos' => $this->input->post('agtUkuranKaos'),
			'agtMetodePembayaran' => $this->input->post('agtMetodePembayaran'),
		];

		if ($this->form_validation->run() == FALSE) {
			$result = 1;
			$params = array($result, $this->pesanColorWarning, $this->pesanAddWarning, '');
			$this->session->set_userdata('pesan', $params);
			redirect('perpanjangan');
		} else {
			$insertId = $this->m_anggota->addDataAction($data);
		}
		
		$add = true;
		if ($add) {
			$this->db->trans_commit();
		} else {
			$this->db->trans_rollback();
		}

		if ($add) {
			$result = 1;
			$params = array($result, $this->pesanColorSuccess, $this->pesanAddSuccess, '');
			$this->session->set_userdata('pesan', $params);
			redirect('perpanjangan/pembayaran/' . $insertId);
		} else {
			redirect('perpanjangan');
		}
	}

	public function pembayaran($id = null) {
		if (!$id) redirect('perpanjangan');
		
		$data['msg'] = $this->session->userdata('pesan');
		$data['title'] = 'Pembayaran Perpanjangan KTA';
		$data['anggota'] = $this->m_anggota->getDataById($id);

		$this->load->view('perpanjangan/view_pembayaran', $data);
	}
}
