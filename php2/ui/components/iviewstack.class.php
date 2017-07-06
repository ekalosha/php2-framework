<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains View stack Interface definition
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 114 $
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
 * View stack interface
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: iviewstack.class.php 114 2010-05-21 15:32:29Z eugene $
 * @access   public
 * @package  PHP2\UI\Components
 */
interface PHP2_UI_Components_IViewStack
{
    /**
     * Returns current state name
     *
     * @return  string
     */
    public function getState();

    /**
     * Sets active state
     *
     * @param   string $stateName
     * @return  boolean
     */
    public function setState($stateName);
}
