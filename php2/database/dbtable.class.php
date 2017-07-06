<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Class which Implements Database Table Element
 *
 * PHP version 5
 * @category   Library Classes
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
// namespace PHP2\Database;


/**
 * Class implements database table operations
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: dbtable.class.php 98 2009-08-19 17:15:05Z eugene $
 * @access   public
 * @package  PHP2\Database
 */
abstract class PHP2_Database_DBTable implements Serializable
{
    /**
     * Current Table Name
     *
     * @var     string
     * @access  protected
     */
    protected $_tableName;

    /**
     * Primary Key Field for Current Table
     *
     * @var     string
     * @access  protected
     */
    protected $_pkFieldName = 'ID';

    /**
     * Connection ID or Handle
     *
     * @var     string
     * @access  protected
     */
    protected $_hConnection;

    /**
     * Class constructor
     *
     * @var     string $connectionId Database connection ID
     * @var     string $pkFieldValue
     * @access  public
     */
    public function __construct($connectionId = null, $pkFieldValue = null)
    {
        // --- Initializing default table Name --- //
        if (!$this->_tableName)
        {
            $classNameParts    = explode('_', get_class($this));
            $this->_tableName  = $classNameParts[count($classNameParts) - 1];
        }

        // --- Set connection ID --- //
        if ($connectionId) $this->setConnection($connectionId);

        // --- Loading Current Value --- //
        if (($pkFieldValue) && ($this->checkRecord($pkFieldValue))) $this->loadDetails($pkFieldValue);
    }

    /**
     * Class destructor. Cleans query resources.
     *
     * @access  public
     */
    public function __destruct()
    {
    }

    /**
     * Set connection identifier for current DB Object
     *
     * @return  void
     * @access  public
     */
    public function setConnection($connectionId)
    {
        $this->_hConnection = $connectionId;
    }

    /**
     * Get connection identifier for current DB Object
     *
     * @return  mixed
     * @access  public
     */
    public function getConnection()
    {
        return $this->_hConnection;
    }

    /**
     * Load Details for current Object
     *
     * @param   integer $pkFieldValue Selected record Unique ID
     * @param   string  $pkFieldName  Primary Key Name
     * @return  boolean
     * @access  public
     */
    public function loadDetails($pkFieldValue, $pkFieldName = false)
    {
        if (!$pkFieldName) $pkFieldName = $this->_pkFieldName;

        $sqlQuery   = 'SELECT * FROM `'.$this->_tableName.'` WHERE '.$pkFieldName.'='.$pkFieldValue;

        try
        {
            $sqlQueryObject  = PHP2_Database_SQLQuery::executePagedQuery($sqlQuery, $this->_hConnection, 1, 1);
            $fieldsList      = $sqlQueryObject->getFieldsList();
            $loadedInfo      = $sqlQueryObject->getVector();

            foreach ($fieldsList as $index => &$fieldMetaInfo)
            {
                if (isset($loadedInfo[$fieldMetaInfo->name]))
                {
                    switch ($fieldMetaInfo->type)
                    {
                        case MYSQLI_TYPE_DATE:
                        case MYSQLI_TYPE_DATETIME:
                            $dateObject  = new PHP2_Database_DataType_DateTime($loadedInfo[$fieldMetaInfo->name]);
                            $fieldValue  = $dateObject->getTime();
                        break;

                        default:
                            $fieldValue = $loadedInfo[$fieldMetaInfo->name];
                        break;
                    }

                    $this->{$fieldMetaInfo->name} = $fieldValue;
                }
            }

            return true;
        }
        catch (PHP2_Exception_EDatabaseException $queryError)
        {
            return false;
        }

        return false;
    }

    /**
     * Checks is current Object exists in the Database
     *
     * @param   integer $pkFieldValue Checked record Unique ID
     * @param   boolean $enableEmptyPKValue Enable/disable empty values in the PK
     * @return  boolean
     * @access  public
     */
    public function checkRecord($pkFieldValue, $enableEmptyPKValue = false)
    {
        $currentRecordId  = (($pkFieldValue) ? $pkFieldValue : $this->{$this->_pkFieldName});
        $sqlQuery         = "SELECT COALESCE($this->_pkFieldName, COUNT($this->_pkFieldName)) FROM `$this->_tableName` WHERE $this->_pkFieldName='$currentRecordId'";

        return PHP2_Database_SQLQuery::executePagedQuery($sqlQuery, $this->_hConnection, 1, 1)->getScalar();
    }

