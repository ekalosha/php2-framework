<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base class for All web Services
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
 * Base class for Web service Request Handlers
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: abstractrequesthandler.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\WebService
 */
abstract class PHP2_WebService_AbstractRequestHandler extends PHP2_Event_EventDispatcher
{
    /**
     * Action constants
     */
    const DEFAULT_ACTION_NAME      = '__default';
    const OLD_REQUEST_ACTION_NAME  = 'action';

    /**
     * Actions array
     *
     * @var     array
     * @access  protected
     */
    protected $_requestHandlers = array();

    /**
     * Current Action
     *
     * @var     string
     * @access  protected
     */
    protected $_action;

    /**
     * Response structure
     *
     * @var     array
     * @access  protected
     */
    protected $_Response;

    /**
     * Action Parameter in Request Array
     *
     * @var     string
     * @access  protected
     */
    protected $_actionParameterName = '__callHandler';

    /**
     * Class constructor.
     * Initializes default parameters for Web Service.
     *
     * @access  public
     */
    public function __construct()
    {
        /**
         * Initializing event dispatcher constructor
         */
        parent::__construct();

        // --- Initializing current Action --- //
        $this->_action = (isset($_REQUEST[$this->_actionParameterName]) ? $_REQUEST[$this->_actionParameterName] : ((isset($_REQUEST[self::OLD_REQUEST_ACTION_NAME])) ? $_REQUEST[self::OLD_REQUEST_ACTION_NAME] : self::DEFAULT_ACTION_NAME));

        // --- Initializing Response Structure --- //
        $this->_Response = array(
                                    'Error'       => array('Code' => 0, 'Message' => null, ),
                                    'Body'        => '',
                                    'SystemInfo'  => '',
                                 );

        $this->addEventListener(PHP2_Event_Event::INIT, 'on_Init');
        $this->addEventListener(PHP2_Event_Event::REQUEST, 'on_Request');
        $this->addEventListener(PHP2_Event_Event::BEFORE_LOAD, 'on_BeforeLoadHandler');

        /**
         * Dispatcing INIT and Request events
         */
        $this->dispatchEvent(new PHP2_Event_Event(PHP2_Event_Event::INIT));
        $this->dispatchEvent(new PHP2_Event_Event(PHP2_Event_Event::REQUEST));
    }

    /**
     * Set action parameter name in Request (_GET or _POST).
     * By default action parameter is set in '__callHandler'.
     *
     * @param   string $actionParameterName
     * @return  boolean
     * @access  public
     */
    public function setActionParameterName($actionParameterName)
    {
        if ($actionParameterName)
        {
            $this->_actionParameterName = $actionParameterName;
            if (isset($_REQUEST[$this->_actionParameterName])) $this->_action = isset($_REQUEST[$this->_actionParameterName]);

            return true;
        }

        return false;
    }

    /**
     * On Init Event Handler.
     *
     * @access  protected
     */
    protected function on_Init()
    {
    }

    /**
     * On Request Event Handler.
     *
     * @access  protected
     */
    protected function on_Request()
    {
    }

    /**
     * Loads Event Handler.
     *
     * @access  protected
     */
    protected function _loadHandler()
    {
        // --- Loading Action if it is exists --- //
        $actionDefined = isset($this->_requestHandlers[$this->_action]);

        /**
         * Checking Handler for current Action
         */
        if (method_exists($this, $this->_action) && (!$actionDefined))
        {
            $methodName = $this->_action;
        }
        elseif ($actionDefined && ($handlerMethodExists = method_exists($this, $this->_requestHandlers[$this->_action]['handler'])))
        {
            $methodName = $this->_requestHandlers[$this->_action]['handler'];
        }
        elseif (!$actionDefined)
        {
            throw new PHP2_WebService_EWebServiceException(PHP2_WebService_EWebServiceException::ERROR_ACTION_NOT_EXISTS, $this->_action);
        }
        else
        {
            throw new PHP2_WebService_EWebServiceException(PHP2_WebService_EWebServiceException::ERROR_HANDLER_NOT_EXISTS, $this->_requestHandlers[$this->_action]['handler']);
        }

        /**
         * Dispatching PHP2_Event_Event::BEFORE_LOAD Event
         */
        $this->dispatchEvent(new PHP2_Event_Event(PHP2_Event_Event::BEFORE_LOAD));

        /**
         * Processing Reflection for Calling method
         */
        $methodReflection    = new ReflectionMethod($this, $methodName);
        $methodParameters    = $methodReflection->getParameters();
        $parameterValuesList = array();
        foreach ($methodParameters as $parameterReflection)
        {
            /* @var $parameterReflection ReflectionParameter */
            $paramName = $parameterReflection->getName();
            if (isset($_REQUEST[$paramName]))
            {
                $parameterValuesList[] = $_REQUEST[$paramName];
            }
            else
            {
                $parameterValuesList[] = null;
            }
        }

        return call_user_func_array(array($this, $methodName), $parameterValuesList);
    }

