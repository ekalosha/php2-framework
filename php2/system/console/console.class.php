<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains PHP2 Base PHP2 Console Class
 *
 * PHP version 5
 * @category   System Classes
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
// namespace PHP2\System\Console;

/**
 * Base PHP2 Console Class
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: console.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\System\Console
 */
class PHP2_System_Console_Console implements PHP2_System_Console_IConsole
{
    /**
     * Console Mode Parameter
     *
     * @var   string
     */
    protected $_consoleMode;

    /**
     * Console History
     *
     * @var   string
     */
    protected $_history;

    /**
     * Flag shows to save History or Not
     *
     * @var   boolean
     */
    protected $_saveHistoryFlag = false;

    /**
     * Quiet mode Flag
     *
     * @var   boolean
     */
    protected $_quietMode = false;

    /**
     * Class Constructor.
     *
     * @access public
     */
    public function __construct()
    {
        $this->_consoleMode = 'cli';
    }

    /**
     * Returns console history
     *
     * @return  string
     * @access  public
     */
    public function getHistory()
    {
        return $this->_history;
    }

    /**
     * Set/Unset save history mode
     *
     * @param   boolean $isSaveHistory
     * @return  string
     * @access  public
     */
    public function setSaveHistoryMode($isSaveHistory = true)
    {
        $this->_saveHistoryFlag = (boolean) $isSaveHistory;
    }

    /**
     * Set/Unset quiet mode
     *
     * @param   boolean $quietMode
     * @return  string
     * @access  public
     */
    public function setQuietMode($quietMode = true)
    {
        $this->_quietMode = (boolean) $quietMode;
    }

    /**
     * Adds message to the console
     *
     * @param   string $message
     * @return  string
     * @access  public
     */
    public function write($message)
    {
        /**
         * Saving History
         */
        if ($this->_saveHistoryFlag) $this->_history .= $message;

        /**
         * Checking Quiet Mode
         */
        if ($this->_quietMode) return true;

        /**
         * Flushing all previous buffers
         */
        while (ob_get_level()) ob_end_flush();

        /**
         * Outputting message to the console
         */
        echo $message;
        flush();
    }

    /**
     * Writes Line to the console
     *
     * @param   string $message
     * @return  string
     * @access  public
     */
    public function writeln($message = '')
    {
        $this->write($message."\n");
    }

    /**
     * Writes Delimiter Line to the console
     *
     * @param   string $delimChar
     * @param   string $lineLength
     * @return  string
     * @access  public
     */
    public function writeDelimiterLine($delimChar = '-', $lineLength = null)
    {
        $this->writeln('---------- ---------- ---------- ---------- ---------- ---------- ----------');
    }

    /**
     * Writes Error to the console
     *
     * @param   string $message
     * @return  string
     * @access  public
     */
    public function writeError($message)
    {
        $this->write('Error: '.$message);
        exit();
    }

    /**
     * Writes Error line to the console
     *
     * @param   string $message
     * @return  string
     * @access  public
     */
    public function writeErrorLine($message = '')
    {
        $this->writeError($message."\n");
    }

}
