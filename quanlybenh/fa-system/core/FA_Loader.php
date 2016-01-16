<?php
NAMESPACE FA\CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class FA_Loader
 * @package FA\CORE
 */
class FA_Loader
{
    /**
     * @var object
     */
    protected $fa_instance;

    /**
     * @var array
     */
    protected $_fa_models = array();

    /**
     * @var string
     */
    protected $_views_dir_name      = 'views';

    /**
     * @var string
     */
    protected $_layouts_dir_name    = 'layouts';

    /**
     * @var string
     */
    protected $_template;

    /**
     * Group all libraries loaded
     *
     * @var array
     */
    protected $_libraries_loaded = array();

    /**
     * Group all helpers loaded
     */
    protected $_helpers_loaded = array();

    /**
     * Global view data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Global view message
     *
     * @var array
     */
    protected $_message = array();

    /**
     * FA_Loader constructor.
     */
    public function __construct()
    {
        /**
         * Get FA Controller instance
         */
        $this->fa_instance =& fa_instance();
    }

    public function initialize()
    {

    }

    /**
     * @param string $template
     */
    public function template($template)
    {
        $this->_template = $template;
    }

    /**
     * Get/Set global view data
     *
     * SET when $value is set a value
     * GET when $value is not set value
     *
     * @param string $key
     * @param null|mixed $value
     * @return bool|null|mixed
     */
    public function data($key, $value = NULL)
    {
        if (isset($value))
        {
            $this->_data[$key] = $value;

            return TRUE;
        }
        else
        {
            if (isset($this->_data[$key]))
            {
                return $this->_data[$key];
            }
            return NULL;
        }
    }

    /**
     * Get/Set global view message
     *
     * SET when $value is set a value
     * GET when $value is not set value
     * When $key is not set, all message will returned
     *
     * if $key is array, this key is key[0], position is key[1].
     * example: array(key, position)
     *
     * @param string $key
     * @param null|mixed $value
     * @return bool|null|mixed
     */
    public function message($key = NULL, $value = NULL)
    {
        if (!isset($key))
        {
            return $this->_message;
        }

        $position = 'default';
        if (is_array($key) && isset($key[0], $key[1]))
        {
            $position = $key[1];
            $key = $key[0];
        }

        if (isset($value))
        {
            if (!isset($this->_message[$position][$key]))
            {
                $this->_message[$position][$key] = array();
            }
            $this->_message[$position][$key][] = $value;

            return TRUE;
        }
        else
        {
            if (isset($this->_message[$position][$key]))
            {
                return $this->_message[$position][$key];
            }
            return NULL;
        }
    }

    /**
     * Load view
     *
     * @param string|array $path
     * @param array $data
     * @param bool $return
     * @return string
     */
    public function view($path, $data = array(), $return = FALSE)
    {
        $module     = '';
        $file_name  = '';

        /**
         * Check if path is array
         */
        if (is_array($path))
        {
            /**
             * If path 0 and path 1 exists, load from other module
             */
            if (isset($path[0], $path[1]))
            {
                $module     = $path[0];
                $file_name  = $path[1];
            }
            else show_error('Wrong path structure view file');
        } else {
            /**
             * Else load from current module
             */
            $module = $this->fa_instance->module;
            $file_name = $path;
        }

        if ($this->_template)
        {
            $location = APP_PATH . 'templates/' . $this->_template . '/';
        }
        else
        {
            $location = APP_PATH . 'modules/' . $module . '/' . $this->_views_dir_name . '/';
        }

        $_view_path = $location . $file_name . ".phtml";

        /**
         * **************************************
         *  Hook "pre_view"
         * **************************************
         */
        $this->fa_instance->hook->action(FA, 'pre_view', array($_view_path));

        /**
         * **************************************
         *  Hook "view_data"
         * **************************************
         */
        $data = $this->fa_instance->hook->filter(FA, 'view_data', $data);

        /**
         * Get content of file
         */
        $content = $this->_load_view_file($_view_path, $data);

        /**
         * **************************************
         *  Hook "view_output"
         * **************************************
         */
        $content = $this->fa_instance->hook->filter(FA, 'view_output', $content);

        /**
         * Return the file data if requested
         */
        if ($return === TRUE)
        {
            return $content;
        }
        echo $content;
    }

