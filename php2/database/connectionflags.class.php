<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Database Connection Flags constants
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 97 $
 * @package    PHP2\Database
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 3.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace PHP2\Database;

/**
 * Defining MySQL Connection Flags
 */
// --- Redefined MySQL Connection Constants, from mysql.h --- //
if (!defined('MYSQL_CLIENT_COMPRESS'))     define('MYSQL_CLIENT_COMPRESS',       32); // Can use compression protocol
if (!defined('MYSQL_CLIENT_IGNORE_SPACE')) define('MYSQL_CLIENT_IGNORE_SPACE',  256); // Ignore spaces before '('
if (!defined('MYSQL_CLIENT_INTERACTIVE'))  define('MYSQL_CLIENT_INTERACTIVE',  1024); // This is an interactive client
if (!defined('MYSQL_CLIENT_SSL'))          define('MYSQL_CLIENT_SSL',          2048); // Switch to SSL after handshake
define('MYSQL_CLIENT_LONG_PASSWORD',         1); // New more secure passwords
define('MYSQL_CLIENT_FOUND_ROWS',            2); // Found instead of affected rows
define('MYSQL_CLIENT_LONG_FLAG',             4); // Get all column flags
define('MYSQL_CLIENT_CONNECT_WITH_DB',       8); // One can specify db on connect
define('MYSQL_CLIENT_NO_SCHEMA',            16); // Don't allow database.table.column
define('MYSQL_CLIENT_ODBC',                 64); // Odbc client
define('MYSQL_CLIENT_LOCAL_FILES',         128); // Can use LOAD DATA LOCAL
define('MYSQL_CLIENT_PROTOCOL_41',         512); // New 4.1 protocol
define('MYSQL_CLIENT_IGNORE_SIGPIPE',     4096); // IGNORE sigpipes
define('MYSQL_CLIENT_TRANSACTIONS',       8192); // Client knows about transactions
define('MYSQL_CLIENT_RESERVED',          16384); // Old flag for 4.1 protocol
define('MYSQL_CLIENT_SECURE_CONNECTION', 32768); // New 4.1 authentication
define('MYSQL_CLIENT_MULTI_STATEMENTS',  65536); // Enable/disable multi-stmt support
define('MYSQL_CLIENT_MULTI_RESULTS',    131072); // Enable/disable multi-results
