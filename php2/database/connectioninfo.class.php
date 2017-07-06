<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Class which Implements Database Connection Info
 *
 * PHP version 5
 * @category   Library Classes
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
// namespace PHP2\Database;

/**
 * Class Implements Database Connection Info
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: connectioninfo.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\Database
 */
class PHP2_Database_ConnectionInfo
{
    /**
     * Connection resource
     *
     * @var     MySQLi
     * @access  protected
     */
    protected $_hConnection;

    /**
     * Connection DSN string
     *
     * @var     string
     * @access  protected
     */
    protected $_connectionDSN;

    /**
     * Connection Host, without Port and Socket Information
     *
     * @var     string
     * @access  protected
     */
    protected $_host;

    /**
     * Connection Port
     *
     * @var     integer
     * @access  protected
     */
    protected $_port;

    /**
     * Connection Socket
     *
     * @var     string
     * @access  protected
     */
    protected $_socket;

    /**
     * Database Name
     */
    protected $_database;

    /**
     * Database user name
     *
     * @var     string
     * @access  protected
     */
    protected $_user;

    /**
     * Database User's password
     *
     * @var     string
     * @access  protected
     */
    protected $_password;

    /**
     * Connection flags
     *
     * @var     string
     * @access  protected
     */
    protected $_flags;

    /**
     * Connection encoding
     *
     * @var     string
     * @access  protected
     */
    protected $_encoding;

    /**
     * Class constructor
     *
     * @var     string $connectionDSN Database Source Name of the connection
     * @access  public
     */
    public function __construct($connectionDSN = false)
    {
        if ($connectionDSN) $this->setConnectionInfo($connectionDSN);
    }

    /**
     * Class destructor. Cleans connection resources.
     *
     * @access  public
     */
    public function __destruct()
    {
        if ($this->_hConnection) $this->_hConnection->close();
    }

    /**
     * Sets Database Source Name.
     *
     * Database Source Name (DSN) - it is the string describe connection Info, in the form:
     *
     * mysql://user:password@server:port/database?{connection-parameters}
     *
     * The list of available connection parameters:
     *
     *  - user
     *  - password
     *  - socket
     *  - encoding
     *  - flags
     *
     * @param   string $connectionDSN Database Source Name of the connection
     * @access  public
     * @return  boolean
     */
    public function setConnectionInfo($connectionDSN)
    {
        /**
         * Checking Connection DSN Url
         */
        if (!$connectionDetails = parse_url($connectionDSN)) return false;
        $this->_connectionDSN = $connectionDSN;

        /**
         * Parsing Connection Parameters
         */
        $connectionParameters = array();
        if (isset($connectionDetails['query'])) parse_str($connectionDetails['query'], $connectionParameters);

        /**
         * Set host details for the Connection
         */
        $this->_host = $connectionDetails['host'];

        if (isset($connectionDetails['port']))
        {
            $this->_port = $connectionDetails['port'];
        }
        elseif (isset($connectionParameters['socket']))
        {
            $this->_socket = $connectionParameters['socket'];
        }

        /**
         * Find User/Password for Connection
         */
        $this->_user = isset($connectionParameters['user']) ? $connectionParameters['user'] : (isset($connectionDetails['user']) ? $connectionDetails['user'] : null);
        if ($this->_user) $this->_password = isset($connectionParameters['password']) ? $connectionParameters['password'] : (isset($connectionDetails['pass']) ? $connectionDetails['pass'] : null);

        /**
         * Check database name
         */
        $this->_database = trim($connectionDetails['path'], '/');

        /**
         * Set connection flags
         */
        $this->_flags = (isset($connectionParameters['flags']) ? $connectionParameters['flags'] : null);

        /**
         * Set connection encoding
         */
        $this->_encoding = (isset($connectionParameters['encoding']) ? $connectionParameters['encoding'] : null);
    }

    /**
     * Returns connection hostname
     *
     * @return  string
     * @access  public
     */
    public function getHost()
    {
        return $this->_host;
    }

    /**
     * Returns database name
     *
     * @return  string
     * @access  public
     */
    public function getDatbase()
    {
        return $this->_database;
    }

    /**
     * Returns database username
     *
     * @return  string
     * @access  public
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Returns Database user password
     *
     * @return  string
     * @access  public
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Returns connection flags
     *
     * @return  string
     * @access  public
     */
    public function getConnectionFlags()
    {
        return $this->_flags;
    }

    /**
     * Returns connection encoding
     *
     * @return  string
     * @access  public
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Returns connection DSN
     *
     * @return  string
     * @access  public
     */
    public function getConnectionDSN()
    {
        return $this->_connectionDSN;
    }


    /**
     * Returns connection resource
     *
     * @return  MySQLi
     * @access  public
     * @throws  PHP2_Exception_EDatabaseException in case of connection Error
     */
    public function getConnection()
    {
        if (!$this->_hConnection)
        {
            /**
             * Triyng to connect to the database server
             */
            PHP2_System_Profiler::getInstance()->setStartProfilerBreakpoint($this->_connectionDSN, 'Connection to the Database', PHP2_System_Profiler::PROFILER_GROUP_DB);
            $this->_hConnection = new MySQLi($this->_host, $this->_user, $this->_password, null, $this->_port, $this->_socket);
            if ((mysqli_connect_errno()) && defined('DATABASE_DEBUG_MODE') && DATABASE_DEBUG_MODE) throw new PHP2_Exception_EDatabaseException(PHP2_Exception_EDatabaseException::ERROR_DB_CONNECT);
            PHP2_System_Profiler::getInstance()->setEndProfilerBreakpoint($this->_connectionDSN, PHP2_System_Profiler::PROFILER_GROUP_DB);

            /**
             * Registering connection DSN and Resource
             */
            PHP2_Database_ConnectionsPool::getInstance()->registerConnectionDSNResource($this->_hConnection, $this->_connectionDSN);

            /**
             * Set encoding for DB session
             */
            if ($this->_encoding) PHP2_Database_SQLQuery::executeQuery('SET NAMES '.$this->_encoding, $this->_hConnection);

            /**
             * Triyng to select the database
             */
            if (!$this->_hConnection->select_db($this->_database)) throw new PHP2_Exception_EDatabaseException(PHP2_Exception_EDatabaseException::ERROR_DB_NOT_EXISTS);
        }

        return $this->_hConnection;
    }

}
