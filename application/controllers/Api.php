<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
	}

	// API untuk get provinces
	public function get_provinces() {
		header('Content-Type: application/json');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://wilayah.id/api/provinces.json');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$response = curl_exec($ch);
		curl_close($ch);
		
		if ($response) {
			echo $response;
		} else {
			echo json_encode(['data' => []]);
		}
	}

	// API untuk get regencies by province
	public function get_regencies($province_code = null) {
		header('Content-Type: application/json');
		$province_code = $this->input->get('province_code') ? $this->input->get('province_code') : $province_code;
		
		if (!$province_code) {
			echo json_encode(['data' => []]);
			return;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://wilayah.id/api/regencies/'.$province_code.'.json');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$response = curl_exec($ch);
		curl_close($ch);
		
		if ($response) {
			$data = json_decode($response, true);
			if (isset($data['data']) && is_array($data['data'])) {
				// Hilangkan prefix "Kabupaten", "Kota", "Kota Administrasi"
				foreach ($data['data'] as &$item) {
					$item['name'] = preg_replace('/^(Kabupaten|Kota Administrasi|Kota)\s+/', '', $item['name']);
				}
			}
			echo json_encode($data);
		} else {
			echo json_encode(['data' => []]);
		}
	}

	// API untuk get districts by regency
	public function get_districts($regency_code = null) {
		header('Content-Type: application/json');
		$regency_code = $this->input->get('regency_code') ? $this->input->get('regency_code') : $regency_code;
		
		if (!$regency_code) {
			echo json_encode(['data' => []]);
			return;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://wilayah.id/api/districts/'.$regency_code.'.json');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$response = curl_exec($ch);
		curl_close($ch);
		
		if ($response) {
			echo $response;
		} else {
			echo json_encode(['data' => []]);
		}
	}

	// API untuk get villages by district
	public function get_villages($district_code = null) {
		header('Content-Type: application/json');
		$district_code = $this->input->get('district_code') ? $this->input->get('district_code') : $district_code;
		
		if (!$district_code) {
			echo json_encode(['data' => []]);
			return;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://wilayah.id/api/villages/'.$district_code.'.json');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$response = curl_exec($ch);
		curl_close($ch);
		
		if ($response) {
			echo $response;
		} else {
			echo json_encode(['data' => []]);
		}
	}
}
