<?php


//----------------------
// Server Configuration
//----------------------
include_once '../config.php';
include_once '../includes.php';

// configure log4php
Logger::configure('../conf_log4php.xml');

//set error handler
set_error_handler("customError");


//--------------
// Zend Library
//--------------
include_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();
$loader->setFallbackAutoloader(true);
$loader->suppressNotFoundWarnings(false);

// @todo: REMOVE DEBUG before production allow for php error reporting



//----------------
// ZEND Server SMD
//----------------

$server = new Zend_Json_Server();
$server->setClass('MainServer');
 
if ('GET' == $_SERVER['REQUEST_METHOD']) {
    // Indicate the URL endpoint, and the JSON-RPC version used:
    $server->setTarget('gameServer.php')
           ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
 
    // Grab the SMD
    $smd = $server->getServiceMap();
 
    // Return the SMD to the client
    header('Content-Type: application/json');
    echo $smd;
    return;
}
 
//----------------------
// ZEND Server Handling
//----------------------
$server->handle();

?>