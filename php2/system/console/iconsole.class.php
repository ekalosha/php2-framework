<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains PHP2 Console Interface
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
 * PHP2 Console Interface
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: iconsole.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\System\Console
 */
interface PHP2_System_Console_IConsole
{
    /**
     * Adds message to the console
     *
     * @param   string $message
     * @return  string
     * @access  public
     */
    public function write($message);

    /**
     * Writes Line to the console
     *
     * @param   string $message
     * @return  string
     * @access  public
     */
    public function writeln($message = '');

    /**
     * Writes Delimiter Line to the console
     *
     * @param   string $delimChar
     * @param   string $lineLength
     * @return  string
     * @access  public
     */
    public function writeDelimiterLine($delimChar = '-', $lineLength = null);

    /**
     * Writes Error to the console
     *
     * @param   string $message
     * @return  string
     * @access  public
     */
    public function writeError($message);

    /**
     * Writes Error line to the console
     *
     * @param   string $message
     * @return  string
     * @access  public
     */
    public function writeErrorLine($message = '');

}
