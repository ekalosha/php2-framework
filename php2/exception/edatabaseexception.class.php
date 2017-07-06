<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Exceptions Class for Database Operations
 *
 * PHP version 5
 * @category   Exception Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 115 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 3.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace PHP2\Exception;

/**
 * Class processes Database Operations Exceptions
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: edatabaseexception.class.php 115 2010-08-12 09:28:36Z eugene $
 * @access   public
 * @package  PHP2\Exception
 */
class PHP2_Exception_EDatabaseException extends PHP2_Exception_EAbstractException
{
    /**
     * Error Code Constants
     */
    const ERROR_DB_CONNECT         = 200;
    const ERROR_DB_NOT_EXISTS      = 201;
    const ERROR_CONNECTION_NOT_FOUND  = 202;
    const ERROR_INSERT_DATA        = 210;
    const ERROR_UPDATE_DATA        = 211;
    const ERROR_DELETE_DATA        = 212;
    const ERROR_INVALID_QUERY      = 213;
    const ERROR_RECORD_NOT_EXISTS  = 221;

    /**
     * Database Exception constructor
     *
     * @param   integer $errorCode Error code
     * @param   array   $exceptionParams Exception additional parameters array
     * @param   string  $errorMessage Displayed Error message
     * @access  public
     */
    public function __construct($errorCode = false, $exceptionParams = false, $errorMessage = false)
    {
        // --- Initializing Parent Object --- //
        parent::__construct($errorCode, $exceptionParams, $errorMessage);
    }

    /**
     * Return Default Error Message Template
     *
     * @param   integer $errorCode
     * @return  string
     * @access  public
     */
    public function getDefaultErrorMessageTemplate($errorCode)
    {
        // --- Initializing Default Error Message Template --- //
        switch ($errorCode)
        {
            case self::ERROR_DB_CONNECT        : $errorMessage  = 'Error Connection to the DataBase Server! Please contact to the System Administrator!'; break;
            case self::ERROR_DB_NOT_EXISTS     : $errorMessage  = 'Project DataBase not Exists or You dont have Permissions to access to the DataBase! Please contact to the System Administrator!'; break;
            case self::ERROR_INVALID_QUERY     : $errorMessage  = 'There were some errors in the database! Please contact to the System Administrator!'; break;
            case self::ERROR_RECORD_NOT_EXISTS : $errorMessage  = 'Needed record is not exists in the database! Please contact to the System Administrator!'; break;
            case self::ERROR_INSERT_DATA       : $errorMessage  = 'There were some errors during inserting data! Data were not saved!'; break;
            case self::ERROR_UPDATE_DATA       : $errorMessage  = 'There were some errors during updating data! Data were not updated!'; break;
            case self::ERROR_DELETE_DATA       : $errorMessage  = 'There were some errors during deleting data! Data were not deleted!'; break;
            case self::ERROR_CONNECTION_NOT_FOUND : $errorMessage  = 'Connection to the database not found! Please contact to the System Administrator!'; break;

            default : $errorMessage = 'Error: Unrecognized Database Exception! Please contact to the System Administrator!'; break;
        }

        return $errorMessage;
    }
}
