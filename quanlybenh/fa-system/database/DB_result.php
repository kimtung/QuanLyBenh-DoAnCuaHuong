<?php
NAMESPACE FA\DATABASE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class FA_DB_result
 * @package FA\DATABASE
 */
abstract class FA_DB_result
{
    /**
     * Connection ID
     *
     * @var	object|resource|FALSE
     */
    public $connID;

    /**
     * Result ID
     *
     * @var resource|object
     */
    public $resultID;

    /**
     * Num rows
     *
     * @var int
     */
    public $num_rows;

    /**
     * Num fields
     *
     * @var int
     */
    public $num_fields;

    /**
     * Result array
     *
     * @var array
     */
    public $result_array;

    /**
     * Result object
     *
     * @var array
     */
    public $result_object;

    /**
     * Current row
     *
     * @var int
     */
    public $current_row;

    /**
     * Row data
     *
     * @var	array
     */
    public $row_data;

    /**
     * FA_DB_result constructor.
     *
     * @param object $driver_object
     */
    public function __construct(&$driver_object)
    {
        /**
         * Log info
         */
        log_message(MSG_INFO, 'Class Database result Initialized');

        $this->connID   = $driver_object->connID;
        $this->resultID = $driver_object->resultID;
    }

    /**
     * Number of rows in the result set
     *
     * @return	int
     */
    abstract public function num_rows();

    /**
     * Number of fields in the result set
     *
     * @return	int
     */
    abstract public function num_fields();

    /**
     * Fetch Field Names
     *
     * Generates an array of column names.
     *
     * @return	array
     */
    abstract public function list_fields();

    /**
     * Result - associative array
     *
     * Returns the result set as an array
     *
     * @return	array
     */
    abstract public function fetch_assoc();

    /**
     * Result - object
     *
     * Returns the result set as an object
     *
     * @param	string	$class_name
     * @return	object
     */
    abstract public function fetch_object($class_name = '\stdClass');

    /**
     * Data Seek
     *
     * Moves the internal pointer to the desired offset. We call
     * this internally before fetching results to make sure the
     * result set starts at zero.
     *
     * @param	int	$n
     * @return	bool
     */
    abstract public function data_seek($n = 0);

    /**
     * Free the result
     *
     * @return	void
     */
    abstract public function free_result();

    /**
     * Query result. Acts as a wrapper function for the following functions.
     *
     * @param	string	$type	'object', 'array' or a custom class name
     * @return	array
     */
    public function result($type = 'array')
    {
        if ($type === 'array')
        {
            return $this->result_array();
        }
        elseif ($type === 'object')
        {
            return $this->result_object();
        }
        return NULL;
    }

    /**
     * Query result. "array" version.
     *
     * @return	array
     */
    public function result_array()
    {
        if ($this->result_array) return $this->result_array;

        while ($row = $this->fetch_assoc())
        {
            $this->result_array[] = $row;
        }

        return $this->result_array;
    }

    /**
     * Query result. "object" version.
     *
     * @return	array
     */
    public function result_object()
    {
        if ($this->result_object) return $this->result_object;

        while ($row = $this->fetch_object())
        {
            $this->result_object[] = $row;
        }

        return $this->result_object;
    }

    /**
     * Row
     *
     * A wrapper method.
     *
     * @param	mixed	$n
     * @param	string	$type	'object' or 'array'
     * @return	mixed
     */
    public function row($n = 0, $type = 'array')
    {
        if ( ! is_numeric($n))
        {
            /**
             * We cache the row data for subsequent uses
             */
            is_array($this->row_data) OR $this->row_data = $this->row_array(0);

            /**
             * array_key_exists() instead of isset() to allow for NULL values
             */
            if (empty($this->row_data) OR ! array_key_exists($n, $this->row_data))
            {
                return NULL;
            }

            return $this->row_data[$n];
        }

        if ($type === 'object') return $this->row_object($n);
        elseif ($type === 'array') return $this->row_array($n);
        else return NULL;
    }

    /**
     * Assigns an item into a particular column slot
     *
     * @param	mixed	$key
     * @param	mixed	$value
     * @return	void
     */
    public function set_row($key, $value = NULL)
    {
        /**
         * We cache the row data for subsequent uses
         */
        if ( ! is_array($this->row_data))
        {
            $this->row_data = $this->row_array(0);
        }

        if (is_array($key))
        {
            foreach ($key as $k => $v)
            {
                $this->row_data[$k] = $v;
            }
            return;
        }

        if ($key !== '' && $value !== NULL)
        {
            $this->row_data[$key] = $value;
        }
    }

    /**
     * Returns a single result row - object version
     *
     * @param	int	$n
     * @return	object
     */
    public function row_object($n = 0)
    {
        $result = $this->result_object ? $this->result_object : $this->result_object();
        if (count($result) === 0)
        {
            return NULL;
        }

        if ($n !== $this->current_row && isset($result[$n]))
        {
            $this->current_row = $n;
        }

        return $result[$this->current_row];
    }

    /**
     * Returns a single result row - array version
     *
     * @param	int	$n
     * @return	array
     */
    public function row_array($n = 0)
    {
        $result = $this->result_array ? $this->result_array : $this->result_array();
        if (count($result) === 0)
        {
            return NULL;
        }

        if ($n !== $this->current_row && isset($result[$n]))
        {
            $this->current_row = $n;
        }

        return $result[$this->current_row];
    }

    /**
     * Returns the "first" row
     *
     * @param	string	$type
     * @return	mixed
     */
    public function first_row($type = 'array')
    {
        $result = $this->result($type);
        return (count($result) === 0) ? NULL : $result[0];
    }

    /**
     * Returns the "last" row
     *
     * @param	string	$type
     * @return	mixed
     */
    public function last_row($type = 'array')
    {
        $result = $this->result($type);
        return (count($result) === 0) ? NULL : $result[count($result) - 1];
    }

    /**
     * Returns the "next" row
     *
     * @param	string	$type
     * @return	mixed
     */
    public function next_row($type = 'array')
    {
        $result = $this->result($type);
        if (count($result) === 0)
        {
            return NULL;
        }

        return isset($result[$this->current_row + 1]) ? $result[++$this->current_row] : NULL;
    }

    /**
     * Returns the "previous" row
     *
     * @param	string	$type
     * @return	mixed
     */
    public function previous_row($type = 'array')
    {
        $result = $this->result($type);
        if (count($result) === 0)
        {
            return NULL;
        }

        if (isset($result[$this->current_row - 1]))
        {
            --$this->current_row;
        }
        return $result[$this->current_row];
    }
}