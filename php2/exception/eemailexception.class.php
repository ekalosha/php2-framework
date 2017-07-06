<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Exceptions Class for Email Operations
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
 * Class processes Email Operations Exceptions
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: eemailexception.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\Exception
 */
class PHP2_Exception_EEmailException extends PHP2_Exception_EAbstractException
{
    /**
     * Error Code Constants
     */
    const ERROR_CANNOT_SEND_EMAIL  = 401;

    /**
     * PHP2_Exception_EEmailException constructor
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
            case self::ERROR_CANNOT_SEND_EMAIL : $errorMessage  = 'Cannot Send Email! Please contact to the System Administrator!'; break;

            default : $errorMessage = 'Error: Unrecognized Email Exception! Please contact to the System Administrator!'; break;
        }

        return $errorMessage;
    }
}
