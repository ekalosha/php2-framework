<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Base class for all UI controls
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
 * Base class for all UI controls
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: controlcontainer.class.php 98 2009-08-19 17:15:05Z eugene $
 * @access   public
 * @package  PHP2\UI
 */
abstract class PHP2_UI_ControlContainer extends PHP2_UI_Control
{
    /**
     * Container types
     */
    const CONTAINER_TYPE_PUBLIC    = 'publicContainer';
    const CONTAINER_TYPE_ISOLATED  = 'isolatedController';

    /**
     * Container type
     *
     * @var     string
     * @access  protected
     */
    protected $_containerType;

    /**
     * Template filename for the container
     *
     * @var     string
     * @access  protected
     */
    protected $_template;

    /**
     * Display objects container
     *
     * @var     PHP2_UI_DisplayObjectContainer
     * @access  protected
     */
    protected $_displayObjectContainer;

    /**
     * Template variables object
     *
     * @var     PHP2_UI_TemplateVariables
     * @access  public
     */
    public $templateVariables;

    /**
     * PHP2_UI_ControlContainer class constructor
     *
     * @param   PHP2_UI_ControlDefinition $controlDefinition control definition Object
     * @access  public
     */
    public function __construct($controlDefinition = null)
    {
        /**
         * Calling parent constructor
         */
        parent::__construct($controlDefinition);

        /**
         * Initializing container
         */
        $this->_initContainer();
    }

    /**
     * Initialize control
     *
     * @return  void
     * @access  protected
     */
    protected function _init()
    {
        parent::_init();

        /**
         * Extracting container type from template
         */
        if ($isolated = $this->_extractAttribute('isolated', 'bool', false))
        {
            $this->_containerType = self::CONTAINER_TYPE_ISOLATED;
        }

        /**
         * Extracting template file name from attributes
         */
        if ($template = $this->_extractAttribute('template'))
        {
            $this->_template = $template;
        }

        /**
         * Set event handlers
         */
        $this->addEventListener(PHP2_UI_UIEvent::LOAD_SESSION, '_loadFromSession', $this);
        $this->addEventListener(PHP2_UI_UIEvent::SAVE_SESSION, '_saveToSession', $this);
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
         * Initializing display object
         */
        $this->_displayObjectContainer             = new PHP2_UI_DisplayObjectContainer();
        $this->_displayObjectContainer->container  = &$this;

        /**
         * Initializing container owner
         */
        if ($this->_containerType && ($this->_containerType == self::CONTAINER_TYPE_ISOLATED))
        {
            $this->_displayObjectContainer->owner = &$this;
        }
        else
        {
            $this->_displayObjectContainer->owner = &$this->owner;
        }

        /**
         * Loading content to the display objects container
         */
        if ($this->_template)
        {
            $this->_displayObjectContainer->loadTemplate(BASE_PATH.'ui/'.$this->_template);
        }
        else
        {
            $this->_displayObjectContainer->setTemplateString($this->_controlDefinition->content);
        }

        $this->_displayObjectContainer->sessionData['__CONTROLS'] = &$this->sessionData['__CONTROLS'];
        $this->_displayObjectContainer->parseTemplate();

        /**
         * Adding link to the template variables object
         */
        $this->templateVariables = &$this->_displayObjectContainer->templateVariables;

        /**
         * Dispatching creation complete event
         */
        $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::CREATION_COMPLETE));
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        return $this->_displayObjectContainer->render();
    }

    /**
     * Handler of the load session event
     *
     * @return  string
     * @access  protected
     */
    protected function _loadFromSession()
    {
        if (isset($this->sessionData['template'])) $this->_template = $this->sessionData['template'];
    }

    /**
     * Handler of the save to session event
     *
     * @return  string
     * @access  protected
     */
    protected function _saveToSession()
    {
        if ($this->_template)
        {
            $this->sessionData['template'] = $this->_template;
        }
        elseif (isset($this->sessionData['template']))
        {
            unset($this->sessionData['template']);
        }
    }

    /**
     * Save session data
     *
     * @return  string
     * @access  protected
     */
    protected function _saveSessionHandler()
    {
        $this->sessionData['__CONTROLS'] = &$this->_displayObjectContainer->sessionData['__CONTROLS'];

        /**
         * Inheriting parent session handler
         */
        parent::_saveSessionHandler();
    }

}
