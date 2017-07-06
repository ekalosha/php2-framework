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
 * Base class for all UI controls
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: control.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\UI
 */
abstract class PHP2_UI_Control extends PHP2_UI_DisplayObject
{
    /**
     * Control definition object
     *
     * @var     PHP2_UI_ControlDefinition
     * @access  protected
     */
    protected $_controlDefinition;

    /**
     * Attributes list
     *
     * @var     array
     * @access  protected
     */
    protected $_attributes;

    /**
     * PHP2_UI_Control class constructor
     *
     * @param   PHP2_UI_ControlDefinition $controlDefinition control definition Object
     * @access  public
     */
    public function __construct($controlDefinition = null)
    {
        /**
         * Initializing component definition
         */
        if ($controlDefinition)
        {
            $this->_controlDefinition  = $controlDefinition;
            $this->_name               = $this->_controlDefinition->name;
            $this->_attributes         = $this->_controlDefinition->attributes;
            $this->owner               = &$this->_controlDefinition->owner;
            $this->container           = &$this->_controlDefinition->container;
        }

        if ($this->owner) $this->_defaultEventHandler = &$this->owner;

        /**
         * Calling parent constructor
         */
        parent::__construct();
    }

    /**
     * Returns control definition
     *
     * @return  PHP2_UI_ControlDefinition
     * @access  protected
     */
    public function &getControlDefinition()
    {
        return $this->_controlDefinition;
    }

    /**
     * Extracts control attribute from attributes array.
     *
     * @param   string $attributeName Attribute name
     * @param   string $type Variable type
     * @param   string $default Default value for attribute
     * @return  string
     * @access  protected
     */
    protected function _extractAttribute($attributeName, $type = null, $default = null)
    {
        /**
         * Extracting attribute value
         */
        $attributeValue = $default;
        if (isset($this->_attributes[$attributeName]))
        {
            $attributeValue = $this->_attributes[$attributeName];
            unset($this->_attributes[$attributeName]);
        }
        elseif ($default === null)
        {
            return null;
        }

        if (!$type) return $attributeValue;

        /**
         * Checking type of the result value
         */
        switch (strtolower($type))
        {
            case 'bool':
            case 'boolean':
                if (is_bool($attributeValue)) return $attributeValue;

                return (((strtolower($attributeValue) == 'false') || (strtolower($attributeValue) === '0')) ? false : true);
            break;

            case 'int':
            case 'integer':
                return intval($attributeValue);
            break;

            default:
                return (string) $attributeValue;
            break;
        }

    }

    /**
     * Initialize control
     *
     * @return  void
     * @access  protected
     */
    protected function _init()
    {
        /**
         * Initializing parent
         */
        parent::_init();

        /**
         * Extracting default control attributes
         */
        if (($visible = $this->_extractAttribute('visible', 'bool')) !== null) $this->visible = $visible;
        if (($disabled = $this->_extractAttribute('disabled', 'bool')) !== null) $this->disabled = $disabled;
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        return '!!!'.$this->getName().'!!!';
    }

    /**
     * Returns attributes as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getAttributesString()
    {
        $result = '';
        if (!isset($this->_attributes['id'])) $result .= ' id="'.$this->getName().'"';
        foreach ($this->_attributes as $attributeName => $attributeValue)
        {
            $result .= ' '.$attributeName.'="'.$attributeValue.'"';
        }

        return $result;
    }

    /**
     * Returns attributes as string
     *
     * @param   array $attributesList
     * @return  string
     * @access  protected
     */
    protected function _buildAttributesString($attributesList = array())
    {
        if ((!is_array($attributesList) && !is_object($attributesList)) || !count($attributesList)) return '';

        $result = '';
        foreach ($attributesList as $attributeName => $attributeValue)
        {
            $result .= ' '.$attributeName.'="'.$attributeValue.'"';
        }

        return $result;
    }

    /**
     * Overloads access to the control properties (attributes)
     *
     * @param   string $attributeName  Attribute name
     * @param   string $attributeValue Attribute value
     * @access  public
     */
    public function __set($attributeName, $attributeValue)
    {
        if (is_object($attributeValue))
        {
            $this->{$attributeName} = $attributeValue;
        }
        else
        {
            $this->_attributes[$attributeName] = $attributeValue;
        }
    }

    /**
     * Overloads access to the control properties (attributes)
     *
     * @param   string $attributeName Attribute name
     * @return  string
     * @access  public
     */
    public function __get($attributeName)
    {
        return isset($this->_attributes[$attributeName]) ? $this->_attributes[$attributeName] : '';
    }

    /**
     * Loads session data
     *
     * @return  string
     * @access  protected
     */
    protected function _loadSessionHandler()
    {
        $ownerObject = $this->container ? $this->container : $this->owner;
        if ($ownerObject)
        {
            if (!isset($ownerObject->sessionData['__CONTROLS'][$this->getName()])) $ownerObject->sessionData['__CONTROLS'][$this->getName()] = array();
            $this->sessionData = $ownerObject->sessionData['__CONTROLS'][$this->getName()];
        }

        /**
         * Loading disabled and visible values
         */
        if (isset($this->sessionData['visible'])) $this->visible = $this->sessionData['visible'];
        if (isset($this->sessionData['disabled'])) $this->disabled = $this->sessionData['disabled'];

        /**
         * Inheriting parent session handler
         */
        parent::_loadSessionHandler();
    }

    /**
     * Save session data
     *
     * @return  string
     * @access  protected
     */
    protected function _saveSessionHandler()
    {
        /**
         * Inheriting parent session handler
         */
        parent::_saveSessionHandler();

        /**
         * Saving visibility flag to the session
         */
        if (!$this->visible)
        {
            $this->sessionData['visible'] = $this->visible;
        }
        else
        {
            unset($this->sessionData['visible']);
        }

        /**
         * Saving disabled flag to the session
         */
        if ($this->disabled)
        {
            $this->sessionData['disabled'] = $this->disabled;
        }
        else
        {
            unset($this->sessionData['disabled']);
        }

        /**
         * Saving session data to the parent session
         */
        $ownerObject = $this->container ? $this->container : $this->owner;
        if ($this->sessionData && count($this->sessionData))
        {
            if ($ownerObject) $ownerObject->sessionData['__CONTROLS'][$this->getName()] = &$this->sessionData;
        }
        elseif ($ownerObject)
        {
            unset($ownerObject->sessionData['__CONTROLS'][$this->getName()]);
        }
    }

}
