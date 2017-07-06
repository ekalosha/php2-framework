<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Recursive Block Template Engine Class, for development Component Based Information Systems.
 *
 * PHP version 5
 * @category   UI Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 97 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 3.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace PHP2\UI;

/**
 * Recursive Block Template Engine Class.
 * Implements ArrayAccess and Countable interfaces to get Access to Pattern variables and Pattern template objects
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: rbtengine.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\UI
 */
class PHP2_UI_RBTEngine implements ArrayAccess, Countable
{
    /**
     * Unique Identifier of Current Template Object
     *
     * @var      string
     * @access   protected
     */
    protected $_name;

    /**
     * Contains the reference to the owner object
     *
     * @var      PHP2_UI_RBTEngine
     * @access   private
     */
    private $_owner;

    /**
     * Open block tag for template engine
     *
     * @var      array
     * @access   public
     * @see      parseTemplate()
     */
    public $tagBlockOpen = array('open' => '<block:', 'close' => '>');

    /**
     * Close block tag for template engine
     *
     * @var      array
     * @access   public
     * @see      parseTemplate()
     */
    public $tagBlockClose = array('open' => '</block:', 'close' => '>');

    /**
     * Unique Block marker to replace in Replacement Engine
     *
     * @var      string
     * @access   public
     * @see      parseTemplate(), renderOutput()
     */
    public $tagUniqueBlockMarker = '__BLOCK';

    /**
     * Open block tag for replacement engine
     *
     * @var      string
     * @access   public
     * @see      parseTemplate(), renderOutput()
     */
    public $tagOpen = '{{';

    /**
     * Close block tag for replacement engine
     *
     * @var      string
     * @access   public
     * @see      parseTemplate(), renderOutput()
     */
    public $tagClose = '}}';

    /**
     * Undefined block content
     *
     * @var      string
     * @access   public
     */
    public $undefinedBlockContent = 'Undefined Block!';

    /**
     * Base template content for the current Template Engine Object
     *
     * @var      string
     * @access   protected
     * @see      parseTemplate(), renderOutput()
     */
    protected $_baseTemplate;

    /**
     * Parsed template for replace in Template Engine
     *
     * @var      string
     * @access   protected
     * @see      parseTemplate(), renderOutput()
     */
    protected $_parsedTemplate;

    /**
     * Matces from parsed template for the Simple Template Engine Object
     *
     * @var      array
     * @access   protected
     * @see      parseTemplate()
     */
    protected $_matches = array();

    /**
     * Link to Array of the pattern elements for replace
     *
     * @var      array
     * @access   public
     * @see      parseTemplate(), renderOutput()
     */
    public $row = array();

    /**
     * Text content of current object
     *
     * @var      string
     * @access   public
     * @see      parseTemplate(), renderOutput()
     */
    protected $_renderResult;

    /**
     * If this flag is true then object outputs errors
     *
     * @var      boolean
     * @access   public
     */
    public $trackErrors = false;

    /**
     * Template Page visibility
     *
     * @var      boolean
     * @access   public
     * @see      renderOutput()
     */
    public $visible = true;

    /**
     * PHP2_UI_RBTEngine constructor
     *
     * @param   string    $name Unique object ID
     * @param   PHP2_UI_RBTEngine &$owner Reference to the owner Object
     * @access  public
     */
    public function __construct($name = null, &$owner = null)
    {
        if ($owner) $this->_owner = &$owner;
        $this->_name    = $name;
        $this->_matches = array();
        $this->row      = array();
    }

    /**
     * Loads template data from file
     *
     * @param   string  $filename Source Template file name
     * @param   boolean $parseAfterLoading Parse template after loading
     * @return  bool
     * @access  public
     */
    public function loadFromFile($filename, $parseAfterLoading = true)
    {
        $this->_baseTemplate = file_get_contents($filename);

        if ($parseAfterLoading) $this->parseTemplate();

        return true;
    }

    /**
     * Loads template data from template string
     *
     * @param   string  $templateString Source Template string
     * @param   boolean $parseAfterLoading Parse template after loading
     * @access  public
     */
    public function loadFromTemplate($templateString, $parseAfterLoading = true)
    {
        $this->_baseTemplate = $templateString;

        if ($parseAfterLoading) $this->parseTemplate();
    }

    /**
     * Parses current template and inits template objects
     *
     * @access  public
     */
    public function parseTemplate()
    {
        // --- Parsing Template Until Ends --- //
        $currPosition = 0;
        while ($currPosition = $this->getNextElement($currPosition, $matches));

        if (is_array($matches) && isset($matches[3]))
        {
            foreach ($matches[3] as $key => $match)
            {
                $blockName = $matches[2][$key];
                $this->_matches[$blockName] = $match;
                $this->{$blockName} = new PHP2_UI_RBTEngine($blockName, $this);
                $this->{$blockName}->loadFromTemplate($match, true);
            }
        }

    }

