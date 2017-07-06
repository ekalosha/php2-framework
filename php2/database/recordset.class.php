<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Class which implements Database recordset abstraction
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
 * Class implements Database recordset abstraction
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: recordset.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\Database
 */
class PHP2_Database_Recordset implements Countable, IteratorAggregate
{
    /**
     * Query result resource
     *
     * @var      MySQLi_Result
     * @access   private
     */
    private $_sqlResult;

    /**
     * Result row
     *
     * @var      array
     * @access   protected
     */
    protected $_row = array();

    /**
     * Rows history
     *
     * @var      array
     * @access   private
     */
    private $_rowHistory = array();

    /**
     * All records Count
     *
     * @var      array
     * @access   protected
     */
    protected $_count;

    /**
     * Class constructor. Initializes Recordset.
     *
     * @param   MySQLi_Result $sqlResult Database query result resource
     * @access  public
     */
    public function __construct($sqlResult)
    {
        $this->_sqlResult = $sqlResult;

        /**
         * Get records count in the result set
         */
        if ($this->_sqlResult)
        {
            $this->_count = $this->_sqlResult->num_rows;
        }
    }

    /**
     * Class destructor. Clears recordset resources.
     *
     * @access  public
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Moves reference to the next record in the Recordset
     *
     * @return  boolean
     * @access  public
     */
    public function next()
    {
        if ($this->_sqlResult)
        {
            $this->_row = $this->_sqlResult->fetch_assoc();

            if ($this->_row) $this->_rowHistory[] = $this->_row;

            return ($this->_row) ? true : false;
        }

        return false;
    }

    /**
     * Returns current row in the Recordset
     *
     * @return  array
     * @access  public
     */
    public function getRow()
    {
        return $this->_row;
    }

    /**
     * Closes recordset.
     *
     * This is deprecated method, you can use SQLQuery to close the result
     *
     * @access  public
     * @deprecated
     */
    public function close()
    {
        if ($this->_sqlResult && is_object($this->_sqlResult)) $this->_sqlResult->free_result();
    }

    /**
     * Redefines Access to unregistered fields
     *
     * @param   string $fieldName
     * @return  boolean
     * @access  public
     */
    public function __get($fieldName)
    {
        if (isset($this->_row[$fieldName])) return $this->_row[$fieldName];

        return false;
    }

    /**
     * Export current Recordset to array
     *
     * @return  array
     * @access  public
     * @deprecated
     */
    public function toArray()
    {
        while ($this->next());

        return $this->_rowHistory;
    }

    /**
     * Implementing Countable interface from SPL to have ability to use function count with the object of this class.
     *
     * @return  integer
     * @access  public
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * Implementing IteratorAggregate interface from SPL to have ability to use foreach with the objects of this class.
     *
     * @return  ArrayIterator
     * @access  public
     */
    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }

}
