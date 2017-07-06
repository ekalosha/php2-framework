<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Messagebox Interface definition
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
// namespace PHP2\UI\Components;

/**
 * Messagebox interface
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: imessagebox.class.php 115 2010-08-12 09:28:36Z eugene $
 * @access   public
 * @package  PHP2\UI\Components
 */
interface PHP2_UI_Components_IMessageBox
{

    /**
     * Adds message to the messagebox and returns UID of the message
     *
     * @return  string message UID
     * @access  public
     */
    public function add($message);

    /**
     * Removes message from the messagebox
     *
     * @param   string $messageUID message UID
     * @return  boolean
     * @access  public
     */
    public function remove($messageUID);

    /**
     * Removes all messages from the messagebox
     *
     * @return  boolean
     * @access  public
     */
    public function clear();
}
