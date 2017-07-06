<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains simple Directory-based garbage collector Class
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 97 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 2.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace PHP2\System\FileSystem;

/**
 * Simple Directory-based garbage collector
 *
 * @author      Eugene A. Kalosha <ekalosha@gmail.com>
 * @version     $Id: garbagecollector.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access      public
 * @package     PHP2\System\FileSystem
 */
class PHP2_System_Filesystem_GarbageCollector
{
    /**
     * Root Directory for Garbage
     *
     * @var     string
     * @access  protected
     */
    protected $_rootDir;

    /**
     * List of files that are not allowed for garbage
     *
     * @var     array
     * @access  protected
     */
    protected $_exceptionsList;

    /**
     * Max Number of Checked files for Garbage
     *
     * @var     integer
     * @access  protected
     */
    protected $_maxCheckedItemsCount   = 200;

    /**
     * Max removed files count within one Garbage process
     *
     * @var     integer
     * @access  protected
     */
    protected $_maxGarbagedItemsCount  = 30;

    /**
     * Item expiration time in seconds. By default 1 day.
     *
     * @var     integer
     * @access  protected
     */
    protected $_itemExpirationTime  = 86400;

    /**
     * Garbage collector constructor. Initialize execution parameters.
     *
     * @param   string $rootDir
     * @access  public
     */
    public function __construct($rootDir = false)
    {
        if ($rootDir) $this->setRootDir($rootDir);
    }

    /**
     * Setup current processing dir
     *
     * @param   string $rootDir
     * @return  boolean
     * @access  public
     * @throws  PHP2_Exception_EFilesystemException in case of File/Directory IO error
     */
    public function setRootDir($rootDir)
    {
        if (!$tmpRootDir = realpath($rootDir)) throw new PHP2_Exception_EFilesystemException(PHP2_Exception_EFilesystemException::ERROR_DIRECTORY_NOT_EXISTS, array('fileName' => $rootDir));
        if (!is_dir($tmpRootDir)) throw new PHP2_Exception_EFilesystemException(PHP2_Exception_EFilesystemException::ERROR_FILE_IS_NOT_DIRECTORY, array('fileName' => $rootDir));
        if (!is_writable($rootDir)) throw new PHP2_Exception_EFilesystemException(PHP2_Exception_EFilesystemException::ERROR_DIRECTORY_IS_NOT_WRITABLE, array('fileName' => $rootDir));

        $this->_rootDir = $tmpRootDir.'/';
    }

    /**
     * Set items expiration time in seconds for temporary dir
     *
     * @param   integer $itemExpirationTime in seconds
     * @return  boolean
     * @access  public
     */
    public function setItemExpirationTime($itemExpirationTime)
    {
        $itemExpirationTime = (int) $itemExpirationTime;
        if ($itemExpirationTime < 60) return false;

        $this->_itemExpirationTime = $itemExpirationTime;

        return true;
    }

    /**
     * Set max count of checked Items to clear
     *
     * @param   integer $maxCheckedItemsCount
     * @return  boolean
     * @access  public
     */
    public function setMaxCheckedItemsCount($maxCheckedItemsCount)
    {
        if ($maxCheckedItemsCount && ($maxCheckedItemsCount > 10)) $this->_maxCheckedItemsCount = $maxCheckedItemsCount;
    }

    /**
     * Set max count of Items to garbage in the Garbaged directory
     *
     * @param   integer $maxGarbagedItemsCount
     * @return  boolean
     * @access  public
     */
    public function setMaxGarbagedItemsCount($maxGarbagedItemsCount)
    {
        if ($maxGarbagedItemsCount > 1) $this->_maxGarbagedItemsCount = $maxGarbagedItemsCount;
    }

    /**
     * Set list of exceptions for garbage
     *
     * @param   array $exceptionsList
     * @return  boolean
     * @access  public
     */
    public function setExceptionsList($exceptionsList)
    {
        if (is_array($exceptionsList)) $this->_exceptionsList = $exceptionsList;
    }

    /**
     * Checks is garbage allowed for this file
     *
     * @return  boolean
     * @access  protected
     */
    protected function isGarbageAllowed($filename)
    {
        if (!is_array($this->_exceptionsList) || !(count($this->_exceptionsList))) return true;

        return (in_array($filename, $this->_exceptionsList) !== false);
    }

    /**
     * Process expired files in current Garbage Directory
     *
     * @return  void
     * @access  public
     * @throws  PHP2_Exception_EFilesystemException in case of File/Directory IO error
     */
    public function clean()
    {
        $lastErrorDetails   = false;
        $deletedFilesCount  = 1;
        $checkedFilesCount  = 1;
        if ($hDirectory = opendir($this->_rootDir))
        {
            // --- Reading directory --- //
            while (false !== ($dirElement = readdir($hDirectory)))
            {
                $fileName = $this->_rootDir.$dirElement;

                if (($dirElement != '.') && ($dirElement != '..') && !is_dir($fileName) && $this->isGarbageAllowed($dirElement))
                {
                    if (filemtime($fileName) + $this->_itemExpirationTime < time())
                    {
                        // --- Checking is file writable to delete it --- //
                        if (!is_writable($fileName))
                        {
                            $lastErrorDetails = array('fileName' => $fileName, );
                            continue;
                        }

                        unlink($fileName);

                        // --- Processing unlink limit --- //
                        if ($this->_maxGarbagedItemsCount <= $deletedFilesCount)
                        {
                            if ($lastErrorDetails) throw new PHP2_Exception_EFilesystemException(PHP2_Exception_EFilesystemException::ERROR_CANT_DELETE_FILE, $lastErrorDetails);

                            return true;
                        }

                        $deletedFilesCount++;
                    }

                    // --- Processing checked files Limit --- //
                    if ($this->_maxCheckedItemsCount <= $checkedFilesCount)
                    {
                        if ($lastErrorDetails) throw new PHP2_Exception_EFilesystemException(PHP2_Exception_EFilesystemException::ERROR_CANT_DELETE_FILE, $lastErrorDetails);

                        return true;
                    }

                    $checkedFilesCount++;
                }
            }

            unset($dirElement);
            closedir($hDirectory);
        }
    }

}
