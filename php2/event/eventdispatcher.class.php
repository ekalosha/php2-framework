<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base Class for all Event-based entities
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
 * Base Class for all Event-based entities
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: eventdispatcher.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\Event
 */
abstract class PHP2_Event_EventDispatcher
{
    /**
     * Default event handler Owner
     *
     * @var     Object
     * @access  protected
     */
    protected $_defaultEventHandler;

    /**
     * List of events listeners
     *
     * @var     array
     * @access  protected
     */
    protected $_eventListeners;

    /**
     * PHP2_Event_EventDispatcher class constructor
     *
     * @access  public
     */
    public function __construct()
    {
        /**
         * Set default events handler as current object
         */
        if (!$this->_defaultEventHandler) $this->_defaultEventHandler = &$this;
    }

    /**
     * Registers event listener for the Object
     *
     * @param   string $type Unique event type
     * @param   string $handlerName Handler method name
     * @param   Object $handlerObject Object which handles current request
     * @access  public
     */
    public function addEventListener($type, $handlerName, &$handlerObject = null)
    {
        /**
         * Creating handler details
         */
        $handlerDetails['handlerName']    = $handlerName;
        $handlerDetails['handlerObject']  = $handlerObject;

        /**
         * Find handler index
         */
        $handlerIndex = ($handlerObject && method_exists($handlerObject, 'getName')) ? $handlerObject->getName().'::'.$handlerName : $handlerName;

        /**
         * Adding event listener to the listeners list.
         * Note: listeners should have unique names.
         */
        $this->_eventListeners[$type][$handlerIndex] = $handlerDetails;
    }

    /**
     * Dispatches an event into the event flow
     *
     * @param   PHP2_Event_Event $event
     * @return  boolean
     * @access  public
     */
    public function dispatchEvent($event)
    {
        if (isset($this->_eventListeners[$event->type]) && is_array($this->_eventListeners[$event->type]))
        {
            /**
             * Set event target object
             */
            $event->target = &$this;

            /**
             * Calling handlers for all registered listeners
             */
            foreach ($this->_eventListeners[$event->type] as $handlerDetails)
            {
                $handlerObject = $handlerDetails['handlerObject'] ? $handlerDetails['handlerObject'] : $this->_defaultEventHandler;
                if (method_exists($handlerObject, $handlerDetails['handlerName']))
                {
                    $handlerObject->{$handlerDetails['handlerName']}($event);
                }
                elseif (method_exists($this, $handlerDetails['handlerName']))
                {
                    $this->{$handlerDetails['handlerName']}($event);
                }
            }

        }
    }

    /**
     * Checks whether the PHP2_Event_EventDispatcher object has any listeners registered for a specific type of event
     *
     * @param   string $type Unique event type
     * @return  boolean
     * @access  public
     */
    public function hasEventListener($type)
    {
        return isset($this->_eventListeners[$type]) && is_array($this->_eventListeners[$type]) && count($this->_eventListeners[$type]);
    }

    /**
     * Removes event listener for the Object
     *
     * @param   string $type Unique event type
     * @param   string $handlerName Handler method name
     * @return  boolean
     * @access  public
     */
    public function removeEventListener($type, $handlerName)
    {
        /**
         * Removing event listener
         */
        if (isset($this->_eventListeners[$type]))
        {
            /**
             * Find handler index
             */
            $handlerIndex = ($handlerObject && method_exists($handlerObject, 'getName')) ? $handlerObject->getName().'::'.$handlerName : $handlerName;

            unset($this->_eventListeners[$type][$handlerIndex]);

            /**
             * Checking is there are other listeners exists for the event type
             */
            if (!count($this->_eventListeners[$type])) unset($this->_eventListeners[$type]);

            return true;
        }

        return false;
    }

}
