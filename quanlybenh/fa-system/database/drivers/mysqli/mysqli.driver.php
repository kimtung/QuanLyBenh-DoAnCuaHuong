<?php
NAMESPACE FA\DATABASE\DRIVERS;
USE \FA\DATABASE AS DATABASE;

defined('BASE_PATH') OR exit('No direct script access allowed');

class mysqli extends DATABASE\FA_DB_driver
{
    /**
     * DELETE hack flag
     *
     * Whether to use the MySQL "delete hack" which allows the number
     * of affected rows to be shown. Uses a preg_replace when enabled,
     * adding a bit more processing to all queries.
     *
     * @var	bool
     */
    public $delete_hack = TRUE;

    /**
     * Database connection
     *
     * @param	bool	$persistent
     * @return	object
     */
    public function db_connect($persistent = FALSE)
    {
        /**
         * Do we have a socket path?
         */
        if ($this->hostname[0] === '/')
        {
            $hostname = NULL;
            $port = NULL;
            $socket = $this->hostname;
        }
        else
        {
            /**
             * Persistent connection support was added in PHP 5.3.0
             */
            $hostname = ($persistent === TRUE && is_php('5.3')) ? 'p:'.$this->hostname : $this->hostname;
            $port = empty($this->port) ? NULL : $this->port;
            $socket = NULL;
        }

        $client_flags = ($this->compress === TRUE) ? MYSQLI_CLIENT_COMPRESS : 0;
        $mysqli = mysqli_init();

        $mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);

