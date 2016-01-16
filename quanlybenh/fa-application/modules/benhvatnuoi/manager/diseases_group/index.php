<?php
/**
 * @var \FA\MODULES\M_BENHVATNUOI\manager $this
 */
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

$page = (int)$this->input->get('page');
$data['page'] = $page ? $page : 1;
$data['page_url'] = BASE_URL . 'manager/diseases_group?page={page}';

$data['BR'] = $BR;
$data['DSS_GR'] = $DSS_GR;
$data['SPC'] = $SPC;
$this->load->data('title', 'Quản lý nhóm bệnh');
$this->load->view('manager/diseases_group/index', $data);