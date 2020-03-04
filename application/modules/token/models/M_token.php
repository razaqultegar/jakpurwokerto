<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_token extends CI_Model {
  protected $table_name = 'default_token';

  private function query(){
    $query = "SELECT $this->table_name.* FROM $this->table_name";
    return $query;
  }

  public function getToken(){
		return $this->db->get($this->table_name);
  }

  public function addDataAction($data){
    $result = $this->db->insert($this->table_name, $data);
    $result = $this->db->insert_id();
    return $result;
  }

  public function editDataAction($data, $where){
    $result = $this->db->update($this->table_name, $data, $where);
    return $result;
  }

  public function delete($where){
    $result= $this->db->where($where)->delete($this->table_name);
    return $result;
  }
}
