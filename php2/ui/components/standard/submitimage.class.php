<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains submit button component
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 115 $
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
 * Submit button component
 *
 * Dispatch the following events:
 *
 * <code>
 *   PHP2_UI_UIEvent::CLICK
 *   PHP2_UI_UIEvent::INIT
 *   PHP2_UI_UIEvent::BEFORE_RENDER
 *   PHP2_UI_UIEvent::AFTER_RENDER
 *   PHP2_UI_UIEvent::LOAD_SESSION
 *   PHP2_UI_UIEvent::SAVE_SESSION
 * </code>
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: submitimage.class.php 115 2010-08-12 09:28:36Z eugene $
 * @access   public
 * @package  PHP2\UI\Components\Standard
 */
class PHP2_UI_Components_Standard_SubmitImage extends PHP2_UI_Control
{
    /**
     * Button text
     *
     * @var     string
     * @access  public
     */
    public $label;

    /**
     * Initialize control
     *
     * @return  void
     * @access  protected
     */
    protected function _init()
    {
        parent::_init();

        if (($label = $this->_extractAttribute('value')) !== null) $this->label = $label;
        if (($label = $this->_extractAttribute('label')) !== null) $this->label = $label;

        /**
         * Checking click event
         */
        if (isset($_REQUEST[$this->getName()]))
        {
            $this->owner->addEventListener(PHP2_UI_UIEvent::CREATION_COMPLETE, '_initClickHandler', $this);
        }
    }

    /**
     * Initialize click event
     *
     * @return  void
     * @access  protected
     */
    protected function _initClickHandler()
    {
        $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::CLICK));
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        return '<input type="image" name="'.$this->getName().'" value="'.$this->label.'"'.$this->_getAttributesString().' />';
    }
}
