<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Beranda extends MY_Controller {
	public function view() {
		$data['title'] = 'Beranda';
		$this->layout->set_layout('beranda/view_beranda', $data);
	}
}
