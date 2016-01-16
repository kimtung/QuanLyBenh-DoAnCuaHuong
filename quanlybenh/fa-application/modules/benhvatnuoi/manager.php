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
                $not_login_needed = array('login');

                if (!in_array(CUR_ACTION, $not_login_needed))
                {
                    redirect(BASE_URL . 'manager/login');
                }

                //$login_needed = array('index');
                //if (in_array(CUR_ACTION, $login_needed))
                //{
                //   redirect(BASE_URL . 'manager/login');
                //}

            }
        });
    }

    public function checkPermisstion($action = '')
    {
        if(!isset($_SESSION["user"])){
            return false;
        }
        $user = $_SESSION["user"];
        if($user['protected'] == 1){
            return true;
        }
        $permission = $user['permission'];
        $permissionArray = explode('&',$permission);
        if(in_array($action ,$permissionArray)){
            return true;
        }
        return false;
    }

    public function index()
    {
        require 'manager/index.php';
    }

    public function species($action = '', $action_id = '')
    {
        $actionPermission = '';
        $actionRequire = '';
        switch ($action)
        {
            default:
                $actionRequire = 'manager/species/index.php';
                $actionPermission = 'S-R';
                break;
            case 'add':
                $actionRequire = 'manager/species/add.php';
                $actionPermission = 'S-C';
                break;
            case 'edit':
                $actionRequire = 'manager/species/edit.php';
                $actionPermission = 'S-U';
                break;
            case 'delete':
                $actionPermission = 'S-D';
                $actionRequire = 'manager/species/delete.php';
                break;
        }
        if($this->checkPermisstion($actionPermission)){
            require $actionRequire;
        }else{
            redirect(BASE_URL . 'manager/forbidden');
        }

    }

    public function breeds($action = '', $action_id = '')
    {
        $actionPermission = '';
        $actionRequire = '';
        switch ($action)
        {
            default:
                $actionRequire = 'manager/breeds/index.php';
                $actionPermission = 'B-R';
                break;
            case 'add':
                $actionRequire = 'manager/breeds/add.php';
                $actionPermission = 'B-C';
                break;
            case 'edit':
                $actionRequire = 'manager/breeds/edit.php';
                $actionPermission = 'B-U';
                break;
            case 'delete':
                $actionPermission = 'B-D';
                $actionRequire = 'manager/breeds/delete.php';
                break;
        }
        if($this->checkPermisstion($actionPermission)){
            require $actionRequire;
        }else{
            redirect(BASE_URL . 'manager/forbidden');
        }

    }

    public function diseases_group($action = '', $action_id = '')
    {
        $actionPermission = '';
        $actionRequire = '';
        switch ($action)
        {
            default:
                $actionRequire = 'manager/diseases_group/index.php';
                $actionPermission = 'G-R';
                break;
            case 'add':
                $actionRequire = 'manager/diseases_group/add.php';
                $actionPermission = 'G-C';
                break;
            case 'edit':
                $actionRequire = 'manager/diseases_group/edit.php';
                $actionPermission = 'G-U';
                break;
            case 'delete':
                $actionPermission = 'G-D';
                $actionRequire = 'manager/diseases_group/delete.php';
                break;
        }
        if($this->checkPermisstion($actionPermission)){
            require $actionRequire;
        }else{
            redirect(BASE_URL . 'manager/forbidden');
        }
    }

    public function diseases($action = '', $action_id = '')
    {
        $actionPermission = '';
        $actionRequire = '';
        switch ($action)
        {
            default:
                $actionRequire = 'manager/diseases/index.php';
                $actionPermission = 'D-R';
                break;
            case 'add':
                $actionRequire = 'manager/diseases/add.php';
                $actionPermission = 'D-C';
                break;
            case 'edit':
                $actionRequire = 'manager/diseases/edit.php';
                $actionPermission = 'D-U';
                break;
            case 'delete':
                $actionPermission = 'D-D';
                $actionRequire = 'manager/diseases/delete.php';
                break;
        }
        if($this->checkPermisstion($actionPermission)){
            require $actionRequire;
        }else{
            redirect(BASE_URL . 'manager/forbidden');
        }
    }

    public function accounts($action = '', $action_id = '')
    {
        if($this->checkPermisstion()){
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
            };
        }else{
            if(isset($_SESSION["user"])){
                $user = $_SESSION["user"];
                if($user['id'] == $action_id){
                    require 'manager/accounts/edit.php';
                }else{
                    redirect(BASE_URL . 'manager/forbidden');
                }
            }else{
                redirect(BASE_URL . 'manager/forbidden');
            }
        }

    }

    public function login()
    {
        require 'manager/login.php';
    }

    public function forbidden()
    {
        require 'manager/forbidden.php';
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