<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base class for All XML web Services
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
 * Base class for XML-based Web service Request Handlers
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: xmlrequesthandler.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\WebService
 * @abstract
 */
abstract class PHP2_WebService_XMLRequestHandler extends PHP2_WebService_AbstractRequestHandler
{
    /**
     * Namespaces List for current Response
     *
     * @var     array
     * @access  protected
     */
    protected $_namespacesList = array();

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
        $additionalHeaders[]  = 'Content-Type: text/xml; charset=utf-8';
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
        if (isset($this->_Response['Commands']) && (count($this->_Response['Commands'])))
        {
            $commands           = new PHP2_WebService_VO_XML();
            $commands->Command  = $this->_Response['Commands'];
            $this->_Response['Commands'] = $commands;
        }
        else
        {
            unset($this->_Response['Commands']);
        }

        /**
         * Initializing XML Response
         */
        $xmlResponse = new PHP2_WebService_VO_XML($this->_Response);

        /**
         * Processing Namespaces for Current response
         */
        if (is_array($this->_namespacesList) && count($this->_namespacesList))
        {
            foreach ($this->_namespacesList as $namespaceName => $namespaceUrl)
            {
                $xmlResponse->attributes['xmlns:'.$namespaceName] = $namespaceUrl;
            }
        }

        /**
         * Processing profiler data
         */
        if (PHP2_System_Profiler::getInstance()->getEnabled())
        {
            $xmlResponse->SystemInfo->Profiler = PHP2_System_Profiler::getInstance()->__toXML();
        }

        return $xmlResponse->getXML();
    }

    /**
     * Registers Namespace in Namespaces List
     *
     * @return   boolean
     * @access   protected
     */
    public function registerNamespace($namespaceName, $namespaceUrl)
    {
        $this->_namespacesList[$namespaceName] = $namespaceUrl;
    }

    /**
     * Returns List of registered Handlers
     *
     * @return  PHP2_WebService_VO_XML
     * @access  public
     */
    public function getRegisteredHandlers()
    {
        $result = new PHP2_WebService_VO_XML();

        $result->Handlers->attributes['handlersCount'] = count($this->_requestHandlers);

        foreach ($this->_requestHandlers as $handlerName => $handlerInfo)
        {
            $handlerXMLObject = new PHP2_WebService_VO_XML(null, array('name' => $handlerName));
            $handlerXMLObject->Parameters = new PHP2_WebService_VO_XML();

            if (is_array($handlerInfo['parameters']))
            {
                foreach ($handlerInfo['parameters'] as $parameterInfo)
                {
                    $handlerXMLObject->Parameters->addToCollection('Parameter', new PHP2_WebService_VO_XML(null, $parameterInfo));
                }
            }

            $result->Handlers->addToCollection('Handler', $handlerXMLObject);
        }

        return $result;
    }

}
