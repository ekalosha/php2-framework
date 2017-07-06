<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains utils Class for Date and Time functions
 *
 * PHP version 5
 * @category   System Utilities
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
// namespace PHP2\Utils;

/**
 * Utils class for date and time functions
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: datetime.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\Utils
 */
class PHP2_Utils_DateTime
{
    /**
     * Date/Time formats
     */
    const FORMAT_DATETIME_SYSTEM  = 'Y-m-d H:i:s';
    const FORMAT_DATE_SYSTEM      = 'Y-m-d';
    const FORMAT_DATETIME_MYSQL   = 'Y-m-d H:i:s';
    const FORMAT_DATE_MYSQL       = 'Y-m-d';

    /**
     * Date timestamp
     *
     * @var  integer
     */
    protected $_timestamp = null;

    /**
     * PHP2_Utils_DateTime class constructor
     *
     * @param   mixed $dateTimeValue
     * @param   mixed $dateTimeMask
     * @access  public
     */
    public function __construct($dateTimeValue = null, $dateTimeMask = null)
    {
        if (!$dateTimeValue) $dateTimeValue = time();

        $this->setDateTime($dateTimeValue);

        if ($dateTimeMask) $this->applyMask($dateTimeMask);
    }

    /**
     * Returns instance of the Current Class
     *
     * This is not a singleton. Such approach is usefull for functional programming.
     *
     * @param   mixed $dateTimeValue
     * @param   mixed $dateTimeMask
     * @return  PHP2_Utils_DateTime
     * @access  public
     * @static
     */
    public static function getInstance($dateTimeValue = null, $dateTimeMask = null)
    {
        return new self($dateTimeValue, $dateTimeMask);
    }

    /**
     * Sets value for current datatype object
     *
     * @param   mixed $value
     * @return  boolean
     * @access  public
     */
    public function setDateTime($value = null)
    {
        if (!$value) return false;

        /**
         * Assigning from the DateTime object
         */
        if (is_object($value) && (($value instanceof  PHP2_Utils_DateTime) || (method_exists($value, 'getTime'))))
        {
            $this->_timestamp = $value->getTime();

            return true;
        }

        /**
         * Set date from String or Timestamp
         */
        if (is_string($value))
        {
            $this->_timestamp = strtotime($value);
        }
        elseif (is_int($value))
        {
            $this->_timestamp = $value;
        }

        return true;
    }

    /**
     * Sets mask for DateTime
     *
     * @param   string $mask
     * @return  boolean
     * @access  public
     */
    public function applyMask($mask)
    {
        /**
         * Recursively applying Masks from Array
         */
        if (is_array($mask))
        {
            $result = true;
            foreach ($mask as $maskItem)
            {
                if (!$this->applyMask($maskItem)) $result = false;
            }

            return $result;
        }

        /**
         * Triyng to apply Mask from atrtotime conversion
         */
        if (($tmpTime = strtotime($mask, $this->_timestamp)) !== false)
        {
            $this->_timestamp = $tmpTime;
        }
        elseif (($tmpTime = strtotime(date($mask, $this->_timestamp))) !== false)
        {
            $this->_timestamp = $tmpTime;
        }
        else
        {
            return false;
        }

        return true;
    }

    /**
     * Return value of current datetime object
     *
     * @return  boolean
     * @access  public
     */
    public function getTime()
    {
        return $this->_timestamp;
    }

    /**
     * Return value of current datetime object
     *
     * @param   string $dateFormat
     * @return  boolean
     * @access  public
     */
    public function getDate($dateFormat = null)
    {
        return date(($dateFormat ? $dateFormat : self::FORMAT_DATETIME_SYSTEM), $this->_timestamp);
    }

    /**
     * Return days count in month
     *
     * @param   integer $monthNumber Month Number
     * @param   integer $yearNumber  Year Number, by default used current Year
     * @return  integer
     * @access  public
     * @static
     */
    public static function getDaysCountInMonth($monthNumber, $yearNumber = false)
    {
        if (!$yearNumber) $yearNumber = date('Y');

        switch ($monthNumber)
        {
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
                return 31;
            break;

            case 2:
                return ($yearNumber % 4 != 0) ? 28 : (($yearNumber % 2000 == 0) ? 29 : (($yearNumber % 400 != 0) ? 29 : 28));
            break;

            case 4:
            case 6:
            case 9:
            case 11:
                return 30;
            break;

            default:
               return false;
            break;
        }
    }


    /**
     * Return formatted string for Date interval in seconds.
     * For example: 3675 s is 1 h 1 min 15 sec
     *
     * @param   integer $secondsCount
     * @return  string
     * @access  public
     * @static
     */
    public static function getDateIntervalString($secondsCount)
    {
        $result = '';
        $level  = 0;
        $yearsSecondsCount   = 365*24*60*60;
        $daySecondsCount     = 24*60*60;
        $hourSecondsCount    = 60*60;
        $minuteSecondsCount  = 60;
        if ($secondsCount >= $yearsSecondsCount)
        {
            $result .= intval($secondsCount / $yearsSecondsCount).'y ';
            $secondsCount = $secondsCount % $yearsSecondsCount;
            if (!$level) $level = 5;
        }

        if ($secondsCount >= $daySecondsCount)
        {
            $result .= intval($secondsCount / $daySecondsCount).'d ';
            $secondsCount = $secondsCount % $daySecondsCount;
            if (!$level) $level = 4;
        }

        if ($secondsCount >= $hourSecondsCount)
        {
            $result .= ((($tmpRes = intval($secondsCount / $hourSecondsCount)) <= 9) ? '0' : '').$tmpRes.'h ';
            $secondsCount = $secondsCount % $hourSecondsCount;
            if (!$level) $level = 3;
        }

        if ($secondsCount >= $minuteSecondsCount)
        {
            $result .= ((($tmpRes = intval($secondsCount / $minuteSecondsCount)) <= 9) ? '0' : '').$tmpRes.'m ';
            $secondsCount = $secondsCount % $minuteSecondsCount;
            if (!$level) $level = 2;
        }

        $result .= ($level > 3) ? '' : $secondsCount.'s';

        return $result;
    }

}
