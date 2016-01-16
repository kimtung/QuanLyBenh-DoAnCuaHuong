<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */
/**
 * Load account model
 */
$this->load->model('account');
/**
 * @var \FA\MODELS\M_BENHVATNUOI\account $ACC
 */
$ACC = $this->model->account;

$data['ACC'] = $ACC;
$this->load->data('title', 'Danh sách admin');
$this->load->view('manager/accounts/index', $data);