<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 */
define('FA_ENVIRONMENT', 'development');

/**
 * ------------------------------------------------------
 * FA keyword
 * ------------------------------------------------------
 */
define('FA', 'FA');

/**
 * ------------------------------------------------------
 * Message keyword
 * ------------------------------------------------------
 */
define('MSG_ERROR', 'error');
define('MSG_SUCCESS', 'success');
define('MSG_INFO', 'info');
define('MSG_DEBUG', 'debug');
define('MSG_POS', 'position');