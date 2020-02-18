<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Layout {

  protected $CI;

  public function __construct(){
    $this->CI =& get_instance();
  }

  public function set_layout($page, $_data){
    $data = array(
      'content' => $this->CI->load->view($page, $_data, true)
    );

    $this->CI->load->view('main/layout-document-full', $data);
  }
}
