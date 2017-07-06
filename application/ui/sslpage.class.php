<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base UI SSL application class
 *
 * PHP version 5
 * @category   UI Classes
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
// namespace Application\UI

/**
 * Base UI SSL page application class
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: sslpage.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  Application\UI
 */
class Application_UI_SSLPage extends Application_UI_Page
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
         * Redirecting to SSL page
         */
        if (PHP2_System_Request::getInstance()->isSSLAllowed() && !PHP2_System_Request::getInstance()->isSSL())
        {
            PHP2_System_Response::getInstance()->urlRedirect('', $_GET, true);
        }

        parent::_init();
    }

}
