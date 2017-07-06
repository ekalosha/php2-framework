<?php

/**
 * This file contains all necessary Includes and Constants for current Application
 *
 * PHP version 5
 * @category   Configuration Files
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @version    SVN: $Revision: 100 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 3.0
 */

    /**
     * PHP2 Profiler Options
     */
    define('PHP2_PROFILER_ENABLE', false);
    define('PHP2_PROFILER_USE_TRUSTED_IP_FILTERS', false);
    define('PHP2_PROFILER_TRUSTED_IPS', '192.168.0.*');
    require_once LIB_PATH.'system/profiler.class.php';

    /**
     * Including common configuration files for current Application
     */
    require_once LIB_PATH.'system/__autoload.inc.php';

    /**
     * Defining debug mode constants
     *
     */
    define('DATABASE_DEBUG_MODE', false);

    /**
     * Defining constants and Global variables for current Application
     */
    define('APPLICATION_VERSION',   '3.0');
    define('APPLICATION_REVISION',  '15');
    define('APPLICATION_DEVELOPER', 'Eugene A. Kalosha <ekalosha@gmail.com>');

    /**
     * Including Database connection Flags constants
     */
    define('DB_MASTER_CONNECTION', 'mysql://*:*@mysqlserver.dev.solartxit.com:3306/PHP2_V3_Development?type=MASTER&encoding=utf8');
    define('DB_SLAVE_CONNECTION', 'mysql://*:*@mysqlserver.dev.solartxit.com:3306/PHP2_V3_Development?type=SLAVE&encoding=utf8');

    /**
     * Registering global connections
     */
    PHP2_Database_ConnectionsPool::getInstance()->registerConnectionDSN(DB_MASTER_CONNECTION, null, PHP2_Database_ConnectionsPool::CONNECTION_TYPE_WRITE);
    PHP2_Database_ConnectionsPool::getInstance()->registerConnectionDSN(DB_SLAVE_CONNECTION, null, PHP2_Database_ConnectionsPool::CONNECTION_TYPE_READ);

    /**
     * Starting session and initializing system objects.
     * Session MUST be started after database connections are initialized.
     * Such logic takes place because some serializad objects use DEFAULT connections on initialization.
     */
    session_start();
