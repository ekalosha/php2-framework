<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains class realizes Temp Folder Object
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
 * Class realizes Temp Folder Object
 *
 * @author      Eugene A. Kalosha <ekalosha@gmail.com>
 * @version     $Id: tmpfolder.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access      public
 * @package     PHP2\System\FileSystem
 */
class PHP2_System_Filesystem_TMPFolder
{
    /**
     * Temp Folder Root path
     */
    const ROOT_PATH = 'tmp/';

    /**
     * Temp Files Expiration time in seconds. After this time all Files must be deleted from the TMP folder
     */
    const FILES_LIFETIME  = 12000;

    /**
     * Max count of files to delete from the TMP folder
     */
    const MAX_GARBAGED_FILES  = 20;

    /**
     * Max count of files to view before deleting procedure ends
     */
    const MAX_VIEWED_FILES    = 100;

    /**
     * Full Root Path to the current TMP Folder
     *
     * @var   string
     */
    protected $_rootPath;

    /**
     * Short Root Path
     *
     * @var   string
     */
    protected $_rootPathDir;

    /**
     * Garbage collector instance
     *
     * @var   PHP2_System_Filesystem_GarbageCollector
     */
    protected $_garbageCollector;

    /**
     * Creates Temp Folder Object
     *
     * @param   string $rootPath
     * @access  public
     */
    public function __construct($rootPathDir = false)
    {
        $this->_rootPath          = ROOT_PATH.self::ROOT_PATH.(($rootPathDir) ? $rootPathDir.'/' : '');
        $this->_rootPathDir       = (($rootPathDir) ? $rootPathDir.((($rpdLength = strlen($rootPathDir)) && ($rootPathDir{$rpdLength - 1} != '/')) ? '/' : '') : '');

        /**
         * Creating instance of the Garbage collector
         */
        $this->_garbageCollector = new PHP2_System_Filesystem_GarbageCollector($this->_rootPath);
        $this->_garbageCollector->setItemExpirationTime(self::FILES_LIFETIME);
        $this->_garbageCollector->setMaxCheckedItemsCount(self::MAX_VIEWED_FILES);
        $this->_garbageCollector->setMaxGarbagedItemsCount(self::MAX_GARBAGED_FILES);
    }

    /**
     * Clearing all outstanding resources
     *
     * @access  public
     */
    public function __destruct()
    {
        try
        {
            $this->_garbageCollector->clean();
        }
        catch (PHP2_Exception_EFilesystemException $filesystemException)
        {
            echo $filesystemException->getMessage();
        }
    }

    /**
     * Returns Root Path of the Temp Folder
     *
     * @param   string $path
     * @access  public
     */
    public function getRootPath()
    {
        return $this->_rootPath;
    }


    /**
     * Found all Expired Files in the TMP Files Location and delete it
     *
     * @return  boolean
     * @access  public
     */
    public function deleteExpiredFiles()
    {
        $this->_garbageCollector->clean();
    }

    /**
     * Moves Temp file into it destination Location
     *
     * @param   string $tmpFileName  Short tmp file name
     * @param   string $destFileName Full Destination FileName
     * @access  public
     * @throws  PHP2_Exception_EFilesystemException in case of Filesystem exception
     */
    public function moveTMPFile($tmpFileName, $destFileName)
    {
        $fullTMPFileName = $this->getFileName($tmpFileName);

        if (!file_exists($fullTMPFileName)) throw new PHP2_Exception_EFilesystemException(PHP2_Exception_EFilesystemException::ERROR_FILE_NOT_EXISTS, array('filename' => $fullTMPFileName));
        if (!rename($fullTMPFileName, $destFileName)) throw new PHP2_Exception_EFilesystemException(PHP2_Exception_EFilesystemException::ERROR_MOVING_FILE, array('srcFileName' => $fullTMPFileName, 'destFileName' => $destFileName, ));

        return true;
    }

    /**
     * Deletes TMP File from the Temp Folder
     *
     * @param   string $tmpFileName
     * @access  public
     * @throws  PHP2_Exception_EFilesystemException in case of Filesystem exception
     */
    public function unlink($tmpFileName)
    {
        $fullTMPFileName = $this->getFileName($tmpFileName);

        if (file_exists($fullTMPFileName) && !unlink($fullTMPFileName)) throw new PHP2_Exception_EFilesystemException(PHP2_Exception_EFilesystemException::ERROR_CANT_DELETE_FILE);

        return true;
    }

    /**
     * Return Filename in the TMP folder
     *
     * @param   string $filename
     * @access  public
     */
    public function getFileName($filename)
    {
        return $this->_rootPath.$filename;
    }

    /**
     * Return File URL in the TMP folder
     *
     * @param   string $filename
     * @access  public
     */
    public function getFileURL($filename)
    {
        return PHP2_System_Response::getInstance()->getUrlPath(self::ROOT_PATH.$this->_rootPathDir).$filename;
    }

}
