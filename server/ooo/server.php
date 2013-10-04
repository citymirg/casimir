<?php


include_once("configServer.php");
include_once("includes.php");

$loader = Zend_Loader_Autoloader::getInstance();
$loader->setFallbackAutoloader(true);
$loader->suppressNotFoundWarnings(false);

require_once "Zend/Soap/Server.php";
require_once "Zend/Soap/AutoDiscover.php";

// configure log4php
//Logger::configure('conf_log4php.xml');
//set_error_handler("customError");


if(isset($_GET['wsdl'])) {
    // BUILD THE XML INFORMATION
   $autodiscover = new Zend_Soap_AutoDiscover('Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex');
   $autodiscover->setClass('MainServer');
   $autodiscover->handle();
} else {
    //TREATING THE REQUEST
   $soap = new Zend_Soap_Server(SERVER_PATH . "server.php?wsdl",array('encoding'=>'ISO-8859-1')); // this current file here
    //Declaring reception classes
   $soap->setClass('MainServer');
    //Identify classes
   $soap->setClassMap(array(    'Vote' => 'Vote',
                                'GenericVote' => 'GenericVote',
                                'Triplet' => 'Triplet',
                                'User' => 'User',
                                'Song' => 'Song'));
   $soap->handle();
}