    /**
     * @param string $path
     * @param array $data
     * @param bool $return
     * @return null|string
     */
    public function layout($path, $data = array(), $return = FALSE)
    {
        $module     = '';
        $file_name  = '';

        /**
         * Check if path is array
         */
        if (is_array($path))
        {
            /**
             * If path 0 and path 1 exists, load from other module
             */
            if (isset($path[0], $path[1]))
            {
                $module     = $path[0];
                $file_name  = $path[1];
            } else {
                show_error('Wrong path structure layout file.');
            }
        } else {
            /**
             * Else load from current module
             */
            $module = $this->fa_instance->module;
            $file_name = $path;
        }

        $from_base = (strpos($file_name, '/') === 0) ? TRUE : FALSE;

        if ($this->_template) {
            $location = APP_PATH . 'templates/' . $this->_template;
        } else {
            $location = APP_PATH . 'modules/' . $module . '/' . $this->_views_dir_name;
        }

        /**
         * If char start of file name is /, load from base views direction
         * else load from layout direction
         */
        if ($from_base && $this->_template)
        {
            $_layout_path = $location . '/' . $file_name . ".phtml";
        }
        elseif (!$from_base && $this->_template)
        {
            $_layout_path = $location  . '/' . $module . '/' . $this->_layouts_dir_name . '/' . $file_name . ".phtml";
        }
        elseif ($from_base && !$this->_template)
        {
            $_layout_path = $location . $file_name . '.phtml';
        }
        else
        {
            $_layout_path = $location . '/' . $this->_layouts_dir_name . '/' .  $file_name . '.phtml';
        }

        /**
         * **************************************
         *  Hook "pre_layout"
         * **************************************
         */
        $this->fa_instance->hook->action(FA, 'pre_layout', array($_layout_path));

        /**
         * **************************************
         *  Hook "layout_data"
         * **************************************
         */
        $data = $this->fa_instance->hook->filter(FA, 'layout_data', $data);

        /**
         * Get content of file
         */
        $content = $this->_load_view_file($_layout_path, $data);

        /**
         * **************************************
         *  Hook "layout_output"
         * **************************************
         */
        $content = $this->fa_instance->hook->filter(FA, 'layout_output', $content);

        /**
         * Return the file data if requested
         */
        if ($return === TRUE)
        {
            return $content;
        }
        echo $content;
    }

    /**
     * Load view file and return content of file
     *
     * @param string $file_path
     * @param array $data
     * @return null|string
     */
    protected function _load_view_file($file_path, $data) {

        if (!$file_path) return NULL;
        /**
         * Check for file path of view exists
         */
        if (!file_exists($file_path))
        {
            show_error('Unable to find the view file: ' . $file_path);
        }
        else
        {
            /**
             * This allows anything loaded using $this->load, $this->router...
             * to become accessible from within the Controller and Model functions.
             */
            $_fa_object_vars = get_object_vars($this->fa_instance);
            foreach ($_fa_object_vars as $k => $val)
            {
                if (!isset($this->$k))
                {
                    $this->$k =& $this->fa_instance->$k;
                }
            }

            /**
             * Extract data
             */
            extract($data);

            /**
             * Buffer the output
             */
            ob_start();
            /**
             * @var \FA\CORE\FA_Controller $this
             */
            /**
             * Load the view file
             */
            include $file_path;
            /**
             * Log info
             */
            log_message(MSG_INFO, 'File loaded: ' . $file_path);

            /**
             * Get output contents
             */
            $buffer = ob_get_contents();

            @ob_end_clean();

            return $buffer;
        }
    }

    /**
     * Model Loader
     *
     * Loads and instantiates models.
     *
     * @param	string	$model	Model name
     * @param	string	$name	An optional object name to assign to
     * @return	object
     */
    public function model($model, $name = '')
    {
        if (empty($model))
        {
            return $this;
        }
        elseif (is_array($model))
        {
            foreach ($model as $key => $value)
            {
                if (is_int($key))
                {
                    $this->model($value);
                }
                elseif (is_string($key))
                {
                    $this->model($key, $value);
                }
            }

            return $this;
        }
        else
        {
            $module = $this->fa_instance->module;
            $path = '';

            //If $model is format: module:model -> get from this module
            if (strpos($model, ':') !== FALSE)
            {
                $_hash = explode(':', $model, 2);
                $module = $_hash[0];
                $model = $_hash[1];
            }

            if (($last_slash = strrpos($model, '/')) !== FALSE)
            {
                // The path is in front of the last slash
                $path = substr($model, 0, ++$last_slash);

                // And the model name behind it
                $model = substr($model, $last_slash);
            }

            if (empty($name))
            {
                $name = $model;
            }

            if (in_array($name, $this->_fa_models, TRUE))
            {
                return $this;
            }

            $FA =& fa_instance();
            if (isset($FA->model->$name))
            {
                show_error('The model name you are loading is already being used: ' . $name);
            }
            else
            {
                if (!class_exists('\FA\CORE\FA_Models', FALSE))
                {
                    load_class('Models', 'core');
                }
                $model_class = '\FA\MODELS\M_' . strtoupper($module) . '\\' . $model;
                if (!class_exists($model_class))
                {
                    $model_file_path = APP_PATH . 'modules/' . $module . '/models/' . $path . $model . '.php';
                    if (!file_exists($model_file_path))
                    {
                        show_error('Unable to find the model file: ' . $model_file_path);
                    }
                    else
                    {
                        require_once $model_file_path;

                        if (!class_exists($model_class, FALSE))
                        {
                            show_error($model_file_path . " exists, but doesn't declare class " . $model);
                        }
                    }
                }

                $this->_fa_models[] = $name;
                /**
                 * @var object $FA->model->$name
                 */
                $FA->model->$name = new $model_class();
            }
            return $this;
        }
    }

