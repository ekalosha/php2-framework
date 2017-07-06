<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Base class for all UI applications
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 101 $
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
 * Base class for all UI applications
 *
 * This class dispatches the following events:
 * <code>
 *   PHP2_UI_UIEvent::INIT
 *   PHP2_UI_UIEvent::INIT_LISTENERS
 *   PHP2_UI_UIEvent::CREATION_COMPLETE
 *   PHP2_UI_UIEvent::BEFORE_RENDER
 *   PHP2_UI_UIEvent::AFTER_RENDER
 *   PHP2_UI_UIEvent::LOAD_SESSION
 *   PHP2_UI_UIEvent::SAVE_SESSION
 * </code>
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: displayobjectcontainer.class.php 101 2009-11-12 14:43:02Z eugene $
 * @access   public
 * @package  PHP2\UI
 */
class PHP2_UI_DisplayObjectContainer extends PHP2_UI_DisplayObject
{
    /**
     * Template filename
     *
     * @var     string
     * @access  protected
     */
    protected $_template;

    /**
     * Template string, usually content of the template filename
     *
     * @var     string
     * @access  protected
     */
    protected $_templateString;

    /**
     * Pre parsed template string
     *
     * @var     string
     * @access  protected
     */
    protected $_templateStringParsed;

    /**
     * Template parser object
     *
     * @var     PHP2_UI_TemplateParser
     * @access  protected
     */
    protected $_templateParser;

    /**
     * Template variables object
     *
     * @var     PHP2_UI_TemplateVariables
     * @access  public
     */
    public $templateVariables;

    /**
     * UI application controls definitions
     *
     * @var     array
     * @access  protected
     */
    protected $_controlsDefinition;

    /**
     * PHP2_UI_DisplayObjectContainer class constructor
     *
     * @access  public
     */
    public function __construct()
    {
        /**
         * Initializing template variables
         */
        $this->templateVariables = new PHP2_UI_TemplateVariables();

        /**
         * Calling parent constructor
         */
        parent::__construct();
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
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        $replacedVariables = $this->getTemplateVariables();
        foreach ($this->_controlsDefinition as &$controlDefinition)
        {
            /* @var $controlDefinition PHP2_UI_ControlDefinition */
            $replacedVariables[$controlDefinition->getControlTemplateUID()] = $this->{$controlDefinition->name}->render();
        }

        return preg_replace('/{{(\w+)}}/e', '(isset($replacedVariables["\1"]) ? $replacedVariables["\1"] : \'\')', $this->_templateParser->getParsedTemplate());
    }

    /**
     * Set template filename for UI application
     *
     * @param   string $fileName Template filename
     * @return  void
     * @access  public
     */
    public function getTemplateVariables()
    {
        $result = $this->templateVariables->getTemplateVariables();

        if ($this->owner) $result = $result + $this->owner->templateVariables->getTemplateVariables();
        if ($this->container && ($this->container != $this->owner)) $result = $result + $this->container->templateVariables->getTemplateVariables();

        return $result;
    }

    /**
     * Set template filename for UI application
     *
     * @param   string $fileName Template filename
     * @return  void
     * @access  public
     */
    public function setTemplate($fileName)
    {
        if ($fileName) $this->_template = $fileName;
    }

    /**
     * Set template string for UI application
     *
     * @param   string $templateString reference to the template string
     * @return  void
     * @access  public
     */
    public function setTemplateString(&$templateString)
    {
        $this->_templateString = $templateString;
    }

    /**
     * Loads template for UI application
     *
     * @param   string $fileName Template filename
     * @return  void
     * @access  public
     */
    public function loadTemplate($fileName = '')
    {
        $this->setTemplate($fileName);

        $templateString = file_get_contents($this->_template);
        $this->setTemplateString($templateString);
    }

    /**
     * Parses application template
     *
     * @return  void
     * @access  public
     */
    public function parseTemplate()
    {
        /**
         * Parsing template
         */
        $this->_templateParser = new PHP2_UI_TemplateParser($this->_templateString);
        $this->_templateParser->parse();

        $this->_templateStringParsed  = $this->_templateParser->getParsedTemplate();
        $this->_controlsDefinition    = $this->_templateParser->getControlsDefinition();
        foreach ($this->_controlsDefinition as &$controlDefinition)
        {
            /* @var $controlDefinition PHP2_UI_ControlDefinition */
            if ($this->owner)
            {
                $controlDefinition->owner =  &$this->owner;
            }
            else
            {
                $controlDefinition->owner =  &$this;
            }

            $controlDefinition->container = &$this;

            /**
             * Adding control to the current container
             */
            $this->addControl($controlDefinition);
        }

        /**
         * Dispatching init Listeners event
         */
        $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::INIT_LISTENERS));

        /**
         * Dispatching creation complete event
         */
        $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::CREATION_COMPLETE));
    }

    /**
     * Add control to current container
     *
     * @param   PHP2_UI_ControlDefinition $controlDefinition
     * @return  void
     * @access  public
     */
    public function addControl($controlDefinition)
    {
        $controlClass  = $controlDefinition->getComponentClass();
        $controlObject = new $controlClass($controlDefinition);
        $this->{$controlDefinition->name} = &$controlObject;

        /**
         * Adding control to the parent containers
         */
        if ($this->owner) $this->owner->addChild($controlDefinition->name, $controlObject);
        if ($this->container && ($this->container != $this->owner)) $this->container->addChild($controlDefinition->name, $controlObject);
    }

    /**
     * Dispatches event for session save action
     *
     * @return  string
     * @access  public
     */
    public function dispatchSaveSessionEvent()
    {
        /**
         * Dispatching save session event for all controls
         */
        if (is_array($this->_controlsDefinition) || is_object($this->_controlsDefinition))
        {
            foreach ($this->_controlsDefinition as $controlDefinition) $this->{$controlDefinition->name}->dispatchSaveSessionEvent();
        }

        parent::dispatchSaveSessionEvent();
    }

}
