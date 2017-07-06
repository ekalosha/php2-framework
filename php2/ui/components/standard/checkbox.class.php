<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains standard checkbox component
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
 * Checkbox component
 *
 * Usage in the template:
 *
 * <code>
 *      <php:checkbox:$objectName [visible="true|false"] [checked="true|false"] [attributes] />
 * </code>
 *
 * Output HTML code for this control is:
 *
 * <code>
 *      <input type="checkbox" id="{objectName}" name="{objectName}" [checked] [attributes] />
 * </code>
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: checkbox.class.php 98 2009-08-19 17:15:05Z eugene $
 * @access   public
 * @package  PHP2\UI\Components\Standard
 */
class PHP2_UI_Components_Standard_Checkbox extends PHP2_UI_Control
{
    /**
     * Checked flag
     *
     * @var     boolean
     * @access  public
     */
    public $checked = false;

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
            $this->checked = (isset($_REQUEST[$this->getName()]) && ($_REQUEST[$this->getName()] == 'on')) ? true : false;
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

        /**
         * Set event handlers
         */
        $this->addEventListener(PHP2_UI_UIEvent::LOAD_SESSION, '_loadFromSession', $this);
        $this->addEventListener(PHP2_UI_UIEvent::SAVE_SESSION, '_saveToSession', $this);
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        return '<input type="checkbox" name="'.$this->getName().'" '.(($this->checked) ? ' checked': '').$this->_getAttributesString().' />';
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
