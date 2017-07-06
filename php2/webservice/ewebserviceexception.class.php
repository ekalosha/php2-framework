<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains core Web Sevice Exception classes
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
// namespace PHP2\WebService;

/**
 * Class implements Core Web Sevice Exception Classes.
 * Locked Exception Ranges [1121, 1140].
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: ewebserviceexception.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\WebService
 */
class PHP2_WebService_EWebServiceException extends PHP2_Exception_EAbstractException
{
    /**
     * Error Code Constants
     */
    const ERROR_ACTION_NOT_EXISTS           = 1121;
    const ERROR_HANDLER_NOT_EXISTS          = 1122;
    const ERROR_INVALID_REQUEST_PARAMETERS  = 1123;

    /**
     * System exception to manage server Responces
     */
    const EXCEPTION_FLUSH_RESPONSE           = 1135;

    /**
     * Exception constructor
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
            case self::ERROR_ACTION_NOT_EXISTS           : $errorMessage = 'Selected Action Does Not Exists'; break;
            case self::ERROR_HANDLER_NOT_EXISTS          : $errorMessage = 'Handler Does Not Exists'; break;
            case self::ERROR_INVALID_REQUEST_PARAMETERS  : $errorMessage = 'Web Service Respond a Bad Request Parameters Code! Please Contact to your Sysstem Administrator!'; break;
            case self::EXCEPTION_FLUSH_RESPONSE          : $errorMessage = ''; break;
            default : $errorMessage = 'Error: Unrecognized Web Service Exception! Please contact to the System Administrator!'; break;
        }

        return $errorMessage;
    }
}
