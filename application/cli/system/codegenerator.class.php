<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains class that implements Commandline script for generating DB and BS classes
 *
 * PHP version 5
 * @category   CLI Applications
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2009 by "SolArt xIT Ltd."
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 97 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 3.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace Application\CLI\System;

/**
 * Class implements Commandline script for generating DB and BS classes
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: codegenerator.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  Application\CLI\System
 */
class Application_CLI_System_CodeGenerator extends PHP2_System_Console_CommandLine
{
    /**
     * Short command line options array
     *
     * @var array
     */
    protected $_shortOptions = 'hqp:t:dblk:n:s:';

    /**
     * All command line options array
     *
     * @var array
     */
    protected $_longOptions = array(
                                      array('help',                     0, 'h'),
                                      array('db-table-name',            1, 'p'),
                                      array('generate-class-type',      1, 't'),
                                      array('dont-create-backup',       0, 'd'),
                                      array('create-as-backup',         0, 'b'),
                                      array('show-tables-list',         0, 'l'),
                                      array('quiet',                    0, 'q'),
                                  );

    /**
     * Default backup extensions for published backup and backup files
     */
    const DEFAULT_PUBLISHED_BACKUP_EXTENSION = 'pbkp';

    /**
     * Template filename
     *
     * @var     string
     * @access  protected
     */
    protected $templateFile;

    /**
     * Create backup File flag
     *
     * @var     boolean
     * @access  protected
     */
    protected $_createBackup = true;

    /**
     * Current Table name
     *
     * @var     string
     * @access  protected
     */
    protected $_tableName = false;

    /**
     * Code Author Name
     *
     * @var     string
     * @access  protected
     */
    protected $_author = 'noAuthorDefined@email.me';

    /**
     * Code Version
     *
     * @var     string
     * @access  protected
     */
    protected $_version = '1.0';

    /**
     * Class Constructor
     *
     * @access public
     */
    public function __construct()
    {
        // --- Calling Parent Constructor --- //
        parent::__construct();

        // --- Specifiyng Program Details --- //
        $this->_programDescription  = 'Generates Database and Business Layer Classes';

        // --- Defining Current Author --- //
        if (defined('APPLICATION_DEVELOPER')) $this->_author = APPLICATION_DEVELOPER;
        if (defined('APPLICATION_VERSION')) $this->_version = APPLICATION_VERSION;
    }


    /**
     * Run method
     *
     * @access public
     */
    public function run()
    {
        // --- Processing "Show Help" event --- //
        if ($this->checkIsOptionSet('h'))
        {
            $this->showHelp();
            $this->terminate();
        }

        // --- Processing "Show Tables List" Event --- //
        if ($this->checkIsOptionSet('l'))
        {
            $this->showTablesList($this->getOptionValue('l'));
            $this->terminate();
        }

        // --- Triyng to Generate Code for Table --- //
        try
        {
            if (!$this->checkIsOptionSet('p')) throw new ECodeGeneratorException(ECodeGeneratorException::ERROR_TABLE_NAME_NOT_SPECIFIED);
            $this->_tableName = $this->getOptionValue('p');

            $classType = (!$this->checkIsOptionSet('t')) ? 'DB' : $this->getOptionValue('t');
            switch (strtolower($classType))
            {
                case 'bslayer':
                case 'bs':
                case 'business':
                    $this->generateBSLayerClass();
                break;

                default:
                    $this->generateDBLayerClass();
                break;
            }

        }
        catch (ECodeGeneratorException $codeGeneratorError)
        {
            $this->writeErrorLine($codeGeneratorError->getMessage());
        }

    }

    /**
     * Program command line options help
     *
     * @access  protected
     * @return  string
     */
    protected function getProgramOptionsHelp()
    {
        $result  = "    -h, --help                   Display this help message and exit.\n";
        $result .= "    -p, --db-table-name          DataBase Table Name.\n";
        $result .= "    -t, --generate-class-type    File Type: db(dblayer), bs(bslayer, business).\n";
        $result .= "    -d, --dont-create-backup     Dont Create backup Files.\n";
        $result .= "    -b, --create-as-backup       Creates Result files, as backup file.\n";
        $result .= "    -l, --show-tables-list       Return Tables List From Database.\n\n";
        $result .= "    -q, --quiet                  Work in the quiet mode - do not show any \n";
        $result .= "                                 messages, except Help and Fatal Error messages.\n";

        return $result;
    }

