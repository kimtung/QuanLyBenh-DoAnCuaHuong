<?php
/**
 * Define base path
 */
define('BASE_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

$application_dir    = 'fa-application';
$system_dir         = 'fa-system';

/**
 * Define systems path
 */
define('APP_PATH', BASE_PATH . $application_dir . '/');
define('SYS_PATH', BASE_PATH . $system_dir . '/');

/**
 * Load core
 */
require_once SYS_PATH . 'core/FA_Core.php';