<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */
/**
 * Load account model
 */
$this->load->model(array('species', 'breed'));
/**
 * @var \FA\MODELS\M_BENHVATNUOI\species $SPC
 * @var \FA\MODELS\M_BENHVATNUOI\breed $BR
 */
$BR = $this->model->breed;
$SPC = $this->model->species;

$page = (int)$this->input->get('page');
$data['page'] = $page ? $page : 1;
$data['page_url'] = BASE_URL . 'manager/breeds?page={page}';

$data['BR'] = $BR;
$data['SPC'] = $SPC;
$this->load->data('title', 'Quản lý giống');
$this->load->view('manager/breeds/index', $data);