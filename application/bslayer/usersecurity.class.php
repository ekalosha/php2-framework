<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains class that implements base User security abstraction
 *
 * @category   Business Model of the Application
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 110 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 3.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace Application\BSLayer;

/**
 * Class realizes base User security abstraction
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: usersecurity.class.php 110 2010-02-22 15:32:22Z eugene $
 * @access   public
 * @package  Application\BSLayer
 */
class Application_BSLayer_UserSecurity extends Application_BSLayer_Abstract
{
    /**
     * User roles constants
     */
    const USER_ROLE_ADMIN      = 1;
    const USER_ROLE_USER       = 2;

    /**
     * Security expiration time in seconds
     */
    const SECURITY_EXPIRATION_TIME = 600;

    /**
     * Security SESSION Key Name
     */
    const SECURITY_SESSION = '__securityInfo';

    /**
     * Security Code Cookie Key
     */
    const SECURITY_COOKIE  = '__aclInfo';

    /**
     * Current user details
     *
     * @var   Application_DBLayer_SysUser
     */
    protected $_userDetails;

    /**
     * Time of the last security update for user
     *
     * @var  integer
     */
    protected $_lastSecurityUpdateTime;

    /**
     * Unique user ID in the system
     *
     * @var  integer
     */
    protected $_userId;

    /**
     * Last login time for the user
     *
     * @var  integer
     */
    protected $_lastLoginTime;

    /**
     * Instance of current Class
     *
     * @var     Application_BSLayer_UserSecurity
     * @access  protected
     * @staticvar
     */
    protected static $_instance;

    /**
     * Application_BSLayer_UserSecurity class constructor
     * As we are inherited Application_BSLayer_Abstract we can't define constructor as protected
     *
     * @access  public
     */
    public function __construct()
    {
        /**
         * As we are inherited Application_BSLayer_Abstract we can't define constructor as protected
         * so our workaround for singleton is to throw an exception in case of $_instance object is initialized.
         * This approach is used for singletons in JavaScript, AS2 and AS3.
         */
        if (self::$_instance) throw new Exception("You must use getInstance() method to initialize singleton", 10112);

        /**
         * Initializing Read and Write connections
         */
        parent::__construct();

        /**
         * Loading user security from Session
         */
        $this->_loadFromSession();

        /**
         * Checking current user security
         */
        if ($this->checkUserSecurity())
        {
            if (!$this->_lastSecurityUpdateTime || ($this->_lastSecurityUpdateTime < time() - self::SECURITY_EXPIRATION_TIME))
            {
                /**
                 * Reload user login data
                 */
                $this->doLogin($this->_userId);
            }
        }

        /**
         * Adding fix for "Save To Session" issue in case of redirect on the page
         */
        PHP2_System_Response::getInstance()->addEventListener(PHP2_Event_Event::REDIRECT, 'forceSessionSave', $this);
    }

    /**
     * Class destructor
     *
     * @access  public
     */
    public function __destruct()
    {
        /**
         * Saving user security to Session
         */
        $this->_saveToSession();
    }

    /**
     * Returns instance of the Current Class
     *
     * @return  Application_BSLayer_UserSecurity
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
     * Process login for user
     *
     * @param   string  $userName User name
     * @param   string  $password User password
     * @param   boolean $saveToCookie Save to cookie flag
     * @return  boolean
     * @throws  Application_Exception_ESecurityException in case of invalid login
     */
    public function login($userName, $password, $saveToCookie = false)
    {
        $userDetailsDO = new Application_DBLayer_SysUser();

        /**
         * Checking is user exists in the database
         */
        if (!$userId = $userDetailsDO->getUserIdByLogin(PHP2_Database_SQLQuery::escape($userName)))
        {
            throw new Application_Exception_ESecurityException(Application_Exception_ESecurityException::ERROR_LOGIN_INVALID);
        }

        $userDetailsDO->loadDetails($userId);

        /**
         * Checking is user password valid
         */
        if ($userDetailsDO->UserPassword != md5($password))
        {
            throw new Application_Exception_ESecurityException(Application_Exception_ESecurityException::ERROR_LOGIN_INVALID);
        }

        $this->doLogin($userId, $userDetailsDO, $saveToCookie);

        /**
         * Saving last login time for user
         */
        $this->_lastLoginTime = $userDetailsDO->LastLogin;

        /**
         * Updating information about last Login
         */
        $this->_updateLoginData();

        return true;
    }


