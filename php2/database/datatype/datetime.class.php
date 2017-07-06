<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Class which Implements Database datatype DateTime
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
// namespace PHP2\Database\DataType;

/**
 * Class Implements Database datatype DateTime
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: datetime.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\Database\DataType
 */
class PHP2_Database_DataType_DateTime extends PHP2_Database_DataType_DataType implements PHP2_WebService_VO_IConvertable
{
    /**
     * Date and DateTime format Constants
     */
    const FORMAT_DATE      = 'Y-m-d';
    const FORMAT_DATETIME  = 'Y-m-d H:i:s';

    /**
     * Returns string value of current datatype object
     *
     * @return  string
     * @access  public
     */
    public function getValue()
    {
        return '\''.date(self::FORMAT_DATETIME, $this->_value).'\'';
    }

    /**
     * Sets value for current datatype object
     *
     * @return  boolean
     * @access  public
     */
    public function setValue($value = null)
    {
        if (!$value) $this->_value = time();

        /**
         * Assigning from the DateTime object
         */
        if (is_object($value) && is_a($value, 'PHP2_Database_DataType_DateTime'))
        {
            $this->_value = $value->getTime();

            return true;
        }

        /**
         * Set date from String or Timestamp
         */
        if (is_string($value))
        {
            $this->_value = strtotime($value);
        }
        elseif (is_int($value))
        {
            $this->_value = $value;
        }

        return true;
    }

    /**
     * Return date in the specified format
     *
     * @return  string
     * @access  public
     */
    public function getTime()
    {
        return $this->_value;
    }

    /**
     * Return date in the specified format
     *
     * @return  string
     * @access  public
     */
    public function getDate($dateFormat = null)
    {
        return ($dateFormat) ? date($dateFormat, $this->_value) : date(self::FORMAT_DATETIME, $this->_value);
    }

    /**
     * Returns this object in Value Object compatible Way
     *
     * @return  array
     * @access  public
     */
    public function getValueObject()
    {
        return $this->getDate();
    }

}
