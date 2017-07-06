<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Interface for VO convertable Objects
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
// namespace PHP2\WebService\VO;

/**
 * Interface for VO convertable Objects
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: iconvertable.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\WebService\VO
 */
interface PHP2_WebService_VO_IConvertable
{
    /**
     * Returns this object in Value Object compatible Way
     *
     * @return  array
     * @access  public
     */
    public function getValueObject();
}
