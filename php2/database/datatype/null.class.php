<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Class which Implements Database datatype NULL
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
 * Class Implements Database datatype NULL
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: null.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\Database\DataType
 */
class PHP2_Database_DataType_Null extends PHP2_Database_DataType_DataType
{
    /**
     * Returns string value of current datatype object
     *
     * @return  string
     * @access  public
     */
    public function getValue()
    {
        return 'NULL';
    }
}
