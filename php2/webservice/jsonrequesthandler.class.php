<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base class for All JSON-based web Services
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
 * Base class for JSON-based Web service Request Handlers
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: jsonrequesthandler.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\WebService
 * @abstract
 */
abstract class PHP2_WebService_JSONRequestHandler extends PHP2_WebService_AbstractRequestHandler
{
    /**
     * Class constructor
     *
     * @access  public
     */
    public function __construct()
    {
        /**
         * Processing parent constructor
         */
        parent::__construct();
    }

    /**
     * Runs WebService and return output
     *
     * @param   boolean $returnRespose if this flag set to true - dont apply Output encoding to the Response
     * @param   array   $additionalHeaders Additional headers Array
     * @access  public
     * @return  mixed
     */
    public function run($returnRespose = false, $additionalHeaders = array())
    {
        /**
         * Adding Additional XML Headers to the Base run() method
         */
        $additionalHeaders[]  = 'Content-Type: text/x-json; charset=utf-8';
        // $additionalHeaders[]  = 'Content-Type: application/json; charset=utf-8';
        $additionalHeaders[]  = 'Content-Encoding: utf-8';

        return parent::run($returnRespose, $additionalHeaders);
    }

    /**
     * Returns Response Encoded in accordance with XML Standard
     *
     * @return   string
     * @access   protected
     */
    public function getEncodedResponse()
    {
        /**
         * Processing Commads List
         */
        if (isset($this->_Response['Commands']) && (!count($this->_Response['Commands']))) unset($this->_Response['Commands']);

        /**
         * Processing profiler data
         */
        if (PHP2_System_Profiler::getInstance()->getEnabled())
        {
            $this->_Response['SystemInfo']['Profiler'] = PHP2_System_Profiler::getInstance()->__toHTML();
        }

        return $this->_getJSONEncodedResponse();
    }

    /**
     * Return JSON string for PHP Object
     *
     * @param   array  $encodedObject Object Translated to JSON
     * @return  string
     * @access  protected
     */
    protected function _jsonEncode($encodedObject)
    {
        $result = '';

        // --- Finding JSON Translation for The Objects --- //
        if (is_array($encodedObject) || (is_object($encodedObject)))
        {
            $isNumericArray = (is_array($encodedObject) && !PHP2_Utils_Array::isAssociative($encodedObject));
            $result .= $isNumericArray ? '[' : '{';
            $elementsCount = count($encodedObject);
            $i = 0;
            foreach ($encodedObject as $fieldName => &$fieldData)
            {
                // --- If object field is Array or Object - then useing Recursion --- //
                if (is_array($fieldData) || (is_object($fieldData)))
                {
                    $result .= ($isNumericArray ? '' : '"'.$fieldName.'":').$this->_jsonEncode($fieldData).'';
                }
                else
                {
                    $result .= '"'.$fieldName.'":"'.addslashes($fieldData).'"';
                }

                $result .= ',';
                $i++;
            }
            if ($i) $result  = substr($result, 0, strlen($result) - 1);
            $result .= $isNumericArray ? ']' : '}';
        }
        else
        {
            $result .= addslashes($encodedObject);
        }

        return $result;
    }

    /**
     * Converts Data in JSON formatted String to JS eval compatible format
     *
     * @param   string $jsonString
     * @return  string
     * @access  protected
     */
    protected function _prepareJSONToEval($jsonString)
    {
        return trim(str_replace("\n", '\n', str_replace("\r\n", '\r\n', $jsonString)));
    }

    /**
     * Returns Response Encoded in accordance with JSON (rfc4627) Standard.
     *
     * For now used small simple and relatively proper encoder. Will be reworked in upcoming releases.
     *
     * @return   string
     * @access   protected
     * @version  JSON rfc4627
     * @todo     Standard JSON encode method "json_encode" dont works with text contained Not standard symbols. Needs to be reworked.
     */
    protected function _getJSONEncodedResponse()
    {
        if (function_exists('__json_encode'))
        {
            return json_encode($this->_Response);
        }
        else
        {
            return $this->_prepareJSONToEval($this->_jsonEncode($this->_Response));
        }

    }

}