    /**
     * Returns value of specified field
     *
     * @param   integer $fieldName Field name
     * @param   integer $pkFieldValue Checked record Unique ID
     * @return  string
     * @access  public
     */
    public function getFieldValue($fieldName, $pkFieldValue)
    {
        $currentRecordId  = (($pkFieldValue) ? $pkFieldValue : $this->{$this->_pkFieldName});
        $sqlQuery         = "SELECT $fieldName FROM `$this->_tableName` WHERE $this->_pkFieldName='$pkFieldValue' LIMIT 1";

        return PHP2_Database_SQLQuery::executeQuery($sqlQuery, $this->_hConnection)->getScalar();
    }

    /**
     * Returns fields list for current database table
     *
     * @return  array
     * @access  public
     */
    public function getFieldsList()
    {
        $sqlQuery        = "SELECT * FROM `$this->_tableName` LIMIT 0";

        return PHP2_Database_SQLQuery::executeQuery($sqlQuery, $this->_hConnection)->getFieldsList();
    }

    /**
     * Return database record Details info as Associate array
     *
     * @return  array
     * @access  public
     */
    public function getDetailsInfo()
    {
        $result = array();

        $fieldsList = $this->getFieldsList();
        foreach ($fieldsList as $fieldMetaInfo)
        {
            if (isset($this->{$fieldMetaInfo->name}))
            {
                /**
                 * Processing IDataType interface
                 */
                if (is_object($this->{$fieldMetaInfo->name}) && ($this->{$fieldMetaInfo->name} instanceof PHP2_Database_DataType_IDataType))
                {
                    $value = $this->{$fieldMetaInfo->name}->getValue();
                }
                else
                {
                    $currentFieldValue = $this->{$fieldMetaInfo->name};
                    switch ($fieldMetaInfo->type)
                    {
                        case MYSQLI_TYPE_DATE:
                        case MYSQLI_TYPE_DATETIME:
                            $this->{$fieldMetaInfo->name} = new PHP2_Database_DataType_DateTime($this->{$fieldMetaInfo->name});
                            $currentFieldValue            = $this->{$fieldMetaInfo->name}->getValue();
                        break;

                        default:
                            if (is_string($currentFieldValue)) $currentFieldValue = '\''.PHP2_Database_SQLQuery::escape($this->{$fieldMetaInfo->name}).'\'';
                        break;
                    }

                    $value = $currentFieldValue;
                }

                $result['`'.$fieldMetaInfo->name.'`'] = $value;
            }
        }

        return $result;
    }

    /**
     * Inserts record to the Database
     *
     * @return  integer new record UID
     * @access  public
     */
    public function insert()
    {
        $insertDetails = $this->getDetailsInfo();
        if (!count($insertDetails)) return false;

        $sqlQuery        = 'INSERT INTO `'.$this->_tableName.'` ('.implode(', ', array_keys($insertDetails)).') VALUES ('.implode(', ', $insertDetails).')';
        $sqlQueryObject  = PHP2_Database_SQLQuery::executeQuery($sqlQuery, $this->_hConnection);

        // --- Get Current Inserted ID --- //
        if (!isset($this->{$this->_pkFieldName}) || !$this->{$this->_pkFieldName}) $this->{$this->_pkFieldName} = $sqlQueryObject->getLastInsertId();

        return $this->{$this->_pkFieldName};
    }

    /**
     * Updates record in the Database
     *
     * @param   integer $pkFieldValue
     * @return  integer
     * @access  public
     */
    public function update($pkFieldValue = null)
    {
        /**
         * Get updated fields Info
         */
        $updateDetails = $this->getDetailsInfo();
        if (!count($updateDetails)) return false;

        /**
         * Check PK field value to update
         */
        if (!$pkFieldValue) $pkFieldValue = $this->{$this->_pkFieldName};

        /**
         * Get values list for update
         */
        foreach ($updateDetails as $fieldName => $fieldValue) $updatedFieldsList[] = $fieldName.'='.$fieldValue;

        $sqlQuery        = 'UPDATE `'.$this->_tableName.'` SET '.implode(', ', $updatedFieldsList).' WHERE '.$this->_pkFieldName.'=\''.$pkFieldValue.'\'';
        $sqlQueryObject  = PHP2_Database_SQLQuery::executeQuery($sqlQuery, $this->_hConnection);

        return $sqlQueryObject->getLastResult() ? $pkFieldValue : false;
    }

