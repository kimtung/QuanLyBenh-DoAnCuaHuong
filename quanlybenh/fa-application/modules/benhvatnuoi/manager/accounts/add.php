<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */
if (isset($_POST['submit-add']))
{
    /**
     * Load account model
     */
    $this->load->model('account');
    /**
     * @var \FA\MODELS\M_BENHVATNUOI\account $ACC
     */
    $ACC = $this->model->account;

    $fullname   = $this->input->post('fullname', true);
    $email      = $this->input->post('email', true);
    $phone      = $this->input->post('phone', true);
    $username   = $this->input->post('username', true);
    $password   = $this->input->post('password');
    $repassword = $this->input->post('re-password');

    if (!$fullname || !$email || !$username || !$password || !$repassword)
    {
        $this->load->message(MSG_ERROR, 'Bạn cần nhập đầy đủ những thông tin cần thiết');
    }
    elseif (strlen($fullname) > 500 || strlen($username) > 500 || strlen($phone) > 500)
    {
        $this->load->message(MSG_ERROR, 'Dữ liệu quá dài. Vui lòng thử lại');
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $this->load->message(MSG_ERROR, 'Định dạng email không hợp lệ');
    }
    elseif (!$ACC->valid_username($username))
    {
        $this->load->message(MSG_ERROR, 'Định dạng tên đăng nhập không đúng');
    }
    elseif ($password != $repassword)
    {
        $this->load->message(MSG_ERROR, 'Xác nhận mật khẩu không chính xác');
    }
    else
    {
        if ($ACC->email_exists($email))
        {
            $this->load->message(MSG_ERROR, 'Email đã tồn tại');
        }
        elseif ($ACC->user_exists($username))
        {
            $this->load->message(MSG_ERROR, 'Tên đăng nhập đã tồn tại');
        }
        else
        {
            $insert['fullname'] = $fullname;
            $insert['email']    = $email;
            $insert['phone']    = $phone;
            $insert['username'] = $username;
            $insert['password'] = $ACC->encrypt_password($password);
            $insert['time_created'] = date("Y-m-d H:i:s");
            $user_id = $ACC->insert_user($insert);
            if (!$user_id)
            {
                $this->load->message(MSG_ERROR, 'Lỗi trong khi thêm dữ liệu');
            }
            else
            {
                $this->load->message('success', 'Thêm tài khoản thành công <a href="' . BASE_URL . 'manager/accounts">Trở lại</a>');
            }
        }
    }
}

$this->load->data('title', 'Thêm người quản lý');
$this->load->view('manager/accounts/add');