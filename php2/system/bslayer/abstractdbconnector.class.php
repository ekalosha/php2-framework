<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base Business Layer class
 *
 * PHP version 5
 * @category   Business Model of the Application
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
// namespace PHP2\System\BSLayer;

/**
 * Base Business Layer class
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: abstractdbconnector.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\System\BSLayer
 */
class PHP2_System_BSLayer_AbstractDBConnector
{
    /**
     * Current read connection ID. This is random connection to one of the registered Slave servers.
     *
     * @var     string
     * @access  protected
     */
    protected $_slaveConnectionId;

    /**
     * Current write connection ID
     *
     * @var     string
     * @access  protected
     */
    protected $_masterConnectionId;

    /**
     * PHP2_System_BSLayer class constructor. Initializes default Read and Write DB connections.
     *
     * @access  public
     */
    public function __construct()
    {
        /**
         * Initializing Read and Write connections
         */
        $this->_masterConnectionId  = PHP2_Database_ConnectionsPool::getInstance()->getConnection(null, PHP2_Database_ConnectionsPool::CONNECTION_TYPE_WRITE);
        $this->_slaveConnectionId   = PHP2_Database_ConnectionsPool::getInstance()->getConnection(null, PHP2_Database_ConnectionsPool::CONNECTION_TYPE_READ, true);
    }

}
