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

if (isset($_POST['submit-edit']))
{
    $bid = (int)$this->input->post('bid');
    $name = trim($this->input->post('name', true));
    $description = trim($this->input->post('description'));
    if (!$bid)
    {
        $this->load->message(MSG_ERROR, 'Bạn cần chọn một giống cho nhóm bệnh này');
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
    elseif ($DSS_GR->name_exists($name, $dss_id))
    {
        $this->load->message(MSG_ERROR, 'Tên nhóm bệnh đã tồn tại');
    }
    else
    {
        $update['bid'] = $bid;
        $update['name'] = $name;
        $update['description'] = $description;
        $id = $DSS_GR->update($dss_id, $update);
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
                    $file_name = upload_file($path, $_FILES['thumbnail'], $base_file_name, false);
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

                        $DSS_GR->update($dss_id, $u);
                    }
                }
            }

            /**
             * Reset data
             */
            $dss = $DSS_GR->data($dss_id);
            $dss = $DSS_GR->tuning($dss);
        }
    }
}

$data['dss'] = $dss;
$data['BR'] = $BR;
$data['DSS_GR'] = $DSS_GR;
$data['SPC'] = $SPC;
$this->load->data('title', 'Chỉnh sửa nhóm bệnh');
$this->load->view('manager/diseases_group/edit', $data);