<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base Class for system Events
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
// namespace PHP2\Event;

/**
 * Base Class for system Events
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: event.class.php 98 2009-08-19 17:15:05Z eugene $
 * @access   public
 * @package  PHP2\Event
 */
class PHP2_Event_Event
{
    /**
     * Base event types
     */
    const BEFORE_LOAD  = 'beforeLoad';
    const CANCEL       = 'cancel';
    const CHANGE       = 'change';
    const CLICK        = 'click';
    const COMPLETE     = 'complete';
    const CONNECT      = 'connect';
    const CREATE       = 'create';
    const DELETE       = 'delete';
    const EDIT         = 'edit';
    const INIT         = 'init';
    const LOAD         = 'load';
    const SELECT       = 'select';
    const SUBMIT       = 'submit';
    const REDIRECT     = 'redirect';
    const REQUEST      = 'request';

    /**
     * Unique Event type
     *
     * @var     string
     * @access  public
     */
    public $type;

    /**
     * Object which dispatched current event
     *
     * @var     PHP2_Event_EventDispatcher
     * @access  public
     */
    public $target;

    /**
     * Data sended with current event
     *
     * @var     mixed
     * @access  public
     */
    public $data;

    /**
     * PHP2_Event_Event class constructor
     *
     * @param   string $type Unique event type
     * @param   mixed  $data
     * @access  public
     */
    public function __construct($type, $data = null)
    {
        $this->type = $type;

        $this->setData($data);
    }

    /**
     * Set event data
     *
     * @param   mixed $data
     * @return  void
     * @access  public
     */
    public function setData($data = null)
    {
        $this->data = $data;
    }

}
