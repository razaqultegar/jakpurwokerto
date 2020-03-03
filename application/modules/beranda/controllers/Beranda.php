<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Beranda extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('anggota/m_anggota');
		$this->load->model('kas/m_kas');
	}

	public function json_anggota(){
		$m = $this->m_anggota->getMan($this->db_condition);
		$w = $this->m_anggota->getWoman($this->db_condition);
		foreach ($m as $value) {
			$man['name'] = 'Laki-laki';
			$man['data'][] = $value->data; 
		}

		foreach ($w as $value) {
			$woma['name'] = 'Perempuan';
			$woma['data'][] = $value->data; 
		}

		$response = [
			$anggota[] = $man,
			$anggota[] = $woma,
		];

		$json = json_encode($anggota, JSON_NUMERIC_CHECK);
		print_r($json);
	}

	public function json_anggota_usia(){
		$m = $this->m_anggota->getManUsia($this->db_condition);
		$w = $this->m_anggota->getWomanUsia($this->db_condition);
		foreach ($m as $value) {
			$man['name'] = 'Laki-laki';
			$man['data'][] = $value->data; 
		}

		foreach ($w as $value) {
			$woma['name'] = 'Perempuan';
			$woma['data'][] = $value->data; 
		}
		
		$response = [
			$anggota[] = $man,
			$anggota[] = $woma,
		];

		$json = json_encode($anggota, JSON_NUMERIC_CHECK);
		print_r($json);
	}

	public function json_pendidikan(){
		$count_pddk = $this->m_statistik_pendidikan->getCountPddk($this->db_condition);
		$json = json_encode($count_pddk, JSON_NUMERIC_CHECK);
		print_r($json);
	}

	public function index() {
		$data['total_anggota'] = $this->m_anggota->getTotal($this->db_condition);
		$kas = $this->m_kas->getData();
		$total_kas = 0;        
		foreach ($kas as $key => $value) {
			$total_kas += $value['kasSaldo'];
		}
		$data['total_kas'] = $total_kas;
		$data['json_agt'] = site_url('beranda/json_anggota');
		// $data['json_pekerjaan'] = site_url('statistik_keanggotaan/json_pekerjaan');
		$data['json_agt_usia'] = site_url('beranda/json_anggota_usia');
		$l = $this->m_anggota->getGender('L', $this->db_condition);
		$p = $this->m_anggota->getGender('P', $this->db_condition);
		$data['total_laki'] = $l[0]['total'];
		$data['total_perempuan'] = $p[0]['total'];
		// $data['belum_sekolah'] = $this->m_statistik_pendidikan->getTotalStatistikPddk($val, '2');
		// $data['belum_tamat'] = $this->m_statistik_pendidikan->getTotalStatistikPddk($val,'10');
		// $data['sd'] = $this->m_statistik_pendidikan->getTotalStatistikPddk($val, '3');
		// $data['sltp'] = $this->m_statistik_pendidikan->getTotalStatistikPddk($val, '4');
		// $data['slta'] = $this->m_statistik_pendidikan->getTotalStatistikPddk($val, '5');
		// $data['d1_d2'] = $this->m_statistik_pendidikan->getTotalStatistikPddk($val, '6');
		// $data['d3'] = $this->m_statistik_pendidikan->getTotalStatistikPddk($val, '11');
		// $data['s1'] = $this->m_statistik_pendidikan->getTotalStatistikPddk($val, '9');
		// $data['total_pendidikan'] = $this->m_statistik_pendidikan->getTotalPendudukPddk($val);
		
		$data['title'] = 'Beranda';
		$this->layout->set_layout('beranda/view_beranda', $data);
	}
}
