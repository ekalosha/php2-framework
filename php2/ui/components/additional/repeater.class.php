<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains repeater component
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
// namespace PHP2\UI\Components\Additional;

/**
 * Repeater component
 *
 * Usage in the template code:
 *
 * <code>
 *      <php:repeater:$objectName>
 *          [Some content]
 *          <block:$blockName>
 *              [Some content]
 *              <block:$subBlockName>
 *                  [Some content]
 *              </block:$subBlockName>
 *              [Some content]
 *          </block:$blockName>
 *          [Some content]
 *      </php:repeater:$objectName>
 * </code>
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: repeater.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\UI\Components\Additional
 */
class PHP2_UI_Components_Additional_Repeater extends PHP2_UI_Control implements ArrayAccess
{
    /**
     * Blocks from parsed template
     *
     * @var      PHP2_UI_RBTEngine
     * @access   protected
     */
    protected $_blocks;

    /**
     * Elements array for replace in template
     *
     * @var      array
     * @access   public
     * @see      parse()
     */
    public $row = null;

    /**
     * Class constructor
     *
     * @param   PHP2_UI_ControlDefinition $controlDefinition control definition Object
     * @access  public
     */
    public function __construct($controlDefinition = null)
    {
        /**
         * Calling parent constructor
         */
        parent::__construct($controlDefinition);

        /**
         * Initializing template objects
         */
        $this->_blocks = new PHP2_UI_RBTEngine($this->getName().'_Block');
        $this->_blocks->loadFromTemplate($controlDefinition->content);
        $this->row     = &$this->_blocks->row;
    }

    /**
     * Replaces template with specified elements
     *
     * @return  bool
     * @access  public
     */
    public function replace()
    {
        if ($this->row  != $this->_blocks->row)
        {
            foreach ($this->row as $variableName => &$variableValue) $this->_blocks->row[$variableName] = $variableValue;
        }

        $this->_blocks->replace();

        return false;
    }

    /**
     * Alias for replace() function
     *
     * @return  bool
     * @access  public
     * @see     replace()
     */
    public function parse()
    {
        return $this->replace();
    }

     /**
     * Clears repeater output
     *
     * @access public
     * @see    parse()
     */
    public function clear()
    {
        $this->_blocks->clearOutput();
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        return $this->_blocks->renderOutput();
    }

    /**
     * Overloads access to the object properties(blocks)
     *
     * @param   string  $propName property name, by default has type PHP2_UI_RBTEngine
     * @return  PHP2_UI_RBTEngine
     * @access  public
     */
    public function __get($propName)
    {
        /**
         * Assigning block from the Template Engine objest
         */
        if (!isset($this->{$propName}) && isset($this->_blocks->{$propName}))
        {
            $this->{$propName}  = &$this->_blocks->{$propName};
        }
        else
        {
            $this->{$propName} = new PHP2_UI_RBTEngine();
        }

        return $this->{$propName};
    }

    /**
     * Overloads access to the object properties(attributes)
     *
     * @param   string $blockName Block name
     * @param   string $blockObject
     * @access  public
     */
    public function __set($blockName, $blockObject)
    {
        $this->{$blockName} = $blockObject;
    }

    /**
     * Checks is offset exists in the template variables.
     * Implementation of the ArrayAccess interface from the SPL.
     *
     * As the result of implementation of ArrayAccess interface from SPL we can use objects of current class as arrays.
     * After implementation of offsetExists method you can use isset() method to check is attribute exists in the object.
     * For example:
     * <code>
     *    if (isset($tplObject['variableName'])) ...
     * </code>
     *
     * @param   string $offset
     * @return  boolean
     */
    public function offsetExists($offset)
    {
        return $this->_blocks->offsetExists($offset);
    }

    /**
     * Returns variable.
     * Implementation of the ArrayAccess interface from the SPL.
     *
     * As the result of implementation of ArrayAccess interface from SPL we can use objects of current class as arrays.
     * After implementation of offsetGet method you can use [] modifier to get attribute.
     * For example:
     * <code>
     *    $value = $tplObject['variableName'];
     * </code>
     *
     * @param   string $offset
     * @return  string
     */
    public function offsetGet($offset)
    {
        return $this->_blocks->offsetGet($offset);
    }

    /**
     * Sets template variable.
     * Implementation of the ArrayAccess interface from the SPL.
     *
     * As the result of implementation of ArrayAccess interface from SPL we can use objects of current class as arrays.
     * After implementation of offsetSet method you can use [] modifier to set attribute.
     * For example:
     * <code>
     *    $tplObject['variableName'] = $value;
     * </code>
     *
     * @param   string $offset
     * @param   string $value
     * @return  string
     */
    public function offsetSet($offset, $value)
    {
        return $this->_blocks->offsetSet($offset, $value);
    }

    /**
     * Unsets template variable.
     * Implementation of the ArrayAccess interface from the SPL.
     *
     * As the result of implementation of ArrayAccess interface from SPL we can use objects of current class as arrays.
     * After implementation of offsetUnset method you can use unset() method to unset attribute of the object.
     * For example:
     * <code>
     *    unset($tplObject['variableName']);
     * </code>
     *
     * @param   string $offset
     * @return  boolean
     */
    public function offsetUnset($offset)
    {
        return $this->_blocks->offsetUnset($offset);
    }

}
