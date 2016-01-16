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

if (isset($_POST['submit-edit']))
{
    $sid = (int)$this->input->post('sid');
    $name = trim($this->input->post('name', true));
    $description = trim($this->input->post('description'));
    if (!$sid)
    {
        $this->load->message(MSG_ERROR, 'Bạn cần chọn loài cho giống mới');
    }
    elseif (!$SPC->id_exists($sid))
    {
        $this->load->message(MSG_ERROR, 'Loài này không tồn tại');
    }
    elseif (!$name)
    {
        $this->load->message(MSG_ERROR, 'Bạn cần nhập tên giống');
    }
    elseif (strlen($name) > 500)
    {
        $this->load->message(MSG_ERROR, 'Tên giống quá dài');
    }
    elseif ($BR->name_exists($name, $breed_id))
    {
        $this->load->message(MSG_ERROR, 'Tên giống đã tồn tại');
    }
    else
    {
        $update['sid'] = $sid;
        $update['name'] = $name;
        $update['description'] = $description;
        if (!$BR->update($breed_id, $update))
        {
            $this->load->message(MSG_ERROR, 'Lỗi trong khi lưu dữ liệu');
        }
        else
        {
            $this->load->message(MSG_SUCCESS, 'Đã lưu thay đổi');

            if (!empty($_FILES['thumbnail']['name']))
            {
                $this->load->helper(array('file', 'upload'));

                $path = APP_PATH . 'uploads';
                $sub_dir = mmdir($path);
                if (!$sub_dir)
                {
                    $this->load->message(MSG_ERROR, 'Không thể tạo thư mục con để upload');
                }
                else
                {
                    $path = $path . '/' . $sub_dir;
                    $this->load->library('seo');
                    $base_file_name = $this->lib->seo->url($name);
                    $file_name = upload_file($path, $_FILES['thumbnail'], $base_file_name, false);
                    if (!$file_name)
                    {
                        $this->load->message(MSG_ERROR, 'Không thể upload ảnh minh họa');
                    }
                    else
                    {
                        $u['thumbnail'] = $sub_dir . '/' . $file_name;

                        if ($u['thumbnail'] != $breed['thumbnail'] && file_exists($breed['full_path_thumbnail']))
                        {
                            @unlink($breed['full_path_thumbnail']);
                        }

                        $BR->update($breed_id, $u);
                    }
                }
            }

            /**
             * Reset data
             */
            $breed = $BR->data($breed_id);
            $breed = $BR->tuning($breed);
        }
    }
}

$data['breed'] = $breed;
$data['BR'] = $BR;
$data['SPC'] = $SPC;
$this->load->data('title', 'Chỉnh sửa giống');
$this->load->view('manager/breeds/edit', $data);