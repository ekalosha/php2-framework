<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Base class for all UI classes
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 98 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 2.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace PHP2\UI;

/**
 * Base class for all UI classes
 *
 * This class dispatches the following events:
 * <code>
 *   PHP2_UI_UIEvent::INIT
 *   PHP2_UI_UIEvent::BEFORE_RENDER
 *   PHP2_UI_UIEvent::AFTER_RENDER
 *   PHP2_UI_UIEvent::LOAD_SESSION
 *   PHP2_UI_UIEvent::SAVE_SESSION
 * </code>
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: displayobject.class.php 98 2009-08-19 17:15:05Z eugene $
 * @access   public
 * @package  PHP2\UI
 */
abstract class PHP2_UI_DisplayObject extends PHP2_Event_EventDispatcher implements PHP2_UI_IDisplayObject
{
    /**
     * Unique control name
     *
     * @var     string
     * @access  protected
     */
    protected $_name;

    /**
     * Unique control ID
     *
     * @var     string
     * @access  protected
     */
    protected $_id;

    /**
     * Reference to the owner object. This property is read-only.
     *
     *
     * @var     PHP2_UI_DisplayObjectContainer
     * @access  public
     */
    public $owner;

    /**
     * Reference to the container object. This property is read-only.
     *
     * @var     PHP2_UI_DisplayObjectContainer
     * @access  public
     */
    public $container;

    /**
     * Control visibility flag
     *
     * @var     boolean
     * @access  public
     */
    public $visible = true;

    /**
     * Control disabled flag
     *
     * @var     boolean
     * @access  public
     */
    public $disabled = false;

    /**
     * Session data for control
     *
     * @var     array
     * @access  protected
     */
    protected $_sessionData;

    /**
     * Session data for control
     *
     * @var     array
     * @access  public
     */
    public $sessionData = array();

    /**
     * Request event Object
     *
     * @var     PHP2_Event_Event
     * @access  public
     */
    protected $_requestEvent;

    /**
     * Class constructor
     *
     * @param   string $name
     * @access  public
     */
    public function __construct($name = null)
    {
        // $this->sessionData = &$this->_sessionData;

        /**
         * Calling parent constructor
         */
        parent::__construct();

        /**
         * Initializing component definition
         */
        if ($name) $this->_name = $name;

        /**
         * Initializing control data
         */
        $this->_init();
        $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::INIT));
        $this->_checkEventFromRequest();

        /**
         * Loading basic session
         */
        $this->_loadSessionHandler();
    }

    /**
     * Class destructor
     *
     * @access  public
     */
    public function __destruct()
    {
        /**
         * Saving basic Session
         */
        $this->_saveSessionHandler();
    }

    /**
     * Returns name of current control
     *
     * @return  string
     * @access  public
     */
    public function getName()
    {
        if ($this->_id) return $this->_id;

        $result = $this->_name;

        if (isset($this->owner->owner) && $this->owner->owner) $result = $this->owner->getName().'_'.$result;

        return $this->_id = $result;
    }

    /**
     * Initialize display object
     *
     * @return  void
     * @access  protected
     */
    protected function _init()
    {
    }

    /**
     * Checks is event in request exists and dispatch this event
     *
     * @return  string
     */
    protected function _checkEventFromRequest()
    {
        $objectName = $this->getName();

        if (isset($_REQUEST[$objectName.'_dispatchEvent']))
        {
            $eventDetails = unserialize(base64_decode($_REQUEST[$objectName.'_dispatchEvent']));

            if (isset($eventDetails['type']) && isset($eventDetails['data']))
            {
                $this->_requestEvent = new PHP2_Event_Event($eventDetails['type'], $eventDetails['data']);
                $this->owner->addEventListener(PHP2_UI_UIEvent::CREATION_COMPLETE, '_initEventFromRequestHandler', $this);
            }
        }
    }

    /**
     * Dispatching request event
     *
     * @return  string
     */
    protected function _initEventFromRequestHandler()
    {
        $this->dispatchEvent($this->_requestEvent);
    }

    /**
     * Returns Url to dispatch the event
     *
     * @param   string $eventType
     * @param   mixed  $eventData
     * @param   string $urlType
     * @param   string $formId
     * @return  string
     */
    public function getEventUrl($eventType, $eventData, $urlType = 'get', $formId = 0)
    {
        return self::getObjectEventUrl($this->getName(), $eventType, $eventData, $urlType, $formId);
    }

    /**
     * Returns Url to dispatch the event
     *
     * @param   string $objectName
     * @param   string $eventType
     * @param   mixed  $eventData
     * @param   string $urlType
     * @param   string $formId
     * @return  string
     */
    public static function getObjectEventUrl($objectName, $eventType, $eventData, $urlType = 'get', $formId = 0)
    {
        $eventDetails          = array();
        $eventDetails['type']  = $eventType;
        $eventDetails['data']  = $eventData;

        $eventDetailsString    = base64_encode(serialize($eventDetails));

        if (strtolower($urlType) == 'get')
        {
            return PHP2_System_Response::getInstance()->getUrl('', array($objectName.'_dispatchEvent' => $eventDetailsString));
        }
        else
        {
            return 'PHP2.Core.dispatchServerEvent(\''.$objectName.'\', \''.$eventDetailsString.'\', \''.$formId.'\');';
        }
    }

    /**
     * Add reference of the existed control to current container
     *
     * @param   string $controlName
     * @param   PHP2_UI_DisplayObject $controlObject
     * @return  void
     * @access  public
     */
    public function addChild($controlName, &$controlObject)
    {
        $this->{$controlName} = $controlObject;
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
    }

    /**
     * Renders control
     *
     * @return  string
     * @access  public
     */
    public function render()
    {
        $result = '';

        $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::BEFORE_RENDER));
        if ($this->visible)
        {
            $result = $this->_getRenderedContent();
        }
        $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::AFTER_RENDER));

        return $result;
    }

    /**
     * Loads session data
     *
     * @return  string
     * @access  protected
     */
    protected function _loadSessionHandler()
    {
        $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::LOAD_SESSION));
    }

    /**
     * Save session data
     *
     * @return  string
     * @access  protected
     */
    protected function _saveSessionHandler()
    {
        $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::SAVE_SESSION));
    }

    /**
     * Dispatches event for session save action
     *
     * @return  string
     * @access  public
     */
    public function dispatchSaveSessionEvent()
    {
        $this->_saveSessionHandler();
    }

}
