<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
  public $realname;
  protected $db_condition = '';

  public function __construct() {
    parent::__construct();
    // session
    if ($this->session->userdata('isLogin') == FALSE) {
      redirect('masuk');
    }
    
    // data header statis
    $this->user_id = $this->session->userId;
    $this->username = $this->user_active()->username;
    $this->realname = $this->user_active()->realname;
    $this->foto = $this->user_active()->foto;

    // layout
    $this->load->library('layout'); 

    // load model
    $this->load->model('masuk/m_masuk');

    // time setting
    date_default_timezone_set('Asia/Jakarta');
    $this->now = date("Y-m-d");
    $this->url_profil = site_url('profil');
    $this->foto_profil = base_url().'files/akun/default.jpg';
  }

  public function format_tanggal($date){
    $BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

    $tahun = substr($date, 0, 4);
    $bulan = substr($date, 5, 2);
    $tgl   = substr($date, 8, 2);

    $result = isset($BulanIndo[(int)$bulan-1]) ? $tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun : '';
    return($result);
  }

  public function format_bulan($date){
    $BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

    $tahun = substr($date, 0, 4);
    $bulan = substr($date, 5, 2);
    $tgl   = substr($date, 8, 2);

    $result = $BulanIndo[(int)$bulan-1] . " ". $tahun;
    return($result);
  }

  public function format_angka($angka) {
    $frmt = "Rp ".number_format($angka, 0, ',', '.');
    return $frmt;
  }

  private function user_active() {
    $this->load->model('pengguna/m_pengguna');
    $result = $this->m_pengguna->getDataById($this->user_id);
    return $result;
  }

  private function profil() {
    $this->load->model('profil/m_profil');
    $result = $this->m_profil->getDataByUser($this->user_id);
    return $result;
  }
}
?>
