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

/**
 * Check breed already exists
 */
if (!$DSS->id_exists($dss_id))
{
    redirect(BASE_URL . 'manager/diseases');
}

/**
 * Get diseases group data
 */
$dss = $DSS->data($dss_id);
$dss = $DSS->tuning($dss);

if (isset($_POST['submit-edit']))
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
    elseif ($DSS->name_exists($name, $dss_id))
    {
        $this->load->message(MSG_ERROR, 'Tên bệnh đã tồn tại');
    }
    elseif ($scientific_name && strlen($scientific_name) > 500)
    {
        $this->load->message(MSG_ERROR, 'Tên khoa học của bệnh quá dài');
    }
    elseif ($video && !valid_youtube_url($video))
    {
        $this->load->message(MSG_ERROR, 'Không đúng định dạng link video từ Youtube');
    }
    else
    {
        $update['gid'] = $gid;
        $update['name'] = $name;
        $update['scientific_name'] = $scientific_name;
        $update['video'] = $video;
        $update['symptoms'] = $symptoms;
        $update['lesions'] = $lesions;
        $update['treatments'] = $treatments;
        $update['prevention'] = $prevention;
        $update['related'] = $related;
        $update['description'] = $description;
        $id = $DSS->update($dss_id, $update);
        if (!$id)
        {
            $this->load->message(MSG_ERROR, 'Lỗi trong khi thêm dữ liệu');
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
                    $file_name = upload_file($path, $_FILES['thumbnail'], $base_file_name);
                    if (!$file_name)
                    {
                        $this->load->message(MSG_ERROR, 'Không thể upload ảnh minh họa');
                    }
                    else
                    {
                        $u['thumbnail'] = $sub_dir . '/' . $file_name;

                        if ($u['thumbnail'] != $dss['thumbnail'] && file_exists($dss['full_path_thumbnail']))
                        {
                            @unlink($dss['full_path_thumbnail']);
                        }

                        $DSS->update($dss_id, $u);
                    }
                }
            }

            /**
             * Reset data
             */
            $dss = $DSS->data($dss_id);
            $dss = $DSS->tuning($dss);
        }
    }
}

$data['dss'] = $dss;
$data['BR'] = $BR;
$data['DSS'] = $DSS;
$data['DSS_GR'] = $DSS_GR;
$data['SPC'] = $SPC;
$this->load->data('title', 'Chỉnh sửa bệnh');
$this->load->view('manager/diseases/edit', $data);