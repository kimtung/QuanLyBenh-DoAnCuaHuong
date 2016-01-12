<?php
NAMESPACE FA\LIBRARIES;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class cookie manager
 * @package FA\LIBRARIES
 */
Class cookie
{
    /**
     * cookie constructor.
     */
    public function __construct()
    {
        /**
         * Log info
         */
        log_message(MSG_INFO, 'Cookie Class Initialized');
    }

    /**
     * Set cookie
     *
     * Accepts an arbitrary number of parameters (up to 7) or an associative
     * array in the first parameter containing all the values.
     *
     * @param	string|mixed[]	$name		Cookie name or an array containing parameters
     * @param	string		    $value		Cookie value
     * @param	int|string		$expire		Cookie expiration time in seconds
     * @param	string		    $domain		Cookie domain (e.g.: '.yourdomain.com')
     * @param	string		    $path		Cookie path (default: '/')
     * @param	string		    $prefix		Cookie name prefix
     * @param	bool		    $secure		Whether to only transfer cookies via SSL
     * @param	bool		    $httponly	Whether to only makes the cookie accessible via HTTP (no javascript)
     * @return	void
     */
    public function set($name, $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = FALSE, $httponly = FALSE)
    {
        if (is_array($name))
        {
            /**
             * always leave 'name' in last place, as the loop will break otherwise, due to $$item
             */
            foreach (array('value', 'expire', 'domain', 'path', 'prefix', 'secure', 'httponly', 'name') as $item)
            {
                if (isset($name[$item]))
                {
                    $$item = $name[$item];
                }
            }
        }

        if ($prefix === '' && get_config_item('cookie_prefix') !== '')
        {
            $prefix = get_config_item('cookie_prefix');
        }

        if ($domain == '' && get_config_item('cookie_domain') != '')
        {
            $domain = get_config_item('cookie_domain');
        }

        if ($path === '/' && get_config_item('cookie_path') !== '/')
        {
            $path = get_config_item('cookie_path');
        }

        if ($secure === FALSE && get_config_item('cookie_secure') === TRUE)
        {
            $secure = get_config_item('cookie_secure');
        }

        if ($httponly === FALSE && get_config_item('cookie_httponly') !== FALSE)
        {
            $httponly = get_config_item('cookie_httponly');
        }

        if ( ! is_numeric($expire))
        {
            $expire = time() - 86500;
        }
        else
        {
            $expire = ($expire > 0) ? time() + $expire : 0;
        }

        setcookie($prefix.$name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * Fetch an item from the COOKIE array
     *
     * @param	mixed	$name		Index for item to be fetched from $_COOKIE
     * @param	bool	$xss_clean	Whether to apply XSS filtering
     * @return	mixed
     */
    public function get($name, $xss_clean = NULL)
    {
        $fa = fa_instance();
        return $fa->input->cookie($name, $xss_clean);
    }

    /**
     * Check cookie is already exists
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * Delete a COOKIE
     *
     * @param	mixed   $name
     * @param	string	$domain the cookie domain. Usually: .yourdomain.com
     * @param	string	$path   the cookie path
     * @param	string	$prefix the cookie prefix
     * @return	void
     */
    function del($name, $domain = '', $path = '/', $prefix = '')
    {
        if ($this->has($name)) {
            $this->set($name, '', '', $domain, $path, $prefix);
        }
    }
}