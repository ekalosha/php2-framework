<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Exceptions Class for Filesystem Operations
 *
 * PHP version 5
 * @category   Exception Classes
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
// namespace PHP2\Exception;

/**
 * Class processes Filesystem Operations Exceptions
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: efilesystemexception.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\Exception
 */
class PHP2_Exception_EFilesystemException extends PHP2_Exception_EAbstractException
{
    /**
     * Error Code Constants
     */
    const ERROR_CANT_DELETE_FILE                = 301;
    const ERROR_MOVING_FILE                     = 302;
    const ERROR_COPIYNG_FILE                    = 303;
    const ERROR_FILE_NOT_EXISTS                 = 307;
    const ERROR_DIRECTORY_NOT_EXISTS            = 308;
    const ERROR_FILE_IS_NOT_DIRECTORY           = 316;
    const ERROR_FILE_IS_NOT_WRITABLE            = 321;
    const ERROR_DIRECTORY_IS_NOT_WRITABLE       = 322;

    /**
     * Filesystem Exception constructor
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
            case self::ERROR_CANT_DELETE_FILE : $errorMessage = 'Error: Can not delete file {{fileName}}! Please contact to the System Administrator!'; break;
            case self::ERROR_MOVING_FILE      : $errorMessage = 'Error: Can not move file: {{fileName}} to {{destFileName}}! Please contact to the System Administrator!'; break;
            case self::ERROR_COPIYNG_FILE     : $errorMessage = 'Error: Can not copy file: {{fileName}} to {{destFileName}}! Please contact to the System Administrator!'; break;
            case self::ERROR_FILE_NOT_EXISTS  : $errorMessage = 'Error: File does not exists {{fileName}}! Please contact to the System Administrator!'; break;
            case self::ERROR_DIRECTORY_NOT_EXISTS       : $errorMessage = 'Error: Directory does not exists {{fileName}}! Please contact to the System Administrator!'; break;
            case self::ERROR_FILE_IS_NOT_DIRECTORY      : $errorMessage = 'Error: Specified file is not a directory - {{fileName}}! Please contact to the System Administrator!'; break;
            case self::ERROR_FILE_IS_NOT_WRITABLE       : $errorMessage = 'Error: Specified file is not writable - {{fileName}}! Please contact to the System Administrator!'; break;
            case self::ERROR_DIRECTORY_IS_NOT_WRITABLE  : $errorMessage = 'Error: Specified directory is not writable - {{fileName}}! Please contact to the System Administrator!'; break;
            default : $errorMessage = 'Error: Unrecognized Filesystem Exception! Please contact to the System Administrator!'; break;
        }

        return $errorMessage;
    }
}
