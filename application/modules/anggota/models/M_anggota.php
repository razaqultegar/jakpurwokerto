<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class M_anggota extends MY_Model
{
	protected $table_name = 'jakpwt_anggota';

	public function queryBase()
	{
		$query = "SELECT $this->table_name.*, wilNama,
				if(agtJnsKelamin = 'L', 'Laki-laki', 'Perempuan') AS jnsKelamin
			FROM $this->table_name
				LEFT JOIN jakpwt_ref_wilayah ON `wilIdWilayah` = `agtIdWilayah`
			WHERE 1 = 1 " . $this->db_condition;

		return $query;
	}

	private function query()
	{
		$query = "SELECT * FROM ( ";
		$query .= $this->queryBase();
		$query .= " ) AS temp_table ";
		return $query;
	}

	public function getListData($options = [])
	{
		$where_like = empty($options['where_like'])
			? ''
			: 'AND (' . implode(' AND ', $options['where_like']) . ')';
		if (!isset($options['condition'])) $options['condition'] = ' ';

		$sql = $this->query() . " WHERE 1 = 1 " . $where_like . $options['condition'] . "
			GROUP BY agtId ORDER BY " . $options['order'] . " " . $options['mode'] . "
			LIMIT " . $options['offset'] . ", " . $options['limit'];
		$query = $this->db->query($sql);

		return $query->result();
	}

	public function getTotalData($options)
	{
		$where_like = empty($options['where_like'])
			? ''
			: 'AND (' . implode(' AND ', $options['where_like']) . ')';
		if (!isset($options['condition'])) $options['condition'] = ' ';

		$sql = "SELECT COUNT(DISTINCT agtId) AS total FROM ( ";
		$sql .= $this->queryBase();
		$sql .= " ) AS temp_table WHERE 1 = 1 " . $where_like . $options['condition'];
		$query = $this->db->query($sql)->row();

		return $query->total;
	}

	public function getTotal($options = '')
	{
		$sql = "SELECT COUNT(DISTINCT agtId) AS total FROM ( ";
		$sql .= $this->queryBase();
		$sql .= ") AS temp_table WHERE 1 = 1 " . $options;
		$query = $this->db->query($sql)->row();

		return $query->total;
	}

	public function addDataAction($data)
	{
		$result = $this->db->insert($this->table_name, $data);
		$result = $this->db->insert_id();
		return $result;
	}

	public function getDataById($id)
	{
		$sql = $this->query() . " WHERE agtId = '$id'";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	public function cekDuplicateNoKta($id)
	{
		$sql = $this->query() . " WHERE agtNoKTA = '$id'";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	public function getCombo($id)
	{
		if (!empty($id)) {
			$param = "WHERE agtNama LIKE '%" . $id . "%'";
		} else {
			$param = "";
		}

		$sql = "SELECT agtNoKTA as id, agtNama as name FROM jakpwt_anggota " . $param;
		$query = $this->db->query($sql);

		return $query->result_array();
	}

	public function editDataAction($data, $where)
	{
		$result = $this->db->update($this->table_name, $data, $where);
		return $result;
	}

	public function deleteImage($id)
	{
		return $this->db->get_where($this->table_name, array('agtId' => $id));
	}

	public function delete($where)
	{
		$result = $this->db->where($where)->delete($this->table_name);
		return $result;
	}

	public function getDataExcel($options = [])
	{
		$where_like = empty($options['where_like'])
			? '1 = 1'
			: '(' . implode(' AND ', $options['where_like']) . ')';

		$sql = $this->query() . " WHERE 1 = 1 AND " . $where_like . $options['condition'];
		$query = $this->db->query($sql);

		return $query->result();
	}

	private function _getMonthlyGenderStats($gender, $db_condition)
	{
		$months = array(
			1 => 'Jan',
			2 => 'Feb',
			3 => 'Mar',
			4 => 'April',
			5 => 'Mei',
			6 => 'Juni',
			7 => 'Juli',
			8 => 'Agustus',
			9 => 'September',
			10 => 'Oktober',
			11 => 'November',
			12 => 'Desember'
		);

		$unions = array();
		foreach ($months as $month => $name) {
			$unions[] = "SELECT '" . $name . "' AS NAME, COUNT(agtId) AS DATA 
				FROM jakpwt_anggota 
				WHERE agtJnsKelamin = '" . $gender . "' 
					AND MONTH(agtTglInsert) = '" . $month . "' " . $db_condition . " 
					AND YEAR(agtTglInsert) = YEAR(NOW())";
		}

		$sql = "SELECT name, data FROM (" . implode(" UNION ", $unions) . ") AS temp";
		$query = $this->db->query($sql);

		return $query->result();
	}

	public function getMan($db_condition)
	{
		return $this->_getMonthlyGenderStats('L', $db_condition);
	}

	public function getWoman($db_condition)
	{
		return $this->_getMonthlyGenderStats('P', $db_condition);
	}

	private function _getAgeRangeStats($gender, $db_condition)
	{
		$age_ranges = array(
			'0-5' => array('min' => 0, 'max' => 5),
			'5-17' => array('min' => 6, 'max' => 17),
			'17-30' => array('min' => 18, 'max' => 30),
			'30-60' => array('min' => 31, 'max' => 60),
			'60+' => array('min' => 61, 'max' => 999)
		);

		$unions = array();
		foreach ($age_ranges as $range => $values) {
			if ($values['max'] === 999) {
				$unions[] = "SELECT '" . $range . "' AS name, CONCAT('+', COUNT(agtId)) AS data 
						FROM jakpwt_anggota 
						WHERE agtJnsKelamin = '" . $gender . "' 
							AND agtUmur > " . $values['min'] . " " . $db_condition;
			} else {
				$unions[] = "SELECT '" . $range . "' AS name, CONCAT('+', COUNT(agtId)) AS data 
						FROM jakpwt_anggota 
						WHERE agtJnsKelamin = '" . $gender . "' 
							AND agtUmur BETWEEN " . $values['min'] . "
							AND " . $values['max'] . " " . $db_condition;
			}
		}

		$sql = "SELECT name, data FROM (" . implode(" UNION ", $unions) . ") AS temp";
		$query = $this->db->query($sql);

		return $query->result();
	}

	public function getManUsia($db_condition)
	{
		return $this->_getAgeRangeStats('L', $db_condition);
	}

	public function getWomanUsia($db_condition)
	{
		return $this->_getAgeRangeStats('P', $db_condition);
	}

	public function getGender($gender, $db_condition)
	{
		$sql = "SELECT count(agtId) as total FROM jakpwt_anggota
			WHERE agtJnsKelamin='" . $gender . "' " . $db_condition . "";
		$query = $this->db->query($sql);

		return $query->result_array();
	}
}
