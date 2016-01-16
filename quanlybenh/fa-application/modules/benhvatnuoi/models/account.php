<?php
NAMESPACE FA\MODELS\M_BENHVATNUOI;
USE \FA\CORE as CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class account
 * @package FA\MODELS\M_BENHVATNUOI
 */
Class account extends CORE\FA_Models
{
    public $table;

    protected $_paging = array();
    protected $_query = array();

    /**
     * UID encrypt key
     * @var int
     */
    public $uid_encrypt_key = 134451;

    /**
     * account constructor.
     */
    public function __construct()
    {
        parent::__settings('database', TRUE);
        parent::__construct();

        $this->table = 'bvn_accounts';
    }

    /**
     * Check user (id, username) is already exists
     *
     * @param string|int $uid
     * @return int|FALSE
     */
    public function user_exists($uid)
    {
        if (preg_match('/^[0-9]+$/', $uid))
        {
            $col = 'id';
        }
        else
        {
            $col = 'username';
        }
        $uid = $this->db->escape_str($uid);
        $query = $this->db->query("SELECT `id` FROM $this->table WHERE `$col` = '$uid'");
        if ($query->num_rows)
        {
            $row = $query->fetch_assoc();
            return $row['id'];
        }
        return FALSE;
    }

    /**
     * Check email is already exists
     *
     * @param string $email
     * @return int|FALSE
     */
    public function email_exists($email)
    {
        $email = $this->db->escape_str($email);
        $query = $this->db->query("SELECT `id` FROM $this->table WHERE `email` = '$email'");
        if ($query->num_rows)
        {
            $row = $query->fetch_assoc();
            return $row['id'];
        }
        return FALSE;
    }

    /**
     * Get user data
     *
     * @param int $user_id
     * @param string $get
     * @return array|null
     */
    public function user_data($user_id, $get = '*')
    {
        if (preg_match('/^[0-9]+$/', $user_id))
        {
            $col = 'id';
        }
        else
        {
            $col = 'username';
        }

        $get = $this->db->handler_get($get);
        $user_id = $this->db->escape_str($user_id);
        $query = $this->db->query("SELECT $get FROM $this->table WHERE `$col` = '$user_id'");
        if ($query->num_rows)
        {
            return $query->fetch_assoc();
        }
        return NULL;
    }

    public function list_users($options = array())
    {
        $default_options = array(
            'get'       => '*',
            'active'    => null,
            'order_by'  => 'time',
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
        if ($options['active'] !== NULL)
        {
            $options['active'] = (int) $options['active'];
            $where[] = "`active` = '" . $options['active'] . "'";
        }
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
            case 'time':
                $order_by = 'time_created';
                break;
                break;
            case 'rand':
                $order_by = 'RAND()';
                break;
            default:
                $order_by = 'time_created';
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
            return $query->fetch_assoc();
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
     * Count users
     *
     * @return int
     */
    public function count()
    {
        $query = $this->db->query("SELECT count(id) as total FROM $this->table");
        $row = $query->fetch_assoc();
        return $row['total'];
    }

    /**
     * Insert new account to table ans_accounts
     *
     * @param array $data
     * @return false|int
     */
    public function insert_user($data)
    {
        if (isset($data['fullname']))
            $data['fullname'] = $this->_encode_fullname($data['fullname']);

        if (isset($data['phone']))
            $data['phone'] = $this->_encode_phone($data['phone']);

        if ($this->db->query($this->db->sql_insert($this->table, $data)))
        {
            return $this->db->insert_id();
        }
        else
        {
            return false;
        }
    }

    public function update_user($user_id, $data)
    {
        $user_id = $this->db->escape_str($user_id);

        if (isset($data['fullname']))
            $data['fullname'] = $this->_encode_fullname($data['fullname']);

        if (isset($data['phone']))
            $data['phone'] = $this->_encode_phone($data['phone']);

        if ($this->db->query($this->db->sql_update($this->table, $data, "id='$user_id'")))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Delete user
     *
     * @param $user_id
     * @return bool
     */
    public function delete_user($user_id)
    {
        $user_id = $this->db->escape_str($user_id);
        return $this->db->query("DELETE FROM $this->table WHERE `id` = '$user_id'");
    }

    /**
     * Set client token when user logged
     *
     * @param int $user_id
     * @param bool $remember
     */
    public function set_token_login($user_id, $remember = FALSE)
    {
        $this->load->library('session');
        $this->load->library('cookie');

        $ip = $this->input->ip_address();
        $ua = $this->input->user_agent();
        $token_login = $this->generate_token_login($user_id, $ip, $ua);
        $token_uid = $this->_encode_uid($user_id);
        /**
         * Get session object
         * @var  \FA\LIBRARIES\session $session
         */
        $session = $this->lib->session;
        $session->set('token_login', $token_login);
        $session->set('token_uid', $token_uid);
        if ($remember)
        {
            /**
             * Get cookie object
             * @var  \FA\LIBRARIES\cookie $cookie
             */
            $cookie = $this->lib->cookie;
            $cookie->set('token_login', $token_login, 365*24*60*60);
            $cookie->set('token_uid', $token_uid, 365*24*60*60);
        }
    }

    /**
     * Valid token login
     * Return User ID when token is valid
     *
     * @param int $token_uid
     * @param string $token_login
     * @return FALSE|INT
     */
    public function valid_token_login($token_uid, $token_login)
    {
        $user_id = $this->_decode_uid($token_uid);
        $ip = $this->input->ip_address();
        $ua = $this->input->user_agent();
        $token = $this->generate_token_login($user_id, $ip, $ua);
        if ($token == $token_login)
        {
            return $user_id;
        }
        return FALSE;
    }

    /**
     * Delete token
     */
    public function unset_token_login()
    {
        $this->load->library('session');
        $this->load->library('cookie');
        $this->lib->session->del('token_login');
        $this->lib->session->del('token_uid');
        $this->lib->cookie->del('token_login');
        $this->lib->cookie->del('token_uid');
    }

    /**
     * Generate token for a client
     *
     * @param int $user_id
     * @param string $ip
     * @param string $user_agent
     * @return string
     */
    public function generate_token_login($user_id, $ip, $user_agent)
    {
        $user = $this->user_data($user_id, 'password');
        $token_password = md5($user['password']);
        $token_client   = md5($ip . '|' . $user_agent);
        $token = md5($token_password . '|' . $token_client);
        return $token;
    }

    public function _encode_uid($uid)
    {
        $token_uid = $this->uid_encrypt_key+$uid;
        return $token_uid;
    }

    public function _decode_uid($token_uid)
    {
        $uid = $token_uid-$this->uid_encrypt_key;
        return $uid;
    }

    /**
     * Encode fullname to insert to database
     *
     * @param string $fullname
     * @return string
     */
    public function _encode_fullname($fullname)
    {
        return htmlspecialchars($fullname);
    }

    /**
     * Encode phone number to insert to database
     *
     * @param string $phone
     * @return string
     */
    public function _encode_phone($phone)
    {
        return htmlspecialchars($phone);
    }

    /**
     * Encrypt password
     *
     * @param string $password
     * @return string
     */
    public function encrypt_password($password)
    {
        return md5(md5($password));
    }

    public function valid_username($username)
    {
        if (preg_match('/^[a-zA-Z0-9_]+$/i', $username))
        {
            return TRUE;
        }
        return FALSE;
    }
}