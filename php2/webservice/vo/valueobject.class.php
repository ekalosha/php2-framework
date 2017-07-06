<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base class for Value Objects
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
 * Base Class for Value Objects
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: valueobject.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\WebService\VO
 * @abstract
 */
abstract class PHP2_WebService_VO_ValueObject
{
    /**
     * Value Object constructor
     *
     * @param   mixed $valueObjectSource
     * @access  pulic
     */
    public function __construct($valueObjectSource = null)
    {
        /**
         * Assigning Default Value of Object
         */
        if ($valueObjectSource) $this->assign($valueObjectSource);
    }

    /**
     * Assign some object to the current Value Object
     *
     * @param   mixed $assignedObject
     * @return  boolean
     */
    public function assign($assignedObject)
    {
        if (!$assignedObject) return false;

        /**
         * Checking is this Object VO Convertable
         */
        if (is_object($assignedObject) && ($assignedObject instanceof PHP2_WebService_VO_IConvertable)) $assignedObject = $assignedObject->getValueObject();

        /**
         * Assigning parameters from Object or Array
         */
        if (($isArray = is_array($assignedObject)) || ($isObject = is_object($assignedObject)))
        {
            foreach ($this as $fieldName => &$fieldValue)
            {
                if ($isArray)
                {
                    if (isset($assignedObject[$fieldName])) $this->{$fieldName} = $assignedObject[$fieldName];
                }
                elseif ($isObject)
                {
                    if (isset($assignedObject->{$fieldName})) $this->{$fieldName} = $assignedObject->{$fieldName};
                }
            }
        }

        return false;
    }

    /**
     * Returns public class name for other applications
     *
     * @return  string
     * @access  public
     */
    public function getPublicClassName()
    {
        $className = get_class($this);
        $nameParts = explode('_', $className);

        return isset($nameParts[count($nameParts) - 1]) ? $nameParts[count($nameParts) - 1] : $className;
    }

    /**
     * Alias of the getPublicClassName() method
     *
     * @return  string
     * @access  public
     */
    public function getASClassName()
    {
        return $this->getPublicClassName().'VO';
    }

}
