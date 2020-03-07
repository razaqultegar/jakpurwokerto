<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_pendidikan extends CI_Model {
  protected $table_name = 'jakpwt_ref_pendidikan';

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
    $sql = $this->query()." WHERE dikIdPendidikan = '$id'";
    $query = $this->db->query($sql);
    return $query->row_array();  
  }

  public function getCombo(){
    $sql = $this->query().' ORDER BY dikPendidikan ASC';
    $query = $this->db->query($sql);
    return $query->result(); 
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

  public function getTotalStatistikAgt($param, $id){
    if($param != ""){
      $where = " AND agtId = '$param' ";
    }else{
      $where = "";
    }

    $sql ="SELECT * FROM jakpwt_ref_pendidikan b INNER JOIN jakpwt_anggota c ON c.agtIdPendidikan = b.`dikIdPendidikan` WHERE 1=1 AND dikIdPendidikan = '$id' ".$where;
    $query = $this->db->query($sql);
    return $query->num_rows();
  }

  public function getCountPddk($db_condition){
    $param = $db_condition;
    $sql = "SELECT dikPendidikan AS name, IF(jml <> '', jml, 0) AS data FROM jakpwt_ref_pendidikan LEFT JOIN (SELECT dikIdPendidikan AS pendId, COUNT(agtId) AS jml FROM `jakpwt_ref_pendidikan` LEFT JOIN jakpwt_anggota ON dikIdPendidikan = agtIdPendidikan WHERE 1=1 ".$param."GROUP BY agtIdPendidikan)AS a ON a.pendId = dikIdPendidikan GROUP BY dikPendidikan";
    $query = $this->db->query($sql);
    return $query->result();
  }
}
