<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Event dispatcher interface
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 97 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 2.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace PHP2\Event;

/**
 * Event dispatcher interface
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: ieventdispatcher.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\Event
 */
interface PHP2_Event_IEventDispatcher
{
    /**
     * Registers event listener for the Object
     *
     * @param   string $type Unique event type
     * @param   string $handlerName Handler method name
     * @param   Object $handlerObject Object which handles current request
     * @access  public
     */
    public function addEventListener($type, $handlerName, &$handlerObject = null);

    /**
     * Dispatches an event into the event flow
     *
     * @param   PHP2_Event_Event $event
     * @return  boolean
     * @access  public
     */
    public function dispatchEvent($event);

    /**
     * Checks whether the PHP2_Event_EventDispatcher object has any listeners registered for a specific type of event
     *
     * @param   string $type Unique event type
     * @return  boolean
     * @access  public
     */
    public function hasEventListener($type);

    /**
     * Removes event listener for the Object
     *
     * @param   string $type Unique event type
     * @param   string $handlerName Handler method name
     * @return  boolean
     * @access  public
     */
    public function removeEventListener($type, $handlerName);

}
