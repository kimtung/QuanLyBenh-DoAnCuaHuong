<?php
NAMESPACE FA\DATABASE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class FA_DB_driver
 * @package FA\DATABASE
 */
abstract class FA_DB_driver
{
    /**
     * Connection ID
     *
     * @var	object|resource|FALSE
     */
    public $connID = FALSE;

    /**
     * Result ID
     *
     * @var object|resource|FALSE
     */
    public $resultID;

    /**
     * Data Source Name / Connect string
     *
     * @var	string
     */
    public $dsn;

    /**
     * Username
     *
     * @var	string
     */
    public $username;

    /**
     * Password
     *
     * @var	string
     */
    public $password;

    /**
     * Hostname
     *
     * @var	string
     */
    public $hostname;

    /**
     * Database port
     *
     * @var	int
     */
    public $port = '';

    /**
     * Database name
     *
     * @var	string
     */
    public $database;

    /**
     * Database driver
     *
     * @var	string
     */
    public $driver = 'mysqli';

    /**
     * Persistent connection flag
     *
     * @var	bool
     */
    public $pconnect = FALSE;

    /**
     * Table prefix
     *
     * @var	string
     */
    public $prefix = '';

    /**
     * Character set
     *
     * @var	string
     */
    public $charset	= 'utf8';

    /**
     * Collation
     *
     * @var	string
     */
    public $collation = 'utf8_general_ci';

    /**
     * Encryption flag/data
     *
     * @var	mixed
     */
    public $encrypt	= FALSE;

    /**
     * MYSQLI CLIENT COMPRESS
     * @var bool
     */
    public $compress = FALSE;
    /**
     * Debug flag
     *
     * Whether to display error messages.
     *
     * @var	bool
     */
    public $debug = FALSE;

    /**
     * ESCAPE character
     *
     * @var	string
     */
    protected $_like_escape_chr = '!';


    /**
     * ------------------------------------------------------------
     * START Declare function needed
     * ------------------------------------------------------------
     */

    /**
     * DB connect
     *
     * This is just a dummy method that all drivers will override.
     *
     * @param bool $persistent
     * @return mixed
     */
    abstract public function db_connect($persistent = FALSE);

    /**
     * Persistent database connection
     *
     * @return	mixed
     */
    abstract public function db_pconnect();

    /**
     * Reconnect
     *
     * Keep / reestablish the db connection if no queries have been
     * sent for a length of time exceeding the server's idle timeout.
     *
     * This is just a dummy method to allow drivers without such
     * functionality to not declare it, while others will override it.
     *
     * @return	void
     */
    abstract public function reconnect();

    /**
     * Close DB Connection
     *
     * @return	void
     */
    abstract public function db_close();

    /**
     * Select database
     *
     * This is just a dummy method to allow drivers without such
     * functionality to not declare it, while others will override it.
     *
     * @return	bool
     */
    abstract public function db_select();

    /**
     * Insert statement
     *
     * @param   string $table   the table name
     * @param   array  $values  the insert values
     * @return  string
     */
    abstract public function sql_insert($table, $values);

    /**
     * Update statement
     *
     * @param string $table the table name
     * @param array $values the update data
     * @param array $where the where clause
     * @return string
     */
    abstract public function sql_update($table, $values, $where = array());

    /**
     * Get last insert id
     *
     * @return int
     */
    abstract public function insert_id();

    /**
     * Error
     *
     * Returns an array containing code and message of the last
     * database error that has occurred.
     *
     * @return	array
     */
    abstract public function error();

    /**
     * Set client character set
     *
     * @param	string
     * @return	bool
     */
    abstract protected function _db_set_charset($charset);

    /**
     * Execute the query
     *
     * @param	string	$sql    An SQL query
     * @return	mixed
     */
    abstract protected function _query($sql);

    /**
     * Platform-dependant string escape
     *
     * @param	string
     * @return	string
     */
    abstract protected function _escape_str($str);

    /**
     * ------------------------------------------------------------
     * END Declare function needed
     * ------------------------------------------------------------
     */

    /**
     * FA_DB_driver constructor.
     * @param array $params
     */
    public function __construct($params)
    {
        if (is_array($params))
        {
            foreach ($params as $key => $val)
            {
                $this->$key = $val;
            }
        }

        log_message(MSG_INFO, 'Database Driver Class Initialized');
    }

    /**
     * Initialize Database Settings
     *
     * @return	bool
     */
    public function initialize()
    {
        /**
         * If an established connection is available, then there's
         * no need to connect and select the database.
         *
         * Depending on the database driver, conn_id can be either
         * boolean TRUE, a resource or an object.
         */
        if ($this->connID)
        {
            return TRUE;
        }

        /**
         * Connect to the database and set the connection ID
         */
        $this->connID = $this->db_connect($this->pconnect);

        /**
         * No connection resource?
         */
        if (!$this->connID)
        {
            log_message(MSG_ERROR, 'Unable to connect to the database');

            if ($this->debug)
            {
                $this->display_error('Unable to connect to the database');
            }
            return FALSE;
        }

        /**
         * Now we set the character set and that's all
         */
        return $this->db_set_charset($this->charset);
    }

    /**
     * Set client character set
     *
     * @param	string
     * @return	bool
     */
    public function db_set_charset($charset)
    {
        if (method_exists($this, '_db_set_charset') && ! $this->_db_set_charset($charset))
        {
            log_message(MSG_ERROR, 'Unable to set database connection charset: ' . $charset);

            if ($this->debug)
            {
                $this->display_error('Unable to set charset %s', $charset);
            }

            return FALSE;
        }

        return TRUE;
    }

