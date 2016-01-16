<?php
NAMESPACE FA\CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Router class
 *
 * Class FA_Router
 * @package FA\CORE
 */
Class FA_Router
{

    /**
     * @var object
     */
    protected $_config;

    /**
     * @var object
     */
    protected $_uri;

    /**
     * @var string
     */
    protected $_uri_string;

    /**
     * @var string
     */
    protected $_module;

    /**
     * @var string
     */
    protected $_controller;

    /**
     * @var string
     */
    protected $_action;

    /**
     * @var array
     */
    protected $_params = array();

    /**
     * @var array
     */
    protected $_routes = array();

    /**
     * @var string
     */
    protected $_route_string;

    /**
     * @var array
     */
    protected $_wildcards   = array();

    /**
     * FA_Router constructor.
     * @param null $routing
     */
    public function __construct($routing = NULL)
    {
        /**
         * ------------------------------------------------------
         *  Instantiate the Config class
         * ------------------------------------------------------
         */
        $this->_config =& load_class('Config', 'core');

        /**
         * ------------------------------------------------------
         *  Instantiate the URI class
         * ------------------------------------------------------
         */
        $this->_uri =& load_class('URI', 'core');

        $this->_uri_string = $this->_uri->uri_string();

        /**
         * Set wildcards
         */
        $this->_wildcards = array(
            ':any'      => '[^/]+',
            ':num'      => '[0-9]+',
            ':abc'      => '[a-zA-Z]+',
            ':abcL'     => '[a-z]+',
            ':abcU'     => '[A-Z]+',
            ':ufl'      => '[A-Za-z0-9\-]+',
            ':uflL'     => '[a-z0-9\-]+',
            ':uflU'     => '[A-Z0-9\-]+',
        );

        $this->_identify_route();

        /**
         * Log info
         */
        log_message(MSG_INFO, 'Router Class Initialized');
    }

    /**
     * @return string
     */
    public function module()
    {
        return $this->_module;
    }

    /**
     * @return string
     */
    public function controller()
    {
        return $this->_controller;
    }

    /**
     * @return string
     */
    public function action()
    {
        return $this->_action;
    }

    /**
     * @return array
     */
    public function params()
    {
        return $this->_params;
    }

    /**
     * @void
     */
    protected function _identify_route() {
        /**
         * Load the routes.php file. It would be great if we could
         */
        if (file_exists(APP_PATH . 'config/routes.php'))
        {
            include(APP_PATH . 'config/routes.php');
        }

        /**
         * Validate & get reserved routes
         */
        if (isset($routes) && is_array($routes))
        {
            foreach ($routes as $group)
            {
                $router_file = APP_PATH . 'modules/' . $group . '/config/router.php';
                if (file_exists($router_file))
                {
                    $router = array();
                    /**
                     * Load group router
                     */
                    include $router_file;

                    if ($router && is_array($router))
                    {
                        if (!empty($this->_routes))
                        {
                            $router = array_diff_key($router, $this->_routes);
                        }
                        $this->_routes = array_merge($this->_routes, $router);
                    }
                }
            }
            $this->_parse_routes();
        }
    }

    /**
     * @void
     */
    protected function _parse_routes()
    {
        $uri = $this->_uri_string;

        $this->_route_string = $uri;

        foreach ($this->_routes as $key => $val)
        {
            /**
             * Convert wildcards to RegEx
             */
            $key = strtr($key, $this->_wildcards);

            /**
             * Does the RegEx match?
             */
            if (preg_match('#^'.$key.'$#', $uri, $matches))
            {
                /**
                 * Are we using callbacks to process back-references?
                 */
                if ( ! is_string($val) && is_callable($val))
                {
                    /**
                     * Remove the original string from the matches array.
                     */
                    array_shift($matches);

                    /**
                     * Execute the callback using the values in matches as its parameters.
                     */
                    $val = call_user_func_array($val, $matches);
                }
                /**
                 * Are we using the default routing method for back-references?
                 */
                elseif (strpos($val, '$') !== FALSE && strpos($key, '(') !== FALSE)
                {
                    $val = preg_replace('#^'.$key.'$#', $val, $uri);
                }
                $this->_route_string = $val;
                break;
            }
        }

        $this->_extract_route();
    }

    /**
     * Extraction route to take module, controller, action
     *
     * @void
     */
    protected function _extract_route()
    {
        $route = $this->_route_string;
        $urls = explode('/', $route);

        /**
         * Get default router
         */
        $default_module     = $this->_config->item('default_module');
        $default_controller = $this->_config->item('default_controller');
        $default_action     = $this->_config->item('default_action');

        $this->_module      = !empty($urls[0]) ? $urls[0] : $default_module;
        $this->_controller  = !empty($urls[1]) ? $urls[1] : ($default_controller ? $default_controller : $this->_module);
        $this->_action      = !empty($urls[2]) ? $urls[2] : $default_action;

        if (count($urls) > 3)
        {
            $this->_params = array_slice($urls, 3);
        }
    }
}