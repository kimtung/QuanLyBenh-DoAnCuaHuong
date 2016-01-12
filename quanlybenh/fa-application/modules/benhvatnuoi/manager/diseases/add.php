<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */

$this->load->helper('youtube');

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
$DSS = $this->model->diseases;
$DSS_GR = $this->model->diseases_group;
$SPC = $this->model->species;

if (isset($_POST['submit-add']))
{
    $gid = (int)$this->input->post('gid');
    $name = trim($this->input->post('name', true));
    $scientific_name = trim($this->input->post('scientific_name', true));
    $video = trim($this->input->post('video'));
    $symptoms = trim($this->input->post('symptoms'));
    $lesions = trim($this->input->post('lesions'));
    $treatments = trim($this->input->post('treatments'));
    $prevention = trim($this->input->post('prevention'));
    $related = trim($this->input->post('related'));
    $description = trim($this->input->post('description'));
    if (!$gid)
    {
        $this->load->message(MSG_ERROR, 'Bạn cần chọn một nhóm bệnh');
    }
    elseif (!$DSS_GR->id_exists($gid))
    {
        $this->load->message(MSG_ERROR, 'Nhóm bệnh đã chọn không tồn tại');
    }
    elseif (!$name)
    {
        $this->load->message(MSG_ERROR, 'Bạn cần nhập tên bệnh');
    }
    elseif (strlen($name) > 500)
    {
        $this->load->message(MSG_ERROR, 'Tên bệnh quá dài');
    }
    elseif ($DSS->name_exists($name))
    {
        $this->load->message(MSG_ERROR, 'Tên bệnh đã tồn tại');
    }
    elseif ($scientific_name && strlen($scientific_name) > 500)
    {
        $this->load->message(MSG_ERROR, 'Tên khoa học của bệnh quá dài');
    }
    /* elseif ($video && !valid_youtube_url($video))
    {
        $this->load->message(MSG_ERROR, 'Không đúng định dạng link video từ Youtube');
    } */
    else
    {
        $insert['gid'] = $gid;
        $insert['name'] = $name;
        $insert['scientific_name'] = $scientific_name;
        $insert['video'] = $video;
        $insert['symptoms'] = $symptoms;
        $insert['lesions'] = $lesions;
        $insert['treatments'] = $treatments;
        $insert['prevention'] = $prevention;
        $insert['related'] = $related;
        $insert['description'] = $description;
        $id = $DSS->insert($insert);
        if (!$id)
        {
            $this->load->message(MSG_ERROR, 'Lỗi trong khi thêm dữ liệu');
        }
        else
        {
            $this->load->message(MSG_SUCCESS, 'Đã thêm bệnh mới');

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
                        $DSS->update($id, $update);
                    }
                }
            }
        }
    }
}

$data['BR'] = $BR;
$data['DSS'] = $DSS;
$data['DSS_GR'] = $DSS_GR;
$data['SPC'] = $SPC;
$this->load->data('title', 'Thêm bệnh mới');
$this->load->view('manager/diseases/add', $data);