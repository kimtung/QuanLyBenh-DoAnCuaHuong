<?php
NAMESPACE FA\CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * URI Class
 *
 * Class FA_URI
 * @package FA\CORE
 */
Class FA_URI
{
    /**
     * @var string
     */
    protected $_uri_string;

    /**
     * @var bool
     */
    protected $_url_friendly = FALSE;

    /**
     * @var string
     */
    protected $_uri_protocol = 'REQUEST_URI';

    /**
     * FA_URI constructor.
     * @param null $routing
     */
    public function __construct($routing = NULL)
    {
        $this->config =& load_class('Config', 'core');

        $this->_url_friendly = $this->config->item('url_friendly') ? TRUE : FALSE;

        $uri_protocol = $this->config->item('uri_protocol');
        if ($uri_protocol)
        {
            $this->_uri_protocol = $uri_protocol;
        }

        switch($this->_uri_protocol)
        {
            case 'REQUEST_URI':
                $uri = $this->_parse_request_uri();
                break;
            case 'PATH_INFO':
            default:
                $uri = isset($_SERVER[$this->_uri_protocol])
                    ? $_SERVER[$this->_uri_protocol]
                    : $this->_parse_request_uri();
                break;
        }

        $this->_set_uri_string($uri);

        log_message(MSG_INFO, 'URI Class Initialized');
    }

    /**
     * Set URI string
     *
     * @param string $uri
     */
    public function _set_uri_string($uri)
    {
        $this->_uri_string = $uri;
    }

    /**
     * Return URI string
     *
     * @return string
     */
    public function uri_string()
    {
        return $this->_uri_string;
    }

    /**
     * Parse request URI
     *
     * @return string
     */
    public function _parse_request_uri()
    {
        if (!isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']))
        {
            return '';
        }

        // parse_url() returns false if no host is present, but the path or query string
        // contains a colon followed by a number
        $uri = parse_url('http://dummy'.$_SERVER['REQUEST_URI']);

        $query = isset($uri['query']) ? $uri['query'] : '';
        $uri = isset($uri['path']) ? $uri['path'] : '';

        if (isset($_SERVER['SCRIPT_NAME'][0]))
        {
            if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
            {
                $uri = (string) substr($uri, strlen($_SERVER['SCRIPT_NAME']));
            }
            elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
            {
                $uri = (string) substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
            }
        }

        // This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
        // URI is found, and also fixes the QUERY_STRING server var and $_GET array.
        if (trim($uri, '/') === '' && strncmp($query, '/', 1) === 0)
        {
            $query = explode('?', $query, 2);
            $uri = $query[0];
            $_SERVER['QUERY_STRING'] = isset($query[1]) ? $query[1] : '';
        }
        else
        {
            $_SERVER['QUERY_STRING'] = $query;
        }

        parse_str($_SERVER['QUERY_STRING'], $_GET);

        $uri = trim($uri, '/');

        if ($uri === '/' OR $uri === '')
        {
            return '';
        }

        // Do some final cleaning of the URI and return it
        return $this->_remove_relative_directory($uri);
    }

    /**
     * Remove relative directory (../) and multi slashes (///)
     *
     * Do some final cleaning of the URI and return it, currently only used in self::_parse_request_uri()
     *
     * @param string $uri
     * @return string
     */
    protected function _remove_relative_directory($uri)
    {
        $uris = array();
        $tok = strtok($uri, '/');
        while ($tok !== FALSE)
        {
            if (( ! empty($tok) OR $tok === '0') && $tok !== '..')
            {
                $uris[] = $tok;
            }
            $tok = strtok('/');
        }

        return implode('/', $uris);
    }
}