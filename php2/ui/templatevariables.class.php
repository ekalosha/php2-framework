<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains class container for template variables
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
 * Class implements container for template variables
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: templatevariables.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\UI
 */
class PHP2_UI_TemplateVariables
{
    /**
     * Template variables array
     *
     * @var      array
     * @access   private
     */
    private $_templateVariables = array();

    /**
     * Class constructor
     *
     * @var     array $variablesList Array of the template variables
     * @access  public
     */
    public function __construct($variablesList = false)
    {
        if ($variablesList) $this->setTemplateVariables($variablesList);
    }

    /**
     * Overloads access to the object properties
     *
     * @param   string $objectName Template element Object name
     * @return  string
     * @access  public
     */
    public function __get($variableName)
    {
        return (isset($this->_templateVariables[$variableName]) ? $this->_templateVariables[$variableName] : null);
    }

    /**
     * Overloads access to the object properties
     *
     * @param   string $variableName  Template variable name
     * @param   string $variableValue Template variable value
     * @access  public
     */
    public function __set($variableName, $variableValue)
    {
        $this->_templateVariables[$variableName] = $variableValue;
    }

    /**
     * Assign variables list
     *
     * @param   array $variablesList  Template variables list
     * @access  public
     */
    public function setTemplateVariables($variablesList = null)
    {
        if (is_array($variablesList)) $this->_templateVariables = $this->_templateVariables + $variablesList;
    }

    /**
     * Returns array of template variables
     *
     * @return  array
     * @access  public
     */
    public function &getTemplateVariables()
    {
        return $this->_templateVariables;
    }

}