    /**
     * Database Loader
     *
     * @param	mixed	$params		Database configuration options
     * @param	bool	$return 	Whether to return the database object
     * @param	bool	$query_builder	Whether to enable Query Builder
     *					(overrides the configuration setting)
     *
     * @return	object|bool	Database object if $return is set to TRUE,
     *					FALSE on failure, FA_Loader instance in any other case
     */
    public function database($params = '', $return = FALSE, $query_builder = NULL)
    {
        /**
         * Grab the super object
         */
        $FA =& fa_instance();

        /**
         * Do we even need to load the database class?
         */
        if ($return === FALSE && $query_builder === NULL && isset($FA->db) && is_object($FA->db) && ! empty($FA->db->conn_id))
        {
            return FALSE;
        }

        /**
         * Load Database class
         */
        $databaseObj =& load_class('Database', 'core');
        $FA->db =& $databaseObj->loader($params, $query_builder);

        if ($return === TRUE)
        {
            return $FA->db;
        }
        return $this;
    }

    /**
     * Load library
     *
     * @param   string|array    $library    use array like: array(library_name => object_name)
     * @param   null|mixed      $param
     * @return  $this
     */
    public function library($library, $param = NULL)
    {
        if (!$library) return $this;

        if (is_array($library))
        {
            if (count($library) == 1)
            {
                $first_key      = key($library);
                $first_value    = reset($library);
                $library        = $first_key;
                $object_name    = $first_value;
                if (!$library || !$object_name)
                {
                    show_error('Error load library inputs');
                }
            }
            else
            {
                foreach ($library as $lib)
                {
                    $this->library($lib, $param);
                }
                return $this;
            }
        }
        else
        {
            $object_name = $library;
        }

        /**
         * Check if class has been loaded
         */
        if (isset($this->_libraries_loaded[$library]))
        {
            return $this->_libraries_loaded[$library];
        }

        $current_module = $this->fa_instance->module;

        $list_location = array(
            APP_PATH . 'modules/' . $current_module . '/libraries/',
            APP_PATH . '/libraries/',
            SYS_PATH . '/libraries/'
        );

        $found = FALSE;

        $class_name = '\FA\LIBRARIES\\' . $library;
        $filename = $library . '.lib.php';

        foreach ($list_location as $location)
        {
            $file_path = $location . $filename;
            /**
             * Check library file exists
             */
            if (file_exists($file_path))
            {
                $found = TRUE;
                /**
                 * Check if the class already exists
                 */
                if (!class_exists($class_name, FALSE))
                {
                    /**
                     * Load the library file
                     */
                    require_once $file_path;
                    /**
                     * Log info
                     */
                    log_message(MSG_INFO, 'Library loaded: ' . $library);
                }
                break;
            }
        }
        if (!$found)
        {
            show_error('Unable to load the library file: libraries/' . $filename);
        }
        else
        {
            /**
             * Re-check if class exists in the file
             */
            if (!class_exists($class_name, FALSE))
            {
                show_error('Class ' . $class_name . ' does not exist in the library file: ' . $filename);
            }
            else
            {
                $this->_libraries_loaded[$library] =
                    isset($param) ? new $class_name($param) : new $class_name();

                /**
                 *
                 */
                if (isset($this->fa_instance->lib->$object_name))
                {
                    show_error('Object name: ' . $object_name . ' has been used');
                }
                else
                {
                    $this->fa_instance->lib->$object_name = $this->_libraries_loaded[$library];
                }
            }
        }
        return $this;
    }

    /**
     * Load helper
     *
     * @param   string|array  $helpers
     * @return  $this
     */
    public function helper($helpers)
    {
        if (!$helpers) return $this;

        if (is_array($helpers))
        {
            foreach ($helpers as $helper)
            {
                $this->helper($helper);
            }
            return $this;
        }
        else
        {
            if (isset($this->_helpers_loaded[$helpers]))
            {
                return $this;
            }

            $file_name = $helpers . '.helper.php';

            $current_module = $this->fa_instance->module;

            $list_location = array(
                APP_PATH . 'modules/' . $current_module . '/helpers/',
                APP_PATH . '/helpers/',
                SYS_PATH . '/helpers/'
            );

            $found = FALSE;
            foreach ($list_location as $location)
            {
                $file_path = $location . $file_name;
                if (file_exists($file_path))
                {
                    /**
                     * Include helpers
                     */
                    include_once $file_path;

                    $found = TRUE;
                    /**
                     * Cache this helper
                     */
                    $this->_helpers_loaded[$helpers] = true;
                    /**
                     * Log info
                     */
                    log_message(MSG_INFO, 'Helper loaded: ' . $helpers);
                    break;
                }
            }
            if (!$found)
            {
                show_error('Unable to load the helper file: helpers/' . $file_name);
            }
            return $this;
        }
    }
}