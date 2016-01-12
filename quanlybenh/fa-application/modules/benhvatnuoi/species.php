<?php
NAMESPACE FA\MODULES\M_BENHVATNUOI;
USE \FA\CORE AS CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class species
 * @package FA\MODULES\M_BENHVATNUOI
 */
Class species Extends CORE\FA_Controller
{
    public function index($id)
    {
        /* $this->load->helper('youtube'); */

        $this->load->model('species');
        /**
         * @var \FA\MODELS\M_BENHVATNUOI\species $SPC
         */
        $SPC = $this->model->species;

        $id = (int) $id;

        if (!$id || !$SPC->id_exists($id))
        {
            redirect(BASE_URL . 'search');
        }

        $spc = $SPC->data($id);
        $spc = $SPC->tuning($spc);

        $data['SPC'] = $SPC;
        $data['spc'] = $spc;
        $this->load->data('title', $spc['name']);
        $this->load->view('species', $data);
    }
}