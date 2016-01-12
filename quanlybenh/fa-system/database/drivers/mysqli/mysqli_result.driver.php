<?php
NAMESPACE FA\DATABASE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class mysqli_result
 * @package FA\DATABASE
 */
class mysqli_result extends FA_DB_result
{
    /**
     * mysqli_result constructor.
     * @param object $driver_object
     */
    public function __construct(&$driver_object)
    {
        parent::__construct($driver_object);

        $this->num_rows     = $driver_object->resultID->num_rows;
        $this->num_fields   = $driver_object->resultID->field_count;
    }

    /**
     * Number of rows in the result set
     *
     * @return	int
     */
    public function num_rows()
    {
        return $this->num_rows;
    }

    /**
     * Number of fields in the result set
     *
     * @return	int
     */
    public function num_fields()
    {
        return $this->num_fields;
    }

    /**
     * Fetch Field Names
     *
     * Generates an array of column names.
     *
     * @return	array
     */
    public function list_fields()
    {
        $field_names = array();
        $this->resultID->field_seek(0);
        while ($field = $this->resultID->fetch_field())
        {
            $field_names[] = $field->name;
        }

        return $field_names;
    }

    /**
     * Result - associative array
     *
     * Returns the result set as an array
     *
     * @return	array
     */
    public function fetch_assoc()
    {
        return $this->resultID->fetch_assoc();
    }

    /**
     * Result - object
     *
     * Returns the result set as an object
     *
     * @param	string	$class_name
     * @return	object
     */
    public function fetch_object($class_name = 'stdClass')
    {
        return $this->resultID->fetch_object($class_name);
    }

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
    public function data_seek($n = 0)
    {
        return $this->resultID->data_seek($n);
    }

    /**
     * Free the result
     *
     * @return	void
     */
    public function free_result()
    {
        if (is_object($this->resultID))
        {
            $this->resultID->free();
            $this->resultID = FALSE;
        }
    }
}