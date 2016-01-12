<?php
NAMESPACE FA\CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class FA_Controller
 *
 * @package FA\CORE
 */
class FA_Controller
{
    /**
     * Reference to the CI singleton
     *
     * @var	object
     */
    private static $instance;

    /**
     * @var \FA\CORE\FA_Loader
     */
    public $load;

    /**
     * @var \FA\CORE\FA_Router
     */
    public $router;

    /**
     * @var \FA\CORE\FA_Config
     */
    public $config;

    /**
     * @var \FA\CORE\FA_UTF8
     */
    public $utf8;

    /**
     * @var \FA\CORE\FA_Input
     */
    public $input;

    /**
     * @var \FA\CORE\FA_Lang
     */
    public $lang;

    /**
     * @var \FA\CORE\FA_Security
     */
    public $security;

    /**
     * @var \FA\CORE\FA_Hook
     */
    public $hook;

    /**
     * @var object
     *
     */
    public $model;

    /**
     * @var \FA\DATABASE\FA_DB_driver
     */
    public $db;

    /**
     * @var \FA\CORE\FA_URI
     */
    public $uri;

    /**
     * @var object
     */
    public $lib;

    /**
     * @var string
     */
    public $module;
    /**
     * @var string
     */
    public $controller;
    /**
     * @var string
     */
    public $action;

    /**
     * FA_Controller constructor.
     */
    public function __construct()
    {
        self::$instance =& $this;
        foreach (is_loaded() as $var => $class)
        {
            $this->$var =& load_class($class);
        }

        /**
         * **************************************
         *  Hook "pre_init_controller"
         * **************************************
         */
        $this->hook->action(FA, 'pre_init_controller');


        $this->module       = $this->router->module();
        $this->controller   = $this->router->controller();
        $this->action       = $this->router->action();

        /**
         * Loader class
         */
        $this->load =& load_class('Loader', 'core');
        $this->load->initialize();

        /**
         * Declare model object
         */
        $this->model = new \stdClass();

        /**
         * Declare lib object
         */
        $this->lib = new \stdClass();

        /**
         * Log info
         */
        log_message(MSG_INFO, 'Controller Class Initialized');
    }

    /**
     * Get the FA singleton
     *
     * @static
     * @return	object
     */
    public static function &get_instance()
    {
        if (!self::$instance) {
            self::$instance = new FA_Controller();
        }
        return self::$instance;
    }
}