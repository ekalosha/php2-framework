<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Exceptions Class for Security subsystem
 *
 * PHP version 5
 * @category   Exception Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2009 by SolArt xIT
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
// namespace Application\Exception;

/**
 * Class processes Security Subsystem Exceptions
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: esecurityexception.class.php 99 2009-10-20 14:44:49Z eugene $
 * @access   public
 * @package  Application\Exception
 */
class Application_Exception_ESecurityException extends PHP2_Exception_EAbstractException
{
    /**
     * Error Code Constants
     */
    const ERROR_USER_NOT_AUTHORIZED             = 2101;
    const ERROR_LOGIN_INVALID                   = 2105;
    const ERROR_NOT_ROOT_USER                   = 2110;
    const ERROR_NOT_ENOUGH_PERMISSIONS          = 2111;

    /**
     * Security Exception constructor
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
            case self::ERROR_USER_NOT_AUTHORIZED : $errorMessage = 'You are not authorized user!'; break;
            case self::ERROR_LOGIN_INVALID       : $errorMessage = 'Invalid login or password!'; break;
            case self::ERROR_NOT_ROOT_USER       : $errorMessage = 'Only root user can use this resource!'; break;
            case self::ERROR_NOT_ENOUGH_PERMISSIONS : $errorMessage = 'You don\'t have enough permissions to access to this resource!'; break;

            default : $errorMessage = 'Error: Unrecognized Security Exception! Please contact to the System Administrator!'; break;
        }

        return $errorMessage;
    }
}
