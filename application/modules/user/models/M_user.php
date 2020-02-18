<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_user extends CI_Model {
    public function getDataById($id){
        $sql = "SELECT 
                    u.userId,
                    u.realname,
                    u.username,
                    u.password,
                    u.foto
                FROM
                    default_user u
                WHERE u.userId='$id'";
        $query = $this->db->query($sql);
        return $query->row();  
    }
}
