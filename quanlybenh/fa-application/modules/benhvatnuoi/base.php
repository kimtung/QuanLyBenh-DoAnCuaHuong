<?php
NAMESPACE FA\MODULES\M_BENHVATNUOI;
USE \FA\CORE AS CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

Class base Extends CORE\FA_Controller
{
	public function a()
	{
		$this->load->model('diseases');
		$data['DSS'] = $this->model->diseases;
		$this->load->data('title', 'Trang chủ');
		$this->load->view('home', $data);
	}
    
}