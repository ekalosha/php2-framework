<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Standard Code Colorer component for PHP2 XML code
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 99 $
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
 * Standard Code Colorer component for PHP2 XML code
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: php2codecolorer.class.php 99 2009-10-20 14:44:49Z eugene $
 * @access   public
 * @package  PHP2\UI\Components\Standard
 */
class PHP2_UI_Components_Standard_PHP2CodeColorer extends PHP2_UI_Control
{
    /**
     * Template engine for rendering
     *
     * @var     PHP2_UI_RBTEngine
     * @access  protected
     */
    protected $_tplEngine;

    /**
     * Template filename
     *
     * @var      string
     * @access   protected
     */
    protected $_template;

    /**
     * Language name
     *
     * @var      string
     * @access   protected
     */
    protected $_language;

    /**
     * Language type
     *
     * @var      string
     * @access   protected
     */
    protected $_languageType;

    /**
     * Language definition
     *
     * @var      array
     * @access   protected
     */
    protected $_languageDefinition;

    /**
     * Programming Code text
     *
     * @var      string
     * @access   protected
     */
    protected $_codeText;

    /**
     * Class constructor
     *
     * @param   PHP2_UI_ControlDefinition $controlDefinition control definition Object
     * @access  public
     */
    public function __construct($controlDefinition = null)
    {
        /**
         * Default language definition will be used in the future
         */
        $this->_languageDefinition = array();
        $this->_languageDefinition['comment']   = array('|\/\*\*.*\*\/|' => 'langdoc', '|\/\*.*\*\/|' => 'multiline', '|\/\/[^\n]|' => 'singleline');
        $this->_languageDefinition['keywords']  = array('/\s+(function|class|for|while|public|private|protected)/' => 'keyword', );
        $this->_languageDefinition['php2']      = array('/<\/*php:[\w]+:[\w]+[^>]/' => 'php2', );

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
        $this->_template = BASE_PATH.'ui/components/php2codecolorer/php2codecolorer.tpl';
        if ($template = $this->_extractAttribute('template')) $this->_template = BASE_PATH.'ui/'.$template;
        if ($language = $this->_extractAttribute('language')) $this->_language = strtolower($language);
        if ($languageType = $this->_extractAttribute('type')) $this->_languageType = strtolower($languageType);

        /**
         * Set default code text
         */
        if ($file = $this->_extractAttribute('file'))
        {
            $fileName = preg_replace('/{([\$\w]+)}/e', '\\1', $file);
            $this->setCode(file_get_contents($fileName));
        }
        else
        {
            $this->setCode($this->_controlDefinition->content);
        }

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
        /**
         * Creating template Engine object
         */
        $this->_tplEngine = new PHP2_UI_RBTEngine();
        $this->_tplEngine->loadFromFile($this->_template);

        $i = 0;
        $codeLines   = $this->_getCodeLines($this->_languageType);
        $linesCount  = count($codeLines);
        $this->_tplEngine['objectName'] = $this->getName();
        $this->_tplEngine['attributes'] = $this->_getAttributesString();
        foreach ($codeLines as $codeLine)
        {
            $i++;

            $this->_tplEngine->code->line['number'] = $i;
            $this->_tplEngine->code->line['code']   = $codeLine;
            if (isset($this->_tplEngine->code->line->number)) $this->_tplEngine->code->line->number->replace();

            $this->_tplEngine->code->line->replace();
        }
        $this->_tplEngine->code->replace();

        if (isset($this->_tplEngine->header)) $this->_tplEngine->header->replace();
        $this->_tplEngine->replace();

        return $this->_tplEngine->renderOutput();
    }

    /**
     * Set code text and language parameters
     *
     * @param   string $codeText
     * @param   string $language
     * @param   string $languageType
     * @return  void
     */
    public function setCode($codeText, $language = null, $languageType = null)
    {
        $this->_codeText = $codeText;

        if ($language !== null) $this->_language = $language;
        if ($languageType !== null) $this->_languageType = $languageType;
    }

