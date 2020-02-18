<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_setting extends CI_Model {
  public function getDataPersonalisasi($kode){
    $sql = "SELECT * FROM default_setting WHERE setKode = '$kode'";
    $query = $this->db->query($sql);
    return $query->result_array();
  }
}
