<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains UI class for Users manager
 *
 * PHP version 5
 * @category   UI Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2009 by "SolArt xIT Ltd."
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 98 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 3.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace Application\UI\Admin

/**
 * UI class for Users Manager
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: index.class.php 98 2009-08-19 17:15:05Z eugene $
 * @access   public
 * @package  Application\UI\Admin
 */
class Application_UI_Admin_Index extends Application_UI_Admin_Page
{

    /**
     * Class constructor
     *
     * @access  public
     */
    public function __construct()
    {
        /**
         * Inheriting parent constructor
         */
        parent::__construct();
    }

    /**
     * Creation complete event handler.
     *
     * You need to set event listeners for your controls in this method.
     *
     * @return  void
     * @access  protected
     */
    protected function _creationComplete()
    {
        parent::_creationComplete();
    }

    /**
     * Page load event handler.
     *
     * You need to process all page data-related logic in this method.
     *
     * @return  void
     * @access  protected
     */
    protected function _load()
    {
        $userDetails = Application_BSLayer_UserSecurity::getInstance()->getUserDetails();

        $this->templateVariables->userName     = $userDetails->LastName.' '.$userDetails->FirstName;
        $this->templateVariables->lastLogin    = PHP2_Utils_DateTime::getInstance(Application_BSLayer_UserSecurity::getInstance()->getLastLoginTime())->getDate('Y-m-d, H:i:s');
        $this->templateVariables->loginsCount  = $userDetails->LoginsCount;
    }

}