    /**
     * Returns lines of the code of the colorer
     *
     * @param   string $type
     * @return  string
     */
    protected function _getCodeLines($type = 'xml')
    {
        if ($type == 'xml')
        {
            $codeBody  = str_replace("\r\n", "\n", $this->_codeText);
            $result    = explode("\n", $codeBody);

            $result = $this->_validateTextShift($result);
            /*foreach ($result as &$codeLine)
            {
                $codeLine = str_replace(array('<', '>'), array('&lt;', '&gt;'), $codeLine);
            }*/

            $codeBody  = implode("\n", $result);

            $langugeDefPattern   = array();
            $langugeDefPattern[] = '/<(\/?[\w\:]+)([^>]*)(\/?)>/e';

            $langugeColoringPattern   = array();
            $langugeColoringPattern[] = '$this->_colorizeXML("\\1", "\\2", "\\3")';

            $codeBody = preg_replace($langugeDefPattern, $langugeColoringPattern, $codeBody);

            $result    = explode("\n", $codeBody);
        }
        else
        {
            $codeBody  = str_replace("\r\n", "\n", $this->_codeText);
            $result    = explode("\n", $codeBody);
            $result    = $this->_validateTextShift($result, false);
            foreach ($result as &$codeLine)
            {
                $codeLine = str_replace(array('<', '>'), array('&lt;', '&gt;'), $codeLine);
            }

            $codeBody  = implode("\n", $result);

            $langugeDefPattern   = array();
            $langugeDefPattern[] = '/&lt;(\?php|\?)(\s+)/';
            $langugeDefPattern[] = '/(\s+)(function|class|extends|self)(\s+)/';
            $langugeDefPattern[] = '/(\s+)(const|static|return)(\s+)/';
            $langugeDefPattern[] = '/(\s+)(public|private|protected)(\s+)/';
            $langugeDefPattern[] = '/(\=\s*|\s+throw\s+)(new)(\s+)/';
            $langugeDefPattern[] = '/(\s+)(if|for|while|catch)(\s*\()/';
            $langugeDefPattern[] = '/(\s+)(try)(\s*\{)/';
            $langugeDefPattern[] = '/(\s*)(self)(\:*)/';
            $langugeDefPattern[] = '/([\s\!\:\(\>]+)(\$[\w]+)/';
            $langugeDefPattern[] = '/(\/\/[^\n]+)/';

            $langugeColoringPattern   = array();
            $langugeColoringPattern[] = '<span style=\'color: #f00\'>&lt;\\1</span>\\2';
            $langugeColoringPattern[] = '\\1<span style=\'color: #00f\'>\\2</span>\\3';
            $langugeColoringPattern[] = '\\1<span style=\'color: #00f\'>\\2</span>\\3';
            $langugeColoringPattern[] = '\\1<span style=\'color: #00f\'>\\2</span>\\3';
            $langugeColoringPattern[] = '<span style=\'color: #00f\'>\\1\\2</span>\\3';
            $langugeColoringPattern[] = '\\1<span style=\'color: #00f\'>\\2</span>\\3';
            $langugeColoringPattern[] = '\\1<span style=\'color: #00f\'>\\2</span>\\3';
            $langugeColoringPattern[] = '\\1<span style=\'color: #00f\'>\\2</span>\\3';
            $langugeColoringPattern[] = '\\1<span style=\'color: #660000\'>\\2</span>';
            $langugeColoringPattern[] = '<span style=\'color: #808080\'>\\1</span>';

            $codeBody = preg_replace($langugeDefPattern, $langugeColoringPattern, $codeBody);

            /**
             * Processing multiline comments
             */
            $status = true;
            $currentPosition = 0;
            $codeBodyTemp = '';
            while ($status)
            {
                $startCommentPos = strpos($codeBody, '/*', $currentPosition);
                $endCommentPos   = strpos($codeBody, '*/', $startCommentPos);

                if (($startCommentPos !== false) && ($endCommentPos !== false))
                {
                    $codeBodyTemp .= substr($codeBody, $currentPosition, $startCommentPos - $currentPosition);
                    $commentsLines = explode("\n", substr($codeBody, $startCommentPos, $endCommentPos + 2 - $startCommentPos));
                    $commentsLinesCount = count($commentsLines);
                    for ($i = 0; $i < $commentsLinesCount; $i++)
                    {
                        $codeLineLength  = strlen($commentsLines[$i]);
                        $commentLine     = ltrim($commentsLines[$i]);
                        $codeBodyTemp   .= str_repeat(' ', $codeLineLength - strlen($commentLine)).'<span style=\'color: #808080\'>'.$commentLine.'</span>'.(($i < $commentsLinesCount - 1) ? "\n" : '');
                    }
                    $currentPosition = $endCommentPos + 2;
                }
                else
                {
                    $codeBodyTemp .= substr($codeBody, $currentPosition);
                    $status = false;
                }
            }
            $codeBody = $codeBodyTemp;

            $result    = explode("\n", $codeBody);
            foreach ($result as &$codeLine)
            {
                $codeLineLength  = strlen($codeLine);
                $codeLine        = ltrim($codeLine);
                $trimedLength    = strlen($codeLine);
                if ($trimedLength < $codeLineLength) $codeLine = str_repeat('&nbsp;', $codeLineLength - $trimedLength).$codeLine;
            }
        }

        return $result;
    }

