<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Class which implements system profiler
 *
 * PHP version 5
 * @category   Library Classes
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
// namespace PHP2\System;

/**
 * Class implements system profiler
 *
 * To use profiling class you need to define the following constants:
 *
 * <code>
 *      define('PHP2_PROFILER_ENABLE', true);
 *   define('PHP2_PROFILER_USE_TRUSTED_IP_FILTERS', true);
 *   define('PHP2_PROFILER_TRUSTED_IPS', '192.168.0.*');
 * </code>
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: profiler.class.php 99 2009-10-20 14:44:49Z eugene $
 * @access   public
 * @package  PHP2\System
 */
class PHP2_System_Profiler
{
    /**
     * Translation mode
     */
    const MODE_HTML  = 'HTML';
    const MODE_XML   = 'XML';
    const MODE_JSON  = 'JSON';

    /**
     * Profiler Group constants
     */
    const PROFILER_GROUP_DB        = 'PROFILER_GROUP_DB';
    const PROFILER_GROUP_AUTOLOAD  = 'PROFILER_GROUP_AUTOLOAD';
    const PROFILER_GROUP_MAIN      = 'PROFILER_GROUP_MAIN';

    const PROFILER_GROUP_DB_CAPTION        = 'Databases/Hosts profiling Info';
    const PROFILER_GROUP_AUTOLOAD_CAPTION  = 'Autoload profiling Info';
    const PROFILER_GROUP_MAIN_CAPTION      = 'Main Application profiling info';

    const DB_MAX_QUERY_EXECUTION_TIME  = 0.005;

    const DEFAULT_ID = 0;

    /**
     * Profiler trace type constants
     */
    const TRACE_TYPE_PRINT_R     = 'print_r';
    const TRACE_TYPE_VAR_DUMP    = 'var_dump';
    const TRACE_TYPE_VAR_EXPORT  = 'var_export';

    /**
     * Default profiler Group name
     *
     * @var     string
     * @access  private
     */
    private $_defaultProfilerGroup;

    /**
     * Default profiler Group Caption
     *
     * @var     string
     * @access  private
     */
    private $_defaultProfilerGroupCaption;

    /**
     * Profiling Groups info array
     *
     * @var     array
     * @access  private
     */
    private $_profilingGroupsInfo = array();

    /**
     * Profiling info array
     *
     * @var     array
     * @access  private
     */
    private $_profilingInfo = array();

    /**
     * Singleton instance of class
     *
     * @staticvar  PHP2_System_Profiler
     * @access     private
     */
    private static $_instance = null;

    /**
     * Enabled status for Profiling
     *
     * @var     boolean
     * @access  private
     */
    private $_enabled = true;

    /**
     * Traced variables Info
     *
     * @var     array
     * @access  private
     */
    private $_tracedVariables = array();

    /**
     * Translation mode parameter
     *
     * @var     string
     * @access  protected
     */
    protected $_translationMode;

    /**
     * Singleton have a private constructor
     *
     * @access  private
     */
    private function __construct()
    {
        /**
         * Checking is Profiling enabled for current page
         */
        if (!defined('PHP2_PROFILER_ENABLE') || !PHP2_PROFILER_ENABLE)
        {
            $this->setEnabled(false);

            return false;
        }
        elseif ((defined('PHP2_PROFILER_USE_TRUSTED_IP_FILTERS') && PHP2_PROFILER_USE_TRUSTED_IP_FILTERS) && !$this->checkTrustedIPs(PHP2_PROFILER_TRUSTED_IPS))
        {
            $this->setEnabled(false);

            return false;
        }

        $this->_defaultProfilerGroup        = self::PROFILER_GROUP_MAIN;
        $this->_defaultProfilerGroupCaption = self::PROFILER_GROUP_MAIN_CAPTION;

        /**
         * Setup Initial profiling Groups
         */
        $this->setProfilerGroup(self::PROFILER_GROUP_MAIN,      self::PROFILER_GROUP_MAIN_CAPTION);
        $this->setProfilerGroup(self::PROFILER_GROUP_AUTOLOAD,  self::PROFILER_GROUP_AUTOLOAD_CAPTION);
        $this->setProfilerGroup(self::PROFILER_GROUP_DB,        self::PROFILER_GROUP_DB_CAPTION);

        /**
         * Set display errors mode for enabled profiler
         */
        ini_set('display_errors',   true);
        error_reporting(E_ALL | E_STRICT);
    }

