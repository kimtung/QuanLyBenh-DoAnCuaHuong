<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */
/**
 * Load account model
 */
$this->load->model('species');
/**
 * @var \FA\MODELS\M_BENHVATNUOI\species $SPC
 */
$SPC = $this->model->species;

$data['SPC'] = $SPC;
$this->load->data('title', 'Quản lý loài');
$this->load->view('manager/species/index', $data);