    /**
     * Process login for user
     *
     * @param   integer $userId
     * @param   Application_DBLayer_SysUser $userDetails
     * @param   boolean $saveSecurityCookie
     * @return  boolean
     */
    public function doLogin($userId, $userDetails = false, $saveSecurityCookie = false)
    {
        if (!$userDetails) $userDetails = new Application_DBLayer_SysUser($userId);

        /**
         * Initializing user details
         */
        $this->_userId       = $userId;
        $this->_userDetails  = $userDetails;

        /**
         * Extending User with Group details
         */
        $groupDetails = new Application_DBLayer_SysGroup($this->_userDetails->GroupID);
        $this->_userDetails->extend($groupDetails);

        /**
         * Processing security cookie
         */
        if ($saveSecurityCookie)
        {
            $securityCookieString = md5($userId.'_'.uniqid(rand(), true).'_'.PHP2_Utils_String::getRandomString(8));

            $userDetailsDO = new Application_DBLayer_SysUser();
            if (!$userDetailsDO->getUserIdBySecuritySession($securityCookieString))
            {
                $userDetailsDO->SecuritySessionID          = $securityCookieString;
                $userDetailsDO->SecuritySessionExpireTime  = strtotime('+2 weeks');
                $userDetailsDO->update($this->_userId);

                $this->_setAuthCookie($securityCookieString);
            }
        }

        /**
         * Changing security update time
         */
        $this->_lastSecurityUpdateTime = time();

        /**
         * Updating LastSecurityUpdateTime for user
         */
        $tmpUserDetails = new Application_DBLayer_SysUser();
        $tmpUserDetails->LastSecurityUpdateTime = $this->_lastSecurityUpdateTime;
        $tmpUserDetails->update($this->_userId);
    }

    /**
     * Returns current user ID
     *
     * @return  integer
     * @access  public
     */
    public function getUserId()
    {
        return $this->_userId ? $this->_userId : false;
    }

    /**
     * Returns current user Details
     *
     * @return  Application_DBLayer_SysUser
     * @access  public
     */
    public function getUserDetails()
    {
        return $this->_userDetails;
    }

    /**
     * Returns enter point to the site for current user
     *
     * @return  string
     * @access  public
     */
    public function getEnterPoint()
    {
        return (isset($this->_userDetails->EnterPoint) ? $this->_userDetails->EnterPoint : false);
    }

    /**
     * Returns current user Details parameter
     *
     * @param   string $paramName
     * @return  mixed
     * @access  public
     */
    public function getUserParam($paramName)
    {
        return isset($this->_userDetails->{$paramName}) ? $this->_userDetails->{$paramName} : null;
    }

    /**
     * Returns time of the last login for the current user
     *
     * @return  integer
     * @access  public
     */
    public function getLastLoginTime()
    {
        return $this->_lastLoginTime;
    }

    /**
     * Checks user Security and process Cookie-based Authentification if possible
     *
     * @return  boolean
     * @access  public
     */
    public function checkUserSecurity()
    {
        $result = false;

        /**
         * If user is already Loged in returning true
         */
        if ($this->isUserLogged()) return true;

        /**
         * Checking cookie security
         */
        if ($this->checkUserSecurityCookie())
        {
            return true;
        }

        return $result;
    }

