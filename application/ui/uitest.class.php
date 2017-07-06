<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains UI class for UI test page
 *
 * PHP version 5
 * @category   UI Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 99 $
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
 * UI class for UI test page
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: uitest.class.php 99 2009-10-20 14:44:49Z eugene $
 * @access   public
 * @package  Application\UI
 */
class Application_UI_UITest extends Application_UI_Page
{

    // {{{ Begin:Published

    /**
     * Automatically generated Published Block, which Contains Controls from Template.
     * Generation time: 2009-07-24, 18:03:35;
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
     * Automatically generated Published field for 'messagebox' control
     *
     * @var      PHP2_UI_Components_Standard_MessageBox
     * @access   public
     */
    public $msbMessageBox;

    /**
     * Automatically generated Published field for 'panel' control
     *
     * @var      PHP2_UI_Components_Additional_Panel
     * @access   public
     */
    public $pnlLeft;

    /**
     * Automatically generated Published field for 'Repeater' control
     *
     * @var      PHP2_UI_Components_Additional_Repeater
     * @access   public
     */
    public $rptBlockTest;

    /**
     * Automatically generated Published field for 'checkbox' control
     *
     * @var      PHP2_UI_Components_Standard_Checkbox
     * @access   public
     */
    public $chkbCheckbox1;

    /**
     * Automatically generated Published field for 'checkbox' control
     *
     * @var      PHP2_UI_Components_Standard_Checkbox
     * @access   public
     */
    public $chkbCheckbox2;

    /**
     * Automatically generated Published field for 'TextArea' control
     *
     * @var      PHP2_UI_Components_Standard_TextArea
     * @access   public
     */
    public $txtTextArea;

    /**
     * Automatically generated Published field for 'radiobutton' control
     *
     * @var      PHP2_UI_Components_Standard_Radiobutton
     * @access   public
     */
    public $rbtRadio1;

    /**
     * Automatically generated Published field for 'radiobutton' control
     *
     * @var      PHP2_UI_Components_Standard_Radiobutton
     * @access   public
     */
    public $rbtRadio2;

    /**
     * Automatically generated Published field for 'radiobutton' control
     *
     * @var      PHP2_UI_Components_Standard_Radiobutton
     * @access   public
     */
    public $rbtRadio3;

    /**
     * Automatically generated Published field for 'dropdownlist' control
     *
     * @var      PHP2_UI_Components_Standard_DropdownList
     * @access   public
     */
    public $ddlTestList;

    /**
     * Automatically generated Published field for 'panel' control
     *
     * @var      PHP2_UI_Components_Additional_Panel
     * @access   public
     */
    public $pnlFormContainer;

    /**
     * Automatically generated Published field for 'panel' control
     *
     * @var      PHP2_UI_Components_Additional_Panel
     * @access   public
     */
    public $pnlFooter;

    /**
     * Automatically generated Published field for 'edit' control
     *
     * @var      PHP2_UI_Components_Standard_Edit
     * @access   public
     */
    public $txtEmail_1;

    /**
     * Automatically generated Published field for 'edit' control
     *
     * @var      PHP2_UI_Components_Standard_Edit
     * @access   public
     */
    public $txtEmail_2;

    /**
     * Automatically generated Published field for 'validator' control
     *
     * @var      PHP2_UI_Components_Standard_Validator
     * @access   public
     */
    public $vldEmail2;

    /**
     * Automatically generated Published field for 'edit' control
     *
     * @var      PHP2_UI_Components_Standard_Edit
     * @access   public
     */
    public $txtEmail_3;

    /**
     * Automatically generated Published field for 'viewStack' control
     *
     * @var      PHP2_UI_Components_Additional_ViewStack
     * @access   public
     */
    public $vsSomeState;

    /**
     * Automatically generated Published field for 'datagrid' control
     *
     * @var      PHP2_UI_Components_Standard_DataGrid
     * @access   public
     */
    public $odgTestDataGrid;

    /**
     * Automatically generated Published field for 'edit' control
     *
     * @var      PHP2_UI_Components_Standard_Edit
     * @access   public
     */
    public $txtLogin;

    /**
     * Automatically generated Published field for 'validator' control
     *
     * @var      PHP2_UI_Components_Standard_Validator
     * @access   public
     */
    public $vldLogin;

    /**
     * Automatically generated Published field for 'edit' control
     *
     * @var      PHP2_UI_Components_Standard_Edit
     * @access   public
     */
    public $txtEmail;

    /**
     * Automatically generated Published field for 'validator' control
     *
     * @var      PHP2_UI_Components_Standard_Validator
     * @access   public
     */
    public $vldEmail;

    /**
     * Automatically generated Published field for 'edit' control
     *
     * @var      PHP2_UI_Components_Standard_Edit
     * @access   public
     */
    public $txtUrl;

