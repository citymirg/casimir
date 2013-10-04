<?php

// allow for php error reporting
error_reporting(E_ALL);
ini_set("display_errors", 0);

define("CLIENT_COMPILED", 1);

define("LOCAL_WEB_PATH","C:/mirg_inetpub/wwwroot/" ); 
define("LOCAL_ROOT_OFFSET","casimir/game/");
define("LOCAL_ROOT_PATH",LOCAL_WEB_PATH. LOCAL_ROOT_OFFSET );

define("EXTERNAL_WEB_PATH","http://chivm.soi.city.ac.uk/" );
define("EXTERNAL_ROOT_PATH",EXTERNAL_WEB_PATH . LOCAL_ROOT_OFFSET );
define("EXTERNAL_MAINROOT_PATH",EXTERNAL_ROOT_PATH ); // proxy? then use central serveradress here

// Paths to Clients, tools, library
define("GAME_SERVER_PATH", EXTERNAL_MAINROOT_PATH . "camir_gameServer/UserConnection/gameServer.php"); //Path to the server folder.
define("SERVER_PATH", EXTERNAL_MAINROOT_PATH . "server/ooo/"); //Path to the server folder.

define("CLIENT_OFFSET","camir_gameClient/"); //Path to ooo client
define("CLIENT_PATH", LOCAL_ROOT_PATH . CLIENT_OFFSET); //Path to ooo client
define("EXTERNAL_CLIENT_PATH", EXTERNAL_ROOT_PATH . CLIENT_OFFSET); //Path to ooo client
define("COMMON_PATH", LOCAL_ROOT_PATH . "common/"); //Path to server and client files and classes


define("APP_ID","INSERT APP ID HERE"); //facebook application identifier
define("APP_SECRET","INSERT APP SECRET HERE");

// MySql Connection
define("MYSQL_HOST",'XXX');
define("MYSQL_USER",'XXX');
define("MYSQL_PASS",'XXX');
define("MYSQL_NAME",'XXX');

// Add toolbox folder to include , ZEND Framework
$newIncludePath = array();
$newIncludePath[] = '.';
$newIncludePath[] = './facebook';
$newIncludePath[] = '../common';
$newIncludePath[] = '../toolboxes';
// new position of ZEND and LimeJs Framework
$newIncludePath[] = LOCAL_WEB_PATH . '_toolboxes';
$newIncludePath[] = get_include_path();
$newIncludePath = implode(PATH_SEPARATOR, $newIncludePath);
set_include_path($newIncludePath);


include_once 'class.FacebookHelper.php';

?>
