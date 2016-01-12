<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */

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

if (isset($_POST['submit-add']))
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
    elseif ($BR->name_exists($name))
    {
        $this->load->message(MSG_ERROR, 'Tên giống đã tồn tại');
    }
    else
    {
        $insert['sid'] = $sid;
        $insert['name'] = $name;
        $insert['description'] = $description;
        $id = $BR->insert($insert);
        if (!$id)
        {
            $this->load->message(MSG_ERROR, 'Lỗi trong khi thêm dữ liệu');
        }
        else
        {
            $this->load->message(MSG_SUCCESS, 'Đã thêm giống mới');

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
                    $file_name = upload_file($path, $_FILES['thumbnail'], $base_file_name);
                    if (!$file_name)
                    {
                        $this->load->message(MSG_ERROR, 'Không thể upload ảnh minh họa');
                    }
                    else
                    {
                        $update['thumbnail'] = $sub_dir . '/' . $file_name;
                        $BR->update($id, $update);
                    }
                }
            }
        }
    }
}

$data['BR'] = $BR;
$data['SPC'] = $SPC;
$this->load->data('title', 'Thêm giống mới');
$this->load->view('manager/breeds/add', $data);