<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */
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

if (isset($_POST['submit-add']))
{
    $bid = (int)$this->input->post('bid');
    $name = trim($this->input->post('name', true));
    $description = trim($this->input->post('description'));
    if (!$bid)
    {
        $this->load->message(MSG_ERROR, 'Bạn cần chọn giống cho nhóm bệnh mới');
    }
    elseif (!$BR->id_exists($bid))
    {
        $this->load->message(MSG_ERROR, 'Giống đã chọn không tồn tại');
    }
    elseif (!$name)
    {
        $this->load->message(MSG_ERROR, 'Bạn cần nhập tên nhóm bệnh');
    }
    elseif (strlen($name) > 500)
    {
        $this->load->message(MSG_ERROR, 'Tên nhóm bệnh quá dài');
    }
    elseif ($DSS_GR->name_exists($name))
    {
        $this->load->message(MSG_ERROR, 'Tên nhóm bệnh đã tồn tại');
    }
    else
    {
        $insert['bid'] = $bid;
        $insert['name'] = $name;
        $insert['description'] = $description;
        $id = $DSS_GR->insert($insert);
        if (!$id)
        {
            $this->load->message(MSG_ERROR, 'Lỗi trong khi thêm dữ liệu');
        }
        else
        {
            $this->load->message(MSG_SUCCESS, 'Đã thêm nhóm bệnh mới');

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
                        $DSS_GR->update($id, $update);
                    }
                }
            }
        }
    }
}

$data['BR'] = $BR;
$data['DSS_GR'] = $DSS_GR;
$data['SPC'] = $SPC;
$this->load->data('title', 'Thêm nhóm bệnh mới');
$this->load->view('manager/diseases_group/add', $data);