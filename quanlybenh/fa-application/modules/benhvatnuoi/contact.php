<?php
NAMESPACE FA\MODULES\M_BENHVATNUOI;
USE \FA\CORE AS CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class contact
 * @package FA\MODULES\M_BENHVATNUOI
 */
Class contact Extends CORE\FA_Controller
{
    public function index()
    {
        if (isset($_POST['submit']))
        {
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');
            $content = $this->input->post('content');
            if (!$name || !$email ||  !$phone || !$content)
            {
                $this->load->message(MSG_ERROR, 'Bạn cần nhập đầy đủ thông tin');
            }
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $this->load->message(MSG_ERROR, 'Không đúng định dạng email');
            }
            elseif (!preg_match('/^[0-9\.]+$/i', $phone))
            {
                $this->load->message(MSG_ERROR, 'Không đúng định dạng số điện thoại');
            }
            else
            {
                $name = htmlspecialchars($name);
                $phone = htmlspecialchars($phone);
                $content = htmlspecialchars($content);
                $mail_subject = 'Liên hệ mới: ' . $name;
                $mail_content = "Người liên hệ: $name\nEmail: $email\nSố điện thoại: $phone\nNội dung: $content";
                // Send
                mail($this->config->item('site_email'), $mail_subject, $mail_content);
                $this->load->message(MSG_SUCCESS, 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ trả lời bạn trong thời gian sớm nhất.');
            }
        }
        $this->load->data('title', 'Liên hệ');
        $this->load->view('contact');
    }
}