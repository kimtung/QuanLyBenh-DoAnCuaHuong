<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

if (!function_exists('is_logged'))
{
    /**
     * Check user is logged
     *
     * @return bool
     */
    function is_logged()
    {
        if (defined('IS_LOGGED'))
        {
            return IS_LOGGED;
        }
        return FALSE;
    }
}

if (!function_exists('user_id'))
{
    /**
     * Get current user id
     *
     * @return null|int
     */
    function user_id()
    {
        if (defined('USER_ID'))
        {
            return USER_ID;
        }
        return NULL;
    }
}

if (!function_exists('user'))
{
    /**
     * Get user data
     * If $uid is int, return data of this uid with current column $col
     * If $uid is string, return current data of current user_id (logged) with column $col
     *
     * @param int|string $uid
     * @param null|string $col
     * @return array|null
     */
    function user($uid, $col = NULL)
    {
        static $_user;
        $key = $uid . '-' . $col;
        if (isset($_user[$key])) return $_user[$key];

        /**
         * Get fa instance
         */
        $fa = fa_instance();

        /**
         * @var \FA\MODELS\M_FANSWERS\account $acc_model
         */
        $acc_model = $fa->model->account;

        $col_first = false;
        if (preg_match('/^[0-9]+$/', $uid))
        {
            $user_id = $uid;
        }
        else
        {
            $user_id = user_id();
            $col_first = true;
            $col = $uid;
        }
        if (!$user_id)
        {
            $out = NULL;
        }
        else
        {
            if ($col_first)
            {
                $user_data = $acc_model->user_data($user_id, $col);
                if (count($user_data) == 1)
                {
                    $out = (isset($user_data[$col]) ? $user_data[$col] : NULL);
                }
                else
                {
                    $out = $user_data;
                }
            }
            else
            {
                $user_data = $acc_model->user_data($user_id, $col);
                $out = $user_data;
            }
        }
        $_user[$key] = $out;
        return $out;
    }
}