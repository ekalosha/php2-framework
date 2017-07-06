<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Base class for all UI applications
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 101 $
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
 * Base class for all UI applications
 *
 * This class dispatches the following events:
 * <code>
 *   PHP2_UI_UIEvent::INIT
 *   PHP2_UI_UIEvent::INIT_LISTENERS
 *   PHP2_UI_UIEvent::CREATION_COMPLETE
 *   PHP2_UI_UIEvent::LOAD
 *   PHP2_UI_UIEvent::BEFORE_RENDER
 *   PHP2_UI_UIEvent::AFTER_RENDER
 *   PHP2_UI_UIEvent::LOAD_SESSION
 *   PHP2_UI_UIEvent::SAVE_SESSION
 * </code>
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: application.class.php 101 2009-11-12 14:43:02Z eugene $
 * @access   public
 * @package  PHP2\UI
 */
abstract class PHP2_UI_Application extends PHP2_UI_DisplayObjectContainer
{
    /**
     * Page changed flag
     *
     * @var  indicates is page changed
     */
    protected  $_pageChanged = false;

    /**
     * PHP2_UI_Application class constructor
     *
     * @access  public
     */
    public function __construct()
    {
        /**
         * Set profiler translation mode to HTML
         */
        PHP2_System_Profiler::getInstance()->setTranslationMode(PHP2_System_Profiler::MODE_HTML);

        PHP2_System_Profiler::setStartBreakpoint('Page Load', 'Init Page');

        /**
         * Initializing component definition
         */
        $this->_name = str_replace(BASE_PATH, '', $_SERVER['SCRIPT_FILENAME']);

        /**
         * Checking page logic
         */
        $previousPage = (isset($_SESSION['__UI']['CURRENT_PAGE'])) ? $_SESSION['__UI']['CURRENT_PAGE'] : false;
        $currentPage  = $this->_name;
        if ($previousPage != $currentPage) $this->_pageChanged = true;
        $_SESSION['__UI']['CURRENT_PAGE'] = $currentPage;

        /**
         * Calling parent constructor
         */
        parent::__construct();

        PHP2_System_Profiler::setEndBreakpoint('Page Load');
    }

    /**
     * PHP2_UI_Application class destructor
     *
     * @access  public
     */
    public function __destruct()
    {
        /**
         * Calling parent destructor
         */
        parent::__destruct();

        /**
         * Run profiling info
         */
        if (PHP2_System_Profiler::getInstance()->getEnabled())
        {
            $timeStart          = microtime(true);
            $profilingHTML      = PHP2_System_Profiler::getInstance()->__toHTML();
            $replacedVariables  = array('totalTime' => (microtime(true) - $GLOBALS['APPLICATION_START_TIME']), 'profilingTime' => (microtime(true) - $timeStart));

            $traceContent = preg_replace('/{{(\w+)}}/e', 'isset(\$replacedVariables["\1"]) ? \$replacedVariables["\1"] : \'\'', $profilingHTML);
            if (ob_get_level())
            {
                $bufferContent = ob_get_contents();
                ob_end_clean();

                echo str_ireplace('</body>', $traceContent.'</body>', $bufferContent);
            }
            else
            {
                echo $traceContent;
            }
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
         * Initializing event handlers
         */
        $this->addEventListener(PHP2_UI_UIEvent::CREATION_COMPLETE, '_creationComplete');
        $this->addEventListener(PHP2_UI_UIEvent::LOAD, '_load');
    }

    /**
     * Creation complete event handler.
     *
     * You need to set event listeners for your controls in this method.
     *
     * @return  void
     * @access  protected
     */
    protected function _creationComplete()
    {
    }

    /**
     * Loads session data
     *
     * @return  string
     * @access  protected
     */
    protected function _loadSessionHandler()
    {
        if (!isset($_SESSION['__UI_APPLICATION'][$this->getName()])) $_SESSION['__UI_APPLICATION'][$this->getName()] = array();
        $this->sessionData = &$_SESSION['__UI_APPLICATION'][$this->getName()];

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
         * Saving session data to the parent session
         */
        if ($this->sessionData && count($this->sessionData))
        {
            $_SESSION['__UI_APPLICATION'][$this->getName()] = &$this->sessionData;
        }
        else
        {
            unset($_SESSION['__UI_APPLICATION'][$this->getName()]);
        }
    }

    /**
     * Runs UI application
     *
     * @param   string $fileName Template filename
     * @return  void
     * @access  public
     */
    public function run($fileName = null)
    {
        /**
         * Checking default template filename
         */
        if (!$fileName && !$this->_template)
        {
            $baseName = str_replace(array(ROOT_PATH, '.php'), array('', ''), $_SERVER['SCRIPT_FILENAME']);
            $fileName = BASE_PATH.'ui/'.$baseName.'.tpl';
        }

        /**
         * Loading template
         */
        if ($fileName) $this->loadTemplate($fileName);

        /**
         * Parsing template
         */
        PHP2_System_Profiler::setStartBreakpoint('Page Load', 'Parse Template');
        $this->parseTemplate();
        PHP2_System_Profiler::setEndBreakpoint('Page Load');

        /**
         * Dispatching UI application Load event
         */
        if ($this->_pageChanged) $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::PAGE_CHANGED));
        $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::LOAD));

        /**
         * Rendering content
         */
        echo $this->render();
    }

}
