<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Class, which implements the functions to work with Request Variables.
 *
 * PHP version 5
 * @category   System Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 97 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 3.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace PHP2\System;

/**
 * Class implements the functions to work with Request Variables. Most part of these functions are static.
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: request.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\System
 * @static
 */
class PHP2_System_Request
{
    /**
     * Shows Is magic quotes turned on or off
     *
     * @var     boolean
     * @access  protected
     */
    protected $_isMagicQuotes = false;

    /**
     * Shows Is surrent request is comes under SSL
     *
     * @var     boolean
     * @access  protected
     */
    protected $_isSSL = false;

    /**
     * Shows Is SSL requests are allowed
     *
     * @var     boolean
     * @access  protected
     */
    protected $_isSSLAllowed = false;

    /**
     * Instance of current Class
     *
     * @var     PHP2_System_Request
     * @access  protected
     * @staticvar
     */
    protected static $_instance;

    /**
     * PHP2_System_Request class constructor
     *
     * @access  public
     */
    protected function __construct()
    {
        if ((substr(ROOT_SSL_URL, 0, 6) != 'https:') || (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')))
        {
            $this->_isSSL = true;
        }

        if ((substr(ROOT_SSL_URL, 0, 6) == 'https:'))
        {
            $this->_isSSLAllowed = true;
        }

        $this->_isMagicQuotes = ini_get('magic_quotes_gpc');
    }

    /**
     * Returns instance of the Current Class
     *
     * @return  PHP2_System_Request
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
            self::$_instance  = new self();
        }

        return self::$_instance;
    }
    /**
     * Returns true if current Request comes under SSL
     *
     * @return  boolean
     * @access  public
     */
    public function isSSL()
    {
        return $this->_isSSL;
    }

    /**
     * Returns true if SSL enabled or false otherwise
     *
     * @return   boolean
     * @access   public
     */
    public function isSSLAllowed()
    {
        return $this->_isSSLAllowed;
    }

    /**
     * Returns value of specified Request variable ($_GET, $_POST, $_COOKIE).
     *
     * @param   string $paramName Parameter Name
     * @param   mixed  $default   Default value
     * @param   string $paramType Parameter Type
     * @return  string
     * @access  public
     */
    public function getValue($paramName, $default = null, $paramType = 'string')
    {
        return ((isset($_REQUEST[$paramName])) ? $_REQUEST[$paramName] : $default);
    }

    /**
     * Returns Integer value from the Request ($_GET, $_POST, $_COOKIE).
     *
     * @param   string  $paramName Parameter Name
     * @param   integer $default   Default value
     * @return  integer
     * @access  public
     */
    public function getInt($paramName, $default = null)
    {
        if (isset($_REQUEST[$paramName])) return intval($_REQUEST[$paramName]);

        return $default;
    }

    /**
     * Returns Boolean value from the Request ($_GET, $_POST, $_COOKIE).
     *
     * @param   string  $paramName Parameter Name
     * @param   boolean $default   Default value
     * @return  boolean
     * @access  public
     */
    public function getBool($paramName, $default = false)
    {
        if (isset($_REQUEST[$paramName]))
        {
            $strBoolValue = strtolower($_REQUEST[$paramName]);

            /**
             * Check is Boolean value represented as String
             */
            switch ($strBoolValue)
            {
                case 'on':
                case 't':
                case 'true':
                    return true;
                break;

                case 'off':
                case 'f':
                case 'false':
                    return false;
                break;
            }

            /**
             * All defined positive Int values represented as true
             */
            return (intval($_REQUEST[$paramName]) ? true : false);
        }

        return ((boolean) $default);
    }

    /**
     * Returns string value from the Request ($_GET, $_POST, $_COOKIE).
     *
     * @param   string $paramName Parameter Name
     * @param   string $default   Default value
     * @return  string
     * @access  public
     */
    public function getString($paramName, $default = null)
    {
        $result = $default;
        if (isset($_REQUEST[$paramName]))
        {
            $result = $this->_isMagicQuotes ? stripslashes((string) $_REQUEST[$paramName]) : (string) $_REQUEST[$paramName];
        }

        return $result;
    }

    /**
     * Returns Array variable specified in the Request ($_GET, $_POST, $_COOKIE).
     *
     * @param   string $paramName Parameter Name
     * @param   array  $default   Default value
     * @return  array
     * @access  public
     */
    public function getArray($paramName, $default = array())
    {
        if (isset($_REQUEST[$paramName])) return (array) ($_REQUEST[$paramName]);

        return $default;
    }

}
