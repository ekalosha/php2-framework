<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains standard dropdownlist component
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
 * Dropdownlist component
 *
 * Usage in the template:
 *
 * <code>
 *      <php:dropdownlist:$objectName [visible="true|false"] [autoSubmit="true|false" formId="formId"] [safeMode="true|false"] [attributes] />
 *
 *      or in extended  mode:
 *
 *      <php:dropdownlist:$objectName
 *          [visible="true|false"]
 *          [autoSubmit="true|false"]
 *          [formId="formId"]
 *          [safeMode="true|false"]
 *          [attributes]
 *      >
 *          <option value="{value}" [selected=true|false]>{text}</option>
 *      </php:dropdownlist:$objectName>
 * </code>
 *
 * Output HTML code for this control is:
 *
 * <code>
 *      <select id="{objectName}" name="{objectName}" [attributes]>
 *          <option value="{value}">{text}</option>
 *      </select>
 * </code>
 *
 * Dispatches the following events:
 *
 * <code>
 *   PHP2_UI_UIEvent::SUBMIT
 *   PHP2_UI_UIEvent::INIT
 *   PHP2_UI_UIEvent::BEFORE_RENDER
 *   PHP2_UI_UIEvent::AFTER_RENDER
 *   PHP2_UI_UIEvent::LOAD_SESSION
 *   PHP2_UI_UIEvent::SAVE_SESSION
 * </code>
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: dropdownlist.class.php 98 2009-08-19 17:15:05Z eugene $
 * @access   public
 * @package  PHP2\UI\Components\Standard
 */
class PHP2_UI_Components_Standard_DropdownList extends PHP2_UI_Control
{

    /**
     * Selected index
     *
     * @var      string
     * @access   private
     */
    private $_selectedIndex = 0;

    /**
     * Old value of the selected index
     *
     * @var      string
     * @access   private
     */
    private $_oldSelIndex = 0;

    /**
     * Options array
     *
     * @var      array
     * @access   private
     */
    private $_options = array();

    /**
     * Auto refresh flag
     *
     * @var      boolean
     * @access   public
     */
    public $autoSubmit = false;

    /**
     * This flag used to validate Selected element with Request value
     *
     * @var      boolean
     * @access   public
     */
    public $safeMode = false;

    /**
     * Form ID
     *
     * @var      boolean
     * @access   public
     */
    public $formId = 0;

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
        $objectName = $this->getName();
        if (isset($_REQUEST[$objectName]))
        {
            $this->_selectedIndex = $_REQUEST[$objectName];
        }

