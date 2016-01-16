<?php
NAMESPACE FA\CORE;
USE \FA\DATABASE AS DATABASE;

defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class FA_Database
 * @package FA\CORE
 */
class FA_Database
{
    public function __construct()
    {
        /**
         * Log info
         */
        log_message(MSG_INFO, 'Database Class Initialized');
    }

    /**
     * @param string|array $params
     * @param null|bool $query_builder_override
     * @return object
     */
    public function &loader($params = '', $query_builder_override = NULL)
    {
        /**
         * Load the DB config file if a DSN string wasn't passed
         */
        if (is_string($params) && strpos($params, '://') === FALSE)
        {
            /**
             * Is the config file in the environment folder?
             */
            if (!file_exists($file_path = APP_PATH . 'config/database.php'))
            {
                show_error('The configuration file database.php does not exist.');
            }

            include $file_path;

            if (!isset($database) OR count($database) === 0)
            {
                show_error('No database connection settings were found in the database config file.');
            }

            if ($params !== '')
            {
                $database['active_group'] = $params;
            }

            if (!isset($database['active_group']))
            {
                show_error('You have not specified a database connection group via $active_group in your config/database.php file.');
            }
            elseif (!isset($database['group'][$database['active_group']]))
            {
                show_error('You have specified an invalid database connection group (' . $database['active_group'] . ') in your config/database.php file.');
            }

            $params = $database['group'][$database['active_group']];
        }
        elseif (is_string($params))
        {
            /**
             * Parse the URL from the DSN string
             * Database settings can be passed as discreet
             * parameters or as a data source name in the first
             * parameter. DSNs must have this prototype:
             * $dsn = 'driver://username:password@hostname/database';
             */
            if (($dsn = @parse_url($params)) === FALSE)
            {
                show_error('Invalid DB Connection String');
            }

            $params = array(
                'driver'	=> $dsn['scheme'],
                'hostname'	=> isset($dsn['host']) ? rawurldecode($dsn['host']) : '',
                'port'		=> isset($dsn['port']) ? rawurldecode($dsn['port']) : '',
                'username'	=> isset($dsn['user']) ? rawurldecode($dsn['user']) : '',
                'password'	=> isset($dsn['pass']) ? rawurldecode($dsn['pass']) : '',
                'database'	=> isset($dsn['path']) ? rawurldecode(substr($dsn['path'], 1)) : ''
            );

            // Were additional config items set?
            if (isset($dsn['query']))
            {
                parse_str($dsn['query'], $extra);

                foreach ($extra as $key => $val)
                {
                    if (is_string($val) && in_array(strtoupper($val), array('TRUE', 'FALSE', 'NULL')))
                    {
                        $val = var_export($val, TRUE);
                    }

                    $params[$key] = $val;
                }
            }
        }

        /**
         * No DB specified yet? Beat them senseless...
         */
        if (empty($params['driver']))
        {
            show_error('You have not selected a database type to connect to.');
        }

        /**
         * Load the DB classes. Note: Since the query builder class is optional
         * we need to dynamically create a class that extends proper parent class
         * based on whether we're using the query builder class or not.
         */
        if ($query_builder_override !== NULL)
        {
            $database['query_builder'] = $query_builder_override;
        }

        require_once(SYS_PATH . 'database/DB_driver.php');

        /**
         * Load the DB driver
         */
        $driver_file = SYS_PATH . 'database/drivers/' . $params['driver'] . '/' . $params['driver'] . '.driver.php';

        file_exists($driver_file) OR show_error('Invalid DB driver');
        require_once $driver_file;

        /**
         * Instantiate the DB adapter
         */
        $driver = 'FA\DATABASE\DRIVERS\\' .  $params['driver'];
        /**
         * @var object $DB
         */
        $DB = new $driver($params);
        $DB->initialize();
        return $DB;
    }
}