<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base UI application class
 *
 * PHP version 5
 * @category   UI Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
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
// namespace Application\UI

/**
 * Base UI page application class
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: page.class.php 98 2009-08-19 17:15:05Z eugene $
 * @access   public
 * @package  Application\UI
 */
class Application_UI_Page extends PHP2_UI_Application
{
    /**
     * Class constructor
     *
     * @access  public
     */
    public function __construct()
    {
        /**
         * Adding fix for "Save To Session" issue in case of redirect on the page
         */
        PHP2_System_Response::getInstance()->addEventListener(PHP2_Event_Event::REDIRECT, 'dispatchSaveSessionEvent', $this);

        /**
         * Inheriting parent constructor
         */
        parent::__construct();

        /**
         * Defining base template variables
         */
        $this->templateVariables->selfUrl    = PHP2_System_Response::getInstance()->getUrl();
        $this->templateVariables->selfUrlEncoded  = urlencode(PHP2_System_Response::getInstance()->getUrl());
        $this->templateVariables->rootUrl    = PHP2_System_Response::getInstance()->getUrlPath();
        $this->templateVariables->staticUrl  = PHP2_System_Response::getInstance()->getStaticUrlPath();
        $this->templateVariables->noRevCache = defined('APPLICATION_REVISION') ? '?rev='.APPLICATION_REVISION : '';

        /**
         * System variables
         */
        $this->templateVariables->systemRootUrl    = ROOT_URL;
        $this->templateVariables->systemStaticUrl  = STATIC_ROOT_URL;
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        PHP2_System_Profiler::setStartBreakpoint('Page Load', 'Render Page');
        $result = str_replace(array('~~/', '~/'), array(PHP2_System_Response::getInstance()->getStaticUrlPath('/'), PHP2_System_Response::getInstance()->getUrlPath('/')), parent::_getRenderedContent());
        PHP2_System_Profiler::setEndBreakpoint('Page Load');

        return $result;
    }

}
