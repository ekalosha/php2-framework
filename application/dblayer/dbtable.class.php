<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Class which Implements Database Table Element with default connection
 *
 * PHP version 5
 * @category   Database Classes
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
// namespace Application\DBLayer;

/**
 * Class implements database table operations
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: dbtable.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  Application\DBLayer
 */
abstract class Application_DBLayer_DBTable extends PHP2_Database_DBTable
{
    /**
     * Class constructor
     *
     * @var     string $pkFieldValue
     * @access  public
     */
    public function __construct($pkFieldValue = null)
    {
        /**
         * Inheriting parent constructor
         */
        parent::__construct(DB_MASTER_CONNECTION, $pkFieldValue);
    }

    /**
     * Class destructor. Cleans query resources.
     *
     * @access  public
     */
    public function __destruct()
    {
        parent::__destruct();
    }

}
