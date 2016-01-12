<?php
NAMESPACE FA\MODULES\M_BENHVATNUOI;
USE \FA\CORE AS CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class disease
 * @package FA\MODULES\M_BENHVATNUOI
 */
Class disease Extends CORE\FA_Controller
{
    public function index($id)
    {
        $this->load->helper('youtube');

        $this->load->model('diseases');
        /**
         * @var \FA\MODELS\M_BENHVATNUOI\diseases $DSS
         */
        $DSS = $this->model->diseases;

        $id = (int) $id;

        if (!$id || !$DSS->id_exists($id))
        {
            redirect(BASE_URL . 'search');
        }

        $dss = $DSS->data($id);
        $dss = $DSS->tuning($dss);

        $data['DSS'] = $DSS;
        $data['dss'] = $dss;
        $this->load->data('title', $dss['name']);
        $this->load->view('disease', $data);
    }
}