    /**
     * Sets Translation mode
     *
     * @param   string $translationMode
     * @access  public
     * @return  boolean
     */
    public function setTranslationMode($translationMode)
    {
        if (!$this->getEnabled()) return false;

        switch ($translationMode)
        {
            case self::MODE_HTML:
                $this->_translationMode = $translationMode;

                if (ob_get_level() <= 1) ob_start();

                return true;
            break;

            case self::MODE_XML:
            case self::MODE_JSON:
                $this->_translationMode = $translationMode;

                return true;
            break;
        }

        return false;
    }

    /**
     * Checks trusted IPs List
     *
     * @param   string $trustedIPsString Trusted IPs string. All IPs needs to be separated by one of the following separators ';', ',', ':', ' '
     * @return  boolean
     * @access  protected
     */
    protected function checkTrustedIPs($trustedIPsString)
    {

        if (!isset($_SERVER['REMOTE_ADDR'])) return false;

        $validIPsString = preg_replace('/[\,\:\;\s]+/', ';', $trustedIPsString);
        $trustedIPsList   = explode(';', $validIPsString);

        /**
         * Find trusted IP patterns
         */
        $trustedIPPatternsList = array();
        foreach ($trustedIPsList as $trustedIP)
        {
            $trustedIPPatternsList[] = '/'.str_replace('*', '([\d\.]*)', $trustedIP).'/';
        }

        /**
         * Check Is the current IP is Valid
         */
        foreach ($trustedIPPatternsList as $trustedIPPattern)
        {
            $pregMatchResult = preg_match($trustedIPPattern, $_SERVER['REMOTE_ADDR'], $matches);
            if ((isset($matches[0])) && ($matches[0] == $_SERVER['REMOTE_ADDR'])) return true;
        }

        return false;
    }

    /**
     * Enables/disables profiling
     *
     * @param   boolean $enabled Enabled flag
     * @access  public
     */
    public function setEnabled($enabled = true)
    {
        $this->_enabled = (boolean) $enabled;
    }

    /**
     * Returns profiling enabled status
     *
     * @return  boolean
     * @access  public
     */
    public function getEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Returns single instance of the Current Class
     *
     * @return  PHP2_System_Profiler
     * @access  public
     * @static
     */
    public static function getInstance()
    {

        /**
         * Checking is current class Initialized
         */
        if (self::$_instance == null)
        {
            $currentClass     = __CLASS__;
            self::$_instance  = new $currentClass();
        }

        return self::$_instance;
   }

    /**
     * Install profiler Group
     *
     * @param  string $profilerGroupName
     * @param  string $profilerGroupCaption
     * @param  string $additionalInfo
     * @return void
     */
    public function setProfilerGroup($profilerGroupName, $profilerGroupCaption, $additionalInfo = false)
    {

        if (!$this->_enabled) return false;

        $this->_profilingGroupsInfo[$profilerGroupName] = array('caption' => $profilerGroupCaption, 'additionalInfo' => $additionalInfo);
        $this->_profilingInfo[$profilerGroupName]       = array();
    }

