<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

if (!function_exists('get_http_host'))
{
    /**
     * Http host
     *
     * @return string
     */
    function get_http_host()
    {
        $scheme = (isset($_SERVER['HTTPS']) && ($_SERVER['SERVER_PORT'] == '443')) ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $request = trim(dirname($_SERVER["PHP_SELF"]),'/');
        $home = sprintf('%s://%s/%s', $scheme, $host, $request);
        $home = rtrim($home, '/');
        return $home;
    }
}


if (!function_exists('site_url'))
{
    /**
     * Site URL
     *
     * Create a local URL based on your basepath. Segments can be passed via the
     * first parameter either as a string or an array.
     *
     * @param	string	$uri
     * @param	string	$protocol
     * @return	string
     */
    function site_url($uri = '', $protocol = NULL)
    {
        static $_site_url;
        /**
         * Check if function executed
         */
        $key = $uri . '-' . $protocol;
        if (isset($_site_url[$key]))
        {
            return $_site_url[$key];
        }
        $_site_url[$key] = fa_instance()->config->site_url($uri, $protocol);
        return $_site_url[$key];
    }
}

if (!function_exists('base_url'))
{
    /**
     * Base URL
     *
     * Create a local URL based on your basepath.
     * Segments can be passed in as a string or an array, same as site_url
     * or a URL to a file can be passed in, e.g. to an image file.
     *
     * @param	string	$uri
     * @param	string	$protocol
     * @return	string
     */
    function base_url($uri = '', $protocol = NULL)
    {
        static $_base_url;
        /**
         * Check if function executed
         */
        $key = $uri . '-' . $protocol;
        if (isset($_base_url[$key]))
        {
            return $_base_url[$key];
        }
        $_base_url[$key] = fa_instance()->config->base_url($uri, $protocol);
        return $_base_url[$key];
    }
}

if (!function_exists('current_url'))
{
    /**
     * Return current page URL
     *
     * @return string
     */
    function current_url()
    {
        static $_current_url;
        /**
         * Check static var
         */
        if (isset($_current_url)) return $_current_url;

        $page_url = 'http';

        /**
         * Check https is enable
         */
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
        {
            $page_url .= "s";
        }
        $page_url .= "://";

        /**
         * Check if user other port
         */
        if ($_SERVER["SERVER_PORT"] != "80")
        {
            $page_url .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        }
        else
        {
            $page_url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }

        /**
         * Save static var
         */
        $_current_url = $page_url;
        return $page_url;
    }
}


if ( ! function_exists('get_config'))
{
    /**
     * Loads the main config.php file
     *
     * This function lets us grab the config file even if the Config class
     * hasn't been instantiated yet
     *
     * @param array
     * @return array
     */
    function &get_config(Array $replace = array())
    {
        static $config;

        if (empty($config))
        {
            $file_path = APP_PATH . 'config/config.php';
            $found = FALSE;
            if (file_exists($file_path))
            {
                $found = TRUE;
                require $file_path;
            }

            if ( ! $found)
            {
                set_status_header(503);
                echo 'The configuration file does not exist.';
                exit(3); // EXIT_CONFIG
            }

            // Does the $config array exist in the file?
            if ( ! isset($config) OR ! is_array($config))
            {
                set_status_header(503);
                echo 'Your config file does not appear to be formatted correctly.';
                exit(3); // EXIT_CONFIG
            }
        }

        // Are any values being dynamically added or replaced?
        foreach ($replace as $key => $val)
        {
            $config[$key] = $val;
        }

        return $config;
    }
}

if ( ! function_exists('get_config_item'))
{
    /**
     * Returns the specified config item
     *
     * @param string
     * @return mixed
     */
    function get_config_item($item)
    {
        static $_config;

        if (empty($_config))
        {
            // references cannot be directly assigned to static variables, so we use an array
            $_config[0] =& get_config();
        }

        return isset($_config[0][$item]) ? $_config[0][$item] : NULL;
    }
}