        /**
         * Checking auto submit event
         */
        if (isset($_REQUEST[$objectName.'_event']) && ($_REQUEST[$objectName.'_event'] == 'on'))
        {
            $this->owner->addEventListener(PHP2_UI_UIEvent::CREATION_COMPLETE, '_initSubmitHandler', $this);
        }
    }

    /**
     * Initialize submit event
     *
     * @return  void
     * @access  protected
     */
    protected function _initSubmitHandler()
    {
        $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::SUBMIT));
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
        if (($autoSubmit = strtolower($this->_extractAttribute('autoSubmit'))) === "true") $this->autoSubmit = true;
        if (($safeMode = strtolower($this->_extractAttribute('safeMode'))) === "true") $this->safeMode = true;
        $this->formId = $this->_extractAttribute('formId', null, 0);

        /**
         * Set event handlers
         */
        $this->addEventListener(PHP2_UI_UIEvent::LOAD_SESSION, '_loadFromSession', $this);
        $this->addEventListener(PHP2_UI_UIEvent::SAVE_SESSION, '_saveToSession', $this);
    }

    /**
     * Initialize default options for Dropdownlist
     *
     * @return  boolean
     * @access  public
     */
    public function initDefaultOptions()
    {
        $this->clear();

        $ddlContent = trim($this->_controlDefinition->content);

        if (!$ddlContent) return false;

        /**
         * Matching options
         */
        preg_match_all("/<option([^\>]+)>([^\<]*)/x", $ddlContent, $matches, PREG_PATTERN_ORDER);

        if (isset($matches[1]) && count($matches[1]))
        {
            foreach ($matches[1] as $index => $attributesString)
            {
                $attributesList = PHP2_UI_ControlDefinition::parseAttributesString($attributesString);

                foreach ($attributesList as $key => &$value) $attributesList[strtolower($key)] = $value;

                if (isset($attributesList['value']))
                {
                    $this->insertItem($attributesList['value'], trim($matches[2][$index]));

                    if (isset($attributesList['selected']) && (strtolower($attributesList['selected']) == 'true')) $this->setSelectedIndex($attributesList['value']);
                }
            }
        }
    }


    /**
     * Inserts new item to the dropdownlist. In case if itemUID exists overwrites Item.
     *
     * @param   string $itemUID
     * @param   string $itemLabel
     * @access  public
     */
    public function insertItem($itemUID, $itemLabel)
    {
        $this->_options[$itemUID] = $itemLabel;
    }

    /**
     * Returns selected index
     *
     * @return  boolean
     * @access  public
     */
    public function getSelectedIndex()
    {
        if (!$this->safeMode)
        {
            return $this->_selectedIndex;
        }
        elseif (array_key_exists($this->_selectedIndex, $this->_options))
        {
            return $this->_selectedIndex;
        }

        return false;
    }

    /**
     * Sets selected index for the dropdownlist
     *
     * @param   string $itemUID
     * @return  boolean
     * @access  public
     */
    public function setSelectedIndex($itemUID)
    {
        if (!is_string($itemUID) && !is_numeric($itemUID)) $itemUID = (string) $itemUID;

        if (!$this->safeMode)
        {
            $this->_selectedIndex = $itemUID;

            return true;
        }
        elseif (is_array($this->_options) && array_key_exists($itemUID, $this->_options))
        {
            $this->_selectedIndex = $itemUID;

            return true;
        }

        return false;
    }

    /**
     * Clears dropdownlist
     *
     * @access  public
     */
    public function clear()
    {
        $this->_options = array();

        if ($this->_selectedIndex) $this->_oldSelIndex = $this->_selectedIndex;

        $this->_selectedIndex = false;
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        $result      = '';
        $objectName  = $this->getName();

        /**
         * Adding auto submit Code
         */
        if ($this->autoSubmit)
        {
            $this->onChange = 'javascript: document.getElementById(\''.$objectName.'_event\').value=\'on\'; this.form.submit();';
            $result .= '<input type="hidden" name="'.$objectName.'_event" id="'.$objectName.'_event" />'."\n";
        }

        $result .= '<select name="'.$objectName.'" '.$this->_getAttributesString().'>';
        foreach ($this->_options as $itemUID => $itemLabel)
        {
            if ((strval($itemUID) != strval($this->_selectedIndex)))
            {
                $result .= '<option value="'.$itemUID.'">'.$itemLabel.'</option>';
            }
            else
            {
                $result .= '<option value="'.$itemUID.'" selected>'.$itemLabel.'</option>';
            }
        }
        $result .= '</select>';

        return $result;
    }

    /**
     * Handler of the load session event
     *
     * @return  string
     * @access  protected
     */
    protected function _loadFromSession()
    {
        if (isset($this->sessionData['_options']))
        {
            $this->_options = $this->sessionData['_options'];
        }
        else
        {
            $this->initDefaultOptions();
        }

        if (isset($this->sessionData['_selectedIndex'])) $this->_selectedIndex = $this->sessionData['_selectedIndex'];
        if (isset($this->sessionData['safeMode'])) $this->safeMode = $this->sessionData['safeMode'];
    }

    /**
     * Handler of the save to session event
     *
     * @return  string
     * @access  protected
     */
    protected function _saveToSession()
    {
        $this->sessionData['_options']        = $this->_options;
        $this->sessionData['_selectedIndex']  = $this->_selectedIndex;
        $this->sessionData['safeMode']        = $this->safeMode;
    }

}
