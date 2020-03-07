<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_pekerjaan extends CI_Model {
  protected $table_name = 'jakpwt_ref_pekerjaan';

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
    $sql = $this->query()." WHERE pkjIdPekerjaan = '$id'";
    $query = $this->db->query($sql);
    return $query->row_array();  
  }

  public function getCombo(){
    $sql = $this->query().' ORDER BY pkjNama ASC';
    $query = $this->db->query($sql);
    return $query->result(); 
  }

  public function getSearchPekerjaan($q){
    $sql = "SELECT pkjNama AS text, pkjIdPekerjaan AS id FROM jakpwt_ref_pekerjaan WHERE 1=1 AND (pkjNama LIKE '%".$q."%')";
    $query = $this->db->query($sql);
    return $query->result_array();
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

  public function getStatistikPekerjaan($db_condition){
    $param = $db_condition;
    $sql = "SELECT pkjNama as name, IF(jml <> '', jml, 0) AS data FROM jakpwt_ref_pekerjaan LEFT JOIN (SELECT pkjIdPekerjaan AS pkjId, COUNT(agtId) AS jml FROM  jakpwt_ref_pekerjaan LEFT JOIN jakpwt_anggota ON pkjIdPekerjaan = agtIdPekerjaan WHERE 1=1 ".$param." GROUP BY agtIdPekerjaan)AS a ON a.pkjId = pkjIdPekerjaan GROUP BY pkjNama ORDER BY jml DESC";
    $query = $this->db->query($sql);
    return $query->result_array();
  }
}
