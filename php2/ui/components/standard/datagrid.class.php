<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains standard datagrid component
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 98 $
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
 * Data Grid component
 *
 * Usage in the template:
 *
 * <code>
 *   <php:datagrid:$objectName [template="{{filename}}"] [visible="true|false"] [attributes]>
 *       <row:header [attributes]>
 *           <col [sortfield="sortfield"] [attributes]>{{content}}</col>
 *              ...
 *           <col [sortfield="sortfield1"] [attributes]>{{content}}</col>
 *       </row>
 *       <row:body [attributes]>
 *           <col [attributes]>{{content}}</col>
 *              ...
 *           <col [attributes]>{{content}}</col>
 *       </row>
 *          ...
 *       <row:otherRow [attributes]>
 *           <col [attributes]>{{content}}</col>
 *              ...
 *           <col [attributes]>{{content}}</col>
 *       </row>
 *   </php:datagrid:$objectName>
 * </code>
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: datagrid.class.php 98 2009-08-19 17:15:05Z eugene $
 * @access   public
 * @package  PHP2\UI\Components\Standard
 */
class PHP2_UI_Components_Standard_DataGrid extends PHP2_UI_Control
{
    /**
     * Sort order types constant
     */
    const SORT_ORDER_ASC  = 'ASC';
    const SORT_ORDER_DESC = 'DESC';

    /**
     * Elements array for replace in template
     *
     * @var      array
     * @access   public
     * @see      parse()
     */
    public $row = array();

    /**
     * Template filename
     *
     * @var      string
     * @access   protected
     */
    protected $_template;

    /**
     * Template engine object
     *
     * @var      PHP2_UI_RBTEngine
     * @access   protected
     */
    protected $_tplEngine;

    /**
     * Current Row index
     *
     * @var      integer
     * @access   public
     * @see      parse()
     */
    public $currentRow = 1;

    /**
     * Contents parse result for current object
     *
     * @var      string
     * @access   protected
     * @see      parse()
     */
    protected $_parseResult = '';

    /**
     * Array of rows data - such as templates
     *
     * @var      array
     * @access   public
     */
    protected $_rowsData = array();

    /**
     * Array of cols visibility
     *
     * @var      array
     * @access   public
     */
    public $colVisible = array();

    /**
     * Sort field
     *
     * @var      string
     * @access   public
     */
    public $sortField = false;

    /**
     * Sort fields array
     *
     * @var      array
     * @access   private
    */
    private $_sortFields = array();

    /**
     * Sort field index
     *
     * @var      integer
     * @access   private
    */
    private $_sortFieldIndex;

    /**
     * Sort order for the field
     *
     * @var      string
     * @access   public
     */
    public $sortOrder;

    /**
     * Pages count in navigator
     *
     * @var      integer
     * @access   public
     */
    public $pagesCount = 1;

    /**
     * Current page number
     *
     * @var      integer
     * @access   public
     */
    public $currPage = 1;

    /**
     * Temp value for page number
     *
     * @var      integer
     * @access   public
     */
    public $tmpCurrPage = 0;

    /**
     * All element count in list
     *
     * @var      integer
     * @access   public
     */
    public $elementsCount;

    /**
     * Count elements per page
     *
     * @var      integer
     * @access   public
     */
    public $pageSize = 50;

    /**
     * Max elements Count per page
     *
     * @var      integer
     * @access   public
     */
    public $maxPageSize = 1000;

    /**
     * Min elements Count per page
     *
     * @var      integer
     * @access   public
     */
    public $minPageSize = 1;

    /**
     * Max count lines showed in navigator
     *
     * @var      integer
     * @access   public
     */
    public $visibleLinesCount = 8;

    /**
     * Begin index for the pages list
     *
     * @var      integer
     * @access   protected
     */
    protected $_beginIndex = 1;

    /**
     * Begin index for the pages list
     *
     * @var      integer
     * @access   protected
     */
    protected $_endIndex = 1;

