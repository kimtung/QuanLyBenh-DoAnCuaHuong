<?php
NAMESPACE FA\CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class FA_Lang
 * @package FA\CORE
 */
Class FA_Lang
{
    public $languages = array();

    protected $_code = 'en';

    protected $_loaded = array();

    /**
     * FA_Lang constructor.
     */
    public function __construct()
    {
        /**
         * Log info
         */
        log_message(MSG_INFO, 'Language Class Initialized');
    }

    /**
     * Using a language package
     *
     * @param string $lang_code
     */
    public function using($lang_code)
    {
        $this->_code = $lang_code;
    }

    /**
     * Translate a string
     *
     * @param string    $key
     * @return mixed
     */
    public function lng($key)
    {
        if (isset($this->languages[$key]))
        {
            if ($this->languages[$key] === NULL)
            {
                $out = $key;
            }
            else
            {
                $out = $this->languages[$key];
            }
        }
        else
        {
            $out = $key;
        }
        $arg_list = func_get_args();
        if (isset($arg_list[1]))
        {
            unset($arg_list[0]);
            foreach ($arg_list as $k => $v)
            {
                $out = str_replace('{' . $k . '}', $v, $out);
            }
        }
        return $out;
    }

    public function elng($key)
    {
        echo $this->lng($key);
    }

    /**
     * Load the language file
     *
     * @param string        $file
     * @param string|null   $using
     * @param bool          $from_system
     */
    public function load($file, $using = NULL, $from_system = false)
    {
        if ($using)
        {
            $code = $using;
        }
        else
        {
            $code = $this->_code;
        }
        if (!$code)
        {
            show_error('You do not select the display language');
        }
        if (is_array($file))
        {
            foreach ($file as $item) {
                $this->load($item, $using, $from_system);
            }
        }
        else
        {
            if ($from_system)
            {
                $location = SYS_PATH . 'languages/' . $code . '/';
            }
            else
            {
                /**
                 * Get FA instance
                 */
                $FA = fa_instance();
                $location = APP_PATH . 'modules/' . $FA->module . '/config/languages/' . $code . '/';
            }

            $file_name = $file . '.lng.php';

            $file_path = $location . $file_name;
            /**
             * Just load once
             */
            if (!isset($this->_loaded[$file_path]))
            {
                if (!file_exists($file_path))
                {
                    show_error('Unable to load the language file: languages/' . $code . '/' . $file_name);
                }
                else
                {
                    /**
                     * Load language file
                     */
                    include $file_path;
                    /**
                     * Log file loaded
                     */
                    $this->_loaded[$file_path] = true;
                    log_message(MSG_INFO, 'Language file loaded: languages/' . $code . '/' . $file_name);

                    if (isset($lng))
                    {
                        $this->languages = array_merge($this->languages, $lng);
                    }
                }
            }
        }
    }
}