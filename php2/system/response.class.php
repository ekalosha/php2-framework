<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Class which implements Logic of the Response Urls
 *
 * PHP version 5
 * @category   System Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 98 $
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
 * Class is consist of the functions which implements Logic of the Response Urls. Most part of these functions are static.
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: response.class.php 98 2009-08-19 17:15:05Z eugene $
 * @access   public
 * @package  PHP2\System
 * @static
 */
class PHP2_System_Response extends PHP2_Event_EventDispatcher
{
    /**
     * Additional url parameters for all URLs
     *
     * @staticvar  string
     * @access     public
     */
    public static $addParams = array();

    /**
     * Amp character to delim Get parameters in the URL
     *
     * @staticvar  string
     * @access     public
     */
    public static $ampUrlDelim = '&amp;';

    /**
     * If this flag is established in true, in this case the random parameter is added to the URL
     *
     * @staticvar  boolean
     * @access     public
     * @deprecated
     */
    public static $useRandomizedValue = false;

    /**
     * Is SSL flag
     *
     * @var     boolean
     * @access  public
     */
    protected $_isSSL = false;

    /**
     * Instance of current Class
     *
     * @var     PHP2_System_Response
     * @access  protected
     * @staticvar
     */
    protected static $_instance;

    /**
     * Flag shows is we need to close the session at the end of the script or not.
     * This logic is used for redirects on the page.
     *
     * @var     boolean
     * @access  protected
     */
    protected $_closeSessionOnExit = false;

    /**
     * PHP2_System_Response class constructor
     *
     * @access  public
     */
    public function __construct()
    {
        /**
         * As we are inherited PHP2_Event_EventDispatcher we can't define constructor as protected
         * so our workaround for singleton is to throw an exception in case of $_instance object is initialized.
         * This approach is used for singletons in JavaScript, AS2 and AS3.
         */
        if (self::$_instance) throw new Exception("You must use getInstance() method to initialize singleton", 10112);

        /**
         * Calling parent constructor
         */
        parent::__construct();

        $this->_isSSL = PHP2_System_Request::getInstance()->isSSL();
    }

    /**
     * PHP2_System_Response class destructor
     *
     * @access  public
     */
    public function __destruct()
    {
    }

    /**
     * Returns instance of the Current Class
     *
     * @return  PHP2_System_Response
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
     * Validates form Url with XHTML standard
     *
     * @param   string  $sourceUrl
     * @return  string
     * @access  public
     */
    public function validateFormUrl($sourceUrl)
    {
        $result = $sourceUrl;

        $urlDetails = parse_url($sourceUrl);
        if (isset($urlDetails['query']) && (strpos($urlDetails['query'], '&amp;') === false))
        {
            $result = str_replace('&', '&amp;', $sourceUrl);
        }

        return $result;
    }

    /**
     * Returns valid system Url according to XHTML standards
     *
     * @param   string  $url    Relative Url which is located on the current server
     * @param   array   $params Array of Get parameters
     * @param   boolean $isSSL  Is this SSL Url
     * @return  string
     * @access  public
     */
    public function getUrl($url = '', $params = array(), $isSSL = null)
    {
        if ($isSSL === null) $isSSL = $this->_isSSL;

        $baseUrl = $this->_getBaseUrl($url);

        /**
         * Old features. Possible will be removed in the next releases.
         */
        if (is_array(self::$addParams) && count(self::$addParams) && ($this->_getBaseUrl() == $baseUrl)) $params = self::$addParams + $params;
        if (self::$useRandomizedValue) $params['rand'] = mt_rand(100000, 999999);

        /**
         * Get parameters string
         */
        $paramsCount   = count($params);
        $domainUrl     = ($isSSL ? ROOT_SSL_URL : ROOT_URL);
        $paramsString  = '';
        $i = 1;
        foreach ($params as $pName => $pValue)
        {
            $paramsString .= $pName.'='.$pValue.(($paramsCount != $i) ? self::$ampUrlDelim : '');
            $i++;
        }

        return $result = $domainUrl.$baseUrl.(($paramsCount) ? '?'.$paramsString : '');
    }

    /**
     * Returns Base System Url for Related URL
     *
     * @param   string $relatedUrl Related Url
     * @return  string
     * @access  protected
     */
    protected function _getBaseUrl($relatedUrl)
    {
        if (!$relatedUrl)
        {
            $rootPath  = preg_replace('/[\\\\,\/]+/', '/', ROOT_PATH);
            $result    = '/'.str_replace($rootPath, '', $_SERVER['SCRIPT_FILENAME']);
        }
        elseif ($relatedUrl{0} == '/')
        {
            $result    = $relatedUrl;
        }
        else
        {
            $scriptDirName  = dirname($_SERVER['SCRIPT_FILENAME']).'/';
            $rootPath       = preg_replace('/[\\\\,\/]+/', '/', ROOT_PATH);
            $rDirName       = str_replace($rootPath, '', $scriptDirName);
            $result         = '/'.$rDirName.((strlen($rDirName) && ($rDirName{strlen($rDirName) - 1} == '/')) ? '' : (strlen($rDirName) ? '/' : '')).$relatedUrl;
        }

        return $result;
    }

    /**
     * Centralized Url Path generation function
     *
     * @param   string  $urlPath Relative Url which is located on the current server
     * @param   boolean $isSSL   Is this SSL Url
     * @return  string
     * @access  public
     */
    public function getUrlPath($urlPath = '', $isSSL = null)
    {
        if ($isSSL === null) $isSSL = $this->_isSSL;

        $domainUrl = (!$isSSL ? ROOT_URL : ROOT_SSL_URL);

        if ((strlen($urlPath) > 1) && ($urlPath{0} != '/')) $domainUrl .= '/';

        return $domainUrl.$urlPath;
    }

    /**
     * Returns Path Url for static content server
     *
     * @param   string  $urlPath Relative Url Path
     * @param   boolean $isSSL   Is this SSL Url
     * @return  string
     * @access  public
     */
    public function getStaticUrlPath($urlPath = '', $isSSL = null)
    {
        if ($isSSL === null) $isSSL = $this->_isSSL;

        $domainUrl = (!$isSSL ? STATIC_ROOT_URL : STATIC_ROOT_SSL_URL);

        if ((strlen($urlPath) > 1) && ($urlPath{0} != '/')) $domainUrl .= '/';

        return $domainUrl.$urlPath;
    }

    /**
     * Centralized Url redirection function
     *
     * @param   string $url Redirection Url
     * @access  public
     */
    public function redirect($url)
    {
        if (self::$ampUrlDelim != '&') $url = str_replace(self::$ampUrlDelim, '&', $url);

        /**
         * Dispatching redirect event
         */
        $this->dispatchEvent(new PHP2_Event_Event(PHP2_Event_Event::REDIRECT));

        /**
         * Closing the sessions
         */
        session_write_close();

        /**
         * Sending Location: header to the client
         */
        header('Location: '.$url);
        exit();
    }

    /**
     * Centralized Url redirection function
     *
     * @param   string  $url    Redirection Url
     * @param   array   $params Array of Get parameters
     * @param   boolean $isSSL  Is this SSL Url
     * @access  public
     */
    public function urlRedirect($url, $params = array(), $isSSL = null)
    {
        $this->redirect($this->getUrl($url, $params, $isSSL));
    }

}