    /**
     * Page navigator visible pages array
     *
     * @var      array
     * @access   protected
     */
    protected $_visiblePagesArray;

    /**
     * Datagrid group events
     *
     * @var      array
     * @access   protected
     */
    protected $_groupEvents = array();

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
         * Initializing Request data for the control
         */
        $this->_initRequestData();

        /**
         * Building sort index
         */
        $this->_buildSortIndex();
    }

    /**
     * Initialize control
     *
     * @return  void
     * @access  protected
     */
    protected function _init()
    {
        parent::_init();

        /**
         * Processing default component attributes
         */
        $this->_template = BASE_PATH.'ui/components/datagrid/datagrid.tpl';
        if ($template = $this->_extractAttribute('template')) $this->_template = BASE_PATH.'ui/'.$template;

        /**
         * Set event handlers
         */
        $this->addEventListener(PHP2_UI_UIEvent::LOAD_SESSION, '_loadFromSession', $this);
        $this->addEventListener(PHP2_UI_UIEvent::SAVE_SESSION, '_saveToSession', $this);

        /**
         * Initializing DataGrid definition
         */
        $this->_initDefinition();

        /**
         * Creating template Engine object
         */
        $this->_tplEngine = new PHP2_UI_RBTEngine();
        $this->_tplEngine->loadFromFile($this->_template);
    }

    /**
     * Initialize Request data for control from Request
     *
     * @return  void
     * @access  protected
     */
    protected function _initRequestData()
    {
        $objectName = $this->getName();

        /**
         * Set sort field
         */
        if ($sortField = PHP2_System_Request::getInstance()->getString($objectName.'_sortField'))
        {
            $tmpSortOrder = (PHP2_System_Request::getInstance()->getString($objectName.'_sortOrder', self::SORT_ORDER_ASC) == self::SORT_ORDER_ASC ? self::SORT_ORDER_ASC : self::SORT_ORDER_DESC);
            $this->setSortField($sortField, $tmpSortOrder);
        }

        /**
         * Set current page
         */
        if ($currPage = PHP2_System_Request::getInstance()->getInt($objectName.'_page'))
        {
            $this->setPage($currPage);
        }

        $navigatorIdTop     = $objectName.'_Top';
        $navigatorIdBottom  = $objectName.'_Bottom';

        /**
         * Set current page size
         */
        $navigatorIdPageSize = (isset($_REQUEST[$navigatorIdTop.'_btnPageSize']) || isset($_REQUEST[$navigatorIdTop.'_btnPageSize_x'])) ? $navigatorIdTop : ((isset($_REQUEST[$navigatorIdBottom.'_btnPageSize']) || isset($_REQUEST[$navigatorIdBottom.'_btnPageSize_x'])) ? $navigatorIdBottom : false);
        if ($navigatorIdPageSize && ($pageSize = PHP2_System_Request::getInstance()->getInt($navigatorIdPageSize.'_pageSize')))
        {
            $this->setPageSize($pageSize);
        }

        /**
         * Set current page from Quick guide
         */
        $navigatorIdPage = (isset($_REQUEST[$navigatorIdTop.'_btnPage']) || isset($_REQUEST[$navigatorIdTop.'_btnPage_x'])) ? $navigatorIdTop : ((isset($_REQUEST[$navigatorIdBottom.'_btnPage']) || isset($_REQUEST[$navigatorIdBottom.'_btnPage_x'])) ? $navigatorIdBottom : false);
        if ($navigatorIdPage && ($currPage = PHP2_System_Request::getInstance()->getInt($navigatorIdPage.'_page')))
        {
            $this->setPage($currPage);
        }
    }

    /**
     * Initializes control definition
     *
     * @return  string
     * @access  protected
     */
    protected function _initDefinition()
    {
        /**
         * Initializing Row Data
         */
        preg_match_all('/<row:([\w]+)([^>]*)?>(.*?)<\/row>/s', $this->_controlDefinition->content, $matches);

        foreach ($matches[1] as $index => $rowName)
        {
            /**
             * Processing Row data
             */
            $rowData = array();
            preg_match_all('/<col([^>]*)?>(.*?)<\/col>/s', $matches[3][$index], $cellMatches);
            foreach ($cellMatches[1] as $colIndex => $colAttributes)
            {
                $rowData[$colIndex] = array('attributesString' => $colAttributes, 'content' => $cellMatches[2][$colIndex], );
                if ($rowName == 'header')
                {
                    $rowData[$colIndex]['attributes'] = PHP2_UI_ControlDefinition::parseAttributesString($colAttributes);
                    if (isset($rowData[$colIndex]['attributes']['sortfield']))
                    {
                        $this->_sortFields[$colIndex] = $rowData[$colIndex]['attributes']['sortfield'];
                    }
                }
            }

            $rowsData[$rowName] = array('content' => $matches[3][$index], 'attributesString' => $matches[2][$index], 'cols' => $rowData, );
        }

        $this->_rowsData = &$rowsData;
    }

    /**
     * Buils sort index css class in the Datagrid columns
     *
     * @return  bool
     * @access  public
     */
    protected function _buildSortIndex()
    {
        if ($this->_sortFieldIndex !== false)
        {
            /**
             * Adding Sort class to the CSS
             */
            foreach ($this->_rowsData as &$rowDataDetails)
            {
                if (isset($rowDataDetails['cols'][$this->_sortFieldIndex]))
                {
                    if (!isset($rowDataDetails['cols'][$this->_sortFieldIndex]['attributes']['class']))
                    {
                        $rowDataDetails['cols'][$this->_sortFieldIndex]['attributes']['class'] = 'selected';
                    }
                    else
                    {
                        $rowDataDetails['cols'][$this->_sortFieldIndex]['attributes']['class'] .= ' selected';
                    }
                }
            }
        }
    }

    /**
     * Apply sort field
     *
     * @param   string $sortField Sort field value
     * @return  bool
     * @access  public
     */
    public function setSortField($sortField, $sortOrder = false)
    {
        if (($sortFieldId = array_search($sortField, $this->_sortFields)) !== false)
        {
            $this->sortField = $sortField;
            if ($sortOrder) $this->sortOrder = ($sortOrder == self::SORT_ORDER_ASC) ? self::SORT_ORDER_ASC : self::SORT_ORDER_DESC;
            $this->_sortFieldIndex = $sortFieldId;

            $this->dispatchEvent(new PHP2_UI_UIEvent(PHP2_UI_UIEvent::SORT));

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Trying to set temp value of the current page.
     * Note: Usually when setPage() method called, control dont know Real number of elements
     *
     * @param   integer $pageNumber
     * @access  public
     */
    public function setPage($pageNumber)
    {
        $this->tmpCurrPage  = ($pageNumber > 0) ? $pageNumber : $this->currPage;
        $this->currPage     = ((($pageNumber > $this->pagesCount) || ($pageNumber < 1)) ? $this->currPage : $pageNumber);
    }

    /**
     * Validates current page number
     *
     * @access  protected
     */
    protected function _checkPage()
    {
        if (($this->currPage > $this->pagesCount) || ($this->currPage < 1))
        {
            $this->currPage = 1;
        }
    }

    /**
     * Trying to set size of current page
     *
     * @param   integer $pageSize
     * @access  public
     */
    public function setPageSize($pageSize)
    {
        $pageSize = intval($pageSize);
        if ($this->pageSize == $pageSize) return true;

        /**
         * Set new page size
         */
        if (($pageSize >= $this->minPageSize) && ($pageSize <= $this->maxPageSize))
        {
            $this->pageSize = $pageSize;

            $this->setElementsCount($this->elementsCount);
            $this->_checkPage();
        }
    }

    /**
     * Trying to set elements count for current control
     *
     * @param   integer $elementsCount
     * @access  public
     */
    public function setElementsCount($elementsCount)
    {
        $this->elementsCount  = $elementsCount;
        $this->pagesCount     = intval($this->elementsCount / $this->pageSize) + ((($this->elementsCount % $this->pageSize == 0)&&($this->elementsCount)) ? 0 : 1);

        if (($this->tmpCurrPage > 0) && ($this->tmpCurrPage <= $this->pagesCount))
        {
            $this->currPage = $this->tmpCurrPage;
        }

        $this->_checkPage();
    }

    /**
     * Implements page navigator logic and initialize _visiblePagesArray.
     *
     * @access  protected
     * @see     $_visiblePagesArray
     */
    protected function _buildVisiblePagesList()
    {
        $this->_visiblePagesArray = array ('firstPage' => false, 'prevPage' => false, 'currPage' => array(), 'nextPage' => false, 'lastPage' => false);

        /**
         * Calculating Begin/End indexes of items
         */
        if ($this->_beginIndex <= 0) $this->_beginIndex = 1;
        if ($this->currPage - $this->visibleLinesCount + 1 > $this->_beginIndex) $this->_beginIndex = $this->currPage - $this->visibleLinesCount + 1;
        if ($this->currPage < $this->_beginIndex) $this->_beginIndex = $this->currPage;

        if (($this->pagesCount - $this->_beginIndex + 1) <= $this->visibleLinesCount)
        {
            $this->_endIndex = $this->pagesCount;
        }
        else
        {
            $this->_endIndex = $this->visibleLinesCount + $this->_beginIndex - 1;
        }

        /**
         * Building First/Prev page indexes
         */
        if ($this->_beginIndex >= 3) $this->_visiblePagesArray['firstPage'] = 1;
        if ($this->_beginIndex >= 2) $this->_visiblePagesArray['prevPage']  = $this->_beginIndex - 1;

        /**
         * Building page indexes
         */
        for ($i = $this->_beginIndex; $i <= $this->_endIndex; $i++)
        {
            $this->_visiblePagesArray['currPage'][$i] = $i;
        }

        /**
         * Building Last/Next page indexes
         */
        if (($this->pagesCount - $this->_beginIndex + 1) > $this->visibleLinesCount)
        {
            if ($this->_endIndex <= $this->pagesCount - 1) $this->_visiblePagesArray['nextPage'] = $this->_endIndex + 1;
            if ($this->_endIndex <= $this->pagesCount - 2) $this->_visiblePagesArray['lastPage'] = $this->pagesCount;
        }

    }

    /**
     * Builds DataGrid Header
     *
     * @return  bool
     * @access  public
     */
    public function buildHeader()
    {
        $this->_tplEngine->assignParameters($this->row);

        $rowName     = 'header';
        $objectName  = $this->getName();

        if (!isset($this->_rowsData[$rowName]['cols']) || !count($this->_rowsData[$rowName]['cols'])) return false;

        /**
         * Rendering cells list
         */
        /* @var $tplEngineRow PHP2_UI_RBTEngine */
        $tplEngineRow = &$this->_tplEngine->gridHeader->gridRow;
        $tplEngineRow['attributes'] = $this->_rowsData[$rowName]['attributesString'];

        /* @var $tplEngineCell PHP2_UI_RBTEngine */
        $tplEngineCell     = &$this->_tplEngine->gridHeader->gridRow->gridCell;
        $tplEngineOrdCell  = &$this->_tplEngine->gridHeader->gridRow->gridCell->normal;
        $tplEngineSortCell = &$this->_tplEngine->gridHeader->gridRow->gridCell->sort;
        foreach ($this->_rowsData[$rowName]['cols'] as &$colData)
        {
            if (isset($colData['attributes']['sortfield']))
            {
                $nextSortOrder = (($this->sortField == $colData['attributes']['sortfield']) ? (($this->sortOrder == self::SORT_ORDER_ASC) ? self::SORT_ORDER_DESC : self::SORT_ORDER_ASC) : self::SORT_ORDER_ASC);
                $sortUrlParams = array($objectName.'_sortField' => $colData['attributes']['sortfield'], $objectName.'_sortOrder' => $nextSortOrder);

                $tplEngineCurrentCell = &$tplEngineSortCell;
                $tplEngineCurrentCell['sortUrl']   = PHP2_System_Response::getInstance()->getUrl('', $sortUrlParams);
                $tplEngineCurrentCell['sortOrder'] = (($this->sortField == $colData['attributes']['sortfield']) ? (($this->sortOrder == self::SORT_ORDER_ASC) ? 'sortOrderDesc' : 'sortOrderAsc') : 'sortOrderAsc');;
            }
            else
            {
                $tplEngineCurrentCell = &$tplEngineOrdCell;
            }
            $tplEngineCurrentCell['attributes'] = (isset($colData['attributes'])) ? $this->_buildAttributesString($colData['attributes']) : '';
            $tplEngineCurrentCell['content']    = preg_replace('/{{(\w+)}}/e', '(isset($this->row["\1"]) ? $this->row["\1"] : \'\')', $colData['content']);
            $tplEngineCurrentCell->replace();
            $tplEngineCell->replace();
        }
        $tplEngineRow->replace();
        $this->_tplEngine->gridHeader->replace();

        $this->row = array();

        return true;
    }

    /**
     * Builds DataGrid page navigator
     *
     * @return  bool
     * @access  public
     */
    public function buildPageNavigator()
    {
        $this->_buildVisiblePagesList();

        $objectName   = $this->getName();

        $this->_tplEngine['pageNavigatorTop']    = $this->_getPageNavigatorCode($objectName.'_Top');
        $this->_tplEngine['pageNavigatorBottom'] = $this->_getPageNavigatorCode($objectName.'_Bottom');
    }

    /**
     * Returns HTML code for the page Navigator block with specified ID
     *
     * @param   string $navigatorId
     * @return  string
     */
    protected function _getPageNavigatorCode($navigatorId)
    {
        /* @var $tplEngine PHP2_UI_RBTEngine */
        $tplEngine = $this->_tplEngine->pageNavigator;

        $objectName   = $this->getName();
        $httpResponse = PHP2_System_Response::getInstance();

        $tplEngine['navigatorId'] = $navigatorId;
        $tplEngine['currPage']    = $this->currPage;
        $tplEngine['pagesCount']  = $this->pagesCount;

        /**
         * Build quick guide and page navigation elements
         */
        if ($this->pagesCount > 1)
        {
            $tplEngine->quickGuide->replace();

            foreach ($this->_visiblePagesArray as $blockName => $pageIndex)
            {
                if (is_array($pageIndex))
                {
                    foreach ($pageIndex as $currPage)
                    {
                        $currentStatusBlock = (($this->currPage != $currPage) ? 'active' : 'passive');

                        $tplEngine->{$blockName}['pageNum'] = $currPage;
                        $tplEngine->{$blockName}['url'] = $httpResponse->getUrl('', array($objectName.'_page' => $currPage));
                        $tplEngine->{$blockName}->{$currentStatusBlock}->replace();
                        $tplEngine->{$blockName}->replace();
                    }
                }
                elseif ($pageIndex !== false)
                {
                    $tplEngine->{$blockName}['url'] = $httpResponse->getUrl('', array($objectName.'_page' => $pageIndex));
                    $tplEngine->{$blockName}->replace();
                }
            }
        }

        if ($this->elementsCount > 1)
        {
            $tplEngine->customize['pageSize'] = $this->pageSize;
            $tplEngine->customize->replace();
            $tplEngine->replace();

            return $tplEngine->renderOutput();
        }

        return '';
    }

    /**
     * Replaces template with specified elements
     *
     * @param   string $rowName Parsed row name
     * @return  bool
     * @access  public
     */
    public function parse($rowName = 'body')
    {
        $this->row['isEvenRow']  = !((boolean) (($this->currentRow++) % 2));
        $this->row['currentRow'] = $this->currentRow++;

        $this->_tplEngine->assignParameters($this->row);
        // print_r($this->_rowsData);

        if (!isset($this->_rowsData[$rowName]['cols']) || !count($this->_rowsData[$rowName]['cols'])) return false;

        /**
         * Rendering cells list
         */
        /* @var $tplEngineRow PHP2_UI_RBTEngine */
        $tplEngineRow = &$this->_tplEngine->gridBody->gridRow;
        $tplEngineRow['attributes'] = $this->_rowsData[$rowName]['attributesString'];

        /* @var $tplEngineCell PHP2_UI_RBTEngine */
        $tplEngineCell = &$this->_tplEngine->gridBody->gridRow->gridCell;
        foreach ($this->_rowsData[$rowName]['cols'] as &$colData)
        {
            $tplEngineCell['attributes'] = (isset($colData['attributes'])) ? $this->_buildAttributesString($colData['attributes']) : '';
            $tplEngineCell['content']    = preg_replace('/{{(\w+)}}/e', '(isset($this->row["\1"]) ? $this->row["\1"] : \'\')', $colData['content']);
            $tplEngineCell->replace();
        }
        $tplEngineRow->replace();
        $this->_tplEngine->gridBody->replace();

        $this->row = array();

        return true;
    }

    /**
     * Alias of the parse() function
     *
     * @param   string $rowName
     * @return  bool
     * @access  public
     * @see     parse()
     */
    public function replace($rowName = 'body')
    {
        return $this->parse($rowName);
    }

    /**
     * Registers group event for Datagrid elements
     *
     * @param   string $eventName
     * @param   string $eventCaption
     * @return  void
     * @access  public
     */
    public function registerGroupEvent($eventName, $eventCaption)
    {
        $this->_groupEvents[$eventName] = $eventCaption;
    }

    /**
     * Unregisters group event for Datagrid elements
     *
     * @param   string $eventName
     * @return  void
     * @access  public
     */
    public function removeGroupEvent($eventName)
    {
        unset($this->_groupEvents[$eventName]);
    }

    /**
     * Unregisters all group events for Datagrid elements
     *
     * @return  void
     * @access  public
     */
    public function removeGroupEvents()
    {
        $this->_groupEvents = array();
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        $this->buildPageNavigator();
        $this->buildHeader();

        $this->_tplEngine['attributes'] = $this->_getAttributesString();
        $this->_tplEngine->replace();

        return $this->_tplEngine->renderOutput();
    }

    /**
     * Handler of the load session event
     *
     * @return  string
     * @access  protected
     */
    protected function _loadFromSession()
    {
        if (isset($this->sessionData['pageSize'])) $this->pageSize = $this->sessionData['pageSize'];
        if (isset($this->sessionData['currPage'])) $this->currPage = $this->sessionData['currPage'];
        if (isset($this->sessionData['pagesCount'])) $this->pagesCount = $this->sessionData['pagesCount'];
        if (isset($this->sessionData['_beginIndex'])) $this->_beginIndex = $this->sessionData['_beginIndex'];
        if (isset($this->sessionData['_endIndex'])) $this->_endIndex = $this->sessionData['_endIndex'];
        if (isset($this->sessionData['elementsCount'])) $this->elementsCount = $this->sessionData['elementsCount'];
        if (isset($this->sessionData['sortField'])) $this->sortField = $this->sessionData['sortField'];
        if (isset($this->sessionData['sortOrder'])) $this->sortOrder = $this->sessionData['sortOrder'];
    }

    /**
     * Handler of the save to session event
     *
     * @return  string
     * @access  protected
     */
    protected function _saveToSession()
    {
        $this->sessionData['pageSize']      = $this->pageSize;
        $this->sessionData['currPage']      = $this->currPage;
        $this->sessionData['pagesCount']    = $this->pagesCount;
        $this->sessionData['_beginIndex']   = $this->_beginIndex;
        $this->sessionData['_endIndex']     = $this->_endIndex;
        $this->sessionData['elementsCount'] = $this->elementsCount;
        $this->sessionData['sortField']     = $this->sortField;
        $this->sessionData['sortOrder']     = $this->sortOrder;
    }

}
