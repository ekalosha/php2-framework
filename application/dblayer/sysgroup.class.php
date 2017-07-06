<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains class, that implemented database entity `SysGroup`
 *
 * PHP version 5
 * @category   Database Model of the Application
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2009 by "SolArt xIT Ltd."
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
 * This file is Automatically generated by PHP2 Code Generator.
 * Generation date: 2009-07-21, 15:14:14
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 */

/**
 * Class realizes database record of entity `SysGroup`
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: sysgroup.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  Application\DBLayer
 */
class Application_DBLayer_SysGroup extends Application_DBLayer_DBTable
{
    /**
     * Current Table Name
     */
    const TABLE_NAME = 'SysGroup';

    /**
     * Automaticaly generated property for field 'ID'
     *
     * @var      integer
     * @access   public
     */
    public $ID;

    /**
     * Automaticaly generated property for field 'GroupName'
     *
     * @var      string
     * @access   public
     */
    public $GroupName;

    /**
     * Automaticaly generated property for field 'GroupDescription'
     *
     * @var      string
     * @access   public
     */
    public $GroupDescription;

    /**
     * Automaticaly generated property for field 'EnterPoint'
     *
     * @var      string
     * @access   public
     */
    public $EnterPoint;

    /**
     * Automaticaly generated property for field 'EnterSSL'
     *
     * @var      string
     * @access   public
     */
    public $EnterSSL;

    /**
     * Class constructor
     *
     * @param   integer $pkFieldValue Current record Unique ID
     * @access  public
     */
    public function __construct($pkFieldValue = null)
    {
        $this->_tableName = self::TABLE_NAME;

        // --- Calling Parent Constructor --- //
        parent::__construct($pkFieldValue);
    }

}