    /**
     * Deletes record from the Database
     *
     * @param   integer $pkFieldValue
     * @return  integer
     * @access  public
     */
    public function delete($pkFieldValue = false)
    {
        /**
         * Check PK field value to update
         */
        if (!$pkFieldValue) $pkFieldValue = $this->{$this->_pkFieldName};

        $sqlQuery = 'DELETE FROM `'.$this->_tableName.'` WHERE '.$this->_pkFieldName.'=\''.$pkFieldValue.'\'';

        return PHP2_Database_SQLQuery::executeQuery($sqlQuery, $this->_hConnection)->getLastResult();
    }

    /**
     * Executes simple query to this page
     *
     * @param   mixed   $fieldsList Fields list as array or string
     * @param   mixed   $searchPattern Search Pattern Structure
     * @param   integer $limit Result limit
     * @param   mixed   $orderAndGroup
     * @return  PHP2_Database_SQLQuery
     * @access  public
     */
    public function select($fieldsList = null, $searchPattern = null, $limit = null, $orderAndGroup = null)
    {
        /**
         * Get fields list
         */
        if (!$fieldsList)
        {
            $fieldsList = '*';
        }
        elseif (is_array($fieldsList) || is_object($fieldsList))
        {
            $fieldsList = implode(', ', $fieldsList);
        }

        $queryAdd = '';
        if ($limit)
        {
            $queryAdd  = ((strpos($limit, ',')) ? ' LIMIT '.$limit : ' LIMIT 0, '.$limit);
        }
        $sqlQuery  = 'SELECT '.$fieldsList.' FROM `'.$this->_tableName.'` WHERE '.$this->_getSearchPattern($searchPattern).($orderAndGroup ? ' '.$orderAndGroup : '').$queryAdd;

        return PHP2_Database_SQLQuery::executeQuery($sqlQuery, $this->_hConnection);
    }

    /**
     * Returns List of Records for Table
     *
     * @param   array   $searchPattern Search Pattern Structure
     * @param   integer $limit Result limit
     * @return  PHP2_Database_SQLQuery
     * @access  public
     */
    public function getList($searchPattern = null, $limit = null)
    {
        $queryAdd  = (($limit) ? ' LIMIT 0, '.$limit : '');
        $sqlQuery  = 'SELECT * FROM `'.$this->_tableName.'` WHERE '.$this->_getSearchPattern($searchPattern).$queryAdd;

        return PHP2_Database_SQLQuery::executeQuery($sqlQuery, $this->_hConnection);
    }

    /**
     * Returns Records Count for Table. Alias of the 'getRecordsCount' method.
     *
     * @param   array   $searchPattern Search Pattern Structure
     * @return  integer
     * @access  public
     * @see     getRecordsCount()
     */
    public function getListSize($searchPattern = false)
    {
        return $this->getRecordsCount($searchPattern);
    }

    /**
     * Returns Records Count for Table
     *
     * @param   array   $searchPattern Search Pattern Structure
     * @return  integer
     * @access  public
     */
    public function getRecordsCount($searchPattern = false)
    {
        $sqlQuery = 'SELECT COUNT(*) AS RecordsCount FROM `'.$this->_tableName.'` WHERE '.$this->_getSearchPattern($searchPattern);

        return PHP2_Database_SQLQuery::executeQuery($sqlQuery, $this->_hConnection)->getScalar();
    }

    /**
     * Returns Paged List of Records for Table
     *
     * @param   integer $pageSize   Recorde per page
     * @param   integer $pageNumber Current page number
     * @param   string  $sortField Sort Field name
     * @param   string  $sortOrder Sort Order
     * @param   array   $searchPattern Search Pattern Structure
     * @return  PHP2_Database_SQLQuery
     * @access  public
     */
    public function getListPaged($pageSize, $pageNumber, $sortField = null, $sortOrder = null, $searchPattern = false)
    {
        $sqlQuery = 'SELECT * FROM `'.$this->_tableName.'` WHERE '.$this->_getSearchPattern($searchPattern);

        return PHP2_Database_SQLQuery::executePagedQuery($sqlQuery, $this->_hConnection, $pageSize, $pageNumber, $sortField, $sortOrder);
    }

