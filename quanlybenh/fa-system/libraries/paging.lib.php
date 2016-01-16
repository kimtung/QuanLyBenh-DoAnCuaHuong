<?php
NAMESPACE FA\LIBRARIES;

defined('BASE_PATH') OR exit('No direct script access allowed');

Class paging
{
    protected $_url;
    protected $_url_arg;

    protected $_page;
    protected $_limit;
    protected $_total;
    protected $_max_page;
    protected $_current_page;
    protected $offset;

    protected $_map;

    /**
     * paging constructor.
     */
    function __construct()
    {
        $this->_page            = 0;
        $this->_limit           = 0;
        $this->_max_page        = 0;
        $this->_total           = 0;
        $this->_current_page    = 1;

        /**
         * Get FA instance
         */
        $fa = fa_instance();

        /**
         * Load paging language file
         */
        $fa->lang->load('paging_lib', NULL, TRUE);

        $this->_map = array(
            'start'         => '<ul class="pagination pagination-sm">',
            'end'           => '</ul>',
            'item'          => '<li><a href="{url}" title="{txt_page} {page}">{content}</a></li>',
            'item_current'  => '<li class="active"><a href="{url}" title="{txt_page} {page}">{content}</a></li>',
            'txt_page'      => $fa->lang->lng('Page'),
            'next_fast'     => '<i class="fa fa-angle-double-right"></i>',
            'prev_fast'     => '<i class="fa fa-angle-double-left"></i>',
            'next_class'    => 'next',
            'prev_class'    => 'previous',
            'last_class'    => 'last',
        );

        /**
         * **************************************
         *  Hook "paging_map"
         * **************************************
         */
        $this->_map = $fa->hook->filter(FA, 'paging_map', $this->_map);
    }

    /**
     * Set number item per page
     *
     * @param int $limit
     */
    public function set_limit($limit = 10)
    {
        $this->_limit = $limit;
    }

    /**
     * Set page to get
     *
     * @param int $page
     */
    public function set_page($page = 0)
    {
        $this->_page = $page;
    }

    /**
     * Set total result
     *
     * @param $total
     */
    public function set_total($total)
    {
        $this->_total = $total;
        $this->_process_max_page();
    }

    /**
     * Process get max page from total
     * @void
     */
    protected function _process_max_page()
    {
        if ($this->_total <= $this->_limit)
        {
            $this->_max_page = 1;
        }
        else
        {
            if ($this->_total % $this->_limit != 0)
            {
                $this->_max_page = ceil($this->_total / $this->_limit);
            }
            else
            {
                $this->_max_page = $this->_total / $this->_limit;
            }
        }
    }

    /**
     * Get offset
     *
     * @return int
     */
    function offset()
    {
        /**
         * Get current page
         */
        $page_number = (int) $this->_page;
        $this->_current_page = ($page_number > 0) ? $page_number : 1;

        if ($this->_current_page <= 1)
        {
            $offset = 0;
            $this->_current_page = 1;
        }
        else
        {
            $offset = ($this->_current_page - 1) * $this->_limit;
        }
        $this->offset = (int) $offset;
        return $this->offset;
    }

    /**
     * Get max page
     *
     * @return int
     */
    public function max_page()
    {
        return $this->_max_page;
    }

    protected function _item($item, $url, $page, $content = NULL)
    {
        $arr = array(
            '{url}' => $url,
            '{page}' => $page,
            '{content}' => $page,
            '{txt_page}'  => $this->_map['txt_page']
        );
        if ($content)
        {
            $arr['{content}'] = $content;
        }
        $out = strtr($item, $arr);
        return $out;
    }

    public function html($url = '', $sts = 5)
    {
        if ($this->_max_page == 1)
        {
            return '';
        }

        $map = $this->_map;
        $this->_url = $url;

        if ($this->_max_page <= $sts)
        {
            $start = 1;
            $end = $this->_max_page;
        }
        /**
         * Max page > sts
         */
        elseif ($this->_current_page <= $sts)//4<=5, max page > sts
        {
            $start = 1;
            $end = $sts;
            $prev_fast = '';
            if ($this->_max_page > $sts && ($end+1) < $this->_max_page)
            {
                $next_fast = $end + 1;
            }
        }
        else
        {
            $_left = round($sts/2);
            $_right = $sts - $_left;
            if ($_left > $_right) $_left--;
            elseif ($_right > $_left) $_right--;

            while ($this->_current_page < $_left)
            {
                $_left--;
                $_right = $sts - $_left;
            }

            $start = $this->_current_page - $_left;
            $end = $this->_current_page + $_right;

            if (($start - 1) > 1)
            {
                $prev_fast = $start - 1;
            }
            if (($end + 1) < $this->_max_page)
            {
                $next_fast = $end + 1;
            }
        }

        if ($end >= $this->_max_page)
        {
            $end = $this->_max_page;
            $next_fast = '';
        }

        if (!empty($prev_fast))
        {
            $prev_html = $this->_item($map['item'], $this->_create_url($prev_fast), $prev_fast, $map['prev_fast']);
        }
        if (!empty($next_fast))
        {
            $next_html = $this->_item($map['item'], $this->_create_url($next_fast), $next_fast, $map['next_fast']);
        }

        $html = $map['start'];

        if (isset($prev_html))
        {
            $html .= $this->_item($map['item'], $this->_create_url(1), 1) . $prev_html;
        }

        for ($t = $start; $t <= $end; $t++)
        {
            if ($t == $this->_current_page)
            {
                $html .= $this->_item($map['item_current'], $this->_create_url($t), $t);
            }
            else
            {
                $html .= $this->_item($map['item'], $this->_create_url($t), $t);
            }
        }

        if ($this->_current_page != $this->_max_page && $this->_max_page > $end)
        {
            $html .= (isset($next_html) ? $next_html : '') . $this->_item($map['item'], $this->_create_url($this->_max_page), $this->_max_page);
        }

        $html .= $map['end'];

        return $html;
    }

    protected function _create_url($num)
    {
        if (!$this->_url)
        {
            /**
             * Get current page url
             */
            $this->_url = current_url();

            $parse = parse_url($this->_url);

            $arr = array();
            if (isset($parse['query']))
            {
                parse_str($parse['query'], $arr);
            }

            /**
             * Change page value
             */
            $arr['page'] = $num;

            /**
             * Rebuild query
             */
            $parse['query'] = http_build_query($arr);

            /**
             * Un parse url
             */
            $new_url = $this->_un_parse_url($parse);
        }
        else
        {
            $new_url = str_replace('{page}', $num, $this->_url);
        }
        return $new_url;
    }

    /**
     * Un parse url from function parse_url
     *
     * @param array $parsed_url
     * @return string
     */
    protected function _un_parse_url($parsed_url)
    {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}