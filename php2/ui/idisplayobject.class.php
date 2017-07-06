<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains control Interface definition
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
// namespace PHP2\UI;

/**
 * Controls interface
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: idisplayobject.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\UI
 */
interface PHP2_UI_IDisplayObject
{
    /**
     * Returns name of current control
     *
     * @return  string
     * @access  public
     */
    public function getName();

    /**
     * Renders control
     *
     * @return  string
     * @access  public
     */
    public function render();

}
