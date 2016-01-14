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

if ($user_id == USER_ID)
{
    redirect(BASE_URL . 'manager/accounts');
}

$user = $ACC->user_data($user_id);

if ($user['protected'])
{
    redirect(BASE_URL . 'manager/accounts');
}

if (isset($_POST['submit-delete']))
{
    if (!$ACC->delete_user($user_id))
    {
        $this->load->message(MSG_ERROR, 'Lỗi trong khi xóa tài khoản, vui lòng thử lại sau');
    }
    else
    {
        redirect(BASE_URL . 'manager/accounts');
    }
}

$data['user'] = $user;
$this->load->data('title', 'Xóa tài khoản');
$this->load->view('manager/accounts/delete', $data);