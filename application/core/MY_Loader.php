<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."third_party/MX/Loader.php";

class MY_Loader extends MX_Loader {
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
}