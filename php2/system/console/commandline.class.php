<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base class for all commandline scripts
 *
 * PHP version 5
 * @category   System Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
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
// namespace PHP2\System\Console;

/**
 * Base class for all commandline scripts
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: commandline.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\System\Console
 * @abstract
 */
abstract class PHP2_System_Console_CommandLine extends PHP2_System_Console_Console
{
    /**
     * Exit code constants
     */
    const EXIT_CODE_INVALID_PARAMETERS = 11010;

    /**
     * Program Name
     *
     * @var     string
     * @access  protected
     */
    protected $_programName;

    /**
     * Program version
     *
     * @var     string
     * @access  protected
     */
    protected $_programVersion;

    /**
     * Program Description
     *
     * @var     string
     * @access  protected
     */
    protected $_programDescription = 'Current Program description.';

    /**
     * String contains _nix C-style command line options
     *
     * @var     string
     * @access  protected
     */
    protected $_shortOptions = 'hqs:e:';

    /**
     * Long command line options array
     *
     * @var     array
     * @access  protected
     */
    protected $_longOptions = array(
                                      array('help',        0, 'h'),
                                      array('quiet',       0, 'q'),
                                  );

    /**
     * Command line arguments for current run
     *
     * @var     array
     * @access  protected
     */
    protected $_executionArguments = array();

    /**
     * Quiet Mode Flag
     *
     * @var     boolean
     * @access  protected
     */
    protected $_quietMode = false;

    /**
     * Is application terminated flag
     *
     * @var     boolean
     * @access  protected
     */
    protected $_isTerminated = false;

    /**
     * Class Constructor. All methods in the descended classes must be called after this method.
     *
     * @access public
     */
    public function __construct()
    {
        /**
         * Calling Parent constructor
         */
        parent::__construct();

        // --- Ignoring Script Execution time Parameter --- //
        set_time_limit(0);
        ini_set('memory_limit', '8191M'); // Set memory limit for Commandline scripts to 8Gb - 1M. You cant set more than 8Gb

        if (!$this->_programName) $this->_programName     = basename($_SERVER['SCRIPT_FILENAME']);
        if (!$this->_programVersion) $this->_programVersion = '3.0';

        $this->_executionArguments = $this->getCommandLineOptions($this->_shortOptions, $this->_longOptions);

        /**
         * Checking quiet mode option
         */
        $this->setQuietMode($this->checkIsOptionSet('q'));
    }

    /**
     * Class destructor
     *
     * @access public
     */
    public function __destruct()
    {
        if (!$this->_isTerminated) $this->terminate();
    }

    /**
     * Abstract run method
     *
     * @abstract
     */
    abstract public function run();

    /**
     * Safety executes run() method
     *
     * @access  public
     */
    public function execute()
    {
        try
        {
            $this->run();
            $this->terminate();
        }
        catch (Exception $cliException)
        {
            $this->writeErrorLine('Error: '.$cliException->getCode().'. '.$cliException->getMessage());
            $this->terminate($cliException->getCode());
        }
    }

    /**
     * Displays command line Program Help
     *
     * @return  string
     * @access  protected
     */
    protected function showHelp()
    {
        $programScriptName = basename($_SERVER['SCRIPT_FILENAME']);

        $helpMessage  = "\nProgram Name: $this->_programName Ver. $this->_programVersion. \n";
        $helpMessage .= "Copyright by SolartXIT, 2006-2008. \n";
        $helpMessage .= "$this->_programDescription\n\n";

        $helpMessage .= "Usage:\n";
        $helpMessage .= "    $programScriptName [Options]\n";
        $helpMessage .= "OR if you do not use executable PHP scripts:\n";
        $helpMessage .= "    path/to/php-cli -f $programScriptName -- [Options]\n";

        $helpMessage .= "\nOptions:\n".$this->getProgramOptionsHelp();

        $helpMessage .= "\n";

        echo $helpMessage;

        return $helpMessage;
    }

    /**
     * Display Program Command line Options Help
     *
     * Displays Program Command line Options Help. You must Override this function to display Valid Help Messages.
     *
     * @return  string
     * @access  protected
     */
    protected function getProgramOptionsHelp()
    {
        $result  = "    -h, --help         Display this help message and exit.\n";
        $result .= "    -q, --quiet        Work in the quiet mode - do not show any messages,\n";
        $result .= "                       except Help messages.\n";

        return $result;
    }

    // --- Command line engine path --- //

