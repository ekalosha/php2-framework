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
 * @version    SVN: $Revision: 110 $
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
 * @version  $Id: usermanage.class.php 110 2010-02-22 15:32:22Z eugene $
 * @access   public
 * @package  Application\UI\Admin
 */
class Application_UI_Admin_UserManage extends Application_UI_Admin_ManageRecordPage
{

    // {{{ Begin:Published

    /**
     * Automatically generated Published Block, which Contains Controls from Template.
     * Generation time: 2009-08-19, 17:34:58;
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
    public $pageMessages;

    /**
     * Automatically generated Published field for 'viewStack' control
     *
     * @var      PHP2_UI_Components_Additional_ViewStack
     * @access   public
     */
    public $vsManageRecord;

    /**
     * Automatically generated Published field for 'panel' control
     *
     * @var      PHP2_UI_Components_Additional_Panel
     * @access   public
     */
    public $pnlFooter;

    /**
     * Automatically generated Published field for 'submit' control
     *
     * @var      PHP2_UI_Components_Standard_Submit
     * @access   public
     */
    public $btnAddNew;

    /**
     * Automatically generated Published field for 'datagrid' control
     *
     * @var      PHP2_UI_Components_Standard_DataGrid
     * @access   public
     */
    public $odgDataGrid;

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
     * Automatically generated Published field for 'dropdownlist' control
     *
     * @var      PHP2_UI_Components_Standard_DropdownList
     * @access   public
     */
    public $ddlGroup;

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
     * Automatically generated Published field for 'password' control
     *
     * @var      PHP2_UI_Components_Standard_Password
     * @access   public
     */
    public $txtPassword;

    /**
     * Automatically generated Published field for 'password' control
     *
     * @var      PHP2_UI_Components_Standard_Password
     * @access   public
     */
    public $txtPasswordRetyped;

    /**
     * Automatically generated Published field for 'edit' control
     *
     * @var      PHP2_UI_Components_Standard_Edit
     * @access   public
     */
    public $txtFirstName;

    /**
     * Automatically generated Published field for 'edit' control
     *
     * @var      PHP2_UI_Components_Standard_Edit
     * @access   public
     */
    public $txtLastName;

    /**
     * Automatically generated Published field for 'checkbox' control
     *
     * @var      PHP2_UI_Components_Standard_Checkbox
     * @access   public
     */
    public $ckbEnabled;

    /**
     * Automatically generated Published field for 'checkbox' control
     *
     * @var      PHP2_UI_Components_Standard_Checkbox
     * @access   public
     */
    public $ckbConfirmed;

    /**
     * Automatically generated Published field for 'submit' control
     *
     * @var      PHP2_UI_Components_Standard_Submit
     * @access   public
     */
    public $btnApplyEdit;

    /**
     * Automatically generated Published field for 'submit' control
     *
     * @var      PHP2_UI_Components_Standard_Submit
     * @access   public
     */
    public $btnCancelEdit;