    /**
     * Checks user Security Cookie and process Authentification if possible
     *
     * @return  boolean
     * @access  public
     */
    public function checkUserSecurityCookie()
    {
        if (!isset($_COOKIE[self::SECURITY_COOKIE]) || !$_COOKIE[self::SECURITY_COOKIE]) return false;

        $userDetailsDO = new Application_DBLayer_SysUser();

        /**
         * Get security Cookie Details
         */
        if (!$userId = $userDetailsDO->getUserIdBySecuritySession($_COOKIE[self::SECURITY_COOKIE], date(PHP2_Utils_DateTime::FORMAT_DATETIME_MYSQL))) return false;

        /**
         * Processing login for user
         */
        $this->doLogin($userId);

        /**
         * Saving last login time for user
         */
        $this->_lastLoginTime = $this->_userDetails->LastLogin;

        /**
         * Updating information about last Login
         */
        $this->_updateLoginData();

        return true;
    }

    /**
     * Check is Current User Logged or not
     *
     * @return  boolean
     * @access  public
     */
    public function isUserLogged()
    {
        if ($this->_userId) return true;

        return false;
    }

    /**
     * Sets Auth cookie for User
     *
     * @param   string  $authCookieString Auth Cookie value
     * @return  void
     * @access  protected
     */
    protected function _setAuthCookie($authCookieString)
    {
        if (setcookie(self::SECURITY_COOKIE, $authCookieString, time() + 3600*24*30, '/')) $_COOKIE[self::SECURITY_COOKIE] = $authCookieString;
    }

    /**
     * Removes Auth cookie for User
     *
     * @return  void
     * @access  protected
     */
    protected function _unsetAuthCookie()
    {
        setcookie(self::SECURITY_COOKIE, null, time() - 3600*48, '/');

        unset ($_COOKIE[self::SECURITY_COOKIE]);
    }

    /**
     * Updates login data for the user
     *
     * @return  boolean
     * @access  protected
     */
    protected function _updateLoginData()
    {
        if (!$this->_userId || !$this->_userDetails) return false;

        $userDetails               = new Application_DBLayer_SysUser();
        $userDetails->LoginsCount  = $this->_userDetails->LoginsCount + 1;
        $userDetails->LastLogin    = time();

        return $userDetails->update($this->_userId);
    }

    /**
     * Implement logout logic
     *
     * @return  void
     * @access  public
     */
    public function logout()
    {
        $this->_unsetAuthCookie();

        $this->_userId       = null;
        $this->_userDetails  = null;
    }

    /**
     * Loads current security Data from Session
     *
     * @access  protected
     */
    protected function _loadFromSession()
    {
        if (isset($_SESSION[self::SECURITY_SESSION]['userId'])) $this->_userId = $_SESSION[self::SECURITY_SESSION]['userId'];
        if (isset($_SESSION[self::SECURITY_SESSION]['userDetails'])) $this->_userDetails = $_SESSION[self::SECURITY_SESSION]['userDetails'];
        if (isset($_SESSION[self::SECURITY_SESSION]['lastSecurityUpdateTime'])) $this->_lastSecurityUpdateTime = $_SESSION[self::SECURITY_SESSION]['lastSecurityUpdateTime'];
        if (isset($_SESSION[self::SECURITY_SESSION]['lastLoginTime'])) $this->_lastLoginTime = $_SESSION[self::SECURITY_SESSION]['lastLoginTime'];
    }

    /**
     * Saves current security Data to Session
     *
     * @access  protected
     */
    protected function _saveToSession()
    {
        if ($this->_userId && $this->_userDetails)
        {
            $_SESSION[self::SECURITY_SESSION]['userId']      = $this->_userId;
            $_SESSION[self::SECURITY_SESSION]['userDetails'] = $this->_userDetails;
            $_SESSION[self::SECURITY_SESSION]['lastSecurityUpdateTime'] = $this->_lastSecurityUpdateTime;
            $_SESSION[self::SECURITY_SESSION]['lastLoginTime'] = $this->_lastLoginTime;
        }
        else
        {
            unset($_SESSION[self::SECURITY_SESSION]);
        }
    }

    /**
     * Force save session data
     *
     * @access  protected
     */
    public function forceSessionSave()
    {
        $this->_saveToSession();
    }

}
