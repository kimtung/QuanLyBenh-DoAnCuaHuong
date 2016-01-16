<?php
NAMESPACE FA\MODULES\M_BENHVATNUOI;
USE \FA\CORE AS CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class group
 * @package FA\MODULES\M_BENHVATNUOI
 */
Class group Extends CORE\FA_Controller
{
    public function index($id)
    {
        $id = (int)$id;
        if (!$id)
        {
            redirect(BASE_URL . 'search');
        }

        /**
         * Load account model
         */
        $this->load->model(array('diseases_group', 'diseases'));
        /**
         * @var \FA\MODELS\M_BENHVATNUOI\diseases_group $DSS_GR
         * @var \FA\MODELS\M_BENHVATNUOI\diseases $DSS
         */
        $DSS_GR = $this->model->diseases_group;
        $DSS = $this->model->diseases;

        if (!$DSS_GR->id_exists($id))
        {
            redirect(BASE_URL . 'search');
        }

        $dss_gr = $DSS_GR->data($id);

        $page = $this->input->get('page') ? (int) $this->input->get('page') : 1;
        $data['page'] = $page;
        $data['page_url'] = BASE_URL . 'group/' . $id . '?page={page}';

        $data['group_id'] = $id;
        $data['dss_gr'] = $dss_gr;
        $data['DSS_GR'] = $DSS_GR;
        $data['DSS'] = $DSS;
        $this->load->data('title', $dss_gr['name']);
        $this->load->view('group', $data);
    }
}