    /**
     * Colorizes XML text
     *
     * @param   string  $startTag
     * @param   string  $attributesText
     * @param   string  $startTagClose
     * @return  string
     */
    protected function _colorizeXML($startTag, $attributesText, $startTagClose)
    {
        $result  = '<span style=\'color: #3F7F7F\'>&lt;'.$startTag.'</span>';
        $result .= preg_replace('/([\w\:]+)\s*\=\s*(\'([^\']*)\'|\"([^\"]*)\")/', '<span style=\'color: #7F007F\'>\\1</span>="<span style=\'color: #2A00FF\'>\\3\\4</span>" ', $attributesText);
        $result .= '<span style=\'color: #3F7F7F\'>'.$startTagClose.'&gt;</span>';

        return $result;
    }

    /**
     * Validates shift of the text with the lowest shift
     *
     * @param   array   $textLines
     * @param   boolean $usenbsp
     * @return  array
     */
    protected function _validateTextShift($textLines, $usenbsp = true)
    {
        $textStats  = array();
        $minShift   = false;
        foreach ($textLines as $lineIndex => $lineContent)
        {
            $lineContent = str_replace("\t", '    ', $lineContent);
            $textStats[$lineIndex]['strlen']      = strlen($lineContent);
            $textStats[$lineIndex]['lineTrimed']  = ltrim($lineContent);
            $textStats[$lineIndex]['trimlen']     = strlen($textStats[$lineIndex]['lineTrimed']);
            $trimSize = $textStats[$lineIndex]['strlen'] - $textStats[$lineIndex]['trimlen'];

            if ($textStats[$lineIndex]['trimlen'] && ($textStats[$lineIndex]['lineTrimed']) && (($minShift === false) || ($minShift > $trimSize)))
            {
                $minShift = $trimSize;
            }
        }

        if ($minShift === false) return $textLines;

        $result = array();
        foreach ($textStats as $index => $lineStats)
        {
            $spaceSize = ($lineStats['strlen'] - $lineStats['trimlen'] - $minShift);
            if ($spaceSize > 0)
            {
                $result[$index] = str_repeat(($usenbsp ? '&nbsp;' : ' '), $spaceSize).$lineStats['lineTrimed'];
            }
            else
            {
                $result[$index] = $lineStats['lineTrimed'];
            }
        }

        /**
         * Fixing empty first || last strings
         */
        $resultSize = count($result);
        if (isset($result[$resultSize - 1]) && (!$result[$resultSize - 1])) unset($result[$resultSize - 1]);
        if (isset($result[0]) && !$result[0]) array_shift($result);

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
    }

    /**
     * Handler of the save to session event
     *
     * @return  string
     * @access  protected
     */
    protected function _saveToSession()
    {
    }

}
