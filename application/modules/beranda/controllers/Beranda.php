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

	public function index() {
		$data['total_anggota'] = $this->m_anggota->getTotal($this->db_condition);
		$kas = $this->m_kas->getData();
		$total_kas = 0;        
		foreach ($kas as $key => $value) {
			$total_kas += $value['kasSaldo'];
		}
		$data['total_kas'] = $total_kas;
		$data['json_agt'] = site_url('beranda/json_anggota');
		$data['json_agt_usia'] = site_url('beranda/json_anggota_usia');
		$l = $this->m_anggota->getGender('L', $this->db_condition);
		$p = $this->m_anggota->getGender('P', $this->db_condition);
		$data['total_laki'] = $l[0]['total'];
		$data['total_perempuan'] = $p[0]['total'];
		
		$data['title'] = 'Beranda';
		$this->layout->set_layout('beranda/view_beranda', $data);
	}
}
