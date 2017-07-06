<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base class for all Web Sevice Commands
 *
 * PHP version 5
 * @category   Library Classes
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
// namespace PHP2\WebService;

/**
 * Base Class for all Server Commands
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: abstractservercommand.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\WebService
 */
abstract class PHP2_WebService_AbstractServerCommand
{
    /**
     * Default command Type
     */
    const DEFAULT_COMMAND = 'Response';

    /**
     * Command UID
     *
     * @var     string
     * @access  public
     */
    public $UID;

    /**
     * Command Type
     *
     * @var     string
     * @access  public
     */
    public $Type;

    /**
     * Command Data
     *
     * @var     mixed
     * @access  public
     */
    public $Data;

    /**
     * Command constructor. Initialized default command
     *
     * @param   string $type Command Type
     * @param   mixed  $data Command Data
     * @access  pulic
     */
    public function __construct($type = null, $data = null)
    {
        $this->Type = $type ? $type : self::DEFAULT_COMMAND;
        $this->Data = $data;
        $this->UID  = md5(uniqid(rand(), true));
    }

}
