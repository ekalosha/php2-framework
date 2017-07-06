<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains message box component
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 115 $
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
 * Message box component
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: messagebox.class.php 115 2010-08-12 09:28:36Z eugene $
 * @access   public
 * @package  PHP2\UI\Components\Standard
 */
class PHP2_UI_Components_Standard_MessageBox extends PHP2_UI_Control implements PHP2_UI_Components_IMessageBox
{
    /**
     * Template engine for rendering
     *
     * @var     PHP2_UI_RBTEngine
     * @access  protected
     */
    protected $_tplEngine;

    /**
     * Messagebox messages list
     *
     * @var     array
     * @access  protected
     */
    protected $_messagesList = array();

    /**
     * Template filename
     *
     * @var      string
     * @access   protected
     */
    protected $_template;

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
         * Processing default component attributes
         */
        $this->_template = BASE_PATH.'ui/components/messagebox/messagebox.tpl';
        if ($template = $this->_extractAttribute('template')) $this->_template = BASE_PATH.'ui/'.$template;

        /**
         * Set event handlers
         */
        $this->addEventListener(PHP2_UI_UIEvent::LOAD_SESSION, '_loadFromSession', $this);
        $this->addEventListener(PHP2_UI_UIEvent::SAVE_SESSION, '_saveToSession', $this);
    }

    /**
     * Adds message to the messagebox and returns UID of the message
     *
     * @return  string message UID
     * @access  public
     */
    public function add($message)
    {
        $messageUID = uniqid(rand());

        $this->_messagesList[$messageUID] = $message;

        return $messageUID;
    }

    /**
     * Removes message from the messagebox
     *
     * @param   string $messageUID message UID
     * @return  boolean
     * @access  public
     */
    public function remove($messageUID)
    {
        unset($this->_messagesList[$messageUID]);
    }

    /**
     * Removes all messages from the messagebox
     *
     * @return  boolean
     * @access  public
     */
    public function clear()
    {
        $this->_messagesList = array();
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        if (!is_array($this->_messagesList) || !count($this->_messagesList)) return '';

        /**
         * Creating template Engine object
         */
        $this->_tplEngine = new PHP2_UI_RBTEngine();
        $this->_tplEngine->loadFromFile($this->_template);

        $i = 0;
        $messagesCount = count($this->_messagesList);
        $this->_tplEngine['objectName'] = $this->getName();
        foreach ($this->_messagesList as $messageText)
        {
            $i++;

            $this->_tplEngine->body->message['number']  = $i;
            $this->_tplEngine->body->message['message'] = $messageText;

            if ($messagesCount > 1) $this->_tplEngine->body->message->number->replace();
            $this->_tplEngine->body->message->replace();
        }
        $this->_tplEngine->body->replace();

        if (isset($this->_tplEngine->header)) $this->_tplEngine->header->replace();
        $this->_tplEngine->replace();

        return $this->_tplEngine->renderOutput();
    }

    /**
     * Handler of the load session event
     *
     * @return  string
     * @access  protected
     */
    protected function _loadFromSession()
    {
        // if (isset($this->sessionData['text'])) $this->text = $this->sessionData['text'];
    }

    /**
     * Handler of the save to session event
     *
     * @return  string
     * @access  protected
     */
    protected function _saveToSession()
    {
        // $this->sessionData['text'] = $this->text;
    }

}
