<?php
error_reporting(E_ALL);

define("LOCAL_WEB_PATH","C:/mirg_inetpub/wwwroot/" ); 
define("LOCAL_ROOT_PATH",LOCAL_WEB_PATH."casimir/game/" );
define("EXTERNAL_ROOT_PATH","http://chivm.soi.city.ac.uk/casimir/game/" );

// Paths to Clients, tools, library
define("SERVER_PATH", EXTERNAL_ROOT_PATH . "server/ooo/"); //Path to the server folder. 
define("CLIENT_PATH", LOCAL_ROOT_PATH . "camir_gameServer"); //Path to ooo client
define("COMMON_PATH", LOCAL_ROOT_PATH . "common/"); //Path to server and client files and classes

define("FINAL_GAME_CLIENT_PATH", "http://chivm.soi.city.ac.uk/casimir/game/" . "camir_gameClient/"); //Path to ooo client


define("APP_ID","INSERT APP ID HERE"); //facebook application identifier graph id
define("APP_SECRET","INSERT APP SECRET HERE");

// MySql Connection
define("MYSQL_HOST",'XXX');
define("MYSQL_USER",'XXX');
define("MYSQL_PASS",'XXX');
define("MYSQL_NAME",'XXX');

// Add toolbox folder to include , ZEND Framework
$newIncludePath = array();
$newIncludePath[] = '.';
$newIncludePath[] = '../toolboxes';
$newIncludePath[] = '../../common';
$newIncludePath[] = '../../common/data_models';
$newIncludePath[] = '../../toolboxes';
// new position of ZEND and LimeJs Framework
$newIncludePath[] = LOCAL_WEB_PATH . '/_toolboxes';
$newIncludePath[] = get_include_path();
$newIncludePath = implode(PATH_SEPARATOR, $newIncludePath);
set_include_path($newIncludePath);
?>
