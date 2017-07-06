<?php

/**
 * Run script for "Privacy" page application
 *
 * PHP version 5
 * @category   UI Applications
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @version    SVN: $Revision: 113 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 2.0
 */

    /**
     * Including Configuration files of the Application
     */
    include_once(str_replace('\\', '/', dirname(dirname(__FILE__))).'/config/config.inc.php');
    require_once CONFIG_PATH.'application.inc.php';

    /**
     * Initializing and Running the Application
     */
    $application = new Application_UI_Privacy();
    $application->run();
