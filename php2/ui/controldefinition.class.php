<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains UI control definition class
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 97 $
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
 * Requiring control aliases config
 *
 * @todo  Create singleton object to find component Class name
 */
require_once '__controlalias.inc.php';

/**
 * UI control definition class
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: controldefinition.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\UI
 */
class PHP2_UI_ControlDefinition
{
    /**
     * Unique control name
     *
     * @var     string
     * @access  public
     */
    public $name;

    /**
     * Control component name
     *
     * @var     string
     * @access  protected
     */
    protected $_componentName;

    /**
     * Control component class name
     *
     * @var     string
     * @access  protected
     */
    protected $_componentClass;

    /**
     * Attributes string
     *
     * @var     string
     * @access  public
     */
    public $attributesString;

    /**
     * Attributes list
     *
     * @var     array
     * @access  public
     */
    public $attributes;

    /**
     * Content of the control as string
     *
     * @var     string
     * @access  public
     */
    public $content;

    /**
     * Reference to the owner object
     *
     * @var     PHP2_UI_Control
     * @access  public
     */
    public $owner;

    /**
     * Reference to the container object
     *
     * @var     PHP2_UI_Control
     * @access  public
     */
    public $container;

    /**
     * PHP2_UI_ControlDefinition class constructor
     *
     * @param   string $name Unique control name
     * @param   string $componentName
     * @param   string $attributesString
     * @param   string $content Control content as template string
     * @param   PHP2_UI_Control $owner Reference to the control's owner
     * @param   PHP2_UI_Control $container Reference to the control's container
     * @access  public
     */
    public function __construct($name, $componentName, $attributesString = '', $content = '', &$owner = null, &$container = null)
    {
        /**
         * Initializing component definition
         */
        $this->name       = $name;
        $this->content    = $content;
        $this->owner      = &$owner;
        $this->container  = &$container;

        $this->_setComponentName($componentName);
        $this->setAttributes($attributesString);
    }

    /**
     * Set component name and component class name
     *
     * @param   string $componentName
     * @return  void
     * @access  protected
     */
    protected function _setComponentName($componentName)
    {
        $this->_componentName   = $componentName;

        /**
         * Trying to find current component class name. Workflow is:
         *
         * 1. Checking is component name exists in the Global components locator.
         * 2. Checking in component name in lowercase exists in the Global components locator.
         * 3. Set component class name as component name.
         */
        $componentLowerName = strtolower($componentName);
        if (isset($GLOBALS['__CONTROLS_ALIAS'][$componentName]))
        {
            $this->_componentClass = $GLOBALS['__CONTROLS_ALIAS'][$componentName];
        }
        elseif (isset($GLOBALS['__CONTROLS_ALIAS'][$componentLowerName]))
        {
            $this->_componentClass = $GLOBALS['__CONTROLS_ALIAS'][$componentLowerName];
        }
        else
        {
            $this->_componentClass = $componentName;
        }
    }

    /**
     * Returns class name of the current component
     *
     * @return  string
     * @access  public
     */
    public function getComponentClass()
    {
        return $this->_componentClass;
    }

    /**
     * Returns name of the current component
     *
     * @return  string
     * @access  public
     */
    public function getComponentName()
    {
        return $this->_componentName;
    }

    /**
     * Returns template UID for current control
     *
     * @return  string
     * @access  public
     */
    public function getControlTemplateUID()
    {
        return $this->name.'_TPL_UID';
    }

    /**
     * Set attributes
     *
     * @param   string $attributesString
     * @return  void
     * @access  public
     */
    public function setAttributes($attributesString)
    {
        $this->attributesString  = $attributesString;
        $this->attributes        = self::parseAttributesString($attributesString);
    }

    /**
     * Returns attributes array from Attributes string
     *
     * @param   string $attributes Control attributes string
     * @return  array
     * @access  public
     * @static
     */
    public static function parseAttributesString($attributesString)
    {
        $result = array();

        /**
         * Matching attributes
         */
        preg_match_all("/([\s]*([\w]+)[\s]*=[\s]*(\'([^\']*)\'|\"([^\"]*)\"))/x", $attributesString, $matches, PREG_PATTERN_ORDER);
        foreach ($matches[2] as $index => $attributeName)
        {
            $result[$attributeName] = (($matches[4][$index] != '') ? $matches[4][$index] : $matches[5][$index]);
        }

        return $result;
    }

}