    /**
     * Automatically generated Published field for 'validator' control
     *
     * @var      PHP2_UI_Components_Standard_Validator
     * @access   public
     */
    public $vldUrl;

    /**
     * Automatically generated Published field for 'password' control
     *
     * @var      PHP2_UI_Components_Standard_Password
     * @access   public
     */
    public $txtPassword;

    /**
     * Automatically generated Published field for 'submit' control
     *
     * @var      PHP2_UI_Components_Standard_Submit
     * @access   public
     */
    public $btnLogin;

    /**
     * Automatically generated Published field for 'submit' control
     *
     * @var      PHP2_UI_Components_Standard_Submit
     * @access   public
     */
    public $btnLogin2;

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
        $this->btnLogin->addEventListener(PHP2_UI_UIEvent::CLICK, 'btnLogin_Click');
        $this->ddlTestList->addEventListener(PHP2_UI_UIEvent::SUBMIT, 'ddlTestList_Submit');
        $this->odgTestDataGrid->addEventListener(PHP2_UI_UIEvent::EDIT, 'odgTestDataGrid_Edit');
        $this->odgTestDataGrid->addEventListener(PHP2_UI_UIEvent::DELETE, 'odgTestDataGrid_Delete');

        $this->vsSomeState->addEventListener(PHP2_UI_UIEvent::INIT_STATE, 'vsSomeState_Initialized');

        $this->msbMessageBox->add('Message 1');
        $this->msbMessageBox->add('Message 2');
    }

    /**
     * Submit event handler
     *
     * @return  void
     * @access  protected
     */
    protected function ddlTestList_Submit()
    {
        echo '### Submited ###';

        $this->vsSomeState->setState('stState3');
    }

    /**
     * Submit event handler
     *
     * @return  void
     * @access  protected
     */
    protected function btnLogin_Click()
    {
        echo '### btnLogin Submited ###';

        $this->vsSomeState->setState('stState2');
    }

    /**
     * Edit event handler
     *
     * @param   PHP2_Event_Event $eventObject
     * @return  void
     * @access  protected
     */
    protected function odgTestDataGrid_Edit($eventObject)
    {
        echo '### odgTestDataGrid Edit: '.$eventObject->data.' ###';
    }

    /**
     * Delete event handler
     *
     * @param   PHP2_Event_Event $eventObject
     * @return  void
     * @access  protected
     */
    protected function odgTestDataGrid_Delete($eventObject)
    {
        PHP2_System_Profiler::getInstance()->trace($eventObject->data, "\$variable");
        echo '### odgTestDataGrid Delete: '.$eventObject->data->ID.' ###';
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
        for ($i = 1; $i <= 10; $i++)
        {
            $this->rptBlockTest['var1'] = "{var1 From Parent}";
            $this->rptBlockTest['var2'] = "{var2 From Parent}";

            if ($i % 2)
            {
                if ($i % 3) $this->rptBlockTest->blcBlock1['var1'] = "{var1 Own value}";
                $this->rptBlockTest->blcBlock1->replace();
            }
            else
            {
                if ($i % 4) $this->rptBlockTest->blcBlock2['var2'] = "{var2 Own value}";
                $this->rptBlockTest->blcBlock2->replace();
            }
            $this->rptBlockTest->replace();
        }

        $dboCountry = new Application_DBLayer_Country();

        // $this->odgTestDataGrid->registerGroupEvent(PHP2_Event_Event::DELETE, 'Delete');
        // $this->odgTestDataGrid->registerGroupEvent(PHP2_Event_Event::EDIT, 'Edit');
        $this->odgTestDataGrid->setElementsCount($dboCountry->getListSize());

        /* @var $recordset Application_DBLayer_Country */
        $recordset  = $dboCountry->getListPaged($this->odgTestDataGrid->pageSize, $this->odgTestDataGrid->currPage, $this->odgTestDataGrid->sortField, $this->odgTestDataGrid->sortOrder)->getRecordset();

        while ($recordset->next())
        {
            $this->odgTestDataGrid->row['id']          = $recordset->ID;
            $this->odgTestDataGrid->row['editUrl']     = $this->odgTestDataGrid->getEventUrl(PHP2_Event_Event::EDIT, $recordset->ID);
            $this->odgTestDataGrid->row['deleteUrl']   = $this->odgTestDataGrid->getEventUrl(PHP2_Event_Event::DELETE, $recordset->ID, false);
            $this->odgTestDataGrid->row['ContinentID'] = $recordset->ContinentID;
            $this->odgTestDataGrid->row['CountryName'] = $recordset->CountryName;
            $this->odgTestDataGrid->row['CountryCode'] = $recordset->CountryCode;
            $this->odgTestDataGrid->row['PhoneCode']   = $recordset->PhoneCode;
            $this->odgTestDataGrid->replace();
        }
    }

}
