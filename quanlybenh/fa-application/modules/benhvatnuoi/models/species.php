<?php
NAMESPACE FA\MODELS\M_BENHVATNUOI;
USE \FA\CORE as CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class species
 * @package FA\MODELS\M_BENHVATNUOI
 */
Class species extends CORE\FA_Models
{
    public $table;

    protected $_paging = array();
    protected $_query = array();

    /**
     * account constructor.
     */
    public function __construct()
    {
        parent::__settings('database', TRUE);
        parent::__construct();

        $this->table = 'bvn_species';
    }

    /**
     * Check name is already exists
     *
     * @param string $name
     * @param int $without
     * @return int|FALSE
     */
    public function name_exists($name, $without = 0)
    {
        $name = $this->db->escape_str($name);
        $query = $this->db->query("SELECT `id` FROM $this->table WHERE `name` = '$name'" . ($without ? " AND `id` != '$without'" : ""));
        if ($query->num_rows)
        {
            $row = $query->fetch_assoc();
            return $row['id'];
        }
        return FALSE;
    }



    /**
     * Check id is already exists
     *
     * @param int $id
     * @return int|FALSE
     */
    public function id_exists($id)
    {
        $id = $this->db->escape_str($id);
        $query = $this->db->query("SELECT `id` FROM $this->table WHERE `id` = '$id'");
        if ($query->num_rows)
        {
            $row = $query->fetch_assoc();
            return $row['id'];
        }
        return FALSE;
    }

    /**
     * Get species data
     *
     * @param int $id
     * @param string $get
     * @return array|null
     */
    public function data($id, $get = '*')
    {
        $get = $this->db->handler_get($get);
        $id = $this->db->escape_str($id);
        $query = $this->db->query("SELECT $get FROM $this->table WHERE `id` = '$id'");
        if ($query->num_rows)
        {
            return $query->fetch_assoc();
        }
        return NULL;
    }

    public function count()
    {
        $query = $this->db->query("SELECT COUNT(id) as total FROM $this->table");
        $row = $query->fetch_assoc();
        return $row['total'];
    }

    public function lists($options = array())
    {
        $default_options = array(
            'get'       => '*',
            'order_by'  => 'id',
            'order_type'=> 'desc',
            'limit'     => 10,
            'offset'    => 0,
            'page'      => NULL,
            'page_url'  => NULL
        );

        foreach ($default_options as $k => $opt)
        {
            if (!isset($options[$k]))
            {
                $options[$k] = $opt;
            }
        }

        /**
         * Get
         */
        $_get = $this->db->handler_get($options['get']);

        /**
         * Where
         */
        $where = array();
        $_where = '';
        if ($where)
        {
            $_where = ' WHERE ' . implode(' AND ', $where);
        }

        /**
         * ORDER
         */
        switch ($options['order_by'])
        {
            case 'id':
                $order_by = 'id';
                break;
                break;
            case 'rand':
                $order_by = 'RAND()';
                break;
            default:
                $order_by = 'id';
                break;
        }
        switch ($options['order_type'])
        {
            case 'desc':
                $order_type = 'DESC';
                break;
            case 'asc':
                $order_type = 'ASC';
                break;
            default:
                $order_type = 'DESC';
                break;
        }
        if ($options['order_by'] == 'rand') $order_type = '';

        $_order = " ORDER BY $order_by $order_type";

        /**
         * Limit
         */
        $_limit = '';
        if ($options['limit'])
        {
            $limit = $options['limit'];
            if ($options['offset'])
            {
                $offset = $options['offset'];
            }
            elseif (is_int($options['page']))
            {
                /**
                 * Load paging class
                 */
                $this->load->library('paging');

                /**
                 * Get paging object
                 *
                 * @var \FA\LIBRARIES\paging $paging
                 */
                $paging = $this->lib->paging;

                $paging->set_limit($limit);
                $paging->set_page($options['page']);
                $offset = $paging->offset();

                /**
                 * Count total
                 */
                $query = $this->db->query("SELECT COUNT(id) as total FROM $this->table" . $_where);
                $row = $query->fetch_assoc();
                $paging->set_total($row['total']);
                if ($options['page_url'])
                {
                    $page_url = $options['page_url'];
                }
                else $page_url = '';

                $this->_paging[__FUNCTION__] = $paging->html($page_url);
            }
            $_limit = " LIMIT $limit" . (isset($offset) ? ' OFFSET ' . $offset : '');
        }

        /**
         * Execute query
         */
        $query = $this->db->query("SELECT $_get FROM $this->table" . $_where . $_order . $_limit);
        /**
         * Save query
         */
        $this->_query[__FUNCTION__] = $query;
    }

    public function fetch($function_name)
    {
        if (isset($this->_query[$function_name]))
        {
            /**
             * @var \FA\DATABASE\FA_DB_result $query
             */
            $query = $this->_query[$function_name];
            return $this->tuning($query->fetch_assoc());
        }
        return array();
    }

    public function has_result($function_name)
    {
        if (isset($this->_query[$function_name]))
        {
            /**
             * @var \FA\DATABASE\FA_DB_result $query
             */
            $query = $this->_query[$function_name];
            if ($query->num_rows)
            {
                return TRUE;
            }
        }
        return false;
    }

    /**
     * Return paging html of a function use paging library
     *
     * @param string $function_name
     * @return string
     */
    public function paging($function_name)
    {
        return isset($this->_paging[$function_name]) ? $this->_paging[$function_name] : '';
    }

    /**
     * Tuning data
     *
     * @param array $data
     * @return array
     */
    public function tuning($data)
    {
        if (isset($data['description']))
        {
            $data['description'] = $this->_decode_description($data['description']);
        }
        if (isset($data['thumbnail']))
        {
            $data['full_thumbnail'] = $data['thumbnail'] ? BASE_URL . 'fa-application/uploads/' . $data['thumbnail'] : '';
            $data['full_path_thumbnail'] = $data['thumbnail'] ? APP_PATH . 'uploads/' . $data['thumbnail'] : '';
        }
        return $data;
    }

    /**
     * Insert new species to table ans_accounts
     *
     * @param array $data
     * @return false|int
     */
    public function insert($data)
    {
        if (isset($data['name']))
            $data['name'] = $this->_encode_name($data['name']);

        if (isset($data['description']))
            $data['description'] = $this->_encode_description($data['description']);

        if ($this->db->query($this->db->sql_insert($this->table, $data)))
        {
            return $this->db->insert_id();
        }
        else
        {
            return false;
        }
    }

    public function update($id, $data)
    {
        $id = $this->db->escape_str($id);

        if (isset($data['name']))
            $data['name'] = $this->_encode_name($data['name']);

        if (isset($data['description']))
            $data['description'] = $this->_encode_description($data['description']);

        if ($this->db->query($this->db->sql_update($this->table, $data, "id='$id'")))
        {
            return $id;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Delete species
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $id = $this->db->escape_str($id);
        return $this->db->query("DELETE FROM $this->table WHERE `id` = '$id'");
    }

    public function _encode_name($name)
    {
        return htmlspecialchars($name);
    }

    public function _encode_description($desc)
    {
        return htmlspecialchars($desc);
    }

    public function _decode_description($desc)
    {
        return htmlspecialchars_decode($desc);
    }
}