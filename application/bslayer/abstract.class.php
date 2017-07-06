<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains class that implements base Business logic functions
 *
 * @category   Business Model of the Application
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
// namespace Application\BSLayer;

/**
 * Class realizes base Business layer entity
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: abstract.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  Application\BSLayer
 */
abstract class Application_BSLayer_Abstract extends PHP2_System_BSLayer_AbstractDBConnector
{
    /**
     * Application_BSLayer_Abstract class constructor
     *
     * @access  public
     */
    public function __construct()
    {
        /**
         * Initializing parent constructor
         */
        parent::__construct();
    }
}
