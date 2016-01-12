<?php
NAMESPACE FA\MODULES\M_BENHVATNUOI;
USE \FA\CORE AS CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class search
 * @package FA\MODULES\M_BENHVATNUOI
 */
Class search Extends CORE\FA_Controller
{
    public function index()
    {
        $this->load->model('diseases');
        $DSS = $this->model->diseases;

        $data['keyword'] = '';
        $keyword = $this->input->get('keyword', true);
        if ($keyword)
        {
            $data['keyword'] = $keyword;
            $data['list_keyword'] = explode(' ', $keyword);
        }

        $page = $this->input->get('page') ? (int) $this->input->get('page') : 1;
        $data['page'] = $page;
        $data['page_url'] = BASE_URL . 'search?keyword=' . urlencode($keyword) . '&page={page}';

        $data['DSS'] = $DSS;
        $this->load->data('title', 'Tìm kiếm');
        $this->load->view('search', $data);
    }
}