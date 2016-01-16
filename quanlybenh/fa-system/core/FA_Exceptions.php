<?php
NAMESPACE FA\CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class FA_Exceptions
 * @package FA\CORE
 */
class FA_Exceptions {

    /**
     * Nesting level of the output buffering mechanism
     *
     * @var	int
     */
    public $ob_level;

    /**
     * List of available error levels
     *
     * @var	array
     */
    public $levels = array(
        E_ERROR			=>	'Error',
        E_WARNING		=>	'Warning',
        E_PARSE			=>	'Parsing Error',
        E_NOTICE		=>	'Notice',
        E_CORE_ERROR		=>	'Core Error',
        E_CORE_WARNING		=>	'Core Warning',
        E_COMPILE_ERROR		=>	'Compile Error',
        E_COMPILE_WARNING	=>	'Compile Warning',
        E_USER_ERROR		=>	'User Error',
        E_USER_WARNING		=>	'User Warning',
        E_USER_NOTICE		=>	'User Notice',
        E_STRICT		=>	'Runtime Notice'
    );

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->ob_level = ob_get_level();
        // Note: Do not log messages from this constructor.
    }

    // --------------------------------------------------------------------

    /**
     * Exception Logger
     *
     * Logs PHP generated error messages
     *
     * @param int $severity Log level
     * @param string $message Error message
     * @param string $file_path File path
     * @param int $line	Line number
     * @return void
     */
    public function log_exception($severity, $message, $file_path, $line)
    {
        $severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
        log_message(MSG_ERROR, 'Severity: ' . $severity . ' --> ' . $message . ' ' . $file_path . ' ' . $line);
    }

    // --------------------------------------------------------------------

    /**
     * 404 Error Handler
     *
     * @param	string	$page		Page URI
     * @param 	bool	$log_error	Whether to log the error
     * @return	void
     */
    public function show_404($page = '', $log_error = TRUE)
    {
        if (is_cli()) {
            $heading = 'Not Found';
            $message = 'The controller/method pair you requested was not found.';
        } else {
            $heading = '404 Page Not Found';
            $message = 'The page you requested was not found.';
        }

        /**
         * By default we log this, but allow a dev to skip it
         */
        if ($log_error) {
            log_message(MSG_ERROR, $heading.': '.$page);
        }

        echo $this->show_error($heading, $message, 'error_404', 404);
        exit;
    }

    // --------------------------------------------------------------------

    /**
     * General Error Page
     *
     * Takes an error message as input (either as a string or an array)
     * and displays it using the specified template.
     *
     * @param	string		    $heading	    Page heading
     * @param	string|string[]	$message	    Error message
     * @param	string		    $template	    Template name
     * @param 	int		        $status_code	(default: 500)
     *
     * @return	string	Error page output
     */
    public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
        $templates_path = get_config_item('error_views_path');
        if (empty($templates_path)) {
            $templates_path = APP_PATH . '/config/views/errors/';
        }

        if (is_cli()) {
            $message = "\t" . (is_array($message) ? implode("\n\t", $message) : $message);
            $template = 'cli/' . $template;
        } else {
            set_status_header($status_code);
            $message = '<p>' . (is_array($message) ? implode('</p><p>', $message) : $message).'</p>';
            $template = 'html/' . $template;
        }

        if (ob_get_level() > $this->ob_level + 1) {
            ob_end_flush();
        }
        ob_start();
        include $templates_path . $template . '.php';
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    // --------------------------------------------------------------------

    public function show_exception($exception)
    {
        $templates_path = get_config_item('error_views_path');
        if (empty($templates_path)) {
            $templates_path = APP_PATH . '/config/views/errors/';
        }

        $message = $exception->getMessage();
        if (empty($message)) {
            $message = '(null)';
        }

        if (is_cli()) {
            $templates_path .= 'cli/';
        } else {
            set_status_header(500);
            $templates_path .= 'html/';
        }

        if (ob_get_level() > $this->ob_level + 1) {
            ob_end_flush();
        }

        ob_start();
        include $templates_path . 'error_exception.php';
        $buffer = ob_get_contents();
        ob_end_clean();
        echo $buffer;
    }

    // --------------------------------------------------------------------

    /**
     * Native PHP error handler
     *
     * @param	int	$severity	Error level
     * @param	string	$message	Error message
     * @param	string	$file_path	File path
     * @param	int	$line		Line number
     * @return	string	Error page output
     */
    public function show_php_error($severity, $message, $file_path, $line)
    {
        $templates_path = get_config_item('error_views_path');
        if (empty($templates_path)) {
            $templates_path = APP_PATH . '/config/views/errors/';
        }

        $severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;

        if ( !is_cli()) {

            $file_path = str_replace('\\', '/', $file_path);
            if (FALSE !== strpos($file_path, '/')) {
                $x = explode('/', $file_path);
                $file_path = $x[count($x)-2].'/'.end($x);
            }

            $template = 'html/' . 'error_php';
        } else {
            $template = 'cli/' . 'error_php';
        }

        if (ob_get_level() > $this->ob_level + 1) {
            ob_end_flush();
        }
        ob_start();
        include $templates_path . $template . '.php';
        $buffer = ob_get_contents();
        ob_end_clean();
        echo $buffer;
    }

}
