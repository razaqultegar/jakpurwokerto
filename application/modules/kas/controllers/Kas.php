<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Kas extends My_Controller {
  public function __construct() {
    parent::__construct();
    $this->load->model('kas/m_kas');
    $this->pesanAddSuccess = "Data Berhasil Disimpan";
		$this->pesanDeleteSuccess = "Data Berhasil Dihapus";
		$this->pesanColorSuccess = "success";
  }
  
  private function data_construct() {
		$this->load->model('ref_wilayah/m_ref_wilayah');

		$data['list_wilayah'] = $this->m_ref_wilayah->getCombo();
		$data['msg'] = $this->session->flashdata('msg');
		return $data;
	}

  public function index() {
    $msg = $this->session->flashdata('pesan');
    $data['msg'] = (!empty($msg)) ? $msg : ['', '', ''];
    $data['title'] = "Data Kas Umum";
    $data['url_get_json'] = site_url('kas/get_data_json');
    $data['url_add'] = site_url('kas/add');
    $this->layout->set_layout('kas/view_kas', $data);
  }

  public function get_data_json() {
    ob_start();
    $data = array();
    $requestData= $_REQUEST;
    $order = $this->input->post('order');
    $columns = $this->input->post('columns');
    $options['order'] = !empty($order) && !empty($columns) ? $columns[$order[0]['column']]['data'] : 'kasId';
    $options['mode'] = !empty($order) ? $order[0]['dir']: 'desc';
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $options['offset'] = empty($start) ? 0 : $start;
    $options['limit'] = empty($length) ? 10 : $length;
    $where_like = array();
    if (!empty($requestData['search']['value'])){
      $options['where_like'] = array(
        "kasTanggal LIKE '%".$requestData['search']['value']."%'",
        "kasMasukUraian LIKE '%".$requestData['search']['value']."%'",
        "kasKeluarUraian LIKE '%".$requestData['search']['value']."%'"
      );
    }else{
      $options['where_like'] = [];
    }

    $dataOutput = $this->m_kas->getListData($options);
    $totalFiltered = $this->m_kas->getTotalData($options);
    $totalData = $this->m_kas->getTotal();
    $no = $options['offset'] + 1;
    if (!empty($dataOutput)){
      foreach ($dataOutput as $key => $value) {
        $value->no = $no;
        $dataNavbar ="";
        if (count($dataOutput) > 3 && $key >= (count($dataOutput) - 2)){
          $dataNavbar = ", pos:'top-left'";
        }

        $value->aksi = '<a href="'.site_url('kas/edit/'.$value->kasId).'" class="btn btn-warning btn-circle btn-sm" title="Edit Data"><i class="fas fa-pencil-alt"></i></a> <a href="'.site_url('kas/delete/'.$value->kasId).'" class="btn btn-danger btn-circle btn-sm" title="Hapus Data"><i class="fas fa-trash"></i></a>';

        $value->kasTanggal = '<a href="'.site_url('kas/detil/'.$value->kasId).'" title="Detil Data">'.$this->format_tanggal($value->kasTanggal).'</a>';
        $value->kasMasuk = (!empty($value->kasMasuk)) ? $this->format_angka($value->kasMasuk) : '0';
        $value->kasKeluar =(!empty($value->kasKeluar)) ? $this->format_angka($value->kasKeluar) : '0'; 
        $value->kasSaldo = (!empty($value->kasSaldo)) ? $this->format_angka($value->kasSaldo) : '0';
        $value->timestamp = date('d/m/Y H:i', strtotime($value->kasLastUpdate));
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

  public function add() {
    $data = $this->data_construct();
    $data['title'] = "Tambah Data Kas Umum";
    $msg = $this->session->flashdata('pesan');
    $post = $this->session->flashdata('post');
    $this->layout->set_layout('kas/add_kas', $data);
  }

  public function addAction() {
    $this->form_validation->set_rules('kasTanggal', 'Tanggal Transaksi', 'required');
    $this->form_validation->set_rules('kasWilId', 'Koordinator Wilayah', 'required');

    if($this->form_validation->run()==FALSE){
			$this->session->set_flashdata('msg', array('1', 'warning', 'Lengkapi data terlebih dulu'));
			redirect('kas/add');
    }

    $tgllhr = explode("/", $_POST['kasTanggal']);
		$tgllhr2 = explode("-", $_POST['kasTanggal']);
		if($tgllhr){
			$tgl =  $tgllhr[2]."-".$tgllhr[1]."-".$tgllhr[0];
		}elseif($tgllhr2){
			$tgl =  $tgllhr2[0]."-".$tgllhr2[1]."-".$tgllhr2[2];
		}else{
			$tgl = NULL;
		}
    
    foreach ($_POST as $key => $value) {
      if ($key != 'simpan') {
        $_POST[$key] = str_replace('Rp ', '', $value); 
        $_POST[$key] = str_replace(',00', '', $_POST[$key]); 
        $_POST[$key] = str_replace('.', '', $_POST[$key]);
      }
    }

    $data = [
      'kasTanggal' => $tgl,
      'kasWilId' => $this->input->post('kasWilId'),
      'kasMasukUraian' => $this->input->post('kasMasukUraian'),
      'kasMasuk' => $this->input->post('kasMasuk'),
      'kasKeluarUraian' => $this->input->post('kasKeluarUraian'),
      'kasKeluar' => $this->input->post('kasKeluar'),
      'kasSaldo' => $this->input->post('kasSaldo')
    ];

    $insert = $this->m_kas->doAdd($data);
    if($insert){    
      $params = array($insert, $this->pesanColorSuccess, $this->pesanAddSuccess);
			$this->session->set_flashdata('pesan', $params);
      redirect('kas');
    }else{
      $params = array($insert, 'danger', 'Data Tidak Berhasil Disimpan');
			$this->session->set_flashdata('pesan', $params);
      redirect('kas/add');
    }
  }

  public function edit($id) {
    $data = $this->m_kas->getDataById($id);
    $data = array_merge($data, $this->data_construct());
    $data['title'] = "Ubah Data Kas Umum";
    $msg = $this->session->flashdata('pesan');
    $data['msg'] = (!empty($msg)) ? $msg : ['', '', ''];
    $this->layout->set_layout('kas/edit_kas', $data);
  }

  public function editAction() {
    $this->form_validation->set_rules('kasTanggal', 'Tanggal Transaksi', 'required');
    $this->form_validation->set_rules('kasWilId', 'Koordinator Wilayah', 'required');

    if($this->form_validation->run()==FALSE){
			$this->session->set_flashdata('msg', array('1', 'warning', 'Lengkapi data terlebih dulu'));
			redirect('kas/add');
    }

    $tgllhr = explode("/", $_POST['kasTanggal']);
		$tgllhr2 = explode("-", $_POST['kasTanggal']);
		if($tgllhr){
			$tgl =  $tgllhr[2]."-".$tgllhr[1]."-".$tgllhr[0];
		}elseif($tgllhr2){
			$tgl =  $tgllhr2[0]."-".$tgllhr2[1]."-".$tgllhr2[2];
		}else{
			$tgl = NULL;
		}
    
    foreach ($_POST as $key => $value) {
      if ($key != 'simpan') {
        $_POST[$key] = str_replace('Rp ', '', $value); 
        $_POST[$key] = str_replace(',00', '', $_POST[$key]); 
        $_POST[$key] = str_replace('.', '', $_POST[$key]);
      }
    }

    $data = [
      'kasTanggal' => $tgl,
      'kasWilId' => $this->input->post('kasWilId'),
      'kasMasukUraian' => $this->input->post('kasMasukUraian'),
      'kasMasuk' => $this->input->post('kasMasuk'),
      'kasKeluarUraian' => $this->input->post('kasKeluarUraian'),
      'kasKeluar' => $this->input->post('kasKeluar'),
      'kasSaldo' => $this->input->post('kasSaldo')
    ];

    $update = $this->m_kas->doUpdate($data, ['kasId' => $this->input->post('kasId')]);
    if($update){    
      $params = array($update, $this->pesanColorSuccess, $this->pesanAddSuccess);
			$this->session->set_flashdata('pesan', $params);
      redirect('kas');
    }else{
      $params = array($update, 'danger', 'Data Tidak Berhasil Disimpan');
			$this->session->set_flashdata('pesan', $params);
      redirect('kas/add');
    }
  }

  public function delete($id){
    $delete = $this->m_kas->doDelete(['kasId' => $id]);     
    if($delete){
      $params = array($delete, $this->pesanColorSuccess, $this->pesanDeleteSuccess);
      $this->session->set_flashdata('pesan', $params);
      redirect('kas');
    }else{
      redirect('kas');
    }
  }

  public function export(){
    require_once APPPATH.'libraries/excel/PHPExcel.php';
    include APPPATH.'libraries/excel/PHPExcel/Writer/Excel2007.php';

    $templateExcel = FCPATH.'files/export_kas.xls';
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
    $where_like = array();
    $key = $_POST['filter_multiple'];
    if (!empty($key)){
      $options['where_like'] = array(
        "AND kasMasukUraian LIKE '%".$key."%' OR kasKeluarUraian LIKE '%".$key."%' OR kasTanggal LIKE '%".$key."%'"
      );
    }else{
      $options['where_like'] = [];
    }
    $dataOutput = $this->m_kas->getDataExcel($options);
    $dataSaldo = $this->m_kas->getDataExcelSaldo($options);
    $objPHPExcel->getActiveSheet()->SetCellValue('C1', $dataSaldo[0]['saldo']);
    $no = 1;
    $row = 4;
    if (!empty($dataOutput)){
      foreach ($dataOutput as $key => $value) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row, $no);
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row, $value->kasTanggal);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row, $value->kasMasukUraian);
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row, $value->kasMasuk);
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $value->kasTanggal);
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$row, $value->kasKeluarUraian);
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$row, $value->kasKeluar);
        $no++;
        $row++;
      }
    }

    $row = $row - 1;
    $objPHPExcel->getActiveSheet()->getStyle("A4:G".$row)->applyFromArray($borderThinStyle); 
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); 
    header('Content-Disposition: attachment;filename="data_kas_'.date("Y-m-d").'.xlsx"'); 
    header('Cache-Control: max-age=0'); 
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save('php://output');
  }
}