if ( ! function_exists('is_https'))
{
    /**
     * Is HTTPS?
     * Determines if the application is accessed via an encrypted
     * (HTTPS) connection.
     * @return	bool
     */
    function is_https()
    {
        if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        {
            return TRUE;
        }
        elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        {
            return TRUE;
        }
        elseif ( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
        {
            return TRUE;
        }

        return FALSE;
    }
}

if ( ! function_exists('is_php'))
{
    /**
     * Determines if the current version of PHP is equal to or greater than the supplied value
     *
     * @param	string
     * @return	bool	TRUE if the current version is $version or higher
     */
    function is_php($version)
    {
        static $_is_php;
        $version = (string) $version;

        if ( ! isset($_is_php[$version]))
        {
            $_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
        }

        return $_is_php[$version];
    }
}

if ( ! function_exists('remove_invisible_characters'))
{
    /**
     * Remove Invisible Characters
     *
     * This prevents sandwiching null characters
     * between ascii characters, like Java\0script.
     *
     * @param	string
     * @param	bool
     * @return	string
     */
    function remove_invisible_characters($str, $url_encoded = TRUE)
    {
        $non_displayables = array();

        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if ($url_encoded)
        {
            $non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

        do
        {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        }
        while ($count);

        return $str;
    }
}

if (!function_exists('hook_action'))
{
    /**
     * Action hooks
     *
     * @param string $group
     * @param string $name
     * @param array $list_params List all arguments when run hook
     * @return null
     */
    function hook_action($group, $name, $list_params = array())
    {
        static $_hook_obj;
        if (!isset($_hook_obj)) $_hook_obj =& load_class('Hook');
        return $_hook_obj->action($group, $name, $list_params);
    }
}

if (!function_exists('hook_filter'))
{
    /**
     * Filter hooks
     *
     * @param string $group
     * @param string $name
     * @param null|mixed $param
     * @return bool|null
     */
    function hook_filter($group, $name, $param = array())
    {
        static $_hook_obj;
        if (!isset($_hook_obj)) $_hook_obj =& load_class('Hook');
        return $_hook_obj->filter($group, $name, $param);
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a url
     *
     * @param string $url
     */
    function redirect($url = '')
    {
        if (strpos($url, 'http') == 0)
        {
            $link = $url;
        }
        else
        {
            $link = BASE_URL . $url;
        }
        header("Location: $link");
        exit;
    }
}

if ( ! function_exists('load_class'))
{
    /**
     * Class registry
     *
     * This function acts as a singleton. If the requested class does not
     * exist it is instantiated and set to a static variable. If it has
     * previously been instantiated the variable is returned.
     *
     * @param	string:	the class name being requested
     * @param	string:	the directory where the class should be found
     * @param	string:	an optional argument to pass to the class constructor
     * @return	object
     */
    function &load_class($class, $directory = 'libraries', $param = NULL)
    {
        static $_classes = array();

        if (isset($_classes[$class])) {
            return $_classes[$class];
        }

        $name = FALSE;

        $NAMESPACE = '\FA\\' . strtoupper($directory);

        $prefix_file = '';
        if ($directory == 'core') $prefix_file = 'FA_';

        /**
         * Look for the class first in the local fa-application/libraries folder
         * then in the native fa-system/libraries folder
         */
        foreach (array(APP_PATH, SYS_PATH) as $path) {

            if (file_exists($path . $directory . '/' . $prefix_file . $class . '.php')) {
                $name = 'FA_' . $class;
                if (class_exists($name, FALSE) === FALSE) {
                    require_once($path . $directory . '/' . $prefix_file . $class . '.php');
                }
                break;
            }
        }

        $subclass_prefix = get_config_item('subclass_prefix');
        /**
         * Is the request a class extension? If so we load it too
         */
        if (file_exists(APP_PATH . $directory . '/' . $subclass_prefix . $prefix_file . $class . '.php')) {

            $name = $subclass_prefix . $class;

            if (class_exists($name, FALSE) === FALSE) {

                require_once APP_PATH . $directory . '/' . $name . '.php';
            }
        }

        /**
         * Did we find the class?
         */
        if ($name === FALSE) {
            set_status_header(503);
            echo 'Unable to locate the specified class: ' . $prefix_file . $class . '.php';
            exit;
        }

        /**
         * Keep track of what we just loaded
         */
        is_loaded($class);
        $full_class_name = $NAMESPACE . '\\' . $name;
        $_classes[$class] = isset($param)
            ? new $full_class_name($param)
            : new $full_class_name();
        return $_classes[$class];
    }
}

// --------------------------------------------------------------------

if ( ! function_exists('is_loaded'))
{
    /**
     * Keeps track of which libraries have been loaded. This function is
     * called by the load_class() function above
     *
     * @param string
     * @return array
     */
    function &is_loaded($class = '')
    {
        static $_is_loaded = array();

        if ($class !== '')
        {
            $_is_loaded[strtolower($class)] = $class;
        }

        return $_is_loaded;
    }
}

if ( ! function_exists('set_status_header'))
{
    /**
     * Set HTTP Status Header
     *
     * @param	int     $code   the status code
     * @param	string  $text
     * @return	void
     */
    function set_status_header($code = 200, $text = '')
    {
        if (is_cli()) {
            return;
        }

        if (empty($code) OR ! is_numeric($code)) {
            show_error('Status codes must be numeric', 500);
        }

        if (empty($text)) {
            is_int($code) OR $code = (int) $code;
            $stt_code_info = array(
                100	=> 'Continue',
                101	=> 'Switching Protocols',

                200	=> 'OK',
                201	=> 'Created',
                202	=> 'Accepted',
                203	=> 'Non-Authoritative Information',
                204	=> 'No Content',
                205	=> 'Reset Content',
                206	=> 'Partial Content',

                300	=> 'Multiple Choices',
                301	=> 'Moved Permanently',
                302	=> 'Found',
                303	=> 'See Other',
                304	=> 'Not Modified',
                305	=> 'Use Proxy',
                307	=> 'Temporary Redirect',

                400	=> 'Bad Request',
                401	=> 'Unauthorized',
                402	=> 'Payment Required',
                403	=> 'Forbidden',
                404	=> 'Not Found',
                405	=> 'Method Not Allowed',
                406	=> 'Not Acceptable',
                407	=> 'Proxy Authentication Required',
                408	=> 'Request Timeout',
                409	=> 'Conflict',
                410	=> 'Gone',
                411	=> 'Length Required',
                412	=> 'Precondition Failed',
                413	=> 'Request Entity Too Large',
                414	=> 'Request-URI Too Long',
                415	=> 'Unsupported Media Type',
                416	=> 'Requested Range Not Satisfiable',
                417	=> 'Expectation Failed',
                422	=> 'Unprocessable Entity',

                500	=> 'Internal Server Error',
                501	=> 'Not Implemented',
                502	=> 'Bad Gateway',
                503	=> 'Service Unavailable',
                504	=> 'Gateway Timeout',
                505	=> 'HTTP Version Not Supported'
            );

            if (isset($stt_code_info[$code])) {
                $text = $stt_code_info[$code];
            } else {
                show_error('No status text available. Please check your status code number or supply your own message text.', 500);
            }
        }

        if (strpos(PHP_SAPI, 'cgi') === 0) {
            header('Status: ' . $code . ' ' . $text, TRUE);
        } else {
            $server_protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
            header($server_protocol . ' ' . $code . ' ' . $text, TRUE, $code);
        }
    }
}

if ( ! function_exists('show_error'))
{
    /**
     * Error Handler
     *
     * This function lets us invoke the exception class and
     * display errors using the standard error template located
     * in fa-application/config/views/errors/../error_general.php
     * This function will send the error page directly to the
     * browser and exit.
     *
     * @param string
     * @param int
     * @param string
     * @return void
     */
    function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
    {
        $status_code = abs($status_code);
        if ($status_code < 100)
        {
            $status_code = 500;
        }

        $_error =& load_class('Exceptions', 'core');
        echo $_error->show_error($heading, $message, 'error_general', $status_code);
        exit;
    }
}

if ( ! function_exists('_error_handler'))
{
    /**
     * Error Handler
     *
     * This is the custom error handler that is declared at the (relative)
     * top of CodeIgniter.php. The main reason we use this is to permit
     * PHP errors to be logged in our own log files since the user may
     * not have access to server logs. Since this function effectively
     * intercepts PHP errors, however, we also need to display errors
     * based on the current error_reporting level.
     * We do that with the use of a PHP error template.
     *
     * @param	int	$severity
     * @param	string	$message
     * @param	string	$file_path
     * @param	int	$line
     * @return	void
     */
    function _error_handler($severity, $message, $file_path, $line)
    {
        $is_error = (((E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);

        /**
         * When an error occurred, set the status header to '500 Internal Server Error'
         * to indicate to the client something went wrong.
         * This can't be done within the $_error->show_php_error method because
         * it is only called when the display_errors flag is set (which isn't usually
         * the case in a production environment) or when errors are ignored because
         * they are above the error_reporting threshold.
         */
        if ($is_error) {
            set_status_header(500);
        }

        /**
         * Should we ignore the error? We'll get the current error_reporting
         * level and add its bits with the severity bits to find out.
         */
        if (($severity & error_reporting()) !== $severity) {
            return;
        }

        $_error =& load_class('Exceptions', 'core');
        $_error->log_exception($severity, $message, $file_path, $line);

        /**
         * Should we display the error?
         */
        if (str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors'))) {
            $_error->show_php_error($severity, $message, $file_path, $line);
        }

        /**
         * If the error is fatal, the execution of the script should be stopped because
         * errors can't be recovered from. Halting the script conforms with PHP's
         * default error handling. See http://www.php.net/manual/en/errorfunc.constants.php
         */
        if ($is_error) {
            exit;
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('_exception_handler'))
{
    /**
     * Exception Handler
     *
     * Sends uncaught exceptions to the logger and displays them
     * only if display_errors is On so that they don't show up in
     * production environments.
     *
     * @param	Exception	$exception
     * @return	void
     */
    function _exception_handler($exception)
    {
        $_error =& load_class('Exceptions', 'core');
        $_error->log_exception(MSG_ERROR, 'Exception: '.$exception->getMessage(), $exception->getFile(), $exception->getLine());

        // Should we display the error?
        if (str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors'))) {
            $_error->show_exception($exception);
        }
        exit;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('_shutdown_handler'))
{
    /**
     * Shutdown Handler
     *
     * This is the shutdown handler that is declared at the top
     * of CodeIgniter.php. The main reason we use this is to simulate
     * a complete custom exception handler.
     *
     * E_STRICT is purposively neglected because such events may have
     * been caught. Duplication or none? None is preferred for now.
     *
     * @link	http://insomanic.me.uk/post/229851073/php-trick-catching-fatal-errors-e-error-with-a
     * @return	void
     */
    function _shutdown_handler()
    {
        $last_error = error_get_last();
        if (isset($last_error) &&
            ($last_error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING)))
        {
            _error_handler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
        }
    }
}

if ( ! function_exists('log_message'))
{
    /**
     * Error Logging Interface
     *
     * We use this as a simple mechanism to access the logging
     * class and send messages to be logged.
     *
     * @param string: the error level: MSG_ERROR, MSG_DEBUG or MSG_INFO
     * @param string: the error message
     * @return void
     */
    function log_message($level, $message)
    {
        static $_log;

        if ($_log === NULL) {
            /**
             * references cannot be directly assigned to static variables, so we use an array
             */
            $_log[0] =& load_class('Log', 'core');
        }

        $_log[0]->write_log($level, $message);
    }
}

if ( ! function_exists('is_cli'))
{
    /**
     * Is CLI?
     * Test to see if a request was made from the command line.
     *
     * @return 	bool
     */
    function is_cli()
    {
        return (PHP_SAPI === 'cli' OR defined('STDIN'));
    }
}

if ( ! function_exists('is_really_writable'))
{
    /**
     * Tests for file writability
     *
     * is_writable() returns TRUE on Windows servers when you really can't write to
     * the file, based on the read-only attribute. is_writable() is also unreliable
     * on Unix servers if safe_mode is on.
     *
     * @link	https://bugs.php.net/bug.php?id=54709
     * @param	string
     * @return	bool
     */
    function is_really_writable($file)
    {
        // If we're on a Unix server with safe_mode off we call is_writable
        if (DIRECTORY_SEPARATOR === '/' && (is_php('5.4') OR ! ini_get('safe_mode')))
        {
            return is_writable($file);
        }

        /* For Windows servers and safe_mode "on" installations we'll actually
         * write a file then read it. Bah...
         */
        if (is_dir($file)) {
            $file = rtrim($file, '/').'/'.md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === FALSE) {
                return FALSE;
            }

            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return TRUE;
        } elseif ( ! is_file($file) OR ($fp = @fopen($file, 'ab')) === FALSE) {
            return FALSE;
        }

        fclose($fp);
        return TRUE;
    }
}