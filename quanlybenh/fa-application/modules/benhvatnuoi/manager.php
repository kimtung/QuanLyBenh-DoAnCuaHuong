<?php
NAMESPACE FA\MODULES\M_BENHVATNUOI;
USE \FA\CORE AS CORE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class manager
 * @package FA\MODULES\M_BENHVATNUOI
 */
Class manager Extends CORE\FA_Controller
{
    public function __construct()
    {
        parent::__construct();
		
        $this->hook->add_action(FA, 'pre_action', function() {
			
            if (!IS_LOGGED)
            {
				
                $login_needed = array('index');
				$not_login_needed = array('login');
                if (!in_array(CUR_ACTION, $not_login_needed))
                {
                    redirect(BASE_URL . 'manager/login');
                }
            }
        });
    }

    public function index()
    {
        require 'manager/index.php';
    }

    public function species($action = '', $action_id = '')
    {
        switch ($action)
        {
            default:
                require 'manager/species/index.php';
                break;
            case 'add':
                require 'manager/species/add.php';
                break;
            case 'edit':
                require 'manager/species/edit.php';
                break;
            case 'delete':
                require 'manager/species/delete.php';
                break;
        }
    }

    public function breeds($action = '', $action_id = '')
    {
        switch ($action)
        {
            default:
                require 'manager/breeds/index.php';
                break;
            case 'add':
                require 'manager/breeds/add.php';
                break;
            case 'edit':
                require 'manager/breeds/edit.php';
                break;
            case 'delete':
                require 'manager/breeds/delete.php';
                break;
        }
    }

    public function diseases_group($action = '', $action_id = '')
    {
        switch ($action)
        {
            default:
                require 'manager/diseases_group/index.php';
                break;
            case 'add':
                require 'manager/diseases_group/add.php';
                break;
            case 'edit':
                require 'manager/diseases_group/edit.php';
                break;
            case 'delete':
                require 'manager/diseases_group/delete.php';
                break;
        }
    }

    public function diseases($action = '', $action_id = '')
    {
        switch ($action)
        {
            default:
                require 'manager/diseases/index.php';
                break;
            case 'add':
                require 'manager/diseases/add.php';
                break;
            case 'edit':
                require 'manager/diseases/edit.php';
                break;
            case 'delete':
                require 'manager/diseases/delete.php';
                break;
        }
    }

    public function accounts($action = '', $action_id = '')
    {
		
        switch ($action)
        {
            default:
                require 'manager/accounts/index.php';
                break;
            case 'add':
                require 'manager/accounts/add.php';
                break;
            case 'edit':
                require 'manager/accounts/edit.php';
                break;
            case 'delete':
                require 'manager/accounts/delete.php';
                break;
        }
    }

    public function login()
    {
        require 'manager/login.php';
    }
	
    public function logout()
    {
        /**
         * Load account model
         */
        $this->load->model('account');

        /**
         * @var \FA\MODELS\M_BENHVATNUOI\account $acc_model
         */
        $acc_model = $this->model->account;

        $acc_model->unset_token_login();
		session_unset();

        redirect(BASE_URL . 'manager/login');
    }		
}