<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains panel component
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
 * Panel component
 *
 * Usage in the template:
 *
 * <code>
 *      <php:panel:$objectName
 *          [isolated="true|false"]
 *          [visible="true|false"]
 *      />
 *      </php:panel:$objectName>
 * </code>
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: panel.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\UI\Components\Additional
 */
class PHP2_UI_Components_Additional_Panel extends PHP2_UI_ControlContainer
{
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
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        if (count($this->_attributes))
        {
            return '<div'.$this->_getAttributesString().'>'.parent::_getRenderedContent().'</div>';
        }
        else
        {
            return parent::_getRenderedContent();
        }
    }
}
