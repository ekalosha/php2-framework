<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Class which Implements Database SQL Query
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 99 $
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
 * Class Implements Database SQL Query
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: sqlquery.class.php 99 2009-10-20 14:44:49Z eugene $
 * @access   public
 * @package  PHP2\Database
 */
class PHP2_Database_SQLQuery
{
    /**
     * Sort order constants
     */
    const SORT_ORDER_DESC = 'DESC';
    const SORT_ORDER_ASC  = 'ASC';

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
     * SQL Query body
     *
     * @var     string
     * @access  protected
     */
    protected $_sqlQuery;

    /**
     * Execution result resource of last query
     *
     * @var     MySQLi_Result
     * @access  protected
     */
    protected $_lastResult;

    /**
     * Free last result in Destructor flag
     *
     * @var     boolean
     * @access  protected
     */
    protected $_freeLastResultOnDestroy;

    /**
     * Class constructor
     *
     * @var     string $sqlQuery
     * @var     string $connectionId Database connection ID
     * @access  public
     */
    public function __construct($sqlQuery = null, $connectionId = null)
    {
        if ($connectionId) $this->setConnection($connectionId);

        if ($sqlQuery) $this->setQuery($sqlQuery);
    }

    /**
     * Class destructor. Cleans query resources.
     *
     * @access  public
     */
    public function __destruct()
    {
        /**
         * Have a problems with Recordset Objects. Should be changed.
         */
        if ($this->_freeLastResultOnDestroy) $this->freeResult();
    }

    /**
     * Set connection by ID. Connection could be initialized from the Different Sources
     *
     * @param   mixed $connectionId
     * @return  boolean
     * @access  public
     */
    public function setConnection($connectionId)
    {
        if (is_resource($connectionId))
        {
            $this->_hConnection = $connectionId;
        }
        else if (is_object($connectionId))
        {
            if ($connectionId instanceof MySQLi)
            {
                $this->_hConnection = $connectionId;
            }
            elseif ($connectionId instanceof PHP2_Database_ConnectionInfo)
            {
                /* @var $connectionId PHP2_Database_ConnectionInfo */
                $this->_hConnection = $connectionId->getConnection();
            }
        }
        else
        {
            $this->_hConnection = PHP2_Database_ConnectionsPool::getInstance()->getConnection($connectionId);
        }
    }

    /**
     * Sets current SQL Query
     *
     * @access  public
     */
    public function setQuery($sqlQuery)
    {
        $this->_sqlQuery = $sqlQuery;
    }

    /**
     * Free last SQL Query result
     *
     * @access  public
     */
    public function freeResult()
    {
        if ($this->_lastResult && is_object($this->_lastResult)) $this->_lastResult->free_result();
    }

    /**
     * Return last SQL result resource
     *
     * @return  integer last SQL result resource
     * @access  public
     */
    public function getLastResult()
    {
        return $this->_lastResult;
    }

    /**
     * Return last inserted Id
     *
     * @return  integer last insert id
     * @access  public
     */
    public function getLastInsertId()
    {
        return $this->_hConnection->insert_id;
    }

    /**
     * Executes current SQL query
     *
     * @return  boolean
     * @access  public
     */
    public function execute()
    {
        /**
         * Clear current result
         */
        $this->freeResult();

        $result = false;

        $connectionDSN = PHP2_Database_ConnectionsPool::getInstance()->getDSNForConnection($this->_hConnection);
        PHP2_System_Profiler::getInstance()->setStartProfilerBreakpoint($connectionDSN, $this->_sqlQuery, PHP2_System_Profiler::PROFILER_GROUP_DB);
        if ($this->_lastResult = $this->_hConnection->query($this->_sqlQuery)) $result = true;

        $additionalParameters = array('queryResult' => $result ? '1' : '0');
        if (!$result)
        {
            $additionalParameters['queryErrorCode']    = $this->_hConnection->errno;
            $additionalParameters['queryErrorMessage'] = $this->_hConnection->error;
        }
        PHP2_System_Profiler::getInstance()->setEndProfilerBreakpoint($connectionDSN, PHP2_System_Profiler::PROFILER_GROUP_DB, $additionalParameters);

        /**
         * Set auto clean result in destructor Flag
         */
        $this->_freeLastResultOnDestroy = true;

        return $result;
    }

