<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Kas extends My_Controller {
  public function __construct() {
    parent::__construct();
    $this->load->model('kas/m_kas');
    $this->pesanAddSuccess = "Data Berhasil Disimpan";
		$this->pesanAddError = "Data Tidak Berhasil Disimpan";
		$this->pesanDeleteSuccess = "Data Berhasil Dihapus";
		$this->pesanIconSuccess = "success";
		$this->pesanIconError = "danger";
  }

  public function index() {
    $msg = $this->session->flashdata('pesan');
    $data['msg'] = (!empty($msg)) ? $msg : ['', '', ''];
    $data['title'] = "Data Kas Umum";
    
    $kas = $this->m_kas->getData();
    $total_kas = 0;
    foreach ($kas as $key => $value) {
      $total_kas += $value['kasSaldo'];
    }
    $data['total_kas_saldo'] = $this->format_angka($total_kas);

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

        $value->aksi = '<a href="'.site_url('anggota/edit/'.$value->kasId).'" class="btn btn-warning btn-circle btn-sm" title="Edit Data"><i class="fas fa-pencil-alt"></i></a> <a href="'.site_url('anggota/delete/'.$value->kasId).'" class="btn btn-danger btn-circle btn-sm" title="Hapus Data"><i class="fas fa-trash"></i></a>';

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
}