    /**
     * Searches record in Database
     *
     * @param   integer $searchPattern Search Pattern Array
     * @return  integer
     * @access  public
     */
    public function searchRecord($searchPattern = false)
    {
        $sqlQuery = 'SELECT COALESCE(`'.$this->_pkFieldName.'`, 0) AS ID  FROM `'.$this->_tableName.'` WHERE '.$this->_getSearchPattern($searchPattern);

        return PHP2_Database_SQLQuery::executeQuery($sqlQuery, $this->_hConnection)->getScalar();
    }

    /**
     * Build Search Pattern from Search Pattern Array
     *
     * Search Pattern must have the following structure:
     *
     * <code>
     *   / **
     *     * Type parameter should take one of the following values:
     *     *
     *     * 'Type' =>  "=, <>, >, <, >=, <=, LIKE, RLIKE, LLIKE"
     *     *
     *     *  /
     *   $searchPattern = array
     *                          (
     *                            0 => array
     *                                       (
     *                                         'Field'  => 'FieldName',
     *                                         'Type'   => 'SearchType',
     *                                         'Value'  => 'SearchedValue',
     *                                         'JOIN'   => 'AND|OR',
     *                                        ),
     *                          );
     *
     * </code>
     *
     * @param   array   $searchPattern Search Pattern Structure
     * @return  string
     * @access  public
     */
    protected function _getSearchPattern($searchPattern, $singleMode = true)
    {
        $result = (($singleMode) ? ' ' : ' AND ');
        if (is_string($searchPattern)) return $result.' '.$searchPattern;
        if (!is_array($searchPattern) || (!count($searchPattern))) return $result.'1';

        $count  = count($searchPattern);
        $i      = 0;
        $result .= '( ';
        foreach ($searchPattern as $index => $searchItem)
        {
            $result .= (($i == 0) ? '' : ((isset($searchItem['JOIN'])) ? $searchItem['JOIN'].' ' : 'AND '));
            $result .= $searchItem['Field'].' '.((isset($searchItem['Type']) ? $searchItem['Type'] : '=')).' \''.$searchItem['Value'].'\' ';
            $i++;
        }
        $result .= ') ';

        return $result;
    }

    /**
     * Returns instance of the Current Class for the specified connection
     *
     * This is not a singleton. Such approach is usefull for SQL queries.
     *
     * @param   string $connectionId Database connection ID
     * @return  PHP2_Database_DBTable
     * @access  public
     * @static
     */
    public static function getInstance($connectionId)
    {
        $currentClass     = __CLASS__;

        return new $currentClass($connectionId);
    }

    /**
     * Describe serialization logic for current object.
     *
     * This method is implementation of Serializable interface from SPL, as a result we can use custom Serialization logic for the objects of this class.
     *
     * @access  public
     */
    public function serialize()
    {
        $result = array();

        foreach ($this as $fieldName => &$fieldValue) $result[$fieldName] = $fieldValue;

        unset($result['_pkFieldName']);
        unset($result['_tableName']);
        unset($result['_hConnection']);

        return serialize($result);
    }

    /**
     * Describe deserialization logic for current object
     *
     * This method is implementation of Serializable interface from SPL, as a result we can use custom Serialization logic for the objects of this class.
     *
     * @access  public
     */
    public function unserialize($serialized)
    {
        /**
         * Calling constructor to initialize default Object data
         */
        $this->__construct();

        if (($resultArray = unserialize($serialized)) && (is_array($resultArray) || is_object($resultArray)))
        {
            foreach ($resultArray as $fieldName => $fieldValue) $this->{$fieldName} = $fieldValue;
        }
    }

    /**
     * Extends current object with the fields from the specified object
     *
     * @param   mixed   $dbObject
     * @param   boolean $forceOverride force override field values
     * @access  public
     */
    public function extend($dbObject, $forceOverride = false)
    {
        if (is_object($dbObject) || is_array($dbObject))
        {
            $classReflection = new ReflectionClass(get_class($this));
            foreach ($dbObject as $fieldName => $fieldValue)
            {
                if ($forceOverride || (!$classReflection->hasProperty($fieldName)))
                {
                    $this->{$fieldName} = $fieldValue;
                }
            }
        }
    }

}
