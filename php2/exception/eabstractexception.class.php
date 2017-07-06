<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Base class for project Exceptions
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
// namespace PHP2\Exception;

/**
 * Base Exceptions Class for All Exceptions in PHP2 based Projects
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: eabstractexception.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\Exception
 * @abstract
 */
abstract class PHP2_Exception_EAbstractException extends Exception
{
    /**
     * Current Error Code
     *
     * @var     integer
     * @access  protected
     */
    protected $_errorCode;

    /**
     * Error Message
     *
     * @var     string
     * @access  protected
     */
    protected $_errorMessage;

    /**
     * Additional exception parameters.
     *
     * @var     array
     * @access  protected
     */
    protected $_exceptionParams = array();

    /**
     * Base Exception constructor
     *
     * @param   integer $errorCode       Error code
     * @param   array   $exceptionParams Exception additional parameters array
     * @param   string  $errorMessage    Error Message
     * @access  public
     */
    public function __construct($errorCode = false, $exceptionParams = false, $errorMessage = false)
    {
        if ($exceptionParams && is_array($exceptionParams)) $this->_exceptionParams = $exceptionParams;

        // --- Initializing Error Code --- //
        if ($errorCode) $this->_errorCode = $errorCode;

        // --- Initializing Error Message Template --- //
        if ($errorMessage)
        {
            $this->_errorMessage = $errorMessage;
        }
        else
        {
            $this->_errorMessage = $this->getDefaultErrorMessageTemplate($this->_errorCode);
        }

        // --- Initializing base Exception --- //
        parent::__construct($this->generateErrorMessage($this->_errorMessage, $this->_exceptionParams), $this->_errorCode);
    }

    /**
     * Return exception additional parameter
     *
     * @param   string $paramName
     * @return  string
     * @access  public
     */
    public function getParameter($paramName)
    {
        if (isset($this->_exceptionParams[$paramName])) return $this->_exceptionParams[$paramName];

        return null;
    }

    /**
     * Generates Error Message
     *
     * @param   string $errorMessage
     * @param   array  $additionalParameters Array of Additional Error parameters
     * @return  string
     * @access  protected
     */
    protected function generateErrorMessage($errorMessage, $additionalParameters = false)
    {
        if (!$additionalParameters)
        {
            return $errorMessage;
        }
        else
        {
            return preg_replace('/{{(\w+)}}/e', '(isset($additionalParameters["\1"]) ? $additionalParameters["\1"] : \'\')', $errorMessage);
        }
    }

    /**
     * Return Default Error Message Template
     *
     * @param   integer $errorCode
     * @return  string
     * @abstract
     */
    abstract public function getDefaultErrorMessageTemplate($errorCode);
}