    /**
     * Sets start Breakpoint for profiler
     *
     * @param  string $profilingId
     * @param  string $profilingCaption
     * @param  string $profilerGroupName
     * @return boolean
     */
    public function setStartProfilerBreakpoint($profilingId = 0, $profilingCaption = false, $profilerGroupName = false)
    {

        if (!$this->_enabled) return false;

        if (!$profilerGroupName) $profilerGroupName = $this->_defaultProfilerGroup;

        /**
         * Checking is Profiling already takes place for specified ID
         */
        if (isset($this->_profilingInfo[$profilerGroupName][$profilingId]))
        {
            $lastPIndex = count($this->_profilingInfo[$profilerGroupName][$profilingId]) - 1;
            if ((isset($this->_profilingInfo[$profilerGroupName][$profilingId][$lastPIndex])) && (!isset($this->_profilingInfo[$profilerGroupName][$profilingId][$lastPIndex]['endTime'])))
            {
                $endTime = microtime(true);
                $this->_profilingInfo[$profilerGroupName][$profilingId][$lastPIndex]['endTime']       = $endTime;
                $this->_profilingInfo[$profilerGroupName][$profilingId][$lastPIndex]['totalTime']     = $endTime - $this->_profilingInfo[$profilerGroupName][$profilingId][$lastPIndex]['startTime'];
                $this->_profilingInfo[$profilerGroupName][$profilingId][$lastPIndex]['isForceClosed'] = true;
            }
        }

        /**
         * Initializing profiling Record
         */
        $this->_profilingInfo[$profilerGroupName][$profilingId][] = array('caption' => substr($profilingCaption, 0, 1024), 'startTime' => microtime(true), 'totalTime' => 0, 'isForceClosed' => true, );

        return true;
    }

    /**
     * Sets end Breakpoint for profiler
     *
     * @param  string $profilingId
     * @param  string $profilerGroupName
     * @param  array  $additionalInfo
     * @return boolean
     */
    public function setEndProfilerBreakpoint($profilingId = 0, $profilerGroupName = false, $additionalInfo = null)
    {

        if (!$this->_enabled) return false;

        if (!$profilerGroupName) $profilerGroupName = $this->_defaultProfilerGroup;

        /**
         * Checking is Profiling already takes place for specified ID
         */
        if (isset($this->_profilingInfo[$profilerGroupName][$profilingId]))
        {
            $lastPIndex = count($this->_profilingInfo[$profilerGroupName][$profilingId]) - 1;
            if ((isset($this->_profilingInfo[$profilerGroupName][$profilingId][$lastPIndex])) && (!isset($this->_profilingInfo[$profilerGroupName][$profilingId][$lastPIndex]['endTime'])))
            {
                $endTime = microtime(true);
                $this->_profilingInfo[$profilerGroupName][$profilingId][$lastPIndex]['endTime']       = $endTime;
                $this->_profilingInfo[$profilerGroupName][$profilingId][$lastPIndex]['totalTime']     = $endTime - $this->_profilingInfo[$profilerGroupName][$profilingId][$lastPIndex]['startTime'];
                $this->_profilingInfo[$profilerGroupName][$profilingId][$lastPIndex]['isForceClosed'] = false;

                if ($additionalInfo && is_array($additionalInfo)) $this->_profilingInfo[$profilerGroupName][$profilingId][$lastPIndex] += $additionalInfo;
            }
        }
    }

    /**
     * Sets start Breakpoint for profiler - static alias for setStartProfilerBreakpoint()
     *
     * @param  string $profilingId
     * @param  string $profilingCaption
     * @param  string $profilerGroupName
     * @return boolean
     * @static
     */
    public static function setStartBreakpoint($profilingId = 0, $profilingCaption = false, $profilerGroupName = false)
    {
        return PHP2_System_Profiler::getInstance()->setStartProfilerBreakpoint($profilingId, $profilingCaption, $profilerGroupName);
    }

    /**
     * Sets end Breakpoint for profiler - static alias for setEndProfilerBreakpoint
     *
     * @param  string $profilingId
     * @param  string $profilerGroupName
     * @return boolean
     * @static
     */
    public static function setEndBreakpoint($profilingId = 0, $profilerGroupName = false)
    {
        return PHP2_System_Profiler::getInstance()->setEndProfilerBreakpoint($profilingId, $profilerGroupName);
    }

