<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains UI class for login page of the site
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
 * UI class for login page of the site
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: login.class.php 98 2009-08-19 17:15:05Z eugene $
 * @access   public
 * @package  Application\UI
 */
class Application_UI_Login extends Application_UI_SSLPage
{

    // {{{ Begin:Published

    /**
     * Automatically generated Published Block, which Contains Controls from Template.
     * Generation time: 2009-07-24, 18:13:42;
     *
     * Warning:
     *
     *     Do not Remove this block from template manually.
     *     If you want to remove this block use commandline script loaduicomponentsdefinition.phpcli
     *     with flag -r.
     *
     * Example:
     *
     *     loaduicomponentsdefinition.phpcli --page-class-file="page.class.file.php" -r
     *
     * @author Eugene A. Kalosha <ekalosha@gmail.com>
     */


    /**
     * Automatically generated Published field for 'panel' control
     *
     * @var      PHP2_UI_Components_Additional_Panel
     * @access   public
     */
    public $pnlHeader;

    /**
     * Automatically generated Published field for 'panel' control
     *
     * @var      PHP2_UI_Components_Additional_Panel
     * @access   public
     */
    public $pnlLeft;

    /**
     * Automatically generated Published field for 'messagebox' control
     *
     * @var      PHP2_UI_Components_Standard_MessageBox
     * @access   public
     */
    public $pageMessages;

    /**
     * Automatically generated Published field for 'edit' control
     *
     * @var      PHP2_UI_Components_Standard_Edit
     * @access   public
     */
    public $txtLogin;

    /**
     * Automatically generated Published field for 'password' control
     *
     * @var      PHP2_UI_Components_Standard_Password
     * @access   public
     */
    public $txtPassword;

    /**
     * Automatically generated Published field for 'checkbox' control
     *
     * @var      PHP2_UI_Components_Standard_Checkbox
     * @access   public
     */
    public $ckbRememberMe;

    /**
     * Automatically generated Published field for 'submit' control
     *
     * @var      PHP2_UI_Components_Standard_Submit
     * @access   public
     */
    public $btnLogin;

    /**
     * Automatically generated Published field for 'panel' control
     *
     * @var      PHP2_UI_Components_Additional_Panel
     * @access   public
     */
    public $pnlFooter;

    // End:Published }}}


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
        /**
         * Triyng to set logout action
         */
        if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'logout')) Application_BSLayer_UserSecurity::getInstance()->logout();

        /**
         * Redirecting logged users to the landing page.
         */
        if (Application_BSLayer_UserSecurity::getInstance()->isUserLogged())
        {
            PHP2_System_Response::getInstance()->urlRedirect(Application_BSLayer_UserSecurity::getInstance()->getEnterPoint(), array(), Application_BSLayer_UserSecurity::getInstance()->getUserParam('EnterSSL'));
        }

        $this->btnLogin->addEventListener(PHP2_UI_UIEvent::CLICK, 'btnLogin_Click');
    }

    /**
     * Button btnLogin Click event Handler
     *
     * @param   PHP2_UI_UIEvent $eventDetails
     * @return  void
     * @access  protected
     */
    protected function btnLogin_Click($eventDetails)
    {
        try
        {
            $userSecurity = Application_BSLayer_UserSecurity::getInstance();
            $userSecurity->login($this->txtLogin->text, $this->txtPassword->text, $this->ckbRememberMe->checked);

            PHP2_System_Response::getInstance()->urlRedirect($userSecurity->getEnterPoint(), array(), $userSecurity->getUserParam('EnterSSL'));
        }
        catch (Application_Exception_ESecurityException $exception)
        {
            $this->pageMessages->add($exception->getMessage());
        }
    }

}
