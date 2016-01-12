<?php
/**
 * @var \FA\CORE\FA_Hook $HOOK
 */

$HOOK->add_action(FA, 'pre_view', function() {
    $fa = fa_instance();
    $site_title = $fa->config->item('site_title');

    $title = $fa->load->data('title');
    if ($title)
    {
        $view_title = $title . ' | ' . $site_title;
    }
    else
    {
        $view_title = $site_title;
    }
    $fa->load->data('view_title', $view_title);
});

$HOOK->add_action(FA, 'pre_action', function() {

    $IS_LOGGED = FALSE;
    $USER_ID = NULL;

    /**
     * Get fa instance
     */
    $fa = fa_instance();

    /**
     * Get session library
     */
    $fa->load->library('session');
    /**
     * Get cookie library
     */
    $fa->load->library('cookie');

    /**
     * Get session object
     * @var  \FA\LIBRARIES\session $session
     */
    $session = $fa->lib->session;

    /**
     * Get cookie object
     * @var  \FA\LIBRARIES\cookie $cookie
     */
    $cookie = $fa->lib->cookie;


    /**
     * Get token from session
     */
    $token_login    = $session->get('token_login');
    $token_uid      = $session->get('token_uid');
    $_from_cookie = false;

    if (!$token_login || !$token_uid)
    {
        /**
         * Get token from cookie
         */
        $token_login    = $cookie->get('token_login');
        $token_uid      = $cookie->get('token_uid');
        $_from_cookie = true;
    }

    /**
     * Make sure token already exists
     */
    if ($token_login && $token_uid)
    {
        /**
         * Load account model
         */
        $fa->load->model('account');

        /**
         * Valid token
         */
        if (($USER_ID = $fa->model->account->valid_token_login($token_uid, $token_login)))
        {
            if ($_from_cookie)
            {
                /**
                 * Re set token to session
                 */
                $session->set('token_login', $token_login);
                $session->set('token_uid', $token_uid);
            }
            $IS_LOGGED = TRUE;
        }
    }

    /**
     * Define user value
     *
     * @var bool IS_LOGGED
     * @var int|null USER_ID
     */
    define('IS_LOGGED', $IS_LOGGED);
    define('USER_ID', $USER_ID);

    /**
     * Load account helper
     */
    $fa->load->helper('account');
});