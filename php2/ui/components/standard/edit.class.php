<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains edit text component
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
 * Edit text component
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: edit.class.php 98 2009-08-19 17:15:05Z eugene $
 * @access   public
 * @package  PHP2\UI\Components\Standard
 */
class PHP2_UI_Components_Standard_Edit extends PHP2_UI_Control
{
    /**
     * Edit text
     *
     * @var     string
     * @access  public
     */
    public $text;

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
         *
         * @todo  add request validation
         */
        if (isset($_REQUEST[$this->getName()]))
        {
            $this->text = PHP2_System_Request::getInstance()->getString($this->getName());
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
        if (!$this->text) $this->text = $this->_controlDefinition->content;
        if (($text = $this->_extractAttribute('value')) !== null) $this->text = $text;
        if (($text = $this->_extractAttribute('text')) !== null) $this->text = $text;

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
        return '<input type="text" name="'.$this->getName().'" value="'.PHP2_Utils_String::validateXMLText($this->text).'"'.$this->_getAttributesString().' />';
    }

    /**
     * Handler of the load session event
     *
     * @return  string
     * @access  protected
     */
    protected function _loadFromSession()
    {
        if (isset($this->sessionData['text'])) $this->text = $this->sessionData['text'];
    }

    /**
     * Handler of the save to session event
     *
     * @return  string
     * @access  protected
     */
    protected function _saveToSession()
    {
        $this->sessionData['text'] = $this->text;
    }

}
