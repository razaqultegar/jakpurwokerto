<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_selamat_datang extends CI_Model {
  public function validate($token) {
    $sql = "SELECT * FROM default_token WHERE token='$token'";
    $query = $this->db->query($sql);
    return $query->row();
  }
}
