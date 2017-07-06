<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Class which Implements Database Connections Pool
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 117 $
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
 * Class Implements Database Connections Pool
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: connectionspool.class.php 117 2011-02-27 15:41:23Z eugene $
 * @access   public
 * @package  PHP2\Database
 */
class PHP2_Database_ConnectionsPool
{
	/**
	 * Predefined connection Types
	 */
	const CONNECTION_TYPE_WRITE    = 'WRITE';
	const CONNECTION_TYPE_READ     = 'READ';
	const CONNECTION_TYPE_DEFAULT  = 'DEFAULT';

	/**
	 * Connection list
	 *
	 * @var     array
	 * @access  protected
	 */
	protected $_connectionsList;

	/**
	 * Connection locator
	 *
	 * @var     array
	 * @access  protected
	 */
	protected $_connectionsLocator;

	/**
	 * Connection DSN locator
	 *
	 * @var     array
	 * @access  protected
	 */
	protected $_connectionsDSNLocator;

	/**
	 * Default connection list
	 *
	 * @var     array
	 * @access  protected
	 */
	protected $_defaultConnections;

	/**
	 * Instance of current Class
	 *
	 * @var     PHP2_Database_ConnectionsPool
	 * @access  protected
	 * @staticvar
	 */
	protected static $_instance;

	/**
	 * Class constructor
	 *
	 * @access  public
	 */
	protected function __construct()
	{
	}

	/**
	 * Returns instance of the Current Class
	 *
	 * @return  PHP2_Database_ConnectionsPool
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

	/**
	 * Registers Database connection via connection DSN
	 *
	 * @param   string $connectionDSN
	 * @param   string $connectionId Identifier of the connection
	 * @param   string $connectionType
	 * @access  public
	 * @return  boolean
	 */
	public function registerConnectionDSN($connectionDSN, $connectionId = null, $connectionType = null)
	{
		$this->registerConnection(new PHP2_Database_ConnectionInfo($connectionDSN), $connectionId, $connectionType);
	}

	/**
	 * Registers Database connection
	 *
	 * @param   PHP2_Database_ConnectionInfo $connectionInfo
	 * @param   string $connectionId Identifier of the Connection
	 * @param   string $connectionType
	 * @return  boolean
	 * @access  public
	 */
	public function registerConnection($connectionInfo, $connectionId, $connectionType = null)
	{
		if (!$connectionType) $connectionType = self::CONNECTION_TYPE_DEFAULT;
		if (!$connectionId) $connectionId = $connectionInfo->getConnectionDSN();

		if (!isset($this->_connectionsList[$connectionId])) $this->_connectionsList[$connectionId] = &$connectionInfo;
		$this->_connectionsLocator[$connectionType][$connectionId]  = &$this->_connectionsList[$connectionId];
		if (!isset($this->_defaultConnections[$connectionType])) $this->_defaultConnections[$connectionType] = &$connectionInfo;
	}

	/**
	 * Registers Database connection DSN and resource
	 *
	 * @param   integer $hConnection
	 * @param   string  $connectionDSN
	 * @return  boolean
	 * @access  public
	 */
	public function registerConnectionDSNResource($hConnection, $connectionDSN)
	{
		if (is_object($hConnection)) $hConnection = spl_object_hash($hConnection);

		$this->_connectionsDSNLocator[(string) $hConnection] = $connectionDSN;
	}

	/**
	 * Returns database connection DSN for resource
	 *
	 * @param   integer $hConnection
	 * @param   string  $connectionDSN
	 * @return  boolean
	 * @access  public
	 */
	public function getDSNForConnection($hConnection)
	{
		if (is_object($hConnection)) $hConnection = spl_object_hash($hConnection);

		if (isset($this->_connectionsDSNLocator[(string) $hConnection])) return $this->_connectionsDSNLocator[(string) $hConnection];

		return false;
	}

	/**
	 * Returns database connection Object
	 *
	 * @param   string $connectionType
	 * @param   string $connectionId Identifier of the Connection
	 * @param   bool   $getRandomConnection Flag shows to return random connection from list
	 * @return  PHP2_Database_ConnectionInfo
	 * @access  public
	 */
	public function getConnectionInfo($connectionId = null, $connectionType = null, $getRandomConnection = false)
	{
		/**
		 * Finding connection by its ID
		 */
		if (!$connectionType) return $this->_connectionsList[$connectionId];

		/**
		 * Processing Typed connections
		 */
		if (isset($this->_connectionsLocator[$connectionType][$connectionId]))
		{
			return $this->_connectionsLocator[$connectionType][$connectionId];
		}
		elseif ($getRandomConnection && isset($this->_connectionsLocator[$connectionType]) && (($connectionsCount = count($this->_connectionsLocator[$connectionType])) > 1))
		{
			/**
			 * Returning random connection
			 */
			$currentConnectionIndex = rand(0, $connectionsCount);
			$i = 1;
			foreach ($this->_connectionsLocator[$connectionType] as $connectionId => &$connectionDetails)
			{
				if ($i >= $currentConnectionIndex) return $connectionDetails;

				$i++;
			}
		}

		/**
		 * Trying to find default connection
		 */
		if (isset($this->_defaultConnections[$connectionType]))
		{
			return $this->_defaultConnections[$connectionType];
		}
		else if (isset($this->_connectionsLocator[self::CONNECTION_TYPE_DEFAULT]) && count($this->_connectionsLocator[self::CONNECTION_TYPE_DEFAULT]))
		{
			$connection = current($this->_connectionsLocator[self::CONNECTION_TYPE_DEFAULT]);

			return $connection;
		}
		else
		{
			throw new PHP2_Exception_EDatabaseException(PHP2_Exception_EDatabaseException::ERROR_CONNECTION_NOT_FOUND);
		}
	}

	/**
	 * Returns database connection resource
	 *
	 * @param   string $connectionId Identifier of the Connection
	 * @param   string $connectionType
	 * @param   bool   $getRandomConnection Flag shows to return random connection from list
	 * @return  MySQLi
	 * @access  public
	 */
	public function getConnection($connectionId = null, $connectionType = null, $getRandomConnection = false)
	{
		return $this->getConnectionInfo($connectionId, $connectionType, $getRandomConnection)->getConnection();
	}

	/**
	 * Returns database connections of some connection type
	 *
	 * @param   string $connectionType
	 * @return  array
	 * @access  public
	 */
	public function getConnectionsList($connectionType = null)
	{
		return (isset($this->_connectionsLocator[$connectionType]) ? $this->_connectionsLocator[$connectionType] : array());
	}

}