    /**
     * Show tables List
     *
     * @access   protected
     */
    protected function showTablesList($tablePattern = '')
    {
        $sqlQuery = "SHOW TABLES".($tablePattern ? ' LIKE \''.$tablePattern.'%\';' : ';');

        // --- Receiving Tables List --- //
        $tablesArray = PHP2_Database_SQLQuery::executeQuery($sqlQuery, PHP2_Database_ConnectionsPool::getInstance()->getConnection(null, PHP2_Database_ConnectionsPool::CONNECTION_TYPE_WRITE))->getMatrix();

        $this->writeln();
        $this->writeln("Tables list".(($tablePattern ? ' LIKE \''.$tablePattern.'%\':' : ':')));
        $this->writeDelimiterLine();

        foreach ($tablesArray as $tableData)
        {
            $tmpTableArray  = each($tableData);
            $tableName      = $tmpTableArray['value'];

            $this->writeln($tableName);
        }

        $this->writeDelimiterLine();
        $this->writeln("Matched tables count: ".count($tablesArray));
        $this->writeln();
    }

    /**
     * Generates DBLayer Class
     *
     * @access   protected
     * @throws   ECodeGeneratorException if Generation failed
     */
    protected function generateDBLayerClass()
    {
        // --- Receiving Tables List --- //
        try
        {
            $sqlQuery = "SELECT * FROM `$this->_tableName` LIMIT 0";
            $sqlQueryObject = PHP2_Database_SQLQuery::executeQuery($sqlQuery, PHP2_Database_ConnectionsPool::getInstance()->getConnection(null, PHP2_Database_ConnectionsPool::CONNECTION_TYPE_WRITE));
        }
        catch (PHP2_Exception_EDatabaseException $database)
        {
            throw new ECodeGeneratorException(ECodeGeneratorException::ERROR_TABLE_NOT_EXISTS, array('tableName' => $this->_tableName));
        }

        // --- Creting Version Controller Instance --- //
        // $__php2VersionController = PHP2VersionController::singleton();

        // --- Loading DB File Template --- //
        $dbLayerTPLName = dirname(__FILE__).'/codegenerator/template.db.class.phptpl';
        $tplEngine = new PHP2_UI_RBTEngine();
        $tplEngine->loadFromFile($dbLayerTPLName);

        $fieldsList  = $sqlQueryObject->getFieldsList();
        $fieldsCount = count($fieldsList);
        $i = 0;
        foreach ($fieldsList as $fieldMetaInfo)
        {
            $tplEngine->dbField->row['fieldName'] = $fieldMetaInfo->name;

            switch ($fieldMetaInfo->type)
            {
                case MYSQLI_TYPE_DATE:
                case MYSQLI_TYPE_DATETIME:
                    $tplEngine->dbField->row['fieldType'] = 'integer';
                break;

                case MYSQLI_TYPE_INT24:
                case MYSQLI_TYPE_LONG:
                case MYSQLI_TYPE_LONGLONG:
                case MYSQLI_TYPE_SHORT:
                case MYSQLI_TYPE_NEWDECIMAL:
                case MYSQLI_TYPE_TIMESTAMP:
                    $tplEngine->dbField->row['fieldType'] = 'integer';
                break;

                default :
                    $tplEngine->dbField->row['fieldType'] = 'string';
                break;
            }

            $tplEngine->dbField->replace();
            $i++;
        }

        $tplEngine->row['dbTableName']       = $this->_tableName;
        $tplEngine->row['currentVersion']    = $this->_version;
        $tplEngine->row['currentDeveloper']  = $this->_author;
        $tplEngine->row['currentDate']       = date('Y-m-d');
        $tplEngine->row['generationTime']    = date('Y-m-d, H:i:s');
        $tplEngine->row['shortFileName']     = strtolower($this->_tableName);
        $tplEngine->row['dbLayerClassName']  = 'Application_DBLayer_'.strtoupper($this->_tableName{0}).substr($this->_tableName, 1);
        $tplEngine->replace();

        $dbLayerFileContent = $tplEngine->renderOutput();
        $dbLayerFileName    = BASE_PATH.'application/dblayer/'.strtolower($this->_tableName).'.class.php';

        $this->saveFile($dbLayerFileName, $dbLayerFileContent);

        return true;
    }

