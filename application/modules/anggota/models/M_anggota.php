<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_anggota extends MY_Model {
  protected $table_name = 'jakpwt_anggota';

  public function queryBase(){
    $query =   "SELECT 
    $this->table_name.*,
      dikPendidikan, pkjNama, wilNama,
      if(agtJnsKelamin = 'L', 'Laki-laki', 'Perempuan') AS jnsKelamin
    FROM $this->table_name
      LEFT JOIN jakpwt_ref_pendidikan ON `dikIdPendidikan`=`agtIdPendidikan`
      LEFT JOIN jakpwt_ref_pekerjaan ON `pkjIdPekerjaan`=`agtIdPekerjaan`
      LEFT JOIN jakpwt_ref_wilayah ON `wilIdWilayah`=`agtIdWilayah`
    WHERE 
      1 = 1 ".
    $this->db_condition;

    return $query;
  }

  private function query(){
    $query = "SELECT * FROM ( ";
    $query .= $this->queryBase(); 
    $query .= " ) AS temp_table ";
    return $query;
  }

  public function getListData($options = []){
    $where_like = empty($options['where_like']) ? '' : 'AND ('.implode(' AND ', $options['where_like']).')'; 
    if (!isset($options['condition']))
    $options['condition'] = ' ';
    $sql = $this->query()." WHERE 1 = 1 ".$where_like.$options['condition']." GROUP BY noKta ORDER BY ".$options['order']." ".$options['mode']." LIMIT ".$options['offset'].", ".$options['limit'];
    // print_r('<pre>');
    // print_r($sql);die;
    $query = $this->db->query($sql);
    return $query->result();
  }

  public function getTotalData($options){
    $where_like = empty($options['where_like']) ? '' : 'AND ('.implode(' AND ', $options['where_like']).')'; 
    if (!isset($options['condition']))
    $options['condition'] = ' ';
    $sql = "SELECT COUNT(DISTINCT noKta) AS total FROM( ";
    $sql .= $this->queryBase();
    $sql .= " ) AS temp_table WHERE 1 = 1 ".$where_like.$options['condition'];
    $query = $this->db->query($sql)->row();
    return $query->total;
  }

  public function getTotal($options = ''){
    $sql = "SELECT COUNT(DISTINCT noKta) AS total FROM( ";
    $sql .= $this->queryBase();
    $sql .= ") AS temp_table WHERE 1 = 1 ".$options;
    $query = $this->db->query($sql)->row();
    return $query->total;
  }

  public function getSearchNamaAnggota($q,$param){
    $sql = "SELECT agtNama as text, noKta as text, wilNama as text FROM jakpwt_anggota WHERE 1=1 AND agtNama LIKE '%".$q."%' $param";
    $query = $this->db->query($sql);
    return $query->result_array();
  }

  public function getDataById($id){
    $sql = $this->query()." WHERE noKta = '$id'";
    $query = $this->db->query($sql);
    return $query->row_array();  
  }

  public function cekDuplicateNik($id){
    $sql = $this->query()." WHERE noKta = '$id'";
    $query = $this->db->query($sql);
    return $query->row_array();  
  }

  public function getCombo($id){
    if(!empty($id)){
      $param = "WHERE agtNama LIKE '%".$id."%'";
    }else{
      $param = "";
    }

    $sql = "SELECT noKta as id, agtNama as name FROM jakpwt_anggota ".$param;
    $query = $this->db->query($sql);
    return $query->result_array();  
  }

  public function addDataAction($data){
    $result = $this->db->insert($this->table_name, $data);
    $result = $this->db->insert_id();
    return $result;
  }

  public function addDataActionImport($data){
    $result = $this->db->insert($this->table_name, $data);
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

  public function getDataExcel($options = []){
    $where_like = empty($options['where_like']) ? '1 = 1' : '('.implode(' AND ', $options['where_like']).')'; 
    $sql = $this->query()." WHERE 1=1 AND ".$where_like.$options['condition'];
    // print_r($sql);die;
    $query = $this->db->query($sql);
    return $query->result();
  }

  public function getMan($db_condition){
    $param = $db_condition;
    $sql = "SELECT name,data FROM (
        SELECT 'jan' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND MONTH(agtTglInsert)='1' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Feb' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND MONTH(agtTglInsert)='2' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Mar' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND MONTH(agtTglInsert)='3' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'April' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND MONTH(agtTglInsert)='4' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Mei' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND MONTH(agtTglInsert)='5' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Juni' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND MONTH(agtTglInsert)='6' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Juli' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND MONTH(agtTglInsert)='7' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Agustus' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND MONTH(agtTglInsert)='8' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'September' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND MONTH(agtTglInsert)='9' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Oktober' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND MONTH(agtTglInsert)='10' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'November' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND MONTH(agtTglInsert)='11' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Desember' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND MONTH(agtTglInsert)='12' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
    )AS temp";

    $query = $this->db->query($sql);
    return $query->result();
  }

  public function getWoman($db_condition){
    $param = $db_condition;
    $sql = "SELECT name,data FROM (
        SELECT 'jan' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND MONTH(agtTglInsert)='1' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Feb' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND MONTH(agtTglInsert)='2' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Mar' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND MONTH(agtTglInsert)='3' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'April' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND MONTH(agtTglInsert)='4' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Mei' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND MONTH(agtTglInsert)='5' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Juni' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND MONTH(agtTglInsert)='6' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Juli' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND MONTH(agtTglInsert)='7' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Agustus' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND MONTH(agtTglInsert)='8' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'September' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND MONTH(agtTglInsert)='9' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Oktober' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND MONTH(agtTglInsert)='10' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'November' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND MONTH(agtTglInsert)='11' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
      UNION 
        SELECT 'Desember' AS NAME, COUNT(noKta) AS DATA FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND MONTH(agtTglInsert)='12' ".$param." AND YEAR(agtTglInsert)=YEAR(NOW())
    )AS temp";

    $query = $this->db->query($sql);
    return $query->result();
  }

  public function getManUsia($db_condition){
    $param = $db_condition;
    $sql = "SELECT `name`,`data` FROM (
        SELECT '0-5' AS `name`, CONCAT('-', COUNT(noKta)) AS `data` FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND (agtUmur BETWEEN 0 AND 5) ".$param."
      UNION 
        SELECT '5-17' AS `name`, CONCAT('-', COUNT(noKta)) AS `data` FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND (agtUmur BETWEEN 6 AND 17) ".$param."
      UNION 
        SELECT '17-30' AS `name`, CONCAT('-', COUNT(noKta)) AS `data` FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND (agtUmur BETWEEN 18 AND 30) ".$param."
      UNION 
        SELECT '30-60' AS `name`, CONCAT('-', COUNT(noKta)) AS `data` FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND (agtUmur BETWEEN 31 AND 60) ".$param."
      UNION 
        SELECT '60+' AS `name`, CONCAT('-', COUNT(noKta)) AS `data` FROM jakpwt_anggota WHERE agtJnsKelamin= 'L' AND agtUmur > 60 ".$param."
    )AS temp";
    
    $query = $this->db->query($sql);
    return $query->result();
  }

  public function getWomanUsia($db_condition){
    $param = $db_condition;
    $sql = "SELECT  `name`,`data` FROM (
        SELECT '0-5' AS `name`, CONCAT('+',COUNT(noKta)) AS `data` FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND (agtUmur BETWEEN 0 AND 5) ".$param." 
      UNION 
        SELECT '5-17' AS `name`, CONCAT('+',COUNT(noKta)) AS `data` FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND (agtUmur BETWEEN 6 AND 17) ".$param."
      UNION 
        SELECT '17-30' AS `name`, CONCAT('+',COUNT(noKta)) AS `data` FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND (agtUmur BETWEEN 18 AND 30) ".$param."
      UNION 
        SELECT '30-60' AS `name`, CONCAT('+',COUNT(noKta)) AS `data` FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND (agtUmur BETWEEN 31 AND 60) ".$param."
      UNION 
        SELECT '60+' AS `name`, CONCAT('+',COUNT(noKta)) AS `data` FROM jakpwt_anggota WHERE agtJnsKelamin= 'P' AND agtUmur > 60 ".$param." 
    )AS temp";
    
    $query = $this->db->query($sql);
    return $query->result();
  }

  public function getStatistikPekerjaan($db_condition){
    $param = $db_condition;
    $sql = "SELECT pkjNama as name, IF(jml <> '', jml, 0) AS data FROM nagari_ref_pekerjaan
    LEFT JOIN (SELECT pkjIdPekerjaan AS pkjId, COUNT(id) AS jml FROM  nagari_ref_pekerjaan
    LEFT JOIN jakpwt_anggota ON pkjIdPekerjaan = agtIdPekerjaan WHERE 1=1 ".$param." GROUP BY agtIdPekerjaan)AS a ON a.pkjId = pkjIdPekerjaan GROUP BY pkjNama ORDER BY jml DESC";
    $query = $this->db->query($sql);
    return $query->result_array();
  }

  public function getGender($gender,$db_condition){
    $sql = "SELECT count(noKta) as total FROM jakpwt_anggota WHERE agtJnsKelamin='".$gender."' ".$db_condition." ";
    $query = $this->db->query($sql);
    return $query->result_array();
  }

  public function getDataAnggotaUnFilteredByNoKta($noKta){
    $query = 
    "SELECT 
      $this->table_name.*,
      dikPendidikan, pkjNama, wilNama,
      if(agtJnsKelamin = 'L', 'Laki-laki', 'Perempuan') AS jnsKelamin
    FROM $this->table_name
      LEFT JOIN nagari_ref_pendidikan ON `dikIdPendidikan`=`agtIdPendidikan`
      LEFT JOIN nagari_ref_pekerjaan ON `pkjIdPekerjaan`=`agtIdPekerjaan`
      LEFT JOIN jakpwt_ref_wilayah ON `wilIdWilayah`=`agtIdWilayah`
    WHERE 
      noKta = '$nokta'
    GROUP BY noKta";
    $result = $this->db->query($query)->row();
    return $result;
  }
}