    /**
     * Automatically generated Published field for 'PostBack' control
     *
     * @var      PHP2_UI_Components_Standard_PostBackDataStorage
     * @access   public
     */
    public $pbdsDataStorage;

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
        parent::_creationComplete();
    }

    /**
     * Checks is the record with specified ID exists in the Database
     *
     * @param   integer $recordId
     * @return  boolean
     */
    protected function _checkRecordExists($recordId)
    {
        $dboSysUser = new Application_DBLayer_SysUser();

        return $dboSysUser->checkRecord($recordId);
    }

    /**
     * Delete record method. Should be overrided.
     *
     * @param   integer $recordId
     * @return  boolean
     */
    protected function _deleteRecord($recordId)
    {
        $dboSysUser = new Application_DBLayer_SysUser();

        return $dboSysUser->delete($recordId);
    }

    /**
     * Initializes Edit state for Records List
     *
     * @param   integer $recordId
     * @return  boolean
     */
    protected function _initEditRecordState($recordId)
    {
        $userDetails = new Application_DBLayer_SysUser($recordId);

        $this->txtLogin->text         = $userDetails->Login;
        $this->txtEmail->text         = $userDetails->EMail;
        $this->txtFirstName->text     = $userDetails->FirstName;
        $this->txtLastName->text      = $userDetails->LastName;
        $this->ckbEnabled->checked    = $userDetails->Enabled ? true : false;
        $this->ckbConfirmed->checked  = $userDetails->IsConfirmed ? true : false;

        $this->_initGroupsList($userDetails->GroupID);
    }

    /**
     * Initializes Add state for Records List
     *
     * @return  void
     */
    protected function _initAddRecordState()
    {
        $this->txtLogin->text         = '';
        $this->txtEmail->text         = '';
        $this->txtFirstName->text     = '';
        $this->txtLastName->text      = '';
        $this->ckbEnabled->checked    = false;
        $this->ckbConfirmed->checked  = false;

        $this->_initGroupsList(Application_BSLayer_UserSecurity::USER_ROLE_USER);
    }

    /**
     * Initialize groups list for editred user
     *
     * @param   integer $groupId
     * @return  void
     */
    protected function _initGroupsList($groupId = false)
    {
        $dboGroup = new Application_DBLayer_SysGroup();
        $this->ddlGroup->clear();
        foreach ($dboGroup->getList()->getMatrix() as $groupDetails)
        {
            $this->ddlGroup->insertItem($groupDetails['ID'], $groupDetails['GroupName']);
        }

        if ($groupId) $this->ddlGroup->setSelectedIndex($groupId);
    }

    /**
     * Loads List state data for Records List
     *
     * @return  void
     */
    protected function _loadEditState()
    {
        ;
    }

    /**
     * Abstarct validation method for record
     *
     * @return  boolean
     */
    protected function _validateRecord()
    {
        $result = true;

        if (!$this->vldEmail->validate()) $result = false;
        if (!$this->vldLogin->validate()) $result = false;

        /**
         * Password validation rules
         */
        if (($this->_currentMode == self::MODE_ADD || $this->txtPassword->text) && (strlen($this->txtPassword->text) < 8))
        {
            $this->pageMessages->add('Password can not be less than 8 symbols');
            $result = false;
        }
        if (($this->txtPassword->text || $this->txtPasswordRetyped->text) && ($this->txtPassword->text != $this->txtPasswordRetyped->text))
        {
            $this->pageMessages->add('Password doesn\'t match retyped password');
            $result = false;
        }

        return $result;
    }

    /**
     * Abstarct save method for record
     *
     * @return  boolean
     */
    protected function _saveRecord()
    {
        $userId     = (integer) $this->pbdsDataStorage->data;

        $dboSysUser               = new Application_DBLayer_SysUser();
        $dboSysUser->Login        = $this->txtLogin->text;
        $dboSysUser->EMail        = $this->txtEmail->text;
        $dboSysUser->FirstName    = $this->txtFirstName->text;
        $dboSysUser->LastName     = $this->txtLastName->text;
        $dboSysUser->Enabled      = $this->ckbEnabled->checked ? 1 : 0;
        $dboSysUser->IsConfirmed  = $this->ckbConfirmed->checked ? 1 : 0;
        $dboSysUser->GroupID      = $this->ddlGroup->getSelectedIndex();

        if ($this->txtPassword->text) $dboSysUser->UserPassword = md5($this->txtPassword->text);

        if ($userId)
        {
            $dboSysUser->update($userId);
        }
        else
        {
            $dboSysUser->insert();
        }

        return true;
    }

    /**
     * Loads List state data for Records List
     *
     * @return  void
     */
    protected function _loadListState()
    {
        $dboUser = new Application_DBLayer_SysUser();

        $this->odgDataGrid->setElementsCount($dboUser->getListSize());

        $recordset  = $dboUser->getUsersListPaged($this->odgDataGrid->pageSize, $this->odgDataGrid->currPage, $this->odgDataGrid->sortField, $this->odgDataGrid->sortOrder)->getRecordset();

        /* @var $recordset Application_DBLayer_SysUser */
        while ($recordset->next())
        {
            $this->odgDataGrid->row['id']                = $recordset->ID;
            $this->odgDataGrid->row['editUrl']           = $this->odgDataGrid->getEventUrl(PHP2_Event_Event::EDIT, $recordset->ID, false);
            $this->odgDataGrid->row['deleteUrl']         = $this->odgDataGrid->getEventUrl(PHP2_Event_Event::DELETE, $recordset->ID, false);
            $this->odgDataGrid->row['Login']             = $recordset->Login;
            $this->odgDataGrid->row['EMail']             = $recordset->EMail;
            $this->odgDataGrid->row['RegistrationDate']  = ($recordset->RegistrationDate) ? PHP2_Utils_DateTime::getInstance($recordset->RegistrationDate)->getDate(PHP2_Utils_DateTime::FORMAT_DATE_SYSTEM) : '-';
            $this->odgDataGrid->row['GroupName']         = $recordset->GroupName;
            $this->odgDataGrid->row['CountryName']       = $recordset->CountryName;
            $this->odgDataGrid->row['LastLogin']         = $recordset->LastLogin;
            $this->odgDataGrid->row['LoginsCount']       = $recordset->LoginsCount;
            $this->odgDataGrid->row['Enabled']           = $recordset->Enabled ? '+' : '-';
            $this->odgDataGrid->row['Confirmed']         = $recordset->IsConfirmed ? '+' : '-';
            $this->odgDataGrid->replace();
        }
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
        parent::_load();
    }

}
