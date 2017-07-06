<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains utils Class for array functions
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
 * Utils class for array functions
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: array.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\Utils
 */
class PHP2_Utils_Array
{
    /**
     * Returns randomized assoc array
     *
     * @param   array    $sourceArray
     * @param   integer  $limit
     * @return  array
     * @access  public
     * @static
     */
    public static function randomize($sourceArray, $limit = null)
    {
        $result = array();

        $arrayKeys  = array_keys($sourceArray);
        shuffle($arrayKeys);

        $i = 0;
        foreach ($arrayKeys as $sourceKey)
        {
            $result[$sourceKey] = $sourceArray[$sourceKey];
            $i++;

            if ($limit && ($i >= $limit)) break;
        }

        return $result;
    }

    /**
     * Returns array from string with delimiters. If source is array, returns it.
     *
     * @param   string  $delimiterString
     * @param   boolean $ignoreEmptyValues ignore or not empty values in the result
     * @param   string  $delimiters Possible delimiters
     * @return  array
     * @access  public
     * @static
     */
    public static function parseDelimiterString($delimiterString, $ignoreEmptyValues = true, $delimiters = '\s\;\:\,')
    {
        /**
         * If source is array return it without any changes
         */
        if (is_array($delimiterString)) return $delimiterString;

        $result = array();

        $validatedString  = preg_replace('/['.$delimiters.']+/', ';', $delimiterString);
        $tmpResult        = explode(';', $validatedString);

        if ($ignoreEmptyValues)
        {
            foreach ($tmpResult as $value)
            {
                if ($value) $result[] = $value;
            }
        }
        else
        {
            $result = $tmpResult;
        }

        return $result;
    }

    /**
     * Checks is current array is associative array.
     * As associative array we assume array with one or more non numeric keys or array with invalid numeric keys order.
     *
     * @param   array $sourceArray
     * @return  boolean
     */
    public static function isAssociative($sourceArray)
    {
        $arrayKeys   = array_keys($sourceArray);
        $arrayLength = count($arrayKeys);
        for ($i = 0; $i < $arrayLength; $i++)
        {
            if ($i !== $arrayKeys[$i]) return true;
        }

        return false;
    }

}
