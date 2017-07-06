<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains class that implements Commandline script for Loading Published properties from Template file to the UI Page class
 *
 * PHP version 5
 * @category   CLI Applications
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2009 by "SolArt xIT Ltd."
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 96 $
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
 * Defining constants
 */
define('APPLICATION_UI_PATH', BASE_PATH.'application/ui/');
define('UI_TEMPLATES_PATH', BASE_PATH.'ui/');

/**
 * Class implements Commandline script for Loading Published properties from Template file to the UI Page class
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: loaduicomponentsdefinition.class.php 96 2009-08-19 08:43:26Z eugene $
 * @access   public
 * @package  Application\CLI\System
 */
class Application_CLI_System_LoadUIComponentsDefinition extends PHP2_System_Console_CommandLine
{
    /**
     * Default skin name
     *
     */
    const DEFAULT_SKIN_NAME = 'common';

    /**
     * Default backup extensions for published backup and backup files
     */
    const DEFAULT_PUBLISHED_BACKUP_EXTENSION  = 'pbkp';
    const DEFAULT_BACKUP_EXTENSION            = 'bkp';

    /**
     * Short command line options array
     *
     * @var array
     */
    protected $_shortOptions = 'hqp:t:dbrn';

    /**
     * All command line options array
     *
     * @var array
     */
    protected $_longOptions = array(
                                      array('help',                     0, 'h'),
                                      array('page-class-file',          1, 'p'),
                                      array('template-file',            1, 't'),
                                      array('create-backup',            0, 'd'),
                                      array('create-as-backup',         0, 'b'),
                                      array('remove-published-block',   0, 'r'),
                                      array('nonskinned-mode',          0, 'n'),
                                      array('quiet',                    0, 'q'),
                                  );

    /**
     * Page class filename
     *
     * @var     string
     * @access  protected
     */
    protected $_pageClassFile;

    /**
     * Page class file contents
     *
     * @var     string
     * @access  protected
     */
    protected $_pageClassFileContents;

    /**
     * Begin Published block Label
     *
     * @var   string
     */
    protected $_beginPublishedBlock = '// {{{ Begin:Published';

    /**
     * End Published block Label
     *
     * @var   string
     */
    protected $_endPublishedBlock = '// End:Published }}}';

    /**
     * Contains start position of the published Block
     *
     * @var     integer
     * @access  protected
     */
    protected $_beginPublishedBlockPos;

    /**
     * Contains end position of the published Block
     *
     * @var     integer
     * @access  protected
     */
    protected $_endPublishedBlockPos;

    /**
     * End Line symbol
     *
     * @var   string
     */
    protected $_endLine = "\r\n";

    /**
     * Tab symbol
     *
     * @var   string
     */
    protected $_tab = "    ";

    /**
     * Template filename
     *
     * @var     string
     * @access  protected
     */
    protected $_templateFile;

    /**
     * Create backup File flag
     *
     * @var     boolean
     */
    protected $_createBackup = true;

    /**
     * Code Author Name
     *
     * @var     string
     * @access  protected
     */
    protected $_author = 'Eugene A. Kalosha <ekalosha@gmail.com>';

    /**
     * Use Skins flag
     *
     * @var     boolean
     * @access  protected
     */
    protected $_useSkins = null;

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
        $this->_programDescription  = 'Loads Published page Properties From Template script';

        // --- Defining Current Author --- //
        if (defined('APPLICATION_DEVELOPER')) $this->_author = APPLICATION_DEVELOPER;
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

        /**
         * Initializing UI Class filename from commandline options
         */
        $this->_initUIClassFilename();

        // --- Remove Published Block Area --- //
        if ($this->checkIsOptionSet('r'))
        {
            $this->_removePublishedBlock();
            $this->terminate();
        }

        /**
         * Initializing UI Template filename
         */
        $this->_initUITemplateFilename();

        // --- Receiving Skinnable Mode --- //
        if ($this->checkIsOptionSet('n')) $this->_useSkins = false;

