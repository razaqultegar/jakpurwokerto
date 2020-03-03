<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_kas extends CI_Model {

	protected $table_name = 'jakpwt_kas';

	private function query(){
    $query = "SELECT * FROM ( ";
    $query .= "SELECT * FROM $this->table_name LEFT JOIN jakpwt_ref_wilayah ON `wilIdWilayah`=`kasWilId`";
    $query .= " ) AS temp ";
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
    return $query->result_array();  
  }

  public function getDataById($id){
    $sql = $this->query()." WHERE kasId = '$id'";
    $query = $this->db->query($sql);
    return $query->row_array();  
  }

  public function doAdd($data){
    $result = $this->db->insert($this->table_name, $data);
    return $result;
  }

  public function doUpdate($data, $where){
    $result = $this->db->update($this->table_name, $data, $where);
    return $result;
  }

  public function doDelete($where){
    $result= $this->db->where($where)->delete($this->table_name);
    return $result;
  }

  public function statistik_total_kas($param){
    $sql = "SELECT SUM(kasSaldo) AS total FROM ".$this->table_name." WHERE 1=1 ";
    $query = $this->db->query($sql);
    return $query->result_array();  
  }

  public function getDataExcel($options){
    $where_like = empty($options['where_like']) ? '1 = 1' : '('.implode(' OR ', $options['where_like']).')'; 
    $sql = $this->query()." WHERE 1 = 1 AND ".$where_like." GROUP BY kasId";
    $query = $this->db->query($sql);
    return $query->result();
  }

  public function getDataExcelSaldo($options){
    $where_like = empty($options['where_like']) ? '' : '('.implode(' OR ', $options['where_like']).')';
    $sql="SELECT SUM(KasSaldo) as saldo FROM jakpwt_kas WHERE 1 = 1 ";
    $query = $this->db->query($sql);
    return $query->result_array();
  }
}
