<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains ViewStack component
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 114 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 2.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace PHP2\UI\Components\Additional;

/**
 * ViewStack component
 *
 * Dispatches the following events:
 *
 * <code>
 *     PHP2_UI_UIEvent::INIT_STATE
 *     PHP2_UI_UIEvent::CHANGE
 *     PHP2_UI_UIEvent::CREATION_COMPLETE
 * </code>
 *
 * Usage in the template:
 *
 * <code>
 *      <php:viewstate:$objectName
 *          [isolated="true|false"]
 *          [visible="true|false"]
 *      />
 *          <state:$stateName>
 *              {Controls}
 *          </state:$stateName>
 *      </php:viewstate:$objectName>
 * </code>
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: viewstack.class.php 114 2010-05-21 15:32:29Z eugene $
 * @access   public
 * @package  PHP2\UI\Components\Additional
 */
class PHP2_UI_Components_Additional_ViewStack extends PHP2_UI_ControlContainer implements PHP2_UI_Components_IViewStack
{
    /**
     * List of the View states definitions
     *
     * @var   array
     */
    protected $_viewStatesDefinitions = array();

    /**
     * List of the View states
     *
     * @var   array
     */
    protected $_viewStates = array();

    /**
     * Current active state object
     *
     * @var   PHP2_UI_DisplayObjectContainer
     */
    protected $_currentStateObject;

    /**
     * Current active state name
     *
     * @var   string
     */
    protected $_currentState;

    /**
     * Default state name
     *
     * @var   string
     */
    protected $_defaultState;

    /**
     * Class constructor
     *
     * @param   PHP2_UI_ControlDefinition $controlDefinition control definition Object
     * @access  public
     */
    public function __construct($controlDefinition = null)
    {
        /**
         * Initializing template variables
         */
        $this->templateVariables = new PHP2_UI_TemplateVariables();

        /**
         * Calling parent constructor
         */
        parent::__construct($controlDefinition);

        /**
         * Initializing default state from attributes
         */
        if (($defaultState = $this->_extractAttribute('default')) && isset($this->_viewStates[$defaultState])) $this->_defaultState = $defaultState;

        /**
         * Initializing state from the session
         */
        if (isset($this->sessionData['_currentState'])) $this->setState($this->sessionData['_currentState']);

        /**
         * Initializing state by Default.
         *
         * This action can not be done
         */
        if (!$this->_currentState && $this->_defaultState) $this->setState($this->_defaultState);
    }

    /**
     * Initializes display objects container
     *
     * @return  void
     * @access  protected
     */
    protected function _initContainer()
    {
        /**
         * Initializing View state Data
         */
        $this->_viewStatesDefinitions = PHP2_UI_TemplateParser::parseBlockTemplate($this->_controlDefinition->content, 'state');

        if (!isset( $this->_viewStatesDefinitions['blocks'])) return;

        foreach ($this->_viewStatesDefinitions['blocks'] as $stateIndex => &$stateDetails)
        {
            $stateDetails['attributes'] = PHP2_UI_ControlDefinition::parseAttributesString($stateDetails['attributesString']);

            /**
             * Initializing display object
             */
            $displayObjectContainer             = new PHP2_UI_DisplayObjectContainer();
            $displayObjectContainer->container  = &$this;

            /**
             * Initializing container owner
             */
            if ($this->_containerType && ($this->_containerType == self::CONTAINER_TYPE_ISOLATED))
            {
                $displayObjectContainer->owner = &$this;
            }
            else
            {
                $displayObjectContainer->owner = &$this->owner;
            }

            /**
             * Loading content to the display objects container
             */
            if (isset($stateDetails['attributes']['template']))
            {
                $displayObjectContainer->loadTemplate(BASE_PATH.'ui/'.$stateDetails['attributes']['template']);
            }
            else
            {
                $displayObjectContainer->setTemplateString($stateDetails['content']);
            }

            $displayObjectContainer->sessionData['__CONTROLS'] = &$this->sessionData['__CONTROLS'][$stateDetails['blockName']];
            $displayObjectContainer->parseTemplate();

            /**
             * Creating link to the current state
             */
            $this->_viewStates[$stateDetails['blockName']]  = $displayObjectContainer;
            $this->{$stateDetails['blockName']}             = $displayObjectContainer;

            /**
             * Initializing default state
             */
            if (isset($stateDetails['attributes']['default']) && (strtolower($stateDetails['attributes']['default']) == 'true')) $this->_defaultState = $stateDetails['blockName'];
            if (!$this->_defaultState) $this->_defaultState = $stateDetails['blockName'];
        }

        /**
         * Dispatching creation complete event
         */
        $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::CREATION_COMPLETE));
    }

    /**
     * Sets active state
     *
     * @param   string $stateName
     * @return  boolean
     */
    public function setState($stateName)
    {
        $oldState = $this->_currentState;

        if (isset($this->_viewStates[$stateName]) && ($oldState != $stateName))
        {
            $this->_currentState        = $stateName;
            $this->_currentStateObject  = $this->_viewStates[$stateName];

            /**
             * Dispatching State Change event
             */
            if ($oldState)
            {
                $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::CHANGE, array('previousState' => $oldState, 'currentState' => $stateName)));
            }

            $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::INIT_STATE, array('state' => $stateName)));

            return true;
        }

        return false;
    }

    /**
     * Returns current state name
     *
     * @return  string
     */
    public function getState()
    {
        return $this->_currentState;
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        $result = '';

        if ($this->_currentStateObject && ($this->_currentStateObject instanceof PHP2_UI_DisplayObjectContainer)) $result = $this->_currentStateObject->render();

        return $result;
    }

    /**
     * Save session data
     *
     * @return  string
     * @access  protected
     */
    protected function _saveSessionHandler()
    {
        foreach ($this->_viewStatesDefinitions['blocks'] as $stateIndex => &$stateDetails)
        {
            $this->sessionData['__CONTROLS'][$stateDetails['blockName']] = &$this->_viewStates[$stateDetails['blockName']]->sessionData['__CONTROLS'];
        }

        /**
         * Inheriting parent session handler
         */
        PHP2_UI_Control::_saveSessionHandler();
    }


    /**
     * Handler of the load session event
     *
     * @return  string
     * @access  protected
     */
    protected function _loadFromSession()
    {
        // if (isset($this->sessionData['_currentState'])) $this->setState($this->sessionData['_currentState']);
    }

    /**
     * Handler of the save to session event
     *
     * @return  string
     * @access  protected
     */
    protected function _saveToSession()
    {
        $this->sessionData['_currentState'] = $this->_currentState;
    }
}
