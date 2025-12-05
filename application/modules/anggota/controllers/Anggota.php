<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Anggota extends MY_Controller {
	protected $jenisKelamin = ['L' => 'Laki-laki', 'P' => 'Perempuan'];
	protected $uploadDir = 'files/anggota/';

	public function __construct() {
		parent::__construct();

		// load model
		$this->load->model('anggota/m_anggota');

		// message
		$this->pesanAddSuccess = "Data Berhasil Disimpan";
		$this->pesanDeleteSuccess = "Data Berhasil Dihapus";
		$this->pesanColorSuccess = "success";

		// load excel library
		$this->load->library('excel/PHPExcel');
	}

	private function data_construct() {
		// load model
		$this->load->model('wilayah/m_wilayah');

		// construct data
		$data['list_wilayah'] = $this->m_wilayah->getCombo();
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
		$data['url_export'] = site_url('anggota/export');

		$this->layout->set_layout('anggota/view_anggota', $data);
	}

	public function get_data_json() {
		ob_start();

		$requestData= $_REQUEST;
		$order = $this->input->post('order');
		$columns = $this->input->post('columns');
		$options['order'] = !empty($order) && !empty($columns)
			? $columns[$order[0]['column']]['data']
			: 'agtLastUpdate';

		$options['mode'] = !empty($order) ? $order[0]['dir']: 'desc';
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$options['offset'] = empty($start) ? 0 : $start;
		$options['limit'] = empty($length) ? 10 : $length;

		if (!empty($requestData['search']['value'])) {
				$options['where_like'] = array(
				"agtNama LIKE '%".$requestData['search']['value']."%'
				OR agtNoKTA LIKE '%".$requestData['search']['value']."%'
				OR wilNama LIkE '%".$requestData['search']['value']."%'"
			);
		}

		$options['condition'] = $this->db_condition;
		$dataOutput = $this->m_anggota->getListData($options);
		$totalFiltered = $this->m_anggota->getTotalData($options);
		$totalData = $this->m_anggota->getTotal($this->db_condition);
		$no = $options['offset'] + 1;

		if (!empty($dataOutput)){
			foreach ($dataOutput as $value) {
				$value->no = $no;
				$value->agtNoKTA = (!empty($value->agtNoKTA)) ? $value->agtNoKTA : "-";
				$value->aksi = '<a href="' . site_url('anggota/edit/' . $value->agtId) . '" class="btn btn-warning btn-circle btn-sm" title="Edit Data"><i class="fas fa-pencil-alt"></i></a> <a href="' . site_url('anggota/delete/' . $value->agtId) . '" class="btn btn-danger btn-circle btn-sm" title="Hapus Data"><i class="fas fa-trash"></i></a>';
				$value->agtNama = '<a href="' . site_url('anggota/detil/' . $value->agtId).'" title="Detil Data">' . strtoupper($value->agtNama) . '</a>';
				$value->wilNama = (!empty($value->wilNama)) ? $value->wilNama : "-";
				$no++;
			}
		}

		$response = array(
			"draw" => isset($requestData['draw']) ? intval( $requestData['draw'] ) : 0,
			"recordsTotal" => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data" => $dataOutput
		);

		echo json_encode($response);
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
		$this->_setValidationRules();

		if ($this->form_validation->run() == FALSE) {
			$this->_handleValidationError();
		}

		$agtId = $this->input->post('agtId');
		$agtNoKTA = $this->input->post('agtNoKTA');

		// Check for duplicate KTA (exclude current record)
		if (!$this->_checkDuplicateKTA($agtNoKTA)) {
			redirect('anggota');
		}

		// Handle file upload
		$oldFile = $this->input->post('agtFoto');
		$file_name = $this->_handleFileUpload('agtFoto', $oldFile);
		if ($file_name === false) {
			$file_name = $oldFile;
		}

		// Prepare form data
		$fields = [
			'agtNoKTA', 'agtIdWilayah', 'agtNama', 'agtNmPendek',
			'agtTmptLahir', 'agtJnsKelamin', 'agtKelurahan', 'agtKecamatan',
			'agtAlamatJalan', 'agtKdPos', 'agtNoTelp', 'agtEmail',
			'agtUkrnKaos', 'agtStatusKta', 'agtBrlkDari', 'agtBrlkSampai'
		];

		$data = $this->_prepareFormData($fields, $this->input->post('agtTglLahir'));
		$data['agtFoto'] = $file_name;
		$data['agtStatusKta'] = $this->input->post('agtStatusKta') ?: '0';

		// Execute update
		$updateId = $this->m_anggota->editDataAction($data, ['agtId' => $agtId]);
		$this->_handleSaveResult($updateId, 'edit');
	}

	public function delete($id){
		$data = $this->m_anggota->deleteImage($id)->row();
		unlink(FCPATH . "files/anggota/$data->agtFoto");
		$delete = $this->m_anggota->delete(['agtId' => $id]);

		if($delete){
			$params = array($delete, $this->pesanColorSuccess, $this->pesanDeleteSuccess);
			$this->session->set_userdata('pesan', $params);
			redirect('anggota');
		}else{
			redirect('anggota');
		}
	}
	
	public function export() {
		set_time_limit(0);
		require_once APPPATH . 'libraries/excel/PHPExcel.php';
		include APPPATH . 'libraries/excel/PHPExcel/Writer/Excel2007.php';

		$templateExcel = FCPATH.'files/export_anggota.xls';
		$objPHPExcel = PHPExcel_IOFactory::load($templateExcel);
		$objPHPExcel->setActiveSheetIndex(0);

		$borderThinStyle = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				),
			),
		);

		$options['order'] = 'agtNama';
		$options['mode'] = 'asc';
		$options['offset'] = 0;
		$options['limit'] = 10000000;
		$options['where_like'] = [];

		$options['db_condition'] = $this->db_condition;
		$dataOutput = $this->m_anggota->getListData($options);
		$no = 1;
		$row = 5;

		if (!empty($dataOutput)){
			foreach ($dataOutput as $value) {
				$tgllahir = $this->format_tanggal($value->agtTglLahir);
				$kelurahan = (!empty($value->agtKelurahan)) ? $value->agtKelurahan : '-';
				$tglinsert = $this->format_tanggal($value->agtTglInsert);

				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$row, $value->agtNoKTA);
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row, $value->agtBrlkDari);
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row, $value->agtBrlkSampai);
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row, $value->agtNama);
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $value->agtTmptLahir.", ".$tgllahir);
				$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row, $value->agtAlamatJalan);
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row, $kelurahan);
				$objPHPExcel->getActiveSheet()->SetCellValue('J'.$row, $value->agtKecamatan);
				$objPHPExcel->getActiveSheet()->SetCellValue('K'.$row, $value->agtKdPos);
				$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row, $value->agtNoTelp);
				$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row, $value->agtEmail);
				$objPHPExcel->getActiveSheet()->SetCellValue('N'.$row, $value->agtUkrnKaos);
				$objPHPExcel->getActiveSheet()->SetCellValue('O'.$row, $tglinsert);

				$no++;
				$row++;
			}
		}

		$row = $row - 1;
		$objPHPExcel->getActiveSheet()->getStyle("A5:O".$row)->applyFromArray($borderThinStyle);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="data_anggota_'.date("d-m-Y").'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	private function _parseTanggal($date) {
		if (empty($date)) return NULL;

		$tgllhr = explode("/", $date);
		$tgllhr2 = explode("-", $date);

		if (!empty($tgllhr) && count($tgllhr) == 3 && strlen($tgllhr[0]) == 2) {
			// dd/mm/yyyy format
			return $tgllhr[2] . "-" . $tgllhr[1] . "-" . $tgllhr[0];
		} elseif (!empty($tgllhr2) && count($tgllhr2) == 3 && strlen($tgllhr2[0]) == 4) {
			// yyyy-mm-dd format
			return $tgllhr2[0] . "-" . $tgllhr2[1] . "-" . $tgllhr2[2];
		}

		return NULL;
	}

	private function _calculateAge($birthDate) {
		if (empty($birthDate)) return 0;

		try {
			$birthDay = new DateTime($birthDate);
			$today = new DateTime();
			return $today->diff($birthDay)->y;
		} catch (Exception $e) {
			return 0;
		}
	}

	private function _handleFileUpload($fieldName, $oldFile = null) {
		if (empty($_FILES[$fieldName]['name'])) {
			return $oldFile; // Return old file if no new file uploaded
		}

		$file = $_FILES[$fieldName]['name'];
		$tmp_file = $_FILES[$fieldName]['tmp_name'];
		$path = FCPATH . $this->uploadDir;

		// Delete old file if exists
		if (!empty($oldFile) && file_exists($path . $oldFile)) {
			unlink($path . $oldFile);
		}

		// Generate new filename
		$random_name = date('dmysi');
		$explode = explode('.', $file);
		$extensi = strtolower($explode[count($explode) - 1]);

		// Validate extension
		$allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
		if (!in_array($extensi, $allowed_ext)) {
			return false;
		}

		$file_name = $random_name . "." . $extensi;
		$upload = move_uploaded_file($tmp_file, $path . $file_name);

		return $upload ? $file_name : false;
	}

	private function _prepareFormData($fields, $tglLahir = null) {
		$data = array();

		foreach ($fields as $field) {
			$value = $this->input->post($field);
			$data[$field] = (!empty($value)) ? $value : NULL;
		}

		// Handle date parsing
		if (!empty($tglLahir)) {
			$tgl = $this->_parseTanggal($tglLahir);
			$data['agtTglLahir'] = $tgl;
			$data['agtUmur'] = $this->_calculateAge($tgl);
		}

		// Set insert/update timestamp
		$data['agtTglInsert'] = date('Y-m-d');

		return $data;
	}

	private function _setValidationRules() {
		$this->form_validation->set_rules('agtIdWilayah', 'Koordinator Wilayah', 'required');
		$this->form_validation->set_rules('agtNama', 'Nama Lengkap', 'required|min_length[2]');
		$this->form_validation->set_rules('agtNmPendek', 'Nama Panggilan', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtTmptLahir', 'Tempat Lahir', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtTglLahir', 'Tanggal Lahir', 'required');
		$this->form_validation->set_rules('agtJnsKelamin', 'Jenis Kelamin', 'required');
		$this->form_validation->set_rules('agtKecamatan', 'Kecamatan', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtAlamatJalan', 'Alamat Jalan', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtKdPos', 'Kode Pos', 'required|trim|max_length[5]');
		$this->form_validation->set_rules('agtStatusKta', 'Status KTA', 'required');
		$this->form_validation->set_rules('agtNoTelp', 'No. Telp/HP', 'required|trim|max_length[14]');
		$this->form_validation->set_rules('agtEmail', 'Alamat Email', 'required|trim|valid_email');
		$this->form_validation->set_rules('agtUkrnKaos', 'Ukuran Kaos', 'required');
	}

	private function _handleValidationError() {
		$params = array('1', 'danger', 'Data Tidak Berhasil Disimpan');
		$this->session->set_userdata('pesan', $params);
		redirect('anggota');
	}

	private function _checkDuplicateKTA($agtNoKTA) {
		$validasi_nokta = $this->m_anggota->cekDuplicateNoKta($agtNoKTA);
		if (sizeof($validasi_nokta) > 0) {
			$params = array(1, 'warning', 'No. KTA Sudah Ada. Silahkan Coba Lagi.');
			$this->session->set_userdata('pesan', $params);
			return false;
		}
		return true;
	}

	private function _handleSaveResult($result, $action = 'add') {
		if ($result) {
			$this->db->trans_commit();
			$params = array($result, $this->pesanColorSuccess, $this->pesanAddSuccess);
			$this->session->set_userdata('pesan', $params);
			redirect('anggota');
		} else {
			$this->db->trans_rollback();
			$params = array($result, 'danger', 'Data Tidak Berhasil Disimpan');
			$this->session->set_userdata('pesan', $params);
			redirect('anggota');
		}
	}
}