    /**
     * Executes SQL Query and return Object of the current Class as result.
     *
     * @param   string $sqlQuery
     * @param   mixed $connectionId Could be Connection Resource, Instanse of the PHP2_Database_ConnectionInfo or Connection ID in the Connections pool
     * @return  PHP2_Database_SQLQuery
     * @access  public
     * @throws  PHP2_Exception_EDatabaseException in case of error during SQL query execution.
     * @static
     */
    public static function executeQuery($sqlQuery, $connectionId)
    {
        $sqlQueryObject = new PHP2_Database_SQLQuery($sqlQuery, $connectionId);

        if (!$sqlQueryObject->execute() && defined('DATABASE_DEBUG_MODE') && DATABASE_DEBUG_MODE) throw new PHP2_Exception_EDatabaseException(PHP2_Exception_EDatabaseException::ERROR_INVALID_QUERY);

        return $sqlQueryObject;
    }

    /**
     * Executes Paged SQL Query and return Object of the current Class as result.
     *
     * @param   string $sqlQuery
     * @param   mixed $connectionId Could be Connection Resource, Instanse of the PHP2_Database_ConnectionInfo or Connection ID in the Connections pool
     * @param   integer $pageSize Size of the recordset page. Used in the paged queries.
     * @param   integer $pageNumber Number of the recordset page. Used in the paged queries.
     * @param   string  $sortField Current sort field. Usually used in the paged queries.
     * @param   string  $sortOrder Current sort field order. Usually used in the paged queries.
     * @return  PHP2_Database_SQLQuery
     * @access  public
     * @throws  PHP2_Exception_EDatabaseException in case of error during SQL query execution.
     * @static
     */
    public static function executePagedQuery($sqlQuery, $connectionId, $pageSize = false, $pageNumber = false, $sortField = false, $sortOrder = false)
    {
        $queryOrderAdd = (($sortField) ? ' ORDER BY '.$sortField.(($sortOrder == self::SORT_ORDER_ASC) ? ' ASC' : ' DESC') : '');
        $queryPageAdd  = ($pageNumber && $pageSize) ? ' LIMIT '.(($pageNumber - 1) * $pageSize).', '.$pageSize : '';

        return self::executeQuery($sqlQuery.$queryOrderAdd.$queryPageAdd, $connectionId);
    }

    // --- Data fetching functions --- //

    /**
     * Returns last SQL result as Recordset
     *
     * @return  PHP2_Database_Recordset
     * @access  public
     */
    public function getRecordset()
    {
        $this->_freeLastResultOnDestroy = false;

        return new PHP2_Database_Recordset($this->_lastResult);
    }

    /**
     * Returns last SQL result as Matrix
     *
     * @param   string $keyField
     * @param   string $groupByField field for Group By operation
     * @return  array
     * @access  public
     */
    public function getMatrix($keyField = false, $groupByField = false)
    {
        $result   = array();

        if (!$this->_lastResult) return $result;

        while ($row = $this->_lastResult->fetch_assoc())
        {
            if ($keyField)
            {
                if (!$groupByField)
                {
                    $result[$row[$keyField]] = $row;
                }
                else
                {
                    $result[$row[$keyField]][$row[$groupByField]] = $row;
                }

            }
            else
            {
                $result[] = $row;
            }
        }

        return $result;
    }

    /**
     * Returns last SQL result as List of values
     *
     * @param   string $listField
     * @param   string $keyField
     * @return  array
     * @access  public
     */
    public function getList($listField, $keyField = false)
    {
        $result   = array();

        while ($row = $this->_lastResult->fetch_assoc())
        {
            if ($keyField)
            {
                $result[$row[$keyField]] = $row[$listField];
            }
            else
            {
                $result[] = $row[$listField];
            }
        }

        return $result;
    }

    /**
     * Returns first field from first Row of last SQL result
     *
     * @return  string
     * @access  public
     */
    public function getScalar()
    {
        if (!$this->_lastResult) return null;

        $row = $this->_lastResult->fetch_array(MYSQLI_NUM);

        return isset($row[0]) ? $row[0] : null;
    }

    /**
     * Returns first Row of last SQL result
     *
     * @return  array
     * @access  public
     */
    public function getVector()
    {
        $row = $this->_lastResult->fetch_assoc();

        return $row ? $row : array();
    }

    /**
     * Returns fields list for last SQL result
     *
     * @return  array
     * @access  public
     */
    public function getFieldsList()
    {
        if (!$this->_lastResult) return false;

        $result = $this->_lastResult->fetch_fields();

        return $result;
    }

    /**
     * Escapes parameter to use in SQL queries
     *
     * @param   string $value Value to escape
     * @return  string
     */
    public static function escape($value)
    {
        return addslashes(stripslashes($value));
    }

}
