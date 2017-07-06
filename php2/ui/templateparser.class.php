<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains UI component template parser class
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
 * UI component template parser
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: templateparser.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\UI
 */
class PHP2_UI_TemplateParser
{
    /**
     * Template string
     *
     * @var      string
     * @access   protected
     * @see      getNextElement()
     */
    protected $_templateString = '';

    /**
     * Array of matches from template
     *
     * @var      array()
     * @access   protected
     * @see      getNextElement()
     */
    protected $_templateControls;

    /**
     * Parsed template string
     *
     * @var      string
     * @access   protected
     * @see      getNextElement()
     */
    protected  $_parsedTemplate = '';

    /**
     * Parser constructor
     *
     * @param   string $templateString Template content as string
     * @access  public
     */
    public function __construct($templateString = '')
    {
        $this->_templateString    = $templateString;
        $this->_templateControls  = array();
    }


    /**
     * Parses template string
     *
     * @return  void
     * @access  public
     */
    public function parse()
    {
        $currPosition = 0;
        while ($currPosition = $this->_getNextElement($currPosition));

        return true;
    }

    /**
     * Returns array of the controls definitions for template
     *
     * @return  array
     * @access  public
     */
    public function &getControlsDefinition()
    {
        return $this->_templateControls;
    }

    /**
     * Returns parsed template string
     *
     * @return  string
     * @access  public
     */
    public function &getParsedTemplate()
    {
        return $this->_parsedTemplate;
    }

    /**
     * Find next element and return its position
     *
     * @param   integer $currPosition Current position
     * @return  integer
     * @access  public
     */
    protected function _getNextElement($currPosition = 0)
    {
        /**
         * Finding open tag position for control
         */
        $beginOpenTagPos = strpos($this->_templateString, '<php:', $currPosition);
        if ($beginOpenTagPos === false)
        {
            $this->_parsedTemplate .= substr($this->_templateString, $currPosition);
            return false;
        }

        /**
         * Find close bracket position for open tag
         */
        $endOpenTagPos = strpos($this->_templateString, '>', $beginOpenTagPos);
        if ($endOpenTagPos === false)
        {
            $this->_parsedTemplate .= substr($this->_templateString, $currPosition, ($beginOpenTagPos + 4 - $currPosition));
            return ($beginOpenTagPos + 4);
        }

        /**
         * Processing open tag content
         */
        $openTagContent = substr($this->_templateString, $beginOpenTagPos, ($endOpenTagPos - $beginOpenTagPos + 1));
        preg_match('/(<php:([\w]+):([\w]+)([^>]*)>)/', $openTagContent, $subPatterns);
        if (!isset($subPatterns[2]) || !isset($subPatterns[3]))
        {
            /**
             * Processing error in the tag structure
             */
            $this->_parsedTemplate .= substr($this->_templateString, $currPosition, ($endOpenTagPos - $currPosition));

            return $endOpenTagPos;
        }
        elseif ($this->_templateString{$endOpenTagPos - 1} == '/')
        {
            /**
             * Allowing Short XML Tag declaration
             */
            $attributesString           = substr($subPatterns[4], 0, strlen($subPatterns[4]) - 1);
            $controlDefinition          = new PHP2_UI_ControlDefinition($subPatterns[3], $subPatterns[2], $attributesString);
            $this->_templateControls[]  = $controlDefinition;

            $this->_parsedTemplate .= substr($this->_templateString, $currPosition, $beginOpenTagPos - $currPosition).'{{'.$controlDefinition->getControlTemplateUID().'}}';

            return $endOpenTagPos + 1;
        }

        /**
         * Triyng to find start position of the end tag for control definition
         */
        $beginCloseTagPos = strpos($this->_templateString, '</php:'.$subPatterns[2].':'.$subPatterns[3].'>', $endOpenTagPos);
        if ($beginCloseTagPos === false)
        {
            $this->_parsedTemplate .= substr($this->_templateString, $currPosition, ($endOpenTagPos - $currPosition));

            return $endOpenTagPos;
        }

        /**
         * Triyng to find end position of the end tag for control definition
         */
        $endCloseTagPos = strpos($this->_templateString, '>', $beginCloseTagPos);
        if ($endOpenTagPos === false)
        {
            $this->_parsedTemplate .= substr($this->_templateString, $currPosition, ($beginCloseTagPos + 5 - $currPosition));

            return ($beginCloseTagPos + 5);
        }

        /**
         * Initializing control definition
         */
        $tagContent                 = substr($this->_templateString, $endOpenTagPos + 1, ($beginCloseTagPos - $endOpenTagPos - 1));
        $controlDefinition          = new PHP2_UI_ControlDefinition($subPatterns[3], $subPatterns[2], $subPatterns[4], $tagContent);
        $this->_templateControls[]  = $controlDefinition;

        $this->_parsedTemplate .= substr($this->_templateString, $currPosition, $beginOpenTagPos - $currPosition).'{{'.$controlDefinition->getControlTemplateUID().'}}';

        return $endCloseTagPos + 1;
    }

