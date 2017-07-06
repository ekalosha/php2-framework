<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base Admin UI application class
 *
 * PHP version 5
 * @category   UI Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2009 by "SolArt xIT Ltd."
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 96 $
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
 * Base Admin UI page application class
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: page.class.php 96 2009-08-19 08:43:26Z eugene $
 * @access   public
 * @package  Application\UI\Admin
 */
class Application_UI_Admin_Page extends Application_UI_SSLPage
{
    /**
     * Init method
     *
     * @return  string
     * @access  protected
     */
    protected function _init()
    {
        /**
         * Checking is current User logged
         */
        if (!Application_BSLayer_UserSecurity::getInstance()->isUserLogged())
        {
            PHP2_System_Response::getInstance()->urlRedirect('/index.php');
        }

        /**
         * Inheriting base SSL functionality
         */
        parent::_init();
    }

}
