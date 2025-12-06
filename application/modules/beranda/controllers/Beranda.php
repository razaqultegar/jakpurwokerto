<?php defined('BASEPATH') or exit('No direct script access allowed');

class Beranda extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('anggota/m_anggota');
		$this->load->model('kas/m_kas');
	}

	public function index()
	{
		$data['title'] = 'Beranda';

		$data['total_anggota'] = $this->m_anggota->getTotal($this->db_condition);
		$man = $this->m_anggota->getGender('L', $this->db_condition);
		$woman = $this->m_anggota->getGender('P', $this->db_condition);
		$data['total_laki'] = !empty($man) && isset($man[0]['total'])
			? (int)$man[0]['total'] : 0;
		$data['total_perempuan'] = !empty($woman) && isset($woman[0]['total'])
			? (int)$woman[0]['total'] : 0;

		$kas = $this->m_kas->getData();
		$total_kas = 0;
		foreach ($kas as $value) {
			$total_kas += isset($value['kasSaldo']) ? (float)$value['kasSaldo'] : 0;
		}
		$data['total_kas'] = $total_kas;

		$data['json_agt'] = site_url('beranda/json_anggota');
		$data['json_agt_usia'] = site_url('beranda/json_anggota_usia');

		$this->layout->set_layout('beranda/view_beranda', $data);
	}

	public function json_anggota()
	{
		$man = $this->m_anggota->getMan($this->db_condition);
		$woman = $this->m_anggota->getWoman($this->db_condition);

		$series = [
			$this->buildSeries($man, 'Laki-laki'),
			$this->buildSeries($woman, 'Perempuan'),
		];

		$this->respondJson($series);
	}

	public function json_anggota_usia()
	{
		$man = $this->m_anggota->getManUsia($this->db_condition);
		$woman = $this->m_anggota->getWomanUsia($this->db_condition);

		$series = [
			$this->buildSeries($man, 'Laki-laki'),
			$this->buildSeries($woman, 'Perempuan'),
		];

		$this->respondJson($series);
	}

	private function buildSeries($rows, $label)
	{
		$series = [
			'name' => $label,
			'data' => [],
		];

		foreach ($rows as $row) {
			$value = null;

			if (is_object($row) && isset($row->data)) {
				$value = $row->data;
			} elseif (is_array($row) && isset($row['data'])) {
				$value = $row['data'];
			}

			$series['data'][] = $value !== null ? (float)$value : 0;
		}

		return $series;
	}

	private function respondJson($payload)
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($payload, JSON_NUMERIC_CHECK));
	}
}