        if (is_array($this->encrypt))
        {
            $ssl = array();
            empty($this->encrypt['ssl_key'])    OR $ssl['key']    = $this->encrypt['ssl_key'];
            empty($this->encrypt['ssl_cert'])   OR $ssl['cert']   = $this->encrypt['ssl_cert'];
            empty($this->encrypt['ssl_ca'])     OR $ssl['ca']     = $this->encrypt['ssl_ca'];
            empty($this->encrypt['ssl_capath']) OR $ssl['capath'] = $this->encrypt['ssl_capath'];
            empty($this->encrypt['ssl_cipher']) OR $ssl['cipher'] = $this->encrypt['ssl_cipher'];

            if (!empty($ssl))
            {
                if ( ! empty($this->encrypt['ssl_verify']) && defined('MYSQLI_OPT_SSL_VERIFY_SERVER_CERT'))
                {
                    $mysqli->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, TRUE);
                }

                $client_flags |= MYSQLI_CLIENT_SSL;
                $mysqli->ssl_set(
                    isset($ssl['key'])    ? $ssl['key']    : NULL,
                    isset($ssl['cert'])   ? $ssl['cert']   : NULL,
                    isset($ssl['ca'])     ? $ssl['ca']     : NULL,
                    isset($ssl['capath']) ? $ssl['capath'] : NULL,
                    isset($ssl['cipher']) ? $ssl['cipher'] : NULL
                );
            }
        }

        if ($mysqli->real_connect($hostname, $this->username, $this->password, $this->database, $port, $socket, $client_flags))
        {
            /**
             * Prior to version 5.7.3, MySQL silently downgrades to an unencrypted connection if SSL setup fails
             */
            if (
                ($client_flags & MYSQLI_CLIENT_SSL)
                && version_compare($mysqli->client_info, '5.7.3', '<=')
                && empty($mysqli->query("SHOW STATUS LIKE 'ssl_cipher'")->fetch_object()->Value)
            )
            {
                $mysqli->close();
                $message = 'MySQLi was configured for an SSL connection, but got an unencrypted connection instead!';
                log_message(MSG_ERROR, $message);
                return ($this->debug) ? $this->display_error($message, '', TRUE) : FALSE;
            }
            return $mysqli;
        }
        return FALSE;
    }

    public function db_pconnect() {}

    /**
     * Reconnect
     *
     * Keep / reestablish the db connection if no queries have been
     * sent for a length of time exceeding the server's idle timeout
     *
     * @return	void
     */
    public function reconnect()
    {
        if ($this->connID !== FALSE && $this->connID->ping() === FALSE)
        {
            $this->connID = FALSE;
        }
    }

    /**
     * Close DB Connection
     *
     * @return	void
     */
    public function db_close()
    {
        $this->connID->close();
        $this->connID = FALSE;
    }

    /**
     * Select the database
     *
     * @param	string	$database
     * @return	bool
     */
    public function db_select($database = '')
    {
        if ($database === '')
        {
            $database = $this->database;
        }

        if ($this->connID->select_db($database))
        {
            $this->database = $database;
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Set client character set
     *
     * @param	string	$charset
     * @return	bool
     */
    protected function _db_set_charset($charset)
    {
        return $this->connID->set_charset($charset);
    }

    /**
     * Execute the query
     *
     * @param	string	$sql    An SQL query
     * @return	mixed
     */
    protected function _query($sql)
    {
        return $this->connID->query($this->_prep_query($sql));
    }

    /**
     * Prep the query
     *
     * If needed, each database adapter can prep the query string
     *
     * @param	string	$sql    An SQL query
     * @return	string
     */
    protected function _prep_query($sql)
    {
        /**
         * mysqli_affected_rows() returns 0 for "DELETE FROM TABLE" queries. This hack
         * modifies the query so that it a proper number of affected rows is returned.
         */
        if ($this->delete_hack === TRUE && preg_match('/^\s*DELETE\s+FROM\s+(\S+)\s*$/i', $sql))
        {
            return trim($sql).' WHERE 1=1';
        }

        return $sql;
    }

    /**
     * Platform-dependant string escape
     *
     * @param	string
     * @return	string
     */
    protected function _escape_str($str)
    {
        return $this->connID->real_escape_string($str);
    }

    /**
     * Insert statement
     *
     * @param   string $table   the table name
     * @param   array  $values  the insert values
     * @return  string
     */
    public function sql_insert($table, $values)
    {
        $valStr = array();
        foreach ($values as $key => $val)
        {
            $val = $this->_escape_str($val);
            $valStr[] = "`$key` = '$val'";
        }
        return "INSERT INTO `$table` SET " . implode(', ', $valStr);
    }

    /**
     * Update statement
     *
     * @param string $table the table name
     * @param array $values the update data
     * @param array $where the where clause
     * @return string
     */
    public function sql_update($table, $values, $where = array())
    {
        $valStr = array();
        foreach ($values as $key => $val)
        {
            if (preg_match('/\{col:([^\{\}\s]+)\}/', $val))
            {
                $val = preg_replace('/\{col:([^\{\}\s]+)\}/', '`$1`', $val);
                $valStr[] = "`$key` = $val";
            }
            else
            {
                $val = $this->_escape_str($val);
                $valStr[] = "`$key` = '$val'";
            }
        }
        $sql = "UPDATE `$table` SET " . implode(', ', $valStr);
        if (!is_array($where))
        {
            $sql .= " WHERE " . $where;
        }
        else
        {
            $sql .= ($where != '' AND count($where) >= 1) ? " WHERE " . implode(" ", $where) : '';
        }
        return $sql;
    }

    /**
     * Insert ID
     *
     * @return	int
     */
    public function insert_id()
    {
        return $this->connID->insert_id;
    }

    /**
     * Error
     *
     * Returns an array containing code and message of the last
     * database error that has occurred.
     *
     * @return	array
     */
    public function error()
    {
        if ( ! empty($this->connID->connect_errno))
        {
            return array(
                'code' => $this->connID->connect_errno,
                'message' => is_php('5.2.9') ? $this->connID->connect_error : mysqli_connect_error()
            );
        }

        return array('code' => $this->connID->errno, 'message' => $this->connID->error);
    }
}