    /**
     * Assign pattern variables for replace
     *
     * @param   string $tValue Name of template variable, ex. variable SomeObject for template word {{SomeObject}}
     * @param   string $rValue Value for replace
     * @access  public
     */
    public function assign($tValue, $rValue)
    {
        $this->row[$tValue] = $rValue;
    }

    /**
     * Assign pattern variables for replace from array.
     *
     * @param   array $parameters Associative array of the template variable, ex. variable SomeObject for template word {{SomeObject}}
     * @access  public
     */
    public function assignParameters($parameters)
    {
        if (is_array($parameters)) $this->row = $this->row + $parameters;
    }

    /**
     * Clears Render Result Output
     *
     * @access  public
     */
    public function clearOutput()
    {
        $this->_renderResult = '';
    }

    /**
     * Force Clears Rendered Result Output
     *
     * @access  public
     */
    public function forceClearOutput()
    {
        $this->_renderResult  = '';

        foreach ($this->_matches as $tValue => $rValue)
        {
            $this->{$tValue}->forceClearOutput();
        }
    }

    /**
     * Replace current pattern with current values
     *
     * @param   boolean $returnResult Return Result flag
     * @param   string  $defaultRValue Default value for replace
     * @access  public
     */
    public function replace($returnResult = false, $defaultRValue = '')
    {
        /**
         * Inserting template variables from the parent
         */
        if (isset($this->_owner) && (isset($this->_owner->row)) && (is_array($this->_owner->row))) $this->row += $this->_owner->row;

        // --- Rendering Blocks Output for Replacing --- //
        foreach ($this->_matches as $blockName => $blockValue)
        {
            $this->row[$blockName.$this->tagUniqueBlockMarker] = $this->{$blockName}->renderOutput(true);
        }

        $this->_renderResult .= preg_replace('/'.$this->tagOpen.'(\w+)'.$this->tagClose.'/e', '(isset($this->row["\1"]) ? $this->row["\1"] : \''.$defaultRValue.'\')', $this->_parsedTemplate);

        // --- Unseting current row --- //
        $this->row  = array();

        if ($returnResult) return $this->_renderResult;
    }

    /**
     * Gets replaced text content
     *
     * @param   boolean $clearContent Clears text Content after result is generated
     * @access  public
     */
    public function renderOutput($clearContent = true)
    {
        // --- Do not display Block if his visibility is False --- //
        if (!$this->visible)
        {
            if ($clearContent) $this->_renderResult = '';

            return '';
        }

        $result = $this->_renderResult;
        if ($clearContent) $this->_renderResult = '';

        return $result;
    }

    /**
     * Overloads access to the object properties(blocks)
     *
     * @param   string $undefinedPropertyName Object Property name, by default has type STEngine
     * @return  PHP2_UI_RBTEngine
     * @access  public
     */
    public function __get($undefinedPropertyName)
    {
        if (!isset($this->{$undefinedPropertyName}))
        {
            $this->{$undefinedPropertyName} = new PHP2_UI_RBTEngine($undefinedPropertyName, $this);
            $this->{$undefinedPropertyName}->loadFromTemplate($this->undefinedBlockContent, true);

            if ($this->trackErrors) echo '<font color="red">Undefined Block - '.$undefinedPropertyName.'</font><br>';

            return $this->{$undefinedPropertyName};
        }
    }

    /**
     * Clones Current Object
     *
     * @return  PHP2_UI_RBTEngine
     * @access  public
     */
    public function __clone()
    {
        foreach ($this->_matches as $tValue => $rValue)
        {
            $this->{$tValue} = clone($this->{$tValue});
        }
    }

