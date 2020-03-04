<?php
class MY_Model extends CI_Model {
  protected $db_condition = '';

  public function __construct() {
    parent::__construct();
    // data header statis
    $this->user_id = $this->session->userdata('userId');
    $this->db_condition = "";
  }
}
?>
