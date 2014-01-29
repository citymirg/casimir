<?php
error_reporting(E_ALL);
ini_set("display_errors", 0);

/** @const string local path to php scripts */
define("LOCAL_WEB_PATH","C:/mirg_inetpub/wwwroot/" ); 
define("LOCAL_ROOT_PATH",LOCAL_WEB_PATH."casimir/game/" );

/** @const string external http path to game server php scripts */
define("EXTERNAL_ROOT_PATH","http://chivm.soi.city.ac.uk/casimir/game/" );

/** @const string local backup directory for songs played by the users */
// define("RUNTIME_DATA_PATH", LOCAL_WEB_PATH."casimir/data/runtime/" );

// MySql Connection
define("MYSQL_HOST",'XXX');
define("MYSQL_USER",'XXX');
define("MYSQL_PASS",'XXX');
/** @const string game server database name */
define("MYSQL_NAME",'casimir_api'); 

/*
 * End of the user input part
 */

// Paths to Clients, tools, library
define("SERVER_PATH", EXTERNAL_ROOT_PATH . "server/ooo/"); //Path to the server folder. 
define("CLIENT_PATH", LOCAL_ROOT_PATH . "client/ooo/"); //Path to ooo client
define("COMMON_PATH", LOCAL_ROOT_PATH . "common/"); //Path to server and client files and classes


// Add toolbox folder to include , ZEND Framework
$newIncludePath = array();
$newIncludePath[] = '.';
$newIncludePath[] = '../../common';
$newIncludePath[] = '../../common/data_models';
$newIncludePath[] = '../../toolboxes';
// new position of ZEND and LimeJs Framework
//$newIncludePath[] = LOCAL_WEB_PATH . '_toolboxes';
$newIncludePath[] = get_include_path();
$newIncludePath = implode(PATH_SEPARATOR, $newIncludePath);
set_include_path($newIncludePath);

?>
