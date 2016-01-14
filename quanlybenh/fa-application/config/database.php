<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

$database['active_group']   = 'default';

/**
 * Database group config
 */
$database['group']['default'] = array(
    'dsn'	    => '',
    'hostname'  => 'localhost',
    'username'  => 'root',
    'password'  => '',
    'database'  => 'benhvatnuoi',
    'driver'    => 'mysqli',
    'prefix'    => '',
    'pconnect'  => FALSE,
    'cache'     => FALSE,
    'cache_dir' => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'encrypt'   => FALSE,
    'compress'  => FALSE,
    'debug'     => TRUE
);