<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */
/**
 * @var int $action_id
 */
if (empty($action_id))
{
    redirect(BASE_URL . 'manager/breeds');
}

$breed_id = $action_id;

/**
 * Load breed, species model
 */
$this->load->model(array('breed', 'species'));

/**
 * @var \FA\MODELS\M_BENHVATNUOI\breed $BR
 * @var \FA\MODELS\M_BENHVATNUOI\species $SPC
 */
$BR = $this->model->breed;
$SPC = $this->model->species;

/**
 * Check breed already exists
 */
if (!$BR->id_exists($breed_id))
{
    redirect(BASE_URL . 'manager/breeds');
}

/**
 * Get breed data
 */
$breed = $BR->data($breed_id);
$breed = $BR->tuning($breed);

if (isset($_POST['submit-delete']))
{
    if (!$BR->delete($breed_id))
    {
        $this->load->message(MSG_ERROR, 'Lỗi trong khi xóa, vui lòng thử lại sau');
    }
    else
    {
        if ($breed['full_path_thumbnail'] && file_exists($breed['full_path_thumbnail']))
        {
            @unlink($breed['full_path_thumbnail']);
        }
        redirect(BASE_URL . 'manager/breeds');
    }
}

$data['breed'] = $breed;
$data['BR'] = $BR;
$data['SPC'] = $SPC;
$this->load->data('title', 'Xóa giống');
$this->load->view('manager/breeds/delete', $data);