    /**
     * Traces variable value. Can be used only in the debug mode.
     *
     * @param   string $value        Value of traced variable
     * @param   string $variableName Variable Name displayed in trace
     * @param   string $traceType    Trace type, available values: PHP2_System_Profiler::TRACE_TYPE_PRINT_R, PHP2_System_Profiler::TRACE_TYPE_VAR_DUMP, PHP2_System_Profiler::TRACE_TYPE_VAR_EXPORT
     * @access  public
     */
    public function trace($value, $variableName = false, $traceType = false)
    {
        $variableType = gettype($value);
        if ($variableType == 'object') $variableType = get_class($value);

        $result = array('variableName' => $variableName, 'variableType' => $variableType);

        switch ($traceType)
        {
            case self::TRACE_TYPE_VAR_DUMP:
                ob_start();
                    var_dump($value);
                    $traceResult = ob_get_contents();
                ob_end_clean();

                $result['traceResult'] = $traceResult;
            break;

            case self::TRACE_TYPE_VAR_EXPORT:
                $result['traceResult'] = var_export($value, true);
            break;

            default:
                $result['traceResult'] = print_r($value, true);
            break;
        }

        $this->_tracedVariables[] = $result;
    }

    /**
     * Returns general server Info
     *
     * @param   boolean $buildTemplateVariables
     * @return  array
     * @access  protected
     */
    protected function _getGeneralServerInfo($buildTemplateVariables = false)
    {
        /**
         * Set general server info
         */
        $generalServerInfo = array();
        if (isset($_SERVER['SERVER_NAME'])) $generalServerInfo[] = array('Server name:', $_SERVER['SERVER_NAME']);
        if (isset($_SERVER['SERVER_ADDR'])) $generalServerInfo[] = array('Server IP:', $_SERVER['SERVER_ADDR']);
        if (isset($_SERVER['REMOTE_ADDR'])) $generalServerInfo[] = array('Client IP:', $_SERVER['REMOTE_ADDR']);
        $generalServerInfo[] = array('Server time:', date(PHP2_Utils_DateTime::FORMAT_DATETIME_SYSTEM));
        $generalServerInfo[] = array('GMT time:', gmdate(PHP2_Utils_DateTime::FORMAT_DATETIME_SYSTEM));
        $generalServerInfo[] = array('Memory Usage:', number_format(memory_get_usage()).' bytes');
        $generalServerInfo[] = array('Memory Peak Usage:', number_format(memory_get_peak_usage()).' bytes');

        if ($buildTemplateVariables)
        {
            $generalServerInfo[] = array('Total time:', '{{totalTime}}');
            $generalServerInfo[] = array('Profiling time:', '{{profilingTime}}');
        }

        return $generalServerInfo;
    }

    /**
     * Return Profiling results as XML Object
     *
     * @return  PHP2_WebService_VO_XML
     * @access  public
     */
    public function __toXML()
    {
        $result = new PHP2_WebService_VO_XML();

        /**
         * Set general server info
         */
        $generalServerInfo = $this->_getGeneralServerInfo();
        $xmlObjectsList    = array();
        foreach ($generalServerInfo as &$parameterInfo)
        {
            $xmlObjectsList[] = new PHP2_WebService_VO_XML((string) $parameterInfo[1], array('label' => (string) $parameterInfo[0]));
        }
        $result->ServerInfo->Parameter = $xmlObjectsList;

        $groupsProfilingInfo = array();
        foreach ($this->_profilingInfo as $groupName => $groupInfo)
        {
            $groupCodeName       = preg_replace('/[^\w\d]+/', '_', $groupName);
            $tmpProfilingResult  = array();
            $totalGroupTime      = 0;
            $groupProfilingInfo  = new PHP2_WebService_VO_XML();

            if (count($groupInfo) > 1)
            {
                foreach ($groupInfo as $subgroupCode => $subgroupInfo)
                {
                    $tmpSubgroupProfilingInfo = array();
                    $totalSubgroupTime        = 0;
                    $subgroupProfilingInfo    = new PHP2_WebService_VO_XML();
                    foreach ($subgroupInfo as $value)
                    {
                        $tmpSubgroupProfilingInfo[]['attributes'] = $value;
                        $totalGroupTime += $value['totalTime'];
                        $totalSubgroupTime += $value['totalTime'];
                    }
                    $subgroupProfilingInfo->Item = $tmpSubgroupProfilingInfo;
                    if ($groupName == self::PROFILER_GROUP_DB)
                    {
                    	$subgroupProfilingInfo->attributes['ID']         = preg_replace('/([\w]+)\:([\w]+)\@/', '******:******@', $subgroupCode);
	                    $subgroupProfilingInfo->attributes['caption']    = $subgroupProfilingInfo->attributes['ID'];
                    }
                    else
                    {
                    	$subgroupProfilingInfo->attributes['ID']         = $subgroupCode;
	                    $subgroupProfilingInfo->attributes['caption']    = $subgroupCode;
                    }
                    $subgroupProfilingInfo->attributes['totalTime']  = sprintf('%01.5f', $totalSubgroupTime);
                    $subgroupProfilingInfo->attributes['itemsCount'] = count($tmpSubgroupProfilingInfo);

                    $tmpProfilingResult[] = $subgroupProfilingInfo;
                }

                if ($tmpProfilingResult) $groupProfilingInfo->SubGroup = $tmpProfilingResult;
            }
            else
            {
                $profiledItemDetails = each($groupInfo);

                if (isset($profiledItemDetails['value']))
                {
                    foreach ($profiledItemDetails['value'] as $value)
                    {
                        $tmpProfilingResult[]['attributes'] = $value;
                        $totalGroupTime += $value['totalTime'];
                    }
                }

                if ($tmpProfilingResult) $groupProfilingInfo->Item = $tmpProfilingResult;
            }

            $groupProfilingInfo->attributes['ID']         = $groupName;
            $groupProfilingInfo->attributes['caption']    = $this->_profilingGroupsInfo[$groupName]['caption'];
            $groupProfilingInfo->attributes['totalTime']  = sprintf('%01.5f', $totalGroupTime);
            $groupProfilingInfo->attributes['itemsCount'] = count($tmpProfilingResult);

            $groupsProfilingInfo[] = $groupProfilingInfo;
        }

        $result->ProfilerGroups->Group      = $groupsProfilingInfo;
        $result->TracedVariables->Variable  = $this->_tracedVariables;

        return $result;
    }


    /**
     * Return Profiling results as HTML String
     *
     * @return  string
     * @access  public
     */
    public function __toHTML()
    {
        if (!$this->getEnabled()) return '';

        $tplEngine = new PHP2_UI_RBTEngine();
        $tplEngine->loadFromFile(dirname(__FILE__).'/profiler.ui.debug.block.tpl');

        $tplEngine['staticUrl'] = STATIC_ROOT_URL;

        /**
         * Set general server info
         */
        $generalServerInfo = $this->_getGeneralServerInfo(true);
        foreach ($generalServerInfo as &$parameterInfo)
        {
            $tplEngine->blcGeneralInfo['variableName']   = $parameterInfo[0];
            $tplEngine->blcGeneralInfo['variableValue']  = $parameterInfo[1];
            $tplEngine->blcGeneralInfo->replace();
        }

        $i = 0;
        $totalTime = 0;
        foreach ($this->_profilingInfo[self::PROFILER_GROUP_AUTOLOAD][0] as &$autoloadInfo)
        {
            $tplEngine->blcProfilingAutoload['index']      = ++$i;
            $tplEngine->blcProfilingAutoload['className']  = $autoloadInfo['caption'];
            $tplEngine->blcProfilingAutoload['loadTime']   = sprintf('%01.5f', $autoloadInfo['totalTime']);
            $tplEngine->blcProfilingAutoload->replace();

            $totalTime += $autoloadInfo['totalTime'];
        }
        $tplEngine['classesCount']       = $i;
        $tplEngine['totalAutoloadTime']  = sprintf('%01.5f', $totalTime);

        /**
         * Processing Main group Info
         */
        $i = 0;
        foreach ($this->_profilingInfo[self::PROFILER_GROUP_MAIN] as $caption => &$profilingInfo)
        {
            $groupItemsCount = 0;
            $groupLoadTime   = 0;
            foreach ($profilingInfo as &$itemInfo)
            {
                $tplEngine->blcGroup->blcItemInfo['index']    = ++$groupItemsCount;
                $tplEngine->blcGroup->blcItemInfo['caption']  = $itemInfo['caption'];
                $tplEngine->blcGroup->blcItemInfo['execTime'] = sprintf('%01.5f', $itemInfo['totalTime']);
                $tplEngine->blcGroup->blcItemInfo->replace();

                $totalTime      += $itemInfo['totalTime'];
                $groupLoadTime  += $itemInfo['totalTime'];
            }

            $tplEngine->blcGroup['groupName']   = preg_replace('/([\w]+)\:([\w]+)\@/', '******:******@', $caption);
            $tplEngine->blcGroup['totalCount']  = $groupItemsCount;
            $tplEngine->blcGroup['totalTime']   = sprintf('%01.5f', $groupLoadTime);
            $tplEngine->blcGroup->replace();
        }

        /**
         * Processing traced variables
         */
        if (count($this->_tracedVariables))
        {
            $i = 0;
            foreach ($this->_tracedVariables as &$itemInfo)
            {
                $tplEngine->blcTracedVariables->blcItemInfo['index']  = ++$i;
                $tplEngine->blcTracedVariables->blcItemInfo['name']   = $itemInfo['variableName'] ? $itemInfo['variableName'] : '-';
                $tplEngine->blcTracedVariables->blcItemInfo['type']   = $itemInfo['variableType'];
                $tplEngine->blcTracedVariables->blcItemInfo['value']  = $itemInfo['traceResult'];
                $tplEngine->blcTracedVariables->blcItemInfo->replace();
            }
            $tplEngine->blcTracedVariables->replace();
        }

        if (is_array($this->_profilingInfo[self::PROFILER_GROUP_DB]) && count($this->_profilingInfo[self::PROFILER_GROUP_DB]))
        {
            /**
             * Processing Database group Info
             */
            $i             = 0;
            $totalTime     = 0;
            $totalQueries  = 0;
            foreach ($this->_profilingInfo[self::PROFILER_GROUP_DB] as $caption => &$profilingInfo)
            {
                $groupItemsCount = 0;
                $groupLoadTime   = 0;
                foreach ($profilingInfo as &$itemInfo)
                {
                    $queryResult       = isset($itemInfo['queryResult']) ? (boolean) (intval($itemInfo['queryResult'])) : true;
                    $queryResultClass  = ($queryResult ? (($itemInfo['totalTime'] > 0.01) ? 'needsOptimization' : 'successQuery') : 'invalidQuery');

                    $tplEngine->blcDatabase->blcDBGroup->blcQueryInfo['queryResultClass']  = $queryResultClass;
                    $tplEngine->blcDatabase->blcDBGroup->blcQueryInfo['queryNum']          = ++$groupItemsCount;
                    $tplEngine->blcDatabase->blcDBGroup->blcQueryInfo['queryText']         = $itemInfo['caption'];
                    $tplEngine->blcDatabase->blcDBGroup->blcQueryInfo['execTime']          = sprintf('%01.5f', $itemInfo['totalTime']);
                    $tplEngine->blcDatabase->blcDBGroup->blcQueryInfo->replace();

                    $totalTime      += $itemInfo['totalTime'];
                    $groupLoadTime  += $itemInfo['totalTime'];
                    $i++;
                }

                $tplEngine->blcDatabase->blcDBGroup['dbGroupName']  = preg_replace('/([\w]+)\:([\w]+)\@/', '******:******@', $caption);
                $tplEngine->blcDatabase->blcDBGroup['totalCount']   = $groupItemsCount;
                $tplEngine->blcDatabase->blcDBGroup['totalTime']    = sprintf('%01.5f', $groupLoadTime);
                $tplEngine->blcDatabase->blcDBGroup->replace();
            }
            $tplEngine->blcDatabase['totalQueriesCount']  = $i;
            $tplEngine->blcDatabase['totalDBTime']        = sprintf('%01.5f', $totalTime);

            $tplEngine->blcDatabase->replace();
        }

        $tplEngine->replace();

        $result = $tplEngine->renderOutput();

        return $result;
    }

}
