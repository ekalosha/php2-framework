<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Driver Class Storage Manager class
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 102 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 3.0
 */


/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace PHP2\FileStorage;

/**
 * PHP2 Remote Storage driver Class
 *
 * @author      Eugene A. Kalosha <ekalosha@gmail.com>
 * @version     $Id: driver.class.php 102 2009-12-02 12:09:16Z eugene $
 * @access      public
 * @package     PHP2\FileStorage
 */
class PHP2_FileStorage_Driver
{
    /**
     * Storage TYPE constants
     */
    const STORAGE_TYPE_HTTP   = 'HTTP';
    const STORAGE_TYPE_LOCAL  = 'LOCAL';

    /**
     * Unique Storage ID
     *
     * @var     integer
     * @access  protected
     */
    protected $_storageId;

    /**
     * Unique Storage Device
     *
     * @var     string
     * @access  protected
     */
    protected $_storageDevice = 'data';

    /**
     * Storage Type
     *
     * @var     string
     * @access  protected
     */
    protected $_storageType;

    /**
     * Storage Manager URL
     *
     * @var     string
     * @access  protected
     */
    protected $_storageWSUrl;

    /**
     * Storage ROOT URL
     *
     * @var     string
     * @access  protected
     */
    protected $_rootUrl;

    /**
     * Storage Data URL
     *
     * @var     string
     * @access  protected
     */
    protected $_dataUrl;

    /**
     * Last response array
     *
     * @var     array
     * @access  protected
     */
    protected $_lastResponse;

    /**
     * Last Response String
     *
     * @var     string
     * @access  protected
     */
    protected $_lastResponseContent;

    /**
     * Last XML Response Object
     *
     * @var     SimpleXMLElement
     * @access  protected
     */
    protected $_lastXMLResponseObject;

    /**
     * Storage driver class constructor
     *
     * @access  public
     */
    public function __construct()
    {
        $this->_storageId    = 1;
        $this->_storageType  = self::STORAGE_TYPE_HTTP;
        $this->setRootUrl(STORAGE_ROOT_URL);
    }

    /**
     * Set Storage Root Url and Manager Url
     *
     * @param   string $storageRootUrl
     * @return  void
     * @access  public
     */
    public function setRootUrl($storageRootUrl)
    {
        $this->_rootUrl       = $storageRootUrl.($storageRootUrl[strlen($storageRootUrl) - 1] == '/' ? '' : '/');
        $this->_storageWSUrl  = $this->_rootUrl.'storage.php';
        $this->_dataUrl       = $this->_rootUrl.$this->_storageDevice.'/';
    }

    /**
     * Set Storage Device and Data URL
     *
     * @param   string $storageDevice
     * @return  void
     * @access  public
     */
    public function setDevice($storageDevice)
    {
        $this->_storageDevice  = $storageDevice;
        $this->_dataUrl        = $this->_rootUrl.$this->_storageDevice.'/';
    }

    /**
     * Returns subdir for file in the storage
     *
     * @param  string $shortFileName
     * @return string
     */
    public static function getStorageSubDir($shortFileName)
    {
        return substr($shortFileName, 0, 2).'/'.substr($shortFileName, 2, 2).'/';;
    }

    /**
     * Return File URL in the storage
     *
     * @access  public
     * @return  string
     */
    public function getFileUrl($shortFileName)
    {
        $shortFileName = basename($shortFileName);

        $subDir  = self::getStorageSubDir($shortFileName);

        return $this->_dataUrl.$subDir.$shortFileName;
    }

    /**
     * Return File URL in the storage
     *
     * @access  public
     * @return  string
     */
    public static function getStoredFileUrl($shortFileName, $device = false)
    {
        $shortFileName = basename($shortFileName);
        $subDir        = self::getStorageSubDir($shortFileName);
        $dataUrl       = STORAGE_ROOT_URL.(($device) ? $device : STORAGE_DEFAULT_DEVICE);

        return $dataUrl.'/'.$subDir.$shortFileName;
    }

    /**
     * Adds Item to the Storage
     *
     * @param   string $fullFilename
     * @param   string $createdFilename
     * @param   string $device
     * @return  array
     * @access  protected
     */
    public function add($fullFilename, $createdFilename = false, $device = false)
    {
        if (!file_exists($fullFilename)) return false;

        $storedFilename = ($createdFilename ? $createdFilename : basename($fullFilename));
        $fileContent    = file_get_contents($fullFilename);

        return $this->filePutContents($fileContent, $storedFilename, $device);
    }