    /**
     * Return command line arguments values
     *
     * @param   array $shortOptions Short program options string (As C-style get_opts() options).
     * @param   array $longOptions Long Options array
     * @return  array
     * @access  protected
     */
    protected function getCommandLineOptions($shortOptions, $longOptions = array())
    {
        $result             = array();
        $optionsConfig      = array();
        $commandLineErrors  = array();
        $argv               = $_SERVER['argv'];

        // --- Extractiong Short And Long Options --- //
        preg_match_all('/([\w]{1})([:]{0,1})/', $shortOptions, $matches);
        foreach ($matches[1] as $key => $optionName)
        {
            $optionsConfig[$optionName] = ($matches[2][$key] == ':');
        }
        $longOptionsList = $this->getLongOptionsListFromCommandline();

        foreach ($optionsConfig as $optName => $notEmpty)
        {
            if ($arrayPos = array_search('-'.$optName, $argv))
            {
                if (isset($argv[$arrayPos + 1]) && (($argv[$arrayPos + 1]{0} != '-') || (!isset($optionsConfig[substr($argv[$arrayPos + 1], 1)]))))
                {
                    $result[$optName] = $argv[$arrayPos + 1];
                }
                elseif ($notEmpty)
                {
                    $commandLineErrors[] = 'Error: Option "-'.$optName.'" cannot be empty.';
                }
                else
                {
                    $result[$optName] = '';
                }
            }
        }

        // --- Long options list --- //
        foreach ($longOptions as $optId => $optData)
        {

            $longOptName   = $optData[0];
            $shortOptName  = $optData[2];
            $notEmpty      = $optData[1];
            $optName       = ($optData[2] == '') ? $optData[0] : $optData[2];

            /**
             * Checking is short option value already defined
             */
            if (isset($result[$shortOptName])) $result[$longOptName] = $result[$shortOptName];

            if (array_key_exists($longOptName, $longOptionsList))
            {
                if ($longOptionsList[$longOptName])
                {
                    $result[$longOptName]  = $longOptionsList[$longOptName];
                    $result[$shortOptName] = $longOptionsList[$longOptName];
                }
                elseif ($notEmpty)
                {
                    $commandLineErrors[] = 'Error: Option "--'.$longOptName.'" cannot be empty.';
                }
                else
                {
                    $result[$longOptName]   = '';
                    $result[$shortOptName]  = '';
                }
            }
        }

        // --- Display command line errors part --- //
        if (count($commandLineErrors))
        {
            $this->writeErrorLine();
            foreach ($commandLineErrors as $error) $this->writeErrorLine($error);

            $this->terminate(self::EXIT_CODE_INVALID_PARAMETERS);
        }
        // --- End of the "Display command line errors" part --- //

        return $result;
    }

    /**
     * Parse argv array and finds all Long options
     *
     * @return  array
     * @access  protected
     */
    protected function getLongOptionsListFromCommandline()
    {
        $argv    = $_SERVER['argv'];
        $result  = array();

        foreach ($argv as $argument)
        {
            preg_match('/--([\w\d-_.]+)([=]{1}(.+)){0,1}/', $argument, $matches);
            if (isset($matches[1]))
            {
                $result[$matches[1]] = isset($matches[3]) ? $matches[3] : false;
            }
        }

        return $result;
    }

    /**
     * Writes message to stderr stream
     *
     * @param   string $message Error message string
     * @access  public
     */
    public function writeError($message)
    {
        fwrite(STDERR, $message);
    }

    /**
     * Checks is commandline option set and return true in case of option set, false otherwise.
     *
     * @return  boolean
     * @access  public
     */
    public function checkIsOptionSet($optionName)
    {
        return isset($this->_executionArguments[$optionName]);
    }

    /**
     * Checks is commandline option set and return value of the option.
     * If option is not set - returns null or $defaultValue, if specified.
     *
     * @return  mixed
     * @access  public
     */
    public function getOptionValue($optionName, $defaultValue = null)
    {
        if (!isset($this->_executionArguments[$optionName])) return $defaultValue;

        return $this->_executionArguments[$optionName];
    }

    /**
     * Terminates current commandline script with specified error code (if needed)
     *
     * @param   integer $errorCode Termination code. 0 - correct exit.
     * @return  mixed
     * @access  public
     */
    public function terminate($errorCode = 0)
    {
        $this->_isTerminated = true;

        exit($errorCode);
    }

}
