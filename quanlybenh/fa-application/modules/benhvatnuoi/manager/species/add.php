<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */

/**
 * Load species model
 */
$this->load->model('species');

/**
 * @var \FA\MODELS\M_BENHVATNUOI\species $SPC
 */
$SPC = $this->model->species;

if (isset($_POST['submit-add']))
{
    $name = $this->input->post('name', true);
    $description = $this->input->post('description');
    if (!$name)
    {
        $this->load->message(MSG_ERROR, 'Bạn cần nhập tên loài');
    }
    elseif (strlen($name) > 500)
    {
        $this->load->message(MSG_ERROR, 'Tên loài quá dài');
    }
    elseif ($SPC->name_exists($name))
    {
        $this->load->message(MSG_ERROR, 'Tên loài đã tồn tại');
    }
    else
    {
        $insert['name'] = $name;
        $insert['description'] = $description;
        $id = $SPC->insert($insert);
        if (!$id)
        {
            $this->load->message(MSG_ERROR, 'Lỗi trong khi thêm dữ liệu');
        }
        else
        {
            $this->load->message(MSG_SUCCESS, 'Đã thêm loài');

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
                        $SPC->update($id, $update);
                    }
                }
            }
        }
    }
}

$this->load->data('title', 'Thêm loài');
$this->load->view('manager/species/add');