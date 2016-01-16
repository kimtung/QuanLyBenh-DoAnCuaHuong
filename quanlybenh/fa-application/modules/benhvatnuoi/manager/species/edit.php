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
    redirect(BASE_URL . 'manager/species');
}

$species = $SPC->data($species_id);
$species = $SPC->tuning($species);

if (isset($_POST['submit-edit']))
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
    elseif ($SPC->name_exists($name, $species_id))
    {
        $this->load->message(MSG_ERROR, 'Tên loài đã tồn tại');
    }
    else
    {
        $update['name'] = $name;
        $update['description'] = $description;
        $id = $SPC->update($species_id, $update);
        if (!$id)
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
                        if ($u['thumbnail'] != $species['thumbnail'] && file_exists($species['full_path_thumbnail']))
                        {
                            @unlink($species['full_path_thumbnail']);
                        }
                        $SPC->update($species_id, $u);
                    }
                }
            }

            /**
             * Reset data
             */
            $species = $SPC->data($species_id);
            $species = $SPC->tuning($species);
        }
    }
}

$data['species'] = $species;
$data['SPC'] = $SPC;
$this->load->data('title', 'Chỉnh sửa loài');
$this->load->view('manager/species/edit', $data);