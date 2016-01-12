<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * ------------------------------------------------------
 * Font Awesome Version
 * @var	string
 * ------------------------------------------------------
 */
define('FA_VERSION', '1.0.0');

/**
 * ------------------------------------------------------
 *  Load the constants
 * ------------------------------------------------------
 */
require_once APP_PATH.'config/constants.php';

/**
 * ------------------------------------------------------
 * ERROR REPORTING
 * ------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */
switch (FA_ENVIRONMENT)
{
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 'FA_Exceptions');
        break;

    case 'production':
        ini_set('display_errors', 0);
        if (version_compare(PHP_VERSION, '5.3', '>='))
        {
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        }
        else
        {
            error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
        }
        break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit;
}

/**
 * ------------------------------------------------------
 *  Load the global functions
 * ------------------------------------------------------
 */
require_once SYS_PATH.'core/FA_Common.php';

/**
 * ------------------------------------------------------
 * Special handler
 * ------------------------------------------------------
 */
set_error_handler('_error_handler');
set_exception_handler('_exception_handler');
register_shutdown_function('_shutdown_handler');

/**
 * ------------------------------------------------------
 * Set charset
 * ------------------------------------------------------
 */
$charset = strtoupper(get_config_item('charset'));
if ($charset)
{
    ini_set('default_charset', $charset);

    if (extension_loaded('mbstring'))
    {
        define('MB_ENABLED', TRUE);
        /**
         * mbstring.internal_encoding is deprecated starting with PHP 5.6
         * and it's usage triggers E_DEPRECATED messages.
         */
        @ini_set('mbstring.internal_encoding', $charset);
        /**
         * This is required for mb_convert_encoding() to strip invalid characters.
         * That's utilized by FA_UTF8, but it's also done for consistency with iconv.
         */
        mb_substitute_character('none');
    }
    else
    {
        define('MB_ENABLED', FALSE);
    }

    /**
     * There's an ICONV_IMPL constant, but the PHP manual says that using
     * iconv's predefined constants is "strongly discouraged".
     */
    if (extension_loaded('iconv'))
    {
        define('ICONV_ENABLED', TRUE);
        /**
         * iconv.internal_encoding is deprecated starting with PHP 5.6
         * and it's usage triggers E_DEPRECATED messages.
         */
        @ini_set('iconv.internal_encoding', $charset);
    }
    else
    {
        define('ICONV_ENABLED', FALSE);
    }

    if (is_php('5.6'))
    {
        ini_set('php.internal_encoding', $charset);
    }
}

/**
 * ------------------------------------------------------
 *  Instantiate the UTF-8 class
 * ------------------------------------------------------
 */
$UTF8 =& load_class('UTF8', 'core');

/**
 * ------------------------------------------------------
 *  Instantiate the Config class
 * ------------------------------------------------------
 */
$CONFIG =& load_class('Config', 'core');

/**
 * ------------------------------------------------------
 *  Load the Input class and sanitize globals
 * ------------------------------------------------------
 */
$INPUT	=& load_class('Input', 'core');

/**
 * ------------------------------------------------------
 *  Instantiate the hook class
 * ------------------------------------------------------
 */
$HOOK =& load_class('Hook', 'core');

/**
 * ------------------------------------------------------
 *  Instantiate the router class
 * ------------------------------------------------------
 */
$ROUTER =& load_class('Router', 'core');

/**
 * ------------------------------------------------------
 *  Load the Language class
 * ------------------------------------------------------
 */
$LANG =& load_class('Lang', 'core');

/**
 * Router info
 */
$CUR_MODULE     = $ROUTER->module();
$CUR_CONTROLLER = $ROUTER->controller();
$CUR_ACTION     = $ROUTER->action();
$CUR_PARAMS     = $ROUTER->params();

/**
 * URL
 */
$BASE_URL       = $CONFIG->base_url();
$CUR_MODULE_URL = $BASE_URL . 'fa-application/modules/' . $CUR_MODULE;

/**
 * **************************************
 *  Hook "init_system"
 * **************************************
 */
$HOOK->action(FA, 'init_system');

$controller_path = APP_PATH . "modules/$CUR_MODULE/$CUR_CONTROLLER.php";

/**
 * Check for controller file exists
 */
if (!file_exists($controller_path))
{
    show_error('Unable to find the controller file: ' . $controller_path);
    die;//Exit from here
}

/**
 * ------------------------------------------------------
 * Define value
 * ------------------------------------------------------
 */
if (!defined('CUR_MODULE'))
{
    define('CUR_MODULE', $CUR_MODULE);
}
if (!defined('CUR_CONTROLLER'))
{
    define('CUR_CONTROLLER', $CUR_CONTROLLER);
}
if (!defined('CUR_ACTION'))
{
    define('CUR_ACTION', $CUR_ACTION);
}
if (!defined('BASE_URL'))
{
    define('BASE_URL', $BASE_URL);
}
if (!defined('CUR_MODULE_URL'))
{
    define('CUR_MODULE_URL', $CUR_MODULE_URL);
}

/**
 * ------------------------------------------------------
 * Load the base controller
 * ------------------------------------------------------
 */
require_once SYS_PATH . 'core/FA_Controller.php';

/**
 * Reference to the FA_Controller method.
 *
 * Returns current FA instance object
 *
 * @return \FA\CORE\FA_Controller
 */
function &fa_instance()
{
    return \FA\CORE\FA_Controller::get_instance();
}

/**
 * Load controller file
 */
require_once $controller_path;

$controller_class = '\FA\MODULES\\M_' . strtoupper($CUR_MODULE) . '\\' . $CUR_CONTROLLER;

/**
 * Make sure class exists?
 */
if (!class_exists($controller_class, false))
{
    show_error('Unable to find class: ' . $CUR_CONTROLLER);
}
else
{
    $initialize = APP_PATH . "modules/$CUR_MODULE/config/initialize.php";
    /**
     * Check initialize file exists
     */
    if (file_exists($initialize))
    {
        /**
         * Load the initialize file
         */
        require_once $initialize;
    }

    /**
     * **************************************
     *  Hook "pre_controller"
     * **************************************
     */
    $HOOK->action(FA, 'pre_controller');

    /**
     * Init controller
     */
    $objController = new $controller_class();

    /**
     * Check action exists
     */
    if (!method_exists($objController, $CUR_ACTION))
    {
        show_error('Unable to find method: ' . $CUR_ACTION);
    }
    else
    {
        /**
         * **************************************
         *  Hook "pre_action"
         * **************************************
         */
        $HOOK->action(FA, 'pre_action');

        /**
         * Run action method
         */
        call_user_func_array(array($objController, $CUR_ACTION), $CUR_PARAMS ? $CUR_PARAMS : array());
    }
}

/**
 * **************************************
 *  Hook "finish_system"
 * **************************************
 */
$HOOK->action(FA, 'finish_system');