    /**
     * Execute the query
     *
     * Accepts an SQL string as input and returns a result object upon
     * successful execution of a "read" type query. Returns boolean TRUE
     * upon successful execution of a "write" type query. Returns boolean
     * FALSE upon failure, and if the $db_debug variable is set to TRUE
     * will raise an error.
     *
     * @param	string  $sql    The sql query
     * @return	\FA\DATABASE\FA_DB_result|bool
     */
    public function query($sql)
    {
        if (!$this->connID)
        {
            $this->initialize();
        }
        $this->resultID = $this->_query($sql);
        if ($this->resultID === FALSE)
        {
            /**
             * Grab the error now, as we might run some additional queries before displaying the error
             */
            $error = $this->error();
            if ($error)
            {
                /**
                 * Log errors
                 */
                log_message(MSG_ERROR, 'Query error: '.$error['message'].' - Invalid query: ' . $sql);

                if ($this->debug)
                {
                    return $this->display_error(array('Error Number: '.$error['code'], $error['message'], $sql));
                }
            }
            return FALSE;
        }
        elseif ($this->resultID === TRUE)
        {
            return true;
        }

        $result_class   = $this->load_result_driver();
        $resultOBJ		    = new $result_class($this);

        return $resultOBJ;
    }

    /**
     * Load the result drivers
     *
     * @return	string	the name of the result class
     */
    public function load_result_driver()
    {
        $driver_class = '\FA\DATABASE\\' . $this->driver . '_result';

        if (!class_exists($driver_class, FALSE))
        {
            require_once(SYS_PATH . 'database/DB_result.php');
            require_once(SYS_PATH . 'database/drivers/' . $this->driver . '/' . $this->driver.'_result.driver.php');
        }

        return $driver_class;
    }

    /**
     * "Smart" Escape String
     *
     * Escapes data based on type
     * Sets boolean and null types
     *
     * @param	string
     * @return	mixed
     */
    public function escape($str)
    {
        if (is_array($str))
        {
            $str = array_map(array(&$this, 'escape'), $str);
            return $str;
        }
        elseif (is_string($str) OR (is_object($str) && method_exists($str, '__toString')))
        {
            return "'".$this->escape_str($str)."'";
        }
        elseif (is_bool($str))
        {
            return ($str === FALSE) ? 0 : 1;
        }
        elseif ($str === NULL)
        {
            return 'NULL';
        }

        return $str;
    }

    /**
     * Escape String
     *
     * @param	string|string[]	$str	Input string
     * @param	bool	$like	Whether or not the string will be used in a LIKE condition
     * @return	string
     */
    public function escape_str($str, $like = FALSE)
    {
        if (is_array($str))
        {
            foreach ($str as $key => $val)
            {
                $str[$key] = $this->escape_str($val, $like);
            }

            return $str;
        }

        $str = $this->_escape_str($str);

        /**
         * escape LIKE condition wildcards
         */
        if ($like === TRUE)
        {
            return str_replace(
                array($this->_like_escape_chr, '%', '_'),
                array($this->_like_escape_chr . $this->_like_escape_chr, $this->_like_escape_chr . '%', $this->_like_escape_chr.'_'),
                $str
            );
        }

        return $str;
    }

    /**
     * Handler get column
     *
     * @param string|array $get
     * @param string $table
     * @return string
     */
    public function handler_get($get, $table = '')
    {
        if ($get == '*' || !$get) return $table ? $table . '.*' : '*';
        if (!is_array($get))
        {
            $arr = explode(',', $get);
        }
        else
        {
            $arr = $get;
        }
        $arr = array_map(function($item) use ($table) {
            return '`' . ($table ? $table . '.' : '') . trim($item) . '`';
        }, $arr);
        return implode(', ', $arr);
    }

    /**
     * The name of the platform in use (mysqli, etc...)
     *
     * @return	string
     */
    public function platform()
    {
        return $this->driver;
    }

    /**
     * Display an error message
     *
     * @param   string	$error  the error message
     * @param	string	$swap   any "swap" values
     * @param	bool	$native whether to localize the message
     * @return	string	sends the fa-application/config/views/errors/error_db.php template
     */
    public function display_error($error = '', $swap = '', $native = FALSE)
    {
        $heading = 'Database error';

        if ($native === TRUE)
        {
            $message = (array) $error;
        }
        else
        {
            $message = is_array($error) ? $error : array(str_replace('%s', $swap, $error));
        }

        /**
         * Find the most likely culprit of the error by going through
         * the backtrace until the source file is no longer in the
         * database folder.
         */
        $trace = debug_backtrace();
        foreach ($trace as $call)
        {
            if (isset($call['file'], $call['class']))
            {
                /**
                 * We'll need this on Windows, as APP_PATH and SYS_PATH will always use forward slashes
                 */
                if (DIRECTORY_SEPARATOR !== '/')
                {
                    $call['file'] = str_replace('\\', '/', $call['file']);
                }

                if (strpos($call['file'], SYS_PATH . 'database') === FALSE && strpos($call['class'], 'Loader') === FALSE)
                {
                    // Found it - use a relative path for safety
                    $message[] = 'Filename: '.str_replace(array(APP_PATH, SYS_PATH), '', $call['file']);
                    $message[] = 'Line Number: ' . $call['line'];
                    break;
                }
            }
        }

        $error =& load_class('Exceptions', 'core');
        echo $error->show_error($heading, $message, 'error_db');
        exit; // EXIT_DATABASE
    }
}