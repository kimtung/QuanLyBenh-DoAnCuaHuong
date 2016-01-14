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
            redirect(BASE_URL . 'disease/species');
        }

        $dss = $DSS->data($id);
        $dss = $DSS->tuning($dss);
        $data['DSS'] = $DSS;
        $data['dss'] = $dss;
        $this->load->data('title', $dss['name']);
        $this->load->view('disease', $data);
    }

    public function species($q='')
    {
        //$speciesId->breedsId -> diseases_groupId -> diseasesId
        $list_key = array();
        $list_key['gia_suc'] = "Gia súc";
        $list_key['gia_cam'] = "Gia cầm";
        $list_key['khac'] = "Khác";

        $this->load->helper('youtube');

        $this->load->model('diseases');
        /**
         * @var \FA\MODELS\M_BENHVATNUOI\diseases $DSS
         */
        $DSS = $this->model->diseases;
        if (array_key_exists($q, $list_key)) {
            $keyword = $list_key[$q];
            $data['keyword'] = $keyword;
            $data['q'] = $q;
            $data['keyword_data'] = "('".$keyword."')";
            if($q == 'khac'){
                $data['keyword_data'] = "('".$list_key['gia_suc']."','".$list_key['gia_cam']."')";
            }
        }

        $page = $this->input->get('page') ? (int) $this->input->get('page') : 1;
        $data['page'] = $page;
        $data['page_url'] = BASE_URL . 'disease/species/'.$q.'?page={page}';

        $data['DSS'] = $DSS;
        $this->load->data('title','Tungck');
        $this->load->view('type_disease', $data);
    }
}