<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Class to manage application Events
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 97 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 2.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace PHP2\System;

/**
 * Class implements Event manager
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: eventmanager.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\System
 */
class PHP2_System_EventManager
{
    /**
     * List of events listeners
     *
     * @var     array
     * @access  protected
     */
    protected $_eventListeners;

    /**
     * Instance of current Class
     *
     * @var     PHP2_System_EventManager
     * @access  protected
     * @staticvar
     */
    protected static $_instance;

    /**
     * PHP2_System_EventManager class constructor
     *
     * @access  public
     */
    protected function __construct()
    {
    }

    /**
     * Returns instance of the Current Class
     *
     * @return  PHP2_System_EventManager
     * @access  public
     * @static
     */
    public static function getInstance()
    {
        /**
         * Checking is Instance of class Initialized
         */
        if (self::$_instance == null)
        {
            $currentClass     = __CLASS__;
            self::$_instance  = new $currentClass();
        }

        return self::$_instance;
    }

}
