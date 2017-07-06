<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains standard radiobutton component
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
// namespace PHP2\UI\Components\Standard;

/**
 * Radiobutton component
 *
 * Usage in the template:
 *
 * <code>
 *      <php:radiobutton:$objectName [group="{groupName}"] [visible="true|false"] [checked="true|false"] [attributes] />
 * </code>
 *
 * Output HTML code for this control is:
 *
 * <code>
 *      <input type="radio" id="{objectName}" name="{groupName}" value="{objectName}" [checked] [attributes] />
 * </code>
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: radiobutton.class.php 98 2009-08-19 17:15:05Z eugene $
 * @access   public
 * @package  PHP2\UI\Components\Standard
 */
class PHP2_UI_Components_Standard_Radiobutton extends PHP2_UI_Control
{
    /**
     * Checked flag
     *
     * @var     boolean
     * @access  public
     */
    public $checked = false;

    /**
     * Group of the current control
     *
     * @var      string
     * @access   protected
     */
    protected $_group;

    /**
     * Class constructor
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
         * Processing request
         */
        if ($_POST || isset($_REQUEST['__postBack']))
        {
            $objectName    = $this->getName();
            $this->checked = (isset($_REQUEST[$this->_group]) && ($_REQUEST[$this->_group] == $objectName)) ? true : false;
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
        parent::_init();

        /**
         * Processing default edit content
         */
        if (($checked = strtolower($this->_extractAttribute('checked'))) === "true") $this->checked = true;
        if ($group = strtolower($this->_extractAttribute('group'))) $this->setGroup($group);

        /**
         * Set event handlers
         */
        $this->addEventListener(PHP2_UI_UIEvent::LOAD_SESSION, '_loadFromSession', $this);
        $this->addEventListener(PHP2_UI_UIEvent::SAVE_SESSION, '_saveToSession', $this);
    }


    /**
     * Initialize control's group
     *
     * @param   string $groupName
     * @return  void
     * @access  protected
     */
    public function setGroup($groupName)
    {
        $this->_group = (($groupName && isset($this->owner->owner) && $this->owner->owner) ? $this->owner->getName().'_' : '').$groupName;
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        $objectName  = $this->getName();
        $controlName = $this->_group ? $this->_group : $objectName;

        return '<input type="radio" name="'.$controlName.'" value="'.$objectName.'" '.(($this->checked) ? ' checked': '').$this->_getAttributesString().' />';
    }

    /**
     * Handler of the load session event
     *
     * @return  string
     * @access  protected
     */
    protected function _loadFromSession()
    {
        if (isset($this->sessionData['checked'])) $this->checked = $this->sessionData['checked'];
    }

    /**
     * Handler of the save to session event
     *
     * @return  string
     * @access  protected
     */
    protected function _saveToSession()
    {
        $this->sessionData['checked'] = $this->checked;
    }

}
