<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_personalisasi extends CI_Model {
  protected $table_name = 'default_setting';

  private function query(){
    $query = "SELECT $this->table_name.* FROM $this->table_name";
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

  public function getDataById($id){
    $sql = $this->query()." WHERE setKode = '$id'";
    $query = $this->db->query($sql);
    return $query->row_array();
  }

  public function editDataAction($data, $where){
    $result = $this->db->update($this->table_name, $data, $where);
    return $result;
  }
}
