<?php
NAMESPACE FA\CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Hooks Class
 *
 * Provides a mechanism to extend the base system without hacking.
 *
 * Class FA_Hook
 * @package FA\CORE
 */
class FA_Hook
{

    /**
     * Determines whether hooks are enabled
     *
     * @var	bool
     */
    public $enabled = FALSE;

    /**
     * List of all action hooks
     *
     * @var	array
     */
    protected $action_hooks =	array();

    /**
     * List of all filter hooks
     *
     * @var	array
     */
    protected $filter_hooks =	array();

    /**
     * Array with class objects to use hooks methods
     *
     * @var array
     */
    protected $_objects = array();

    /**
     * In progress flag
     *
     * Determines whether hook is in progress, used to prevent infinte loops
     *
     * @var	bool
     */
    protected $_in_progress = FALSE;

    /**
     * Class constructor
     *
     * FA_Hooks constructor.
     */
    public function __construct()
    {
        $CFG =& load_class('Config', 'core');

        log_message(MSG_INFO, 'Hooks Class Initialized');

        $hook =& $this;

        /**
         * If hooks are not enabled in the config file
         * there is nothing else to do
         */
        if ($CFG->item('enable_hook') === FALSE)
        {
            return;
        }

        /**
         * Grab the "hooks" definition file.
         */
        if (file_exists(APP_PATH.'config/hooks.php'))
        {
            include APP_PATH.'config/hooks.php';
        }

        $this->enabled = TRUE;
    }

    /**
     * Add action to action hooks
     *
     * @param string $group
     * @param string $name
     * @param object|string|array $function Closures/lambda functions and array($object, 'method') callable
     * @param int $priority
     * @return bool
     */
    public function add_action($group, $name, $function, $priority = 10)
    {
        if (!$group || !$name || !$function) {
            return FALSE;
        }
        if (!isset($this->action_hooks[$group])) {
            $this->action_hooks[$group] = array();
        }
        if (!isset($this->action_hooks[$group][$name])) {
            $this->action_hooks[$group][$name] = array();
        }
        /**
         * Push a hook action to end of list hooks
         */
        array_push($this->action_hooks[$group][$name], array('data' => $function, 'priority' => $priority));
        return TRUE;
    }

    /**
     * Add filter to filter hooks
     *
     * @param string $group
     * @param string $name
     * @param object|string|array $function Closures/lambda functions and array($object, 'method') callable
     * @param int $priority
     * @return bool
     */
    public function add_filter($group, $name, $function, $priority = 10)
    {
        if (!$group || !$name) {
            return FALSE;
        }
        if (!isset($this->filter_hooks[$group])) {
            $this->filter_hooks[$group] = array();
        }
        if (!isset($this->filter_hooks[$group][$name])) {
            $this->filter_hooks[$group][$name] = array();
        }
        /**
         * Push a hook action to end of list hooks
         */
        array_push($this->filter_hooks[$group][$name], array('data' => $function, 'priority' => $priority));
        return TRUE;
    }

    /**
     * @param string $group
     * @param string $name
     * @param mixed $value
     * @param int $priority
     * @return bool
     */
    public function apply_filter($group, $name, $value, $priority = 10)
    {
        $this->add_filter($group, $name, function($old_val) use ($value) {
            return $old_val . $value;
        }, $priority);
        return TRUE;
    }

    /**
     * Action hooks
     *
     * @param string $group
     * @param string $name
     * @param array $list_params List all arguments when run hook
     * @return null
     */
    public function action($group, $name, $list_params = array())
    {
        if (!isset($this->action_hooks[$group][$name])) return NULL;

        $sort_arr = array();
        foreach ($this->action_hooks[$group][$name] as $k => $hook)
        {
            $sort_arr[$k] = $hook['priority'];
        }

        /**
         * Sort hook by priority
         */
        uasort($sort_arr, function($a, $b) {
            if ($a == $b) {
                return 1;
            }
            return ($a < $b) ? -1 : 1;
        });

        $list_keys = array_keys($sort_arr);

        foreach ($list_keys as $k)
        {
            $hook = $this->action_hooks[$group][$name][$k];

            $this->_run_hook($hook, $list_params, TRUE);
        }

        return TRUE;
    }

    /**
     * Filter hooks
     *
     * @param string $group
     * @param string $name
     * @param null|mixed $param
     * @return mixed|null
     */
    public function filter($group, $name, $param = NULL)
    {
        if (!isset($this->filter_hooks[$group][$name])) return $param;

        $sort_arr = array();
        foreach ($this->filter_hooks[$group][$name] as $k => $hook)
        {
            $sort_arr[$k] = $hook['priority'];
        }

        /**
         * Sort hook by priority
         */
        asort($sort_arr);
        $list_keys = array_keys($sort_arr);

        foreach ($list_keys as $k)
        {
            $hook = $this->filter_hooks[$group][$name][$k];

            $param = $this->_run_hook($hook, array($param), FALSE);
        }

        return $param;
    }

    /**
     * Run a hook with hook data
     *
     * @param object|string|array $hook
     * @param array $list_params
     * @param bool $is_action_hook
     * @return bool|mixed
     */
    protected function _run_hook($hook, $list_params, $is_action_hook = TRUE)
    {
        /**
         * Closures/lambda functions and array($object, 'method') callable
         */
        if (is_callable($hook['data']))
        {
            if (is_array($hook['data']))
            {
                $result = call_user_func_array($hook['data'], $list_params);
            }
            else
            {
                $result = call_user_func_array($hook['data'], $list_params);
            }
            return ($is_action_hook ? TRUE : $result);
        }
        elseif (is_array($hook['data']))
        {
            if (empty($hook['data']['file']) || (empty($hook['data']['class']) && empty($hook['data']['function'])))
            {
                return FALSE;
            }
            else
            {
                $file       = $hook['data']['file'];
                $class      = $hook['data']['class'];
                $function   = $hook['data']['function'];

                // Set the _in_progress flag
                $this->_in_progress = TRUE;

                if ($class)
                {
                    if (isset($this->_objects[$class]))
                    {
                        if (method_exists($this->_objects[$class], $function))
                        {
                            $result = call_user_func_array(array($this->_objects[$class], $function), $list_params);
                        }
                        else
                        {
                            return $this->_in_progress = FALSE;
                        }
                    }
                    else
                    {
                        class_exists($class, FALSE) OR require_once $file;

                        if (!class_exists($class, FALSE) OR !method_exists($class, $function))
                        {
                            return $this->_in_progress = FALSE;
                        }

                        // Store the object and execute the method
                        $this->_objects[$class] = new $class();
                        $result = call_user_func_array(array($this->_objects[$class], $function), $list_params);
                    }
                }
                else
                {
                    function_exists($function) OR require_once $file;

                    if (!function_exists($function))
                    {
                        return $this->_in_progress = FALSE;
                    }

                    $result = call_user_func_array($function, $list_params);
                }

                $this->_in_progress = FALSE;

                return ($is_action_hook ? TRUE : $result);
            }
        }
        else
        {
            return FALSE;
        }
    }
}
