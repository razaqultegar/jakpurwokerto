<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

  public $realname;
  protected $db_condition = '';

  public function __construct() {
    parent::__construct();

    // session
    if ($this->session->userdata('isLogin') == FALSE) {
      redirect('login');
    }

    $this->nama_app = $this->personalisasi('S001')[0]['setValue'];
    $this->versi_aplikasi = $this->personalisasi('S002')[0]['setValue'];
    $this->logo_app = $this->personalisasi('S003')[0]['setValue'];
    $this->tahun_pembuatan = $this->personalisasi('S005')[0]['setValue'];
    $this->hak_cipta = $this->personalisasi('S006')[0]['setValue'];

    //data header statis
    $this->user_id = $this->session->userId;
    $this->username = $this->user_active()->username;
    $this->realname = $this->user_active()->realname;
    $this->foto = $this->user_active()->foto;

    $this->load->library('layout'); 

    // load model
    $this->load->model('login/m_login');

    // time setting
    date_default_timezone_set('Asia/Jakarta');
    $this->now = date("Y-m-d");

    $this->url_profil = site_url('profil/view');
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
    $frmt = "Rp ".number_format($angka, 2, ',', '.');
    return $frmt;
  }

  public function format_angka2($angka) {
    $frmt = number_format($angka, 0, ',', '.');
    return $frmt;
  }

  public function replace_spt($angka) {
    $str = str_replace('.', '', $angka);
    return $str;
  }
    
  private function personalisasi($kode) {
    $this->load->model('setting/m_setting');
    $result = $this->m_setting->getDataPersonalisasi($kode);
    return $result;
  }

  private function user_active() {
    $this->load->model('user/m_user');
    $result = $this->m_user->getDataById($this->user_id);
    return $result;
  }

  private function profil() {
    $this->load->model('profil/m_profil');
    $result = $this->m_profil->getDataByUser($this->user_id);
    return $result;
  }
}

?>