        // --- Loading Controls to page --- //
        $this->_loadComponentsToPageClass();

    }

    /**
     * Initializes UI Class file name
     *
     * @access  protected
     * @return  string
     */
    protected function _initUIClassFilename()
    {
        // --- Processing main actions --- //
        if (!$this->checkIsOptionSet('p')) throw new ELoadUIComponentsDefinitionException(ELoadUIComponentsDefinitionException::ERROR_CLASS_FILENAME_NOT_SPECIFIED);

        $this->_pageClassFile = preg_replace('/[\\\\\\/]+/', '/', $this->getOptionValue('p'));

        /**
         * Checking application UI class file
         */
        if (file_exists(APPLICATION_UI_PATH.$this->_pageClassFile))
        {
            $this->_pageClassFile = APPLICATION_UI_PATH.$this->_pageClassFile;
        }
        elseif (!file_exists($this->_pageClassFile))
        {
            throw new ELoadUIComponentsDefinitionException(ELoadUIComponentsDefinitionException::ERROR_CLASS_FILENAME_NOT_EXISTS);
        }

        // --- Find Real Path of the UI Class Name --- //
        $this->_pageClassFile = realpath($this->_pageClassFile);
        $this->_pageClassFile = preg_replace('/[\\\\\\/]+/', '/', $this->_pageClassFile);
    }

    /**
     * Initializes UI Template file name
     *
     * @access  protected
     * @return  string
     */
    protected function _initUITemplateFilename()
    {
        if ($this->checkIsOptionSet('t'))
        {
            $this->_templateFile = $this->getOptionValue('t');

            // --- Triyng To Find Template File --- //
            $this->writeln();
            $this->writeln("Trying to find template file at the current location:");
            $this->write($this->_templateFile);

            if (!file_exists($this->_templateFile))
            {
                $this->writeln(" - NOT FOUND");
                $this->writeDelimiterLine();

                // --- Trying to find template file in the Skin location --- //
                $this->_templateFile = UI_TEMPLATES_PATH.'/'.$this->_templateFile;
                $this->_templateFile = preg_replace('/[\\\\\\/]+/', '/', $this->_templateFile);

                $this->writeln("Trying to find template file at the current location:");
                $this->write($this->_templateFile);

                if (!file_exists($this->_templateFile))
                {
                    $this->writeln(" - NOT FOUND");
                    $this->writeDelimiterLine();
                    throw new ELoadUIComponentsDefinitionException(ELoadUIComponentsDefinitionException::ERROR_DEFAULT_TEMPLATE_NOT_EXISTS);
                }

            }

            $this->writeln(" - OK");
            $this->writeDelimiterLine();
            $this->writeln();
        }
        else
        {
            $tmpClassDirName = str_replace(APPLICATION_UI_PATH, '', dirname($this->_pageClassFile).'/');
            $tmpBaseNameInfo = explode('.', basename($this->_pageClassFile));

            // --- Trying to find template file in the Skin location --- //
            $this->_templateFile = UI_TEMPLATES_PATH.self::DEFAULT_SKIN_NAME.'/'.$tmpClassDirName.'/'.$tmpBaseNameInfo[0].'.tpl';
            $this->_templateFile = preg_replace('/[\\\\\\/]+/', '/', $this->_templateFile);

            // --- Triyng To Find Template File --- //
            $this->writeln();
            $this->writeln("Trying to find template file at the current location:");
            $this->write($this->_templateFile);

            if (!file_exists($this->_templateFile))
            {
                $this->writeln(" - NOT FOUND");
                $this->writeDelimiterLine();

                // --- Trying to find template file in the Skin location --- //
                $this->_templateFile = UI_TEMPLATES_PATH.$tmpClassDirName.'/'.$tmpBaseNameInfo[0].'.tpl';
                $this->_templateFile = preg_replace('/[\\\\\\/]+/', '/', $this->_templateFile);

                $this->writeln("Trying to find template file at the current location:");
                $this->write($this->_templateFile);

                if (!file_exists($this->_templateFile))
                {
                    $this->writeln(" - NOT FOUND");
                    $this->writeDelimiterLine();
                    throw new ELoadUIComponentsDefinitionException(ELoadUIComponentsDefinitionException::ERROR_DEFAULT_TEMPLATE_NOT_EXISTS);
                }

            }

            $this->writeln(" - OK");
            $this->writeDelimiterLine();
            $this->writeln();
        }
    }

    /**
     * Removes Published Block from the UI Class File
     *
     * @access  protected
     * @throws  ELoadUIComponentsDefinitionException if IO error appears when Writing to the Target file
     */
    protected function _removePublishedBlock()
    {
        $this->_pageClassFileContents = file_get_contents($this->_pageClassFile);

        if (!$this->_getPublishedBlockLocation($this->_pageClassFileContents))
        {
            $this->writeln("There is no Published Block Found in the Pages class file: $this->_pageClassFile");
            $this->writeln();
        }
        else
        {
            $codeBeforePublishedBlock  = substr($this->_pageClassFileContents, 0, $this->_beginPublishedBlockPos);
            $codeAfterPublishedBlock   = substr($this->_pageClassFileContents, $this->_endPublishedBlockPos + strlen($this->_endPublishedBlock));

            $eregResult = true;
            while ($eregResult)
            {
                $prevSymbolPos = strlen($codeBeforePublishedBlock) - 1;
                $prevSymbol = $codeBeforePublishedBlock{$prevSymbolPos};
                preg_match('/[\n\t\r\s]{1}/s', $prevSymbol, $matches);
                if (count($matches))
                {
                    $codeBeforePublishedBlock = substr($codeBeforePublishedBlock, 0, $prevSymbolPos);
                }
                else
                {
                    $eregResult = false;
                }
            }

            $eregResult = true;
            while ($eregResult)
            {
                $nextSymbol = $codeAfterPublishedBlock{0};
                preg_match('/[\n\t\r\s]{1}/s', $nextSymbol, $matches);
                if (count($matches))
                {
                    $codeAfterPublishedBlock = substr($codeAfterPublishedBlock, 1);
                }
                else
                {
                    $eregResult = false;
                }
            }

            $pageClassFileContents = $codeBeforePublishedBlock.$this->_endLine.$this->_endLine.$this->_tab.$codeAfterPublishedBlock;

            $this->write("Triyng to Write Data into the Page Class File $this->_pageClassFile");
            if (!file_put_contents($this->_pageClassFile, $pageClassFileContents)) throw new ELoadUIComponentsDefinitionException(ELoadUIComponentsDefinitionException::ERROR_CANT_WRITE_TO_FILE, array('fileName' => $this->_pageClassFile));
            $this->writeln(" - SUCCESS");
        }
    }

    /**
     * Returns published Block Location for page Class file content
     *
     * @param   string $pageClassFileContents
     * @access  protected
     */
    protected function _getPublishedBlockLocation($pageClassFileContents)
    {
        if ($beginBlockLocation = strpos($pageClassFileContents, $this->_beginPublishedBlock))
        {
            if (!$endBlockLocation = strpos($pageClassFileContents, $this->_endPublishedBlock)) throw new ELoadUIComponentsDefinitionException(ELoadUIComponentsDefinitionException::ERROR_INVALID_PUBLISHED_BLOCK);

            $this->_beginPublishedBlockPos = $beginBlockLocation;
            $this->_endPublishedBlockPos   = $endBlockLocation;

            return true;
        }

        return false;
    }

    /**
     * Load controls data to page class file
     *
     * @access  protected
     * @throws  ELoadUIComponentsDefinitionException if Load process faileures
     */
    protected function _loadComponentsToPageClass()
    {
        $this->_pageClassFileContents = file_get_contents($this->_pageClassFile);

        $this->_checkPublishedBlock();

        // --- Creating Page object, parsing Template file and creating all Controls --- //
        $controlsLoader = new UIComponentsLoader();
        $controlsLoader->loadTemplate($this->_templateFile);
        $controlsLoader->parseTemplate();

        $controlsDefinition = $controlsLoader->getControlsDefinition();
        $publishedBlockCopntents = $this->_endLine;
        foreach ($controlsDefinition as &$controlDefinition)
        {
            /* @var $controlDefinition PHP2_UI_ControlDefinition */
            $publishedBlockCopntents .= $this->_getControlPublishedBlockContents($controlDefinition);
        }
        $publishedBlockCopntents .= $this->_endLine;

        // --- End of the "Creating Page object, parsing Template file and creating all Controls" --- //

        $codeBeforePublishedBlock = substr($this->_pageClassFileContents, 0, $this->_beginPublishedBlockPos + strlen($this->_beginPublishedBlock));
        $codeAfterPublishedBlock = substr($this->_pageClassFileContents, $this->_endPublishedBlockPos);
        $pageClassFileContents = $codeBeforePublishedBlock.$this->_endLine.$this->_getInfoBlock().$publishedBlockCopntents.$this->_tab.$codeAfterPublishedBlock;

        if ($this->checkIsOptionSet('b'))
        {
            $fileName = $this->_pageClassFile.'.'.self::DEFAULT_PUBLISHED_BACKUP_EXTENSION;

            $this->write("Triyng to Write Data into the Page Class Published backup File: $fileName");
            if (!file_put_contents($fileName, $pageClassFileContents)) throw new ELoadUIComponentsDefinitionException(ELoadUIComponentsDefinitionException::ERROR_CANT_WRITE_TO_FILE, array('fileName' => $fileName));
            $this->writeln(" - SUCCESS");
            $this->writeln();
        }
        elseif (!$this->checkIsOptionSet('d'))
        {
            $this->write("Triyng to Write Data into the Page Class File! ");
            if (!file_put_contents($this->_pageClassFile, $pageClassFileContents)) throw new ELoadUIComponentsDefinitionException(ELoadUIComponentsDefinitionException::ERROR_CANT_WRITE_TO_FILE, array('fileName' => $this->_pageClassFile));
            $this->writeln(" - SUCCESS");
            $this->writeln();
        }
        else
        {
            $bkpFileName = $this->_pageClassFile.'.'.self::DEFAULT_BACKUP_EXTENSION;
            $this->write("Triyng to Create Backup of the Page Class File: $bkpFileName");
            if (!copy($this->_pageClassFile, $bkpFileName))
            {
                throw new ELoadUIComponentsDefinitionException(ELoadUIComponentsDefinitionException::ERROR_CANT_COPY_FILE, array('destFileName' => $this->_pageClassFile, 'destFileName' => $bkpFileName));
            }
            $this->writeln(" - SUCCESS");

            $this->write("Triyng to Write Data into the Page Class File: $this->_pageClassFile");
            if (!file_put_contents($this->_pageClassFile, $pageClassFileContents)) throw new ELoadUIComponentsDefinitionException(ELoadUIComponentsDefinitionException::ERROR_CANT_WRITE_TO_FILE, array('fileName' => $this->_pageClassFile));
            $this->writeln(" - SUCCESS");
            $this->writeln();
        }
    }

    /**
     * Checks published Block
     *
     * @access  protected
     * @throws  ELoadUIComponentsDefinitionException if Page Class Not founded
     */
    protected function _checkPublishedBlock()
    {
        if (!$this->_getPublishedBlockLocation($this->_pageClassFileContents))
        {
            $constructorPattern = '/(((\r?\n?[\s]*)*(\/\*\*)([\\\\\*\/\w\d\s\@\$\_\-]*)(\*\/)[\s]*)?(public){0,1}[\s]*function[\s]*__construct)/s';
            preg_match($constructorPattern, $this->_pageClassFileContents, $matches);

            if (count($matches))
            {
                $publishedBlock  = $this->_endLine.$this->_endLine;
                $publishedBlock .= $this->_tab.$this->_beginPublishedBlock.$this->_endLine;
                $publishedBlock .= $this->_tab.$this->_endPublishedBlock.$this->_endLine.$this->_endLine;
                $this->_pageClassFileContents = preg_replace($constructorPattern, $publishedBlock.'\1', $this->_pageClassFileContents, 1);
            }
            else
            {
                $classPattern = '/(class ([\*\w\d\s\_]*){)/s';
                preg_match($classPattern, $this->_pageClassFileContents, $matches);

                if (!count($matches)) throw new ELoadUIComponentsDefinitionException(ELoadUIComponentsDefinitionException::ERROR_PAGE_CLASS_NOT_FOUND);

                $publishedBlock  = $this->_endLine.$this->_endLine;
                $publishedBlock .= $this->_tab.$this->_beginPublishedBlock.$this->_endLine;
                $publishedBlock .= $this->_tab.$this->_endPublishedBlock.$this->_endLine.$this->_endLine;
                $this->_pageClassFileContents = preg_replace($classPattern, '\1'.$publishedBlock, $this->_pageClassFileContents, 1);
            }

            $this->_getPublishedBlockLocation($this->_pageClassFileContents);

        }

        return true;
    }

    /**
     * Return Info block contents for the Published Block
     *
     * @return  string
     * @access  protected
     */
    protected function _getInfoBlock()
    {
        $result  = $this->_endLine;
        $result .= $this->_tab.'/**'.$this->_endLine;
        $result .= $this->_tab.' * Automatically generated Published Block, which Contains Controls from Template.'.$this->_endLine;
        $result .= $this->_tab.' * Generation time: '.date('Y-m-d, H:i:s').';'.$this->_endLine;
        $result .= $this->_tab.' * '.$this->_endLine;
        $result .= $this->_tab.' * Warning:'.$this->_endLine;
        $result .= $this->_tab.' *'.$this->_endLine;
        $result .= $this->_tab.' *     Do not Remove this block from template manually.'.$this->_endLine;
        $result .= $this->_tab.' *     If you want to remove this block use commandline script loaduicomponentsdefinition.phpcli'.$this->_endLine;
        $result .= $this->_tab.' *     with flag -r.'.$this->_endLine;
        $result .= $this->_tab.' *'.$this->_endLine;
        $result .= $this->_tab.' * Example:'.$this->_endLine;
        $result .= $this->_tab.' *'.$this->_endLine;
        $result .= $this->_tab.' *     loaduicomponentsdefinition.phpcli --page-class-file="page.class.file.php" -r'.$this->_endLine;
        $result .= $this->_tab.' *'.$this->_endLine;
        $result .= $this->_tab.' * @author '.$this->_author.$this->_endLine;
        $result .= $this->_tab.' */'.$this->_endLine;

        return  $result;
    }

    /**
     * Return contents of the published documented block for control
     *
     * @param   PHP2_UI_ControlDefinition $controlDefinition  Control definition
     * @return  string
     * @access  protected
     */
    protected function _getControlPublishedBlockContents($controlDefinition)
    {
        $result  = $this->_endLine;
        $result .= $this->_tab.'/**'.$this->_endLine;
        $result .= $this->_tab.' * Automatically generated Published field for \''.$controlDefinition->getComponentName().'\' control'.$this->_endLine;
        $result .= $this->_tab.' * '.$this->_endLine;
        $result .= $this->_tab.' * @var      '.$controlDefinition->getComponentClass().$this->_endLine;
        $result .= $this->_tab.' * @access   public'.$this->_endLine;
        $result .= $this->_tab.' */'.$this->_endLine;
        $result .= $this->_tab.'public $'.$controlDefinition->name.';'.$this->_endLine;

        return  $result;
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
        $result .= "    -p, --page-class-file        Page Class Filename.\n";
        $result .= "    -t, --template-file          Template filename.\n";
        $result .= "    -d, --create-backup          Create backup of Page Class file.\n";
        $result .= "    -b, --create-as-backup       Creates Result file, as backup file.\n\n";
        $result .= "    -r, --remove-published-block Removes Published Block from the Page\n";
        $result .= "                                 class file.\n\n";
        $result .= "    -n, --nonskinned-mode        Dont use skins for current Page Class file\n";
        $result .= "    -q, --quiet                  Work in the quiet mode - do not show any \n";
        $result .= "                                 messages, except Help and Fatal Error messages.\n";

        return $result;
    }

}

