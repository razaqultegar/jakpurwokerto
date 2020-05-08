<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_pengguna extends CI_Model {
  protected $table_name = 'default_user';

  private function query(){
    $query = "SELECT * FROM $this->table_name";
    return $query;
  }

  public function getListData($options = []){
    $where_like = empty($options['where_like']) ? '1 = 1' : '('.implode(' OR ', $options['where_like']).')'; 
    $sql = $this->query()." WHERE 1 = 1 AND ".$where_like." ORDER BY ".$options['order']." ".$options['mode']." LIMIT ".$options['offset'].", ".$options['limit'];
    $query = $this->db->query($sql);
    return $query->result();
  }

  public function getTotalData($options){
    $where_like = empty($options['where_like']) ? '1 = 1' : '('.implode(' OR ', $options['where_like']).')'; 
    $sql = $this->query()." WHERE 1 = 1 AND ".$where_like;
    $query = $this->db->query($sql);
    return $query->num_rows();
  }

  public function getTotal(){
    $sql = $this->query();
    $query = $this->db->query($sql);
    return $query->num_rows();
  }
    
  public function getData(){
    $sql = $this->query();
    $query = $this->db->query($sql);
    return $query->result();
  }

  public function addDataAction($data){
    $result = $this->db->insert($this->table_name, $data);
    $result = $this->db->insert_id();
    return $result;
  }

  public function getDataById($id){
    $sql = "SELECT * FROM $this->table_name WHERE userId='$id'";
    $query = $this->db->query($sql);
    return $query->row();
  }

  public function getDatabyIdd($id){
    $sql = "SELECT * FROM $this->table_name WHERE userId='$id'";
    $query = $this->db->query($sql);
    return $query->row_array();  
  }

  public function getDataByUsername($id){
    $sql = "SELECT * FROM $this->table_name WHERE username='$id'";
    $query = $this->db->query($sql);
    return $query->row();
  }

  public function update($data){
    $result = $this->db->update($this->table_name, $data, $where);
    return $result;
  }

  public function editDataAction($data, $where){
    $result = $this->db->update($this->table_name, $data, $where);
    return $result;
  }

  public function updatePassword($data){
    $pass_old = $data['pass_old'];
    $pass_new = $data['pass_new'];
    $pass_new_retype = md5($data['pass_new_retype']);
    $id = $data['id'];
    $update_user = "UPDATE default_user SET password='$pass_new_retype' WHERE userId='$id'";
    $result = $this->db->query($update_user);
    return $result;
  }

  public function delete($where){
    $result= $this->db->where($where)->delete($this->table_name);
    return $result;
  }

  public function resetPassword($data){
    $password = $data['password'];
    $id_akun = $data['id_akun'];
    $group = "UPDATE default_user SET password = '$password' WHERE userId='$id_akun'";
    $result= $this->db->query($group);
    return $result;
  }
}
