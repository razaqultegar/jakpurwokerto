<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Class Personalisasi extends My_Controller {
  public function __construct() {
    parent::__construct();
    $this->load->model('m_personalisasi');
    $this->pesanAddSuccess = "Data Berhasil Ditambahkan";
    $this->pesanAddError = "Data Tidak Berhasil Ditambahkan";
    $this->pesanEditSuccess = "Data Berhasil Diubah";
    $this->pesanEditError = "Data Tidak Berhasil Diubah";
    $this->pesanDeleteSuccess = "Data Berhasil Dihapus";
    $this->pesanDeleteError = "Data Tidak Berhasil Dihapus";
    $this->pesanIconSuccess = "uk-icon-check-circle uk-icon-medium";
    $this->pesanIconError = "uk-icon-info-circle uk-icon-medium";
  }

  private function data_construct(){
    $data = array();
    return $data;
  }

  public function view(){
    $msg = $this->session->userdata('pesan');
    $data = $this->data_construct();
    $data['msg'] = (!empty($msg)) ? $msg : ['', '', ''];
    $data['title'] = "Personalisasi";
    $data['url_get_json'] = site_url('personalisasi/get_data_json');
    $this->layout->set_layout('personalisasi/view_personalisasi', $data);
  }

  public function get_data_json(){
    ob_start();
    $data = array();
    $requestData= $_REQUEST;
    $order = $this->input->post('order');
    $columns = $this->input->post('columns');
    $options['order'] = !empty($order) && !empty($columns) ? $columns[$order[0]['column']]['data'] : 'setTimestamp';

    $options['mode'] = !empty($order) ? $order[0]['dir']: 'asc';
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $options['offset'] = empty($start) ? 0 : $start;
    $options['limit'] = empty($length) ? 10 : $length;
    $where_like = array();
    if (!empty($requestData['search']['value'])){
      $options['where_like'] = array(
        "setLabel LIKE '%".$requestData['search']['value']."%'"
      );
    }else{
      $options['where_like'] = [];
    }
    
    $dataOutput = $this->m_personalisasi->getListData($options);
    
    $totalFiltered = $this->m_personalisasi->getTotalData($options);
    $totalData = $this->m_personalisasi->getTotal();
    $no = $options['offset'] + 1;
    if (!empty($dataOutput)){
      foreach ($dataOutput as $key => $value) {
        $value->aksi = '<a href="'.site_url('personalisasi/edit/'.$value->setKode).'" class="btn btn-warning btn-circle btn-sm" title="Ubah Data"><i class="fas fa-pencil-alt"></i></a>';
        $value->setValue = strip_tags($value->setValue);
        
        $value->setTimestamp = date('d/m/Y H:i', strtotime($value->setTimestamp));
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

  public function edit() {
    $id = $this->uri->segment(3);
    $data = $this->m_personalisasi->getDataById($id);
    $data = array_merge($data, $this->data_construct());
    $msg = $this->session->flashdata('pesan');
    $data['msg'] = (!empty($msg)) ? $msg : ['', '', ''];

    if($data['setType'] == 'file'){
      $this->layout->set_layout('personalisasi/edit_personalisasi_file', $data);
    }elseif($data['setType'] == 'textarea'){
      $this->layout->set_layout('personalisasi/edit_personalisasi_textarea', $data);
    }else{
      $this->layout->set_layout('personalisasi/edit_personalisasi', $data);
    }
  }

  public function editAction(){
    $this->db->trans_begin();
    if (!empty($_FILES['setValue']['name'])){
      $file=$_FILES['setValue']['name'];
      $tmp_file=$_FILES['setValue']['tmp_name'];
      $path = FCPATH.'assets/images/logo/';
      $random_name= 'logo';
      $explode = explode('.',$file);
      $extensi = 'png';
      $file_name = $this->input->post('setValue');
      move_uploaded_file($tmp_file, $path.$file_name);
    }else{
      $this->form_validation->set_rules('value', 'Value', 'required');
      $file_name = $this->input->post('setValue');
    }

    $data = [
      'setValue' => $this->input->post('setValue')
    ];

    $update = $this->m_personalisasi->editDataAction($data, ['setKode' => $this->input->post('setKode')]);

    if ($update){
      $this->db->trans_commit();
    }else{
      $this->db->trans_rollback();
    }

    $id = $this->input->post('setKode');
    if($update){    
      $params = array($update, $this->pesanIconSuccess, $this->pesanEditSuccess);
      $this->session->set_userdata('pesan',$params);
      redirect('personalisasi/view/'.$id, $data);
    }else{
      $params = array('danger', $this->pesanIconError, $this->pesanEditError);
      $data = isset($_POST['simpan']) ? $this->input->post() : $this->m_personalisasi->getDataById($id);
      $data = array_merge($data, $this->data_construct());
      $msg = $this->session->flashdata('pesan');
      $data['msg'] = (!empty($msg)) ? $msg : ['', '', ''];
      $this->layout->set_layout('personalisasi/edit_personalisasi/'.$id, $data);
      return;
    }
  }
}
