<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */
if (IS_LOGGED)
{
    redirect(BASE_URL . 'manager');
}

/**
 * Load account model
 */
$this->load->model('account');

/**
 * @var \FA\MODELS\M_BENHVATNUOI\account $acc_model
 */
$acc_model = $this->model->account;

if (isset($_POST['submit-login']))
{
    $username   = $this->input->post('username', true);
    $password   = $this->input->post('password');
    $remember   = $this->input->post('remember') ? TRUE : FALSE;

    $msg_error = 'Tên đăng nhập hoặc mật khẩu không chính xác';
    /**
     * Check username is already exists
     */
    if (!($user_id = $acc_model->user_exists($username)))
    {
        $this->load->message(MSG_ERROR, $msg_error);
    }
    else
    {
        /**
         * Get user data
         */
        $user = $acc_model->user_data($user_id);
        if (!$user['password'])
        {
            $this->load->message(MSG_ERROR, $msg_error);
        }
        elseif ($acc_model->encrypt_password($password) !== $user['password'])
        {
            $this->load->message(MSG_ERROR, $msg_error);
        }
        else
        {
            $acc_model->set_token_login($user_id, $remember);
            redirect(BASE_URL . 'manager');
        }
    }
}

$this->load->data('title', 'Đăng nhập trang quản trị');
$this->load->view('manager/login');