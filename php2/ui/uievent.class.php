<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base Class for UI Events
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
 * UI Events class
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: uievent.class.php 101 2009-11-12 14:43:02Z eugene $
 * @access   public
 * @package  PHP2\UI
 */
class PHP2_UI_UIEvent extends PHP2_Event_Event
{
    /**
     * UI event types
     */
    const AFTER_RENDER       = 'afterRender';
    const BEFORE_RENDER      = 'beforeRender';
    const CREATION_COMPLETE  = 'creationComplete';
    const FIRST_LOAD         = 'firstLoad';
    const INIT_LISTENERS     = 'initListeners';
    const INIT_STATE         = 'initState';
    const LOAD               = 'load';
    const LOAD_SESSION       = 'loadSession';
    const PAGE_CHANGED       = 'pageChanged';
    const SAVE_SESSION       = 'saveSession';
    const SORT               = 'sort';
}
