<?php
NAMESPACE FA\CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Models Class
 * Class FA_Models
 * @package FA\CORE
 *
 * @property-read \FA\CORE\FA_Loader $load
 * @property-read \FA\CORE\FA_Router $router
 * @property-read \FA\CORE\FA_Config $config
 * @property-read \FA\CORE\FA_Hook $hook
 * @property-read \FA\CORE\FA_Input $input
 * @property-read \FA\CORE\FA_Lang $lang
 * @property-read \FA\DATABASE\FA_DB_driver $db
 * @property-read object $model
 * @property-read string $module
 * @property-read string $controller
 * @property-read string $action
 * @property-read object $lib
 */
class FA_Models
{
    /**
     * @var object
     */
    private $_fa_instance;

    /**
     * @var array
     */
    private $_settings = array();

    /**
     * FA_Models constructor.
     */
    public function __construct()
    {
        /**
         * Log info
         */
        log_message(MSG_INFO, 'Models Class Initialized');

        /**
         * Get FA instance
         */
        $this->_fa_instance =& fa_instance();

        /**
         * Init database
         */
        $database_params = empty($this->_settings['database_params']) ? '' : $this->_settings['database_params'];
        if (!empty($this->_settings['database']))
        {
            $this->load->database($database_params, FALSE, TRUE);
        }
    }

    /**
     * __get magic
     *
     * Allows models to access FA's loaded classes using the same
     * syntax as controllers.
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        /**
         * Debugging note:
         * If you're here because you're getting an error message
         * saying 'Undefined Property: fa-system/core/FA_Models.php', it's
         * most likely a typo in your model code.
         */
        return $this->_fa_instance->$key;
    }

    /**
     * @param string|array $key
     * @param null|mixed $val
     */
    public function __settings($key, $val = NULL)
    {
        if (is_array($key))
        {
            foreach ($key as $k => $v)
            {
                $this->_settings[$k] = $v;
            }
        }
        else
        {
            $this->_settings[$key] = $val;
        }
    }
}