    /**
     * Creates new Item in the Storage
     *
     * @param   string $fileData
     * @param   string $createdFilename
     * @param   string $device
     * @return  array
     * @access  protected
     */
    public function filePutContents($fileData, $createdFilename, $device = false)
    {
        $device         = ($device ? $device : $this->_storageDevice);

        $httpSocket = new PHP2_Net_HTTPSocket();
        // $httpSocket->setConsole($this);

        $addItemUrl = $this->_storageWSUrl.'?action=add&device='.$device.'&fileName='.$createdFilename;
        $httpSocket->putFileContent($addItemUrl, $fileData);
        $this->_parseXMLSocketResponse($httpSocket->getResponseBody());

        $this->_lastResponse['FileUrl']  = (string) $this->_lastXMLResponseObject->Response->FileUrl;
        $this->_lastResponse['FileName'] = (string) $this->_lastXMLResponseObject->Response->FileName;

        return (boolean) (intval($this->_lastXMLResponseObject->Response->Result));
    }

    /**
     * Copies Item in the Storage
     *
     * @param   string $srcFilename
     * @param   string $destFilename
     * @param   string $srcDevice
     * @param   string $destDevice
     * @return  array
     * @access  protected
     */
    public function copy($srcFilename, $destFilename, $srcDevice = false, $destDevice = false)
    {
        $copyItemUrl = $this->_storageWSUrl.'?action=copy&device='.$this->_storageDevice.'&srcFilename='.$srcFilename.'&destFilename='.$destFilename;
        if ($srcDevice) $copyItemUrl  .= '&srcDevice='.$srcDevice;
        if ($destDevice) $copyItemUrl .= '&destDevice='.$destDevice;

        $httpSocket = new PHP2_Net_HTTPSocket();
        $httpSocket->get($copyItemUrl);
        $this->_parseXMLSocketResponse($httpSocket->getResponseBody());

        $this->_lastResponse['DestFileUrl']  = (string) $this->_lastXMLResponseObject->Response->DestFileUrl;
        $this->_lastResponse['DestFileName'] = (string) $this->_lastXMLResponseObject->Response->DestFileName;

        return (boolean) (intval($this->_lastXMLResponseObject->Response->Result));
    }

    /**
     * Deletes Item from the Storage
     *
     * @param   string $fileName Deleted filename
     * @param   string $device File device name
     * @return  array
     * @access  protected
     */
    public function delete($fileName, $device = false)
    {
        $httpSocket  = new PHP2_Net_HTTPSocket();
        $device      = ($device ? $device : $this->_storageDevice);
        $itemUrl     = $this->_storageWSUrl.'?action=delete&device='.$device.'&fileName='.$fileName;
        $httpSocket->get($itemUrl);
        $this->_parseXMLSocketResponse($httpSocket->getResponseBody());

        return (boolean) (intval($this->_lastXMLResponseObject->Response->Result));
    }

    /**
     * Checks Item in the the Storage
     *
     * @param   string $fileName Checked filename
     * @param   string $device File device name
     * @return  array
     * @access  protected
     */
    public function check($fileName, $device = false)
    {
        $httpSocket  = new PHP2_Net_HTTPSocket();
        $device      = ($device ? $device : $this->_storageDevice);
        $copyItemUrl = $this->_storageWSUrl.'?action=check&device='.$device.'&fileName='.$fileName;
        $httpSocket->get($copyItemUrl);
        $this->_parseXMLSocketResponse($httpSocket->getResponseBody());

        return (boolean) (intval($this->_lastXMLResponseObject->Response->Result));
    }

    /**
     * Returns Last Response content
     *
     * @return  string
     * @access  public
     */
    public function getLastResponseContent()
    {
        return $this->_lastResponseContent;
    }

    /**
     * Returns last response as array
     *
     * @return  array
     * @access  public
     */
    public function getLastResponse()
    {
        return $this->_lastResponse;
    }

    /**
     * Parses XML socket response
     *
     * @return  void
     */
    protected function _parseXMLSocketResponse($responseXMLString)
    {
        $this->_lastResponseContent = $responseXMLString;
        $this->_lastResponse        = array();

        $lastXMLTagPos = strrpos('>', $responseXMLString);
        if ($openXMLTagPos = strpos($responseXMLString, '<')) $responseXMLString = substr($responseXMLString, $openXMLTagPos, $lastXMLTagPos - $openXMLTagPos - 1);
        $this->_lastXMLResponseObject = new SimpleXMLElement($responseXMLString);
    }

}