    /**
     * Generates BSLayer Class
     *
     * @access   protected
     * @throws   ECodeGeneratorException if Generation failed
     */
    protected function generateBSLayerClass()
    {
        // --- Receiving Tables List --- //
        try
        {
            $sqlQuery = "SELECT * FROM `$this->_tableName` LIMIT 0";
            $sqlQueryObject = PHP2_Database_SQLQuery::executeQuery($sqlQuery, PHP2_Database_ConnectionsPool::getInstance()->getConnection(null, PHP2_Database_ConnectionsPool::CONNECTION_TYPE_WRITE));
        }
        catch (PHP2_Exception_EDatabaseException $database)
        {
            throw new ECodeGeneratorException(ECodeGeneratorException::ERROR_TABLE_NOT_EXISTS, array('tableName' => $this->_tableName));
        }

        // --- Creting Version Controller Instance --- //
        // $__php2VersionController = PHP2VersionController::singleton();

        // --- Loading DB File Template --- //
        $bsLayerTPLName = dirname(__FILE__).'/codegenerator/template.bs.class.phptpl';
        $tplEngine = new PHP2_UI_RBTEngine();
        $tplEngine->loadFromFile($bsLayerTPLName);

        $tplEngine->row['dbTableName']       = $this->_tableName;
        $tplEngine->row['currentDeveloper']  = $this->_author;
        $tplEngine->row['currentVersion']    = $this->_version;
        $tplEngine->row['currentDate']       = date('Y-m-d');
        $tplEngine->row['generationTime']    = date('Y-m-d, H:i:s');
        $tplEngine->row['shortFileName']     = strtolower($this->_tableName);
        $tplEngine->row['dbLayerClassName']  = 'Application_DBLayer_'.strtoupper($this->_tableName{0}).substr($this->_tableName, 1);
        $tplEngine->row['bsLayerClassName']  = 'Application_BSLayer_'.strtoupper($this->_tableName{0}).substr($this->_tableName, 1);
        $tplEngine->replace();

        $bsLayerFileContent = $tplEngine->renderOutput();
        $bsLayerFileName    = BASE_PATH.'application/bslayer/'.strtolower($this->_tableName).'.class.php';

        $this->saveFile($bsLayerFileName, $bsLayerFileContent);

        return true;
    }

    /**
     * Saves content into the File
     *
     * @param   string $fileName
     * @param   string $fileContent
     * @return  boolean
     * @access  protected
     * @throws  ECodeGeneratorException if Save Process failed
     */
    protected function saveFile($fileName, $fileContent)
    {
        if (isset($this->clineArguments['b'])) $fileName .= '.'.self::DEFAULT_PUBLISHED_BACKUP_EXTENSION;

        if (!file_exists($fileName) || isset($this->clineArguments['b']))
        {
            if (file_put_contents($fileName, $fileContent))
            {
                $this->writeln("File: $fileName Successfuly generated!");
            }
        }
        else
        {
            throw new ECodeGeneratorException(ECodeGeneratorException::ERROR_GENERATED_FILE_EXISTS, array('fileName' => $fileName, ));
        }
    }

}

/**
 * Code Generator Exception
 *
 * @author      Eugene A. Kalosha <ekalosha@gmail.com>
 * @access      public
 * @package     Application\CLI\System
 * @subpackage  Exceptions
 */
class ECodeGeneratorException extends PHP2_Exception_EAbstractException
{
    const ERROR_TABLE_NAME_NOT_SPECIFIED        = 1131;
    const ERROR_TABLE_NOT_EXISTS                = 1132;
    const ERROR_CANT_WRITE_TO_FILE              = 1133;
    const ERROR_GENERATED_FILE_EXISTS           = 1134;
    const ERROR_TEMPLATE_FILENAME_NOT_EXISTS    = 1135;
    const ERROR_CANT_COPY_FILE                  = 1136;

    /**
     * Exception constructor
     *
     * @param   integer $errorCode Error code
     * @param   array   $exceptionParams Exception additional parameters array
     * @param   string  $errorMessage Displayed Error message
     * @access  public
     */
    public function __construct($errorCode = false, $exceptionParams = false, $errorMessage = false)
    {
        // --- Initializing Parent Object --- //
        parent::__construct($errorCode, $exceptionParams, $errorMessage);
    }

    /**
     * Return Default Error Message Template
     *
     * @param   integer $errorCode
     * @return  string
     * @access  public
     */
    public function getDefaultErrorMessageTemplate($errorCode)
    {
        // --- Initializing Default Error Message Template --- //
        switch ($errorCode)
        {
            case self::ERROR_TABLE_NAME_NOT_SPECIFIED     : $errorMessage = 'Error: You must specify database table name! Please see program help!'; break;
            case self::ERROR_TABLE_NOT_EXISTS             : $errorMessage = 'Error: Specified dataBase table \'{{tableName}}\' does not exists!'; break;
            case self::ERROR_TEMPLATE_FILENAME_NOT_EXISTS : $errorMessage = 'Error: Template File Name not specified!'; break;
            case self::ERROR_GENERATED_FILE_EXISTS        : $errorMessage = 'Error: File \'{{fileName}}\' already Exists! Please Delete This File and Try Again!'; break;
            case self::ERROR_CANT_WRITE_TO_FILE           : $errorMessage = "Error writing data into file: {{fileName}}! Please Check write file permissions!"; break;
            case self::ERROR_CANT_COPY_FILE               : $errorMessage = "Error copy file file: \n    '{{srcFileName}}'\nto\n    '{{destFileName}}'! Please Check write file permissions!"; break;

            default : $errorMessage = 'Error: Unrecognized CodeGenerator Exception! Please contact to the System Administrator!'; break;
        }

        return $errorMessage;
    }
}
