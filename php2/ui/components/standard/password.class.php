<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains assword text component
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
// namespace PHP2\UI\Components\Standard;

/**
 * Password text component
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: password.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\UI\Components\Standard
 */
class PHP2_UI_Components_Standard_Password extends PHP2_UI_Control
{
    /**
     * Edit text
     *
     * @var     string
     * @access  public
     */
    public $text;

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
         * Processing request
         *
         * @todo  add request validation
         */
        if (isset($_REQUEST[$this->getName()]))
        {
            $this->text = $_REQUEST[$this->getName()];
        }
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        return '<input type="password" name="'.$this->getName().'" value="'.PHP2_Utils_String::validateXMLText($this->text).'"'.$this->_getAttributesString().' />';
    }

}
