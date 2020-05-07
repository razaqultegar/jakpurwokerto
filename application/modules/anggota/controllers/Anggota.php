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

		$this->load->library('excel/PHPExcel');
	}

	private function data_construct() {
		$this->load->model('pendidikan/m_pendidikan');
		$this->load->model('pekerjaan/m_pekerjaan');
		$this->load->model('wilayah/m_wilayah');

		$data['list_pendidikan'] = $this->m_pendidikan->getCombo();
		$data['list_pekerjaan'] = $this->m_pekerjaan->getCombo();
		$data['list_wilayah'] = $this->m_wilayah->getCombo();
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
		$data['url_export'] = site_url('anggota/export');
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
		$this->form_validation->set_rules('agtAlamatJalan', 'Alamat Jalan', 'required|trim|min_length[2]');
		$this->form_validation->set_rules('agtKdPos', 'Kode Pos', 'required|trim|max_length[5]');
		$this->form_validation->set_rules('agtStatusKta', 'Status KTA', 'required');
		$this->form_validation->set_rules('agtNoTelp', 'No. Telp/HP', 'required|trim|max_length[14]');
		$this->form_validation->set_rules('agtEmail', 'Alamat Email', 'required|trim|valid_email');
		$this->form_validation->set_rules('agtUkrnKaos', 'Ukuran Kaos', 'required');

		if($this->form_validation->run()==FALSE){
			$this->session->set_flashdata('msg', array('1', 'warning', 'Lengkapi data terlebih dulu'));
			redirect('anggota/add');
		}

		$agtNoKta = $this->input->post('agtNoKta');
		$validasi_nokta = $this->m_anggota->cekDuplicateNoKta($agtNoKta);
		if(sizeof($validasi_nokta) > 0){
			$result = 1;
			$params = array($result, 'warning', 'No. KTA Sudah Ada. Silahkan Coba Lagi.', '');
			$this->session->set_userdata('pesan',$params); 
			redirect('anggota');
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
			$params = array($add, 'danger', 'Data Tidak Berhasil Disimpan');
			$this->session->set_userdata('pesan', $params);
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
		$this->form_validation->set_rules('agtAlamatJalan', 'Alamat Jalan', 'required|trim|min_length[2]');
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

		$agtNoKta = $this->input->post('agtNoKta');
		$validasi_nokta = $this->m_anggota->cekDuplicateNoKta($agtNoKta);
		if(sizeof($validasi_nokta) > 0){
			$result = 1;
			$params = array($result, 'warning', 'No. KTA Sudah Ada. Silahkan Coba Lagi.', '');
			$this->session->set_userdata('pesan',$params); 
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

	public function cetak($id) {
		$template = base_url('files/formulir.docx');
		$data = $this->m_anggota->getDataById($id);
		$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($template);
		$tgllahir = $this->format_tanggal($data['agtTglLahir']);

		$templateProcessor->setValue('nokta', $data['agtNoKta']);
		$templateProcessor->setValue('nama', $data['agtNama']);
		$templateProcessor->setValue('nmpendek', $data['agtNmPendek']);
		$templateProcessor->setValue('tmptlahir', $data['agtTmptLahir']);
		$templateProcessor->setValue('tgllahir', $tgllahir);
		$templateProcessor->setValue('pendidikan', $data['dikPendidikan']);
		$templateProcessor->setValue('pekerjaan', $data['pkjNama']);
		$templateProcessor->setValue('alamatjalan', $data['agtAlamatJalan']);
		$templateProcessor->setValue('kelurahan', $data['agtKelurahan']);
		$templateProcessor->setValue('kecamatan', $data['agtKecamatan']);
		$templateProcessor->setValue('kodepos', $data['agtKdPos']);
		$templateProcessor->setValue('notelp', $data['agtNoTelp']);
		$templateProcessor->setValue('email', $data['agtEmail']);
		$templateProcessor->setValue('kaos', $data['agtUkrnKaos']);

		$filename = "FORMULIR_".str_replace(' ', '_', $data['agtNama']);
		$temp_file = tempnam(sys_get_temp_dir(), $filename);
		$templateProcessor->saveAs($temp_file);

		// download
		$filename .= ".docx";
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$filename);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($temp_file));
		readfile($temp_file);
		unlink($temp_file);
	}
	
	public function export() {
		set_time_limit(0);
		require_once APPPATH.'libraries/excel/PHPExcel.php';
		include APPPATH.'libraries/excel/PHPExcel/Writer/Excel2007.php';

		$templateExcel = FCPATH.'files/export_anggota.xls';
		$objPHPExcel = PHPExcel_IOFactory::load($templateExcel);
		$objPHPExcel->setActiveSheetIndex(0);

		$borderThinStyle = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$centerStyle = array(
		 'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
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
			foreach ($dataOutput as $key => $value) {
				$tgllahir = $this->format_tanggal($value->agtTglLahir);
				$kelurahan = (!empty($value->agtKelurahan)) ? $value->agtKelurahan : '-';
				$tglinsert = $this->format_tanggal($value->agtTglInsert);

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row, $value->agtNoKta);
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row, $value->agtBrlkDari);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row, $value->agtBrlkSampai);
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row, $value->agtNama);
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $value->agtTmptLahir.", ".$tgllahir);
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row, $value->dikPendidikan);
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row, $value->pkjNama);
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
}