    /**
     * Find next element and return its position.
     *
     * @param   integer $currPosition  Current position
     * @param   array   $resultMatches Array of the result matches
     * @return  integer
     * @access  public
     */
    public function getNextElement($currPosition = 0, &$resultMatches)
    {

        $beginOpenTagPos = strpos($this->_baseTemplate, $this->tagBlockOpen['open'], $currPosition);
        if ($beginOpenTagPos === false)
        {
            $this->_parsedTemplate .= substr($this->_baseTemplate, $currPosition);
            return false;
        }

        $openTagLength  = strlen($this->tagBlockOpen['open']);
        $closeTagLength = strlen($this->tagBlockOpen['close']);

        $endOpenTagPos  = strpos($this->_baseTemplate, $this->tagBlockOpen['close'], $beginOpenTagPos);
        if ($endOpenTagPos === false)
        {
            $this->_parsedTemplate .= substr($this->_baseTemplate, $currPosition, ($beginOpenTagPos + $openTagLength - $currPosition));
            return ($beginOpenTagPos + $openTagLength);
        }

        $openTagContent = substr($this->_baseTemplate, $beginOpenTagPos + $openTagLength, ($endOpenTagPos - ($beginOpenTagPos + $openTagLength)));

        $beginCloseTagPos = strpos($this->_baseTemplate, $this->tagBlockClose['open'].$openTagContent.$this->tagBlockClose['close'], $endOpenTagPos);
        if ($beginCloseTagPos === false)
        {
            $this->_parsedTemplate .= substr($this->_baseTemplate, $currPosition, ($endOpenTagPos - $currPosition));
            return $endOpenTagPos;
        }

        $endCloseTagPos = $beginCloseTagPos + strlen($this->tagBlockClose['open'].$openTagContent.$this->tagBlockClose['close']);

        $tagContent = substr($this->_baseTemplate, $endOpenTagPos + $closeTagLength, ($beginCloseTagPos - ($endOpenTagPos + $closeTagLength)));

        if (!is_array($resultMatches)) $resultMatches = array();
        $resultMatches[2][] = $openTagContent; //$subPatterns[2];
        $resultMatches[3][] = $tagContent; //$subPatterns[3];
        $resultMatches[4][] = ''; // $subPatterns[4];
        $resultMatches[5][] = ''; //$tagContent;

        $this->_parsedTemplate .= substr($this->_baseTemplate, $currPosition, $beginOpenTagPos - $currPosition).$this->tagOpen.$openTagContent.$this->tagUniqueBlockMarker.$this->tagClose;

        return $endCloseTagPos;
    }

    /**
     * Checks is offset exists in the template variables.
     * Implementation of the ArrayAccess interface from the SPL.
     *
     * As the result of implementation of ArrayAccess interface from SPL we can use objects of current class as arrays.
     * After implementation of offsetExists method you can use isset() method to check is attribute exists in the object.
     * For example:
     * <code>
     *    $tplObject = new PHP2_UI_RBTEngine();
     *    ...
     *    if (isset($tplObject['variableName'])) ...
     * </code>
     *
     * @param   string $offset
     * @return  boolean
     */
    public function    offsetExists($offset)
    {
        return (isset($this->row[$offset]));
    }

    /**
     * Returns variable.
     * Implementation of the ArrayAccess interface from the SPL.
     *
     * As the result of implementation of ArrayAccess interface from SPL we can use objects of current class as arrays.
     * After implementation of offsetGet method you can use [] modifier to get attribute.
     * For example:
     * <code>
     *    $tplObject = new PHP2_UI_RBTEngine();
     *    ...
     *    $value = $tplObject['variableName'];
     * </code>
     *
     * @param   string $offset
     * @return  string
     */
    public function    offsetGet($offset)
    {
        return (isset($this->row[$offset]) ? $this->row[$offset] : null);
    }

    /**
     * Sets template variable.
     * Implementation of the ArrayAccess interface from the SPL.
     *
     * As the result of implementation of ArrayAccess interface from SPL we can use objects of current class as arrays.
     * After implementation of offsetSet method you can use [] modifier to set attribute.
     * For example:
     * <code>
     *    $tplObject = new PHP2_UI_RBTEngine();
     *    ...
     *    $tplObject['variableName'] = $value;
     * </code>
     *
     * @param   string $offset
     * @param   string $value
     * @return  string
     */
    public function    offsetSet($offset, $value)
    {
        $this->row[$offset] = $value;
    }

    /**
     * Unsets template variable.
     * Implementation of the ArrayAccess interface from the SPL.
     *
     * As the result of implementation of ArrayAccess interface from SPL we can use objects of current class as arrays.
     * After implementation of offsetUnset method you can use unset() method to unset attribute of the object.
     * For example:
     * <code>
     *    $tplObject = new PHP2_UI_RBTEngine();
     *    ...
     *    unset($tplObject['variableName']);
     * </code>
     *
     * @param   string $offset
     * @return  boolean
     */
    public function    offsetUnset($offset)
    {
        if (isset($this->row[$offset])) unset($this->row[$offset]);
    }

    /**
     * Returns blocks count in the object.
     * Implementation of the Countable interface from the SPL.
     *
     * As the result of implementation of Countable interface from SPL we can properly use count() method for objects of this class.
     * For example:
     * <code>
     *    $tplObject = new PHP2_UI_RBTEngine();
     *    ...
     *    $blocksCount = count($tplObject);
     * </code>
     *
     * @return  integer
     */
    public function    count()
    {
        if (is_array($matches) && isset($matches[3])) return count($matches[3]);

        return 0;
    }

}