/**
 * Load Controls to Page Exception Class
 *
 * @author      Eugene A. Kalosha <ekalosha@gmail.com>
 * @access      public
 * @package     Application\CLI\System
 * @subpackage  Exceptions
 */
class ELoadUIComponentsDefinitionException extends PHP2_Exception_EAbstractException
{
    const ERROR_CLASS_FILENAME_NOT_SPECIFIED    = 1031;
    const ERROR_CLASS_FILENAME_NOT_EXISTS       = 1032;
    const ERROR_TEMPLATE_FILENAME_NOT_EXISTS    = 1033;
    const ERROR_DEFAULT_TEMPLATE_NOT_EXISTS     = 1034;
    const ERROR_INVALID_PUBLISHED_BLOCK         = 1041;
    const ERROR_PAGE_CLASS_NOT_FOUND            = 1042;
    const ERROR_CANT_WRITE_TO_FILE              = 1043;
    const ERROR_CANT_COPY_FILE                  = 1044;

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
            case self::ERROR_CLASS_FILENAME_NOT_SPECIFIED : $errorMessage = 'You must specify Class filename! Please see program help!'; break;
            case self::ERROR_CLASS_FILENAME_NOT_EXISTS    : $errorMessage = 'Class filename does not exists! Please specify valid class filename!'; break;
            case self::ERROR_TEMPLATE_FILENAME_NOT_EXISTS : $errorMessage = 'Template filename does not exists! Please specify valid template filename!'; break;
            case self::ERROR_DEFAULT_TEMPLATE_NOT_EXISTS  : $errorMessage = "Template filename does not exists! \nPlease specify valid template filename manually, using '--template-file' parameter!\n"; break;
            case self::ERROR_INVALID_PUBLISHED_BLOCK      : $errorMessage = 'Published Block is corrupt. Please Remove Published Block from Page class file manualy and try again.'; break;
            case self::ERROR_PAGE_CLASS_NOT_FOUND         : $errorMessage = 'There is no Page Class found. Please create Page class in the current file!'; break;
            case self::ERROR_CANT_WRITE_TO_FILE           :
                if ($filename = $this->getParameter('fileName'))
                {
                    $errorMessage = "Error writing Data into file: '$filename'! Please Check write file permissions!";
                }
                else
                {
                    $errorMessage = 'Error writing Data into file! Please Check write file permissions!';
                }
            break;

