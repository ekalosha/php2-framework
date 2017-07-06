<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains UI class for records manager
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
 * Base UI class for Records Manager
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: managerecordpage.class.php 98 2009-08-19 17:15:05Z eugene $
 * @access   public
 * @package  Application\UI\Admin
 */
abstract class Application_UI_Admin_ManageRecordPage extends Application_UI_Admin_Page
{
    /**
     * Mode constants
     */
    const MODE_ADD   = 'ADD';
    const MODE_EDIT  = 'EDIT';
    const MODE_LIST  = 'LIST';

    // {{{ Begin:Published

    /**
     * Automatically generated Published Block, which Contains Controls from Template.
     * Generation time: 2009-03-10, 20:44:50;
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
     * Automatically generated Published field for 'viewStack' control
     *
     * @var      PHP2_UI_Components_Additional_ViewStack
     * @access   public
     */
    public $vsManageRecord;

    /**
     * Automatically generated Published field for 'datagrid' control
     *
     * @var      PHP2_UI_Components_Standard_DataGrid
     * @access   public
     */
    public $odgDataGrid;

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
     * Automatically generated Published field for 'submit' control
     *
     * @var      PHP2_UI_Components_Standard_Submit
     * @access   public
     */
    public $btnAddNew;

    /**
     * Automatically generated Published field for 'PostBack' control
     *
     * @var      PHP2_UI_Components_Standard_PostBackDataStorage
     * @access   public
     */
    public $pbdsDataStorage;

    // End:Published }}}

    /**
     * Current mode for the record page
     *
     * @var     string
     * @access  protected
     */
    protected $_currentMode;

    /**
     * Class constructor
     *
     * @access  public
     */
    public function __construct()
    {
        /**
         * Initializing default page mode
         */
        $this->_currentMode = self::MODE_LIST;

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
        if (isset($this->btnAddNew)) $this->btnAddNew->addEventListener(PHP2_UI_UIEvent::CLICK, 'btnAddNew_Click');
        $this->btnCancelEdit->addEventListener(PHP2_UI_UIEvent::CLICK, 'btnCancelEdit_Click');
        $this->btnApplyEdit->addEventListener(PHP2_UI_UIEvent::CLICK, 'btnApplyEdit_Click');
        $this->odgDataGrid->addEventListener(PHP2_UI_UIEvent::EDIT, 'odgDataGrid_Edit');
        $this->odgDataGrid->addEventListener(PHP2_UI_UIEvent::DELETE, 'odgDataGrid_Delete');

        /**
         * Set session event handlers
         */
        $this->addEventListener(PHP2_UI_UIEvent::LOAD_SESSION, '_loadFromSession');
        $this->addEventListener(PHP2_UI_UIEvent::SAVE_SESSION, '_saveToSession');
    }

    /**
     * On btnCancelEdit Click event handler
     *
     * @return  void
     * @access  protected
     */
    protected function btnCancelEdit_Click()
    {
        $this->vsManageRecord->setState('stViewList');
        $this->_currentMode = self::MODE_LIST;
    }

    /**
     * On btnApplyEdit Click event handler
     *
     * @return  void
     * @access  protected
     */
    protected function btnApplyEdit_Click()
    {
        if ($this->_validateRecord())
        {
            PHP2_System_Profiler::getInstance()->trace(true, "\$");

            $this->_saveRecord();

            $this->vsManageRecord->setState('stViewList');
            $this->_currentMode = self::MODE_LIST;
        }
    }

    /**
     * On btnApplyEdit Click event handler
     *
     * @return  void
     * @access  protected
     */
    protected function btnAddNew_Click()
    {
        $this->pbdsDataStorage->data = null;
        $this->_initAddRecordState();
        $this->vsManageRecord->setState('stEditRecord');
        $this->_currentMode = self::MODE_ADD;
    }

    /**
     * Edit event handler
     *
     * @param   PHP2_Event_Event $eventObject
     * @return  void
     * @access  protected
     */
    protected function odgDataGrid_Edit($eventObject)
    {
        if ($this->_checkRecordExists($eventObject->data))
        {
            $this->pbdsDataStorage->data = $eventObject->data;

            $this->_initEditRecordState($eventObject->data);
            $this->vsManageRecord->setState('stEditRecord');
            $this->_currentMode = self::MODE_EDIT;
        }
    }

    /**
     * Delete event handler
     *
     * @param   PHP2_Event_Event $eventObject
     * @return  void
     * @access  protected
     */
    protected function odgDataGrid_Delete($eventObject)
    {
        if ($this->_checkRecordExists($eventObject->data)) $this->_deleteRecord($eventObject->data);
    }

    /**
     * Checks is the record with specified ID exists in the Database
     *
     * @param   integer $recordId
     * @return  boolean
     */
    protected function _checkRecordExists($recordId)
    {
        return true;
    }

    /**
     * Abstract method for Edit record state initialization.
     *
     * @param   integer $recordId
     * @return  boolean
     * @abstract
     */
    abstract protected function _initEditRecordState($recordId);

    /**
     * Initializes Add state for Records List
     *
     * @return  void
     * @abstract
     */
    abstract protected function _initAddRecordState();

    /**
     * Loads Edit state data
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
        return true;
    }

    /**
     * Abstract data loader method for Records List
     *
     * @return  void
     * @abstract
     */
    abstract protected function _loadListState();

    /**
     * Abstarct save method for record
     *
     * @return  boolean
     */
    abstract protected function _saveRecord();

    /**
     * Abstract method for Delete record. Should be implemented.
     *
     * @param   integer $recordId
     * @return  boolean
     * @abstract
     */
    abstract protected function _deleteRecord($recordId);

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
        switch ($this->vsManageRecord->getState())
        {
            case 'stEditRecord' :
                $this->_loadEditState();
            break;

            case 'stViewList' :
            default:
                $this->_loadListState();
            break;
        }
    }

    /**
     * Handler of the load session event
     *
     * @return  string
     * @access  protected
     */
    protected function _loadFromSession()
    {
        if (isset($this->sessionData['_currentState'])) $this->_currentMode = $this->sessionData['_currentState'];
    }

    /**
     * Handler of the save to session event
     *
     * @return  string
     * @access  protected
     */
    protected function _saveToSession()
    {
        $this->sessionData['_currentState'] = $this->_currentMode;
    }

}
