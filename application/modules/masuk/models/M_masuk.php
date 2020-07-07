<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_masuk extends CI_Model {
  public function validate($username, $password) {
    $sql = "SELECT userId, realname, username, PASSWORD FROM default_user WHERE username=? AND password=?";
    $query = $this->db->query($sql, array($username, $password));
    return $query->row();
  }

  public function get_user($username){
    $sql = "SELECT realName FROM default_user WHERE username='$username'";
    $query = $this->db->query($sql);
    return $query->row();           
  }
}