            case self::ERROR_CANT_COPY_FILE               :
                if (($destFileName = $this->getParameter('destFileName')) || ($srcFileName = $this->getParameter('srcFileName')))
                {
                    $errorMessage = "Error copy file file: \n    '$srcFileName'\nto\n    '$destFileName'! Please Check write file permissions!";
                }
                else
                {
                    $errorMessage = 'Error copy file! Please Check write file permissions!';
                }
            break;

            default : $errorMessage = 'Error: Unrecognized UI definitions Loader Exception!'; break;
        }

        return $errorMessage;
    }
}

/**
 * Class realizes page for components definition loading
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @access   public
 * @package  Application\CLI\System
 */
class UIComponentsLoader extends PHP2_UI_Application
{

    /**
     * Returns controls definition for UI object
     *
     * @return array
     */
    public function getControlsDefinition()
    {
        return $this->_controlsDefinition;
    }

    /**
     * Class destructor
     *
     * @access  public
     */
    public function __destruct(){}

    /**
     * Add reference of the existed control to current container
     *
     * @param   string $controlName
     * @param   PHP2_UI_Control $controlObject
     * @return  void
     * @access  public
     */
    public function addChild($controlName, &$controlObject)
    {
        $this->{$controlName} = $controlObject;

        if ($controlObject instanceof PHP2_UI_Control) $this->_controlsDefinition[$controlName] = $controlObject->getControlDefinition();
    }
}
