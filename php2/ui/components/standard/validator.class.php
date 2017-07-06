<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains standard validator component
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
 * Default validation Reg Exps Collections
 */
$GLOBALS['__ValidatorRegExpCollection'] = array();

$regExps                   = &$GLOBALS['__ValidatorRegExpCollection'];
$regExps['TYPE_EMAIL']     = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
$regExps['TYPE_USERNAME']  = '/[а-яА-Я\w\d\_\-]+/u';
$regExps['TYPE_LETTER']    = '/[a-zA-Z]+/';
$regExps['TYPE_FLOAT']     = '/[-+]?[0-9]*\.?[0-9]*/';
$regExps['TYPE_URL']       = '/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?([\w\.\-]+)?(:[0-9]+)?(\/([\w\#\!\:\.\?\;\+\=\&\%@!\-\/]*))?/';

/**
 * Validator component
 *
 * Usage in the template:
 *
 * <code>
 *      <php:validator:$objectName
 *          control="{controlId}"
 *          [minLength="{minLength}"]
 *          [maxLength="{maxLength}"]
 *          [regExp="{regExp}"]
 *          [onBlur="true|false"]
 *          [showMessage="true|false"]
 *          [messageBox="{messageBoxComponent}"]
 *          [button="{submitId}"]
 *          [visible="true|false"]
 *      />
 * </code>
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: validator.class.php 115 2010-08-12 09:28:36Z eugene $
 * @access   public
 * @package  PHP2\UI\Components\Standard
 */
class PHP2_UI_Components_Standard_Validator extends PHP2_UI_Control
{
    /**
     * Validated control UID
     *
     * @var     string
     * @access  public
     */
    public $control;

    /**
     * Min length of the text
     *
     * @var     integer
     * @access  public
     */
    public $minLength;

    /**
     * Max length of the text
     *
     * @var     integer
     * @access  public
     */
    public $maxLength;

    /**
     * Regular expression or RegExp type
     *
     * @var     string
     * @access  public
     */
    public $regExp;

    /**
     * Use onBlur validation or not
     *
     * @var     boolean
     * @access  public
     */
    public $onBlur = true;

    /**
     * Show/Hide a validation error message
     *
     * @var     boolean
     * @access  public
     */
    public $showMessage = true;

    /**
     * Link to the Messagebox component
     *
     * @var     PHP2_UI_Components_IMessageBox
     * @access  public
     */
    public $messageBox;

    /**
     * Submit button UID
     *
     * @var     string
     * @access  public
     */
    public $button;

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
         * Processing default control content
         */
        $this->onBlur      = $this->_extractAttribute('onBlur', 'boolean', true);
        $this->showMessage = $this->_extractAttribute('showMessage', 'boolean', true);
        $this->control     = $this->_extractAttribute('control', 'string');
        $this->minLength   = $this->_extractAttribute('minLength', 'string');
        $this->maxLength   = $this->_extractAttribute('maxLength', 'string');
        $this->regExp      = $this->_extractAttribute('regExp', 'string');
        $this->button      = $this->_extractAttribute('button', 'string');
        
        if ($messageBox = $this->_extractAttribute('messageBox', 'string', null))
        {
        	if (isset($this->owner->{$messageBox}) && ($this->owner->{$messageBox} instanceof PHP2_UI_Components_IMessageBox))
        	{
        		$this->messageBox = $this->owner->{$messageBox};
        	}
        }

        /**
         * Set event handlers
         */
        // $this->addEventListener(PHP2_UI_UIEvent::LOAD_SESSION, '_loadFromSession', $this);
        // $this->addEventListener(PHP2_UI_UIEvent::SAVE_SESSION, '_saveToSession', $this);
    }

    /**
     * Validates result
     *
     * @return  boolean
     */
    public function validate()
    {
        $result = true;

        if (!isset($this->owner->{$this->control})) return $result;

        $controlObject = $this->owner->{$this->control};
        $validatedText = (isset($controlObject->text)) ? $controlObject->text : $controlObject->value;

        /**
         * Checking min Length of the text
         */
        if ($this->minLength)
        {
            if (strlen($validatedText) < $this->minLength)
            {
                $result = false;
            }
        }

        /**
         * Checking max Length of the text
         */
        if ($this->maxLength)
        {
            if (strlen($validatedText) > $this->maxLength)
            {
                $result = false;
            }
        }

        /**
         * Checking regexp pattern
         */
        if ($this->regExp)
        {
            $regExpPattern = $this->regExp;
            $regExpIndex   = 'TYPE_'.strtoupper($this->regExp);
            if (isset($GLOBALS['__ValidatorRegExpCollection'][$regExpIndex])) $regExpPattern = $GLOBALS['__ValidatorRegExpCollection'][$regExpIndex];

            preg_match($regExpPattern, $validatedText, $matches);
            if (!isset($matches[0]) || ($matches[0] != $validatedText))
            {
                $result = false;
            }
        }

        return $result;
    }
    
    /**
     * Sets error message
     * 
     * @param   string $errorMessage
     * @return  void
     */
    private function _setErrorMessage($errorMessage)
    {
    	if ($this->messageBox)
    	{
    		$this->messageBox->add($errorMessage);
    	}
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
        $controlName = ($this->control && isset($this->owner->{$this->control})) ? $this->owner->{$this->control}->getName() : $this->control;
        $buttonName  = ($this->button && isset($this->owner->{$this->button})) ? $this->owner->{$this->button}->getName() : $this->button;

        $result  = '';
        $result .= '<span id="'.$objectName.'_ValidationResult" class="validationErrorPanel" style="display: none;">*</span>';
        $result .= '<script type="text/javascript">';
        $result .= 'jQuery(document).ready(function(){';
        $result .= 'var '.$objectName.' = new PHP2.Validator(\''.$controlName.'\', '.intval($this->minLength).', '.intval($this->maxLength).', '.($this->regExp ? '\''.addslashes($this->regExp).'\'' : 'null').');';
        $result .= $objectName.'.registerHandlers(\''.$buttonName.'\', '.($this->onBlur ? 'true' : 'false').');';
        $result .= $objectName.'.setResultPanel(\''.$objectName.'_ValidationResult\', '.($this->showMessage ? 'true' : 'false').');';
        $result .= '});';
        $result .= '</script>';

        return $result;
    }

}
