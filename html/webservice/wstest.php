<?php

/**
 * Run script for Test web service.
 *
 * PHP version 5
 * @category   Web Applications
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @version    SVN: $Revision: 97 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 3.0
 */

    /**
     * Including Configuration files of the Application
     */
    include_once(str_replace('\\', '/', dirname(dirname(dirname(__FILE__)))).'/config/config.inc.php');
    require_once CONFIG_PATH.'application.inc.php';

    /**
     * Initializing and Running the Application
     */
    $application = new Application_WebService_WSTest();
    echo $application->run();
