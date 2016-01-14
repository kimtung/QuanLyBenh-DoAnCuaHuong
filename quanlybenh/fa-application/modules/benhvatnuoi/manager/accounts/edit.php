<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */
/**
 * @var int $action_id
 */
if (empty($action_id))
{
    redirect(BASE_URL . 'manager/accounts');
}

$user_id = $action_id;

/**
 * Load account model
 */
$this->load->model('account');
/**
 * @var \FA\MODELS\M_BENHVATNUOI\account $ACC
 */
$ACC = $this->model->account;

if (!$ACC->user_exists($user_id))
{
    redirect(BASE_URL . 'manager/accounts');
}

$user = $ACC->user_data($user_id);

if (isset($_POST['submit-edit']))
{
    $fullname   = $this->input->post('fullname', true);
    $email      = $this->input->post('email', true);
    $phone      = $this->input->post('phone', true);

    if (!$fullname || !$email)
    {
        $this->load->message(MSG_ERROR, 'Bạn cần nhập tên đầy đủ và email');
    }
    elseif (strlen($fullname) > 500 || strlen($phone) > 500)
    {
        $this->load->message(MSG_ERROR, 'Dữ liệu quá dài. Vui lòng thử lại');
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $this->load->message(MSG_ERROR, 'Định dạng email không hợp lệ');
    }
    else
    {
        if ($email != $user['email'] && $ACC->email_exists($email))
        {
            $this->load->message(MSG_ERROR, 'Email đã tồn tại. Vui lòng chọn một email khác');
        }
        else
        {
            $update['fullname'] = $fullname;
            $update['email']    = $email;
            $update['phone']    = $phone;

            if (!$ACC->update_user($user_id, $update))
            {
                $this->load->message(MSG_ERROR, 'Lỗi trong khi lưu dữ liệu');
            }
            else
            {
                $this->load->message(MSG_SUCCESS, 'Đã lưu dữ liệu');
                /**
                 * Refresh user data
                 */
                $user = $ACC->user_data($user_id);
            }
        }
    }
}

if (isset($_POST['submit-change-password']))
{
    $password   = $this->input->post('password');
    $repassword = $this->input->post('re-password');

    if (!$password || !$repassword)
    {
        $this->load->message(array(MSG_ERROR, 'change_password'), 'Vui lòng nhập mật khẩu và xác nhận mật khẩu');
    }
    elseif ($password != $repassword)
    {
        $this->load->message(array(MSG_ERROR, 'change_password'), 'Xác nhận mật khẩu không đúng');
    }
    else
    {
        $update['password'] = $ACC->encrypt_password($password);
        if (!$ACC->update_user($user_id, $update))
        {
            $this->load->message(array(MSG_ERROR, 'change_password'), 'Lỗi trong khi lưu dữ liệu');
        }
        else
        {
            $this->load->message(array(MSG_SUCCESS, 'change_password'), 'Đã thay đổi mật khẩu');
            /**
             * Refresh user data
             */
            $user = $ACC->user_data($user_id);
        }
    }
}

$data['user'] = $user;
$this->load->data('title', 'Chỉnh sửa tài khoản');
$this->load->view('manager/accounts/edit', $data);