    /**
     * Parse linear template and returns blocks definition.
     *
     * Template example:
     *
     * <code>
     *     <state:stState1 attr1="ss" attr2="dd">
     *         <php:edit:txtEmail1 class="textField" />
     *     </state:stState1>
     *     <state:stState2>
     *         content 2
     *         <php:edit:txtEmail2 class="textField" />
     *     </state:stState2>
     * </code>
     *
     * @param   string $templateString
     * @param   string $tagName
     * @param   boolean $useShortCloseTags use or not short tags with only blockName
     * @return  array
     */
    public static function parseBlockTemplate(&$templateString, $tagName = 'block', $useShortCloseTags = false)
    {
        $currPosition = 0;
        $templateDetails = array('tagName' => $tagName, 'templateString' => $templateString, 'useShortCloseTags' => $useShortCloseTags);
        while ($currPosition = self::_getNextBlock($currPosition, $templateDetails));

        return $templateDetails;
    }

    /**
     * Parse template and returns blocks definition
     *
     * @param   integer $startPosition
     * @param   string  $templateDetails
     * @return  boolean
     */
    protected static function _getNextBlock($startPosition, &$templateDetails)
    {
        $beginOpenTagPos = strpos($templateDetails['templateString'], '<'.$templateDetails['tagName'].':', $startPosition);
        if ($beginOpenTagPos === false) return false;

        $openTagLength  = strlen('<'.$templateDetails['tagName'].':');
        $closeTagLength = strlen('>');

        $endOpenTagPos  = stripos($templateDetails['templateString'], '>', $beginOpenTagPos);
        if ($endOpenTagPos === false) return ($beginOpenTagPos + $openTagLength);

        $openTagContent = substr($templateDetails['templateString'], $beginOpenTagPos + $openTagLength, ($endOpenTagPos - ($beginOpenTagPos + $openTagLength)));

        /**
         * Parsing Tag contents
         */
        preg_match('/([\w]+)([^>]*)/', $openTagContent, $matches);
        $blockName   = $matches[1];
        $attributes  = $matches[2];

        $closeTagContent = $templateDetails['useShortCloseTags'] ? '</'.$templateDetails['tagName'].'>' : '</'.$templateDetails['tagName'].':'.$blockName.'>';

        $beginCloseTagPos = stripos($templateDetails['templateString'], $closeTagContent, $endOpenTagPos);
        if ($beginCloseTagPos === false) return $endOpenTagPos;

        $endCloseTagPos = $beginCloseTagPos + strlen($closeTagContent);

        $tagContent = substr($templateDetails['templateString'], $endOpenTagPos + $closeTagLength, ($beginCloseTagPos - ($endOpenTagPos + $closeTagLength)));

        /**
         * Generating result structure
         */
        $result                      = array();
        $result['blockName']         = $blockName;
        $result['content']           = $tagContent;
        $result['attributesString']  = $attributes;

        $templateDetails['blocks'][$blockName] = $result;

        return $endCloseTagPos;
    }

}
