<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */
/**
 * @var int $action_id
 */
if (empty($action_id))
{
    redirect(BASE_URL . 'manager/species');
}

$species_id = $action_id;

/**
 * Load species model
 */
$this->load->model('species');
/**
 * @var \FA\MODELS\M_BENHVATNUOI\species $SPC
 */
$SPC = $this->model->species;

if (!$SPC->id_exists($species_id))
{
    redirect(BASE_URL . 'manager/accounts');
}

$species = $SPC->tuning($SPC->data($species_id));

if (isset($_POST['submit-delete']))
{
    if (!$SPC->delete($species_id))
    {
        $this->load->message(MSG_ERROR, 'Lỗi trong khi xóa, vui lòng thử lại sau');
    }
    else
    {
        if ($species['full_path_thumbnail'] && file_exists($species['full_path_thumbnail']))
        {
            @unlink($species['full_path_thumbnail']);
        }
        redirect(BASE_URL . 'manager/species');
    }
}

$data['species'] = $species;
$this->load->data('title', 'Xóa loài');
$this->load->view('manager/species/delete', $data);