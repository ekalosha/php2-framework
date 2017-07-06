<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains PHP2 autoload Library
 *
 * PHP version 5
 * @category   System
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 97 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 3.0
 * @package    PHP2\System
 */

    if (!isset($GLOBALS['__AUTOLOAD_CACHE_CLEARS'])) $GLOBALS['__AUTOLOAD_CACHE_CLEARS'] = false;

    // --- Loading AutoLoad Config if It is not Loaded --- //
    loadAutoLoadConfig();

    /**
     * Classes autoload function. Loads classes data from configuration files.
     *
     * @param    string $autoloadedClassName
     * @return   boolean
     * @package  PHP2\System
     */
    function __autoload($autoloadedClassName)
    {
        $result = false;

        // --- Starting autoload profiling --- //
        PHP2_System_Profiler::getInstance()->setStartProfilerBreakpoint(PHP2_System_Profiler::DEFAULT_ID, $autoloadedClassName, PHP2_System_Profiler::PROFILER_GROUP_AUTOLOAD);

        if (isset($GLOBALS['__AUTOLOAD_CLASSES_CONFIG'][$autoloadedClassName]))
        {
            $result = require_once($GLOBALS['__AUTOLOAD_CLASSES_CONFIG'][$autoloadedClassName]);
        }
        else
        {
            $nameSpaces = explode('_', $autoloadedClassName);
            if (isset($GLOBALS['__AUTOLOAD_EXCEPTION_NAMESPACES'][$nameSpaces[0]]))
            {
                $fullFileName  = BASE_PATH.str_replace('_', '/', $autoloadedClassName).'.php';
            }
            else
            {
                $baseFileName  = strtolower($autoloadedClassName);
                $baseFileName  = str_replace(array('_', ), array('/', ), $baseFileName);
                $fullFileName  = BASE_PATH.$baseFileName.'.class.php';
            }

            $result = require_once($fullFileName);
        }

        // --- Ending autoload profiling --- //
        PHP2_System_Profiler::getInstance()->setEndProfilerBreakpoint(PHP2_System_Profiler::DEFAULT_ID, PHP2_System_Profiler::PROFILER_GROUP_AUTOLOAD);

        return $result;
    }

    /**
     * Loads AutoLoad Config
     *
     * @package  PHP2\System
     */
    function loadAutoLoadConfig()
    {
        $cacheAutoLoadData = (defined('CACHE_AUTOLOAD_DATA') && (CACHE_AUTOLOAD_DATA));

        // --- Clearing autoload cache if autoload cache is disabled in config --- //
        if ((!$cacheAutoLoadData) && isset($GLOBALS['__AUTOLOAD_CLASSES_CONFIG']))
        {
            unset($GLOBALS['__AUTOLOAD_CLASSES_CONFIG']);
            $GLOBALS['__AUTOLOAD_CACHE_CLEARS'] = true;
        }

        // --- Loading AutoLoad Config if it is not Loaded --- //
        if ((!isset($GLOBALS['__AUTOLOAD_CLASSES_CONFIG'])) || !(is_array($GLOBALS['__AUTOLOAD_CLASSES_CONFIG'])))
        {
            if (($cacheAutoLoadData) && (file_exists($autoloadCacheFile = CONFIG_PATH.'__cache/__autoload.cache')))
            {
                // --- If AutoLoad Cache enabled, then Load AutoLoad Data from Cache --- //
                $GLOBALS['__AUTOLOAD_CLASSES_CONFIG'] = unserialize(file_get_contents($autoloadCacheFile));
            }
            else
            {
                // --- Creating Global AutoLoading classes config --- //
                $GLOBALS['__AUTOLOAD_CLASSES_CONFIG'] = array();

                // --- Including Autoloading site configs --- //
                include_once(CONFIG_PATH.'__autoload/classes.config.inc.php');

                // --- Saving AutoLoad Data To Cache --- //
                if (($cacheAutoLoadData) && (!file_exists($autoloadCacheFile))) file_put_contents($autoloadCacheFile, serialize($GLOBALS['__AUTOLOAD_CLASSES_CONFIG']));
            }

        }
    }