    /**
     * Add predefined Request Action
     *
     * @param   string $actionName Action name
     * @param   string $actionHandler Action handler name
     * @access  public
     * @deprecated
     */
    public function addAction($actionName, $actionHandler)
    {
        $this->_requestHandlers[$actionName] = array('handler' => $actionHandler);
    }

    /**
     * Register predefined Request handler
     *
     * Handler parameters is array of Objects with fields 'name' and 'type'.
     * For example:
     *
     * <code>
     *     array('name' => 'userName', 'type' => 'String')
     * </code>
     *
     * @param   string $requestHandler
     * @param   array  $handlerParameters
     * @access  public
     */
    public function registerHandler($requestHandler, $handlerParameters = array(), $requestHandlerClientName = false)
    {
        if (!$requestHandlerClientName) $requestHandlerClientName = $requestHandler;

        $this->_requestHandlers[$requestHandlerClientName] = array('handler' => $requestHandler, 'parameters' => $handlerParameters);
    }

    /**
     * Returns List of registered Handlers
     *
     * @return  array
     * @access  public
     */
    public function getRegisteredHandlers()
    {
        return $this->_requestHandlers;
    }

    /**
     * Set server Command
     *
     * @param   PHP2_WebService_AbstractServerCommand $commandDetails
     * @return  void
     * @access  public
     */
    public function setCommand(PHP2_WebService_AbstractServerCommand $commandDetails)
    {
        if (!isset($this->_Response['Commands'])) $this->_Response['Commands'] = array();

        $this->_Response['Commands'][] = $commandDetails;
    }

    /**
     * Returns requested Action
     *
     * @return  string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Checks requested Action
     *
     * @param   string $actionName Action name
     * @return  boolean
     */
    public function checkAction($actionName)
    {
        return ($this->_action == $actionName);
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
        try
        {
            ob_start();

            // --- Trying to Load Response --- //
            $timeStart = microtime(true);
            /**
             * Check getRegisteredHandlers action
             */
            if ($this->_action == 'getRegisteredHandlers')
            {
                $this->_Response['Body'] = $this->getRegisteredHandlers();
            }
            else
            {
                $this->_Response['Body']  = $this->_loadHandler();
            }
            $bufferContent = ob_get_contents();
            ob_end_clean();

            $this->_Response['SystemInfo']['GenerateTime']  = (microtime(true) - $timeStart);
            $this->_Response['SystemInfo']['BufferContent'] = $bufferContent;
        }
        catch (PHP2_WebService_EWebServiceException $EWebServiceException)
        {
            switch ($errorCode = $EWebServiceException->getCode())
            {
                case PHP2_WebService_EWebServiceException::EXCEPTION_FLUSH_RESPONSE:
                    $bufferContent = ob_get_contents();
                    ob_end_clean();
                    $this->_Response['SystemInfo']['GenerateTime']  = (microtime(true) - $timeStart);
                    $this->_Response['SystemInfo']['BufferContent'] = $bufferContent;
                break;

                default:
                    // --- Setting Error Code and Message --- //
                    $this->_Response['Error']['Code']     = $EWebServiceException->getCode();
                    $this->_Response['Error']['Message']  = $EWebServiceException->getMessage();
                break;
            }
        }
        catch (Exception $defaultException)
        {
            // --- Setting Default Error Code and Message. I think, this should be overriden. --- //
            $this->_Response['Error']['Code']     = $defaultException->getCode();
            $this->_Response['Error']['Message']  = $defaultException->getMessage();
        }

        // --- Processing Additional Headers --- //
        if ($additionalHeaders && is_array($additionalHeaders) && count($additionalHeaders))
        {
            foreach ($additionalHeaders as $header) header($header);
        }

        return (($returnRespose) ? $this->_Response : $this->getEncodedResponse());
    }

    /**
     * Returns Response Encoded in accordance with current Web Service Standard
     *
     * @return   string
     * @access   public
     */
    abstract public function getEncodedResponse();

}
