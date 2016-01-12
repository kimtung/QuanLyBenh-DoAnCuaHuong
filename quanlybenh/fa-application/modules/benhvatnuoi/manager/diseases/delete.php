<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */
/**
 * @var int $action_id
 */
if (empty($action_id))
{
    redirect(BASE_URL . 'manager/diseases');
}

$dss_id = $action_id;

/**
 * Load account model
 */
$this->load->model(array('diseases', 'diseases_group', 'breed', 'species'));
/**
 * @var \FA\MODELS\M_BENHVATNUOI\diseases $DSS
 * @var \FA\MODELS\M_BENHVATNUOI\diseases_group $DSS_GR
 * @var \FA\MODELS\M_BENHVATNUOI\breed $BR
 * @var \FA\MODELS\M_BENHVATNUOI\species $SPC
 */
$BR = $this->model->breed;
$DSS_GR = $this->model->diseases_group;
$DSS = $this->model->diseases;
$SPC = $this->model->species;

/**
 * Check diseases already exists
 */
if (!$DSS->id_exists($dss_id))
{
    redirect(BASE_URL . 'manager/diseases');
}

/**
 * Get diseases data
 */
$dss = $DSS->data($dss_id);
$dss = $DSS->tuning($dss);

if (isset($_POST['submit-delete']))
{
    if (!$DSS->delete($dss_id))
    {
        $this->load->message(MSG_ERROR, 'Lỗi trong khi xóa, vui lòng thử lại sau');
    }
    else
    {
        if ($dss['full_path_thumbnail'] && file_exists($dss['full_path_thumbnail']))
        {
            @unlink($dss['full_path_thumbnail']);
        }
        redirect(BASE_URL . 'manager/diseases');
    }
}

$data['dss'] = $dss;
$data['BR'] = $BR;
$data['DSS_GR'] = $DSS_GR;
$data['DSS'] = $DSS;
$data['SPC'] = $SPC;
$this->load->data('title', 'Xóa bệnh');
$this->load->view('manager/diseases/delete', $data);