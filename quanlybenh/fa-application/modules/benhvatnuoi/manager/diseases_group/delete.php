<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */
/**
 * @var int $action_id
 */
if (empty($action_id))
{
    redirect(BASE_URL . 'manager/diseases_group');
}

$dss_id = $action_id;

/**
 * Load account model
 */
$this->load->model(array('diseases_group', 'breed', 'species'));
/**
 * @var \FA\MODELS\M_BENHVATNUOI\diseases_group $DSS_GR
 * @var \FA\MODELS\M_BENHVATNUOI\breed $BR
 * @var \FA\MODELS\M_BENHVATNUOI\species $SPC
 */
$BR = $this->model->breed;
$DSS_GR = $this->model->diseases_group;
$SPC = $this->model->species;

/**
 * Check breed already exists
 */
if (!$DSS_GR->id_exists($dss_id))
{
    redirect(BASE_URL . 'manager/diseases_group');
}

/**
 * Get diseases group data
 */
$dss = $DSS_GR->data($dss_id);
$dss = $DSS_GR->tuning($dss);

if (isset($_POST['submit-delete']))
{
    if (!$DSS_GR->delete($dss_id))
    {
        $this->load->message(MSG_ERROR, 'Lỗi trong khi xóa, vui lòng thử lại sau');
    }
    else
    {
        if ($dss['full_path_thumbnail'] && file_exists($dss['full_path_thumbnail']))
        {
            @unlink($dss['full_path_thumbnail']);
        }
        redirect(BASE_URL . 'manager/diseases_group');
    }
}

$data['dss'] = $dss;
$data['BR'] = $BR;
$data['DSS_GR'] = $DSS_GR;
$data['SPC'] = $SPC;
$this->load->data('title', 'Xóa nhóm bệnh');
$this->load->view('manager/diseases_group/delete', $data);