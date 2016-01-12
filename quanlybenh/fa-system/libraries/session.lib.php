<?php
NAMESPACE FA\LIBRARIES;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class session
 * @package FA\LIBRARIES
 */
Class session
{
    /**
     * session constructor.
     */
    public function __construct()
    {
        if(!headers_sent())
        {
            if (!session_id())
            {
                if(session_start())
                {
                    session_regenerate_id();
                }
                else
                {
                    show_error('Unable to start the session');
                }
            }
            else
            {
                log_message(MSG_DEBUG, 'Session already start');
            }
        }
        else
        {
            show_error('Unable to start the session for reasons headers already sent');
        }

        /**
         * Log info
         */
        log_message(MSG_INFO, 'Session Class Initialized');
    }

    /**
     * Set session
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Check session already exists
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Get session value
     *
     * @param string $key
     * @return NULL|mixed
     */
    public function get($key)
    {
        return (isset($_SESSION[$key])) ? $_SESSION[$key] : NULL;
    }

    /**
     * Delete a session
     *
     * @param string $key
     */
    public function del($key)
    {
        if(isset($_SESSION[$key]))
        {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy session
     */
    public function destroy()
    {
        if(isset($_SESSION))
        {
            session_destroy();
        }
    }

    public function dump()
    {
        if(isset($_SESSION))
        {
            print_r($_SESSION);
        }
        throw new \Exception("Session is not initialized");
    }
}