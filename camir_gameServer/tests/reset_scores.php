<?php

/*
 *
 * this script adds achievements to the
 * game database and registers them in faccebook
 */

// @todo: REMOVE DEBUG before production allow for php error reporting
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once '../config.php';
include_once '../includes.php';

require_once('FirePHPCore/FirePHP.class.php');
ob_start();

/*
 * ------------Reset Facebook Scores
 */
 //Get an App Access Token
//$token_url = 'https://graph.facebook.com/oauth/access_token?'
//. 'client_id=' . APP_ID
//. '&client_secret=' . APP_SECRET
//. '&grant_type=client_credentials';
// echo ('<a href="'.$token_url .'" > tokenUrl </a>');
//
//$token_response = CURL($token_url);
//var_dump($token_response);
//
//$params = array();
//parse_str($token_response, $params);
//
//$oauth_token = $params['access_token'];
//var_dump($oauth_token);
//
//$scoredel_url ='https://graph.facebook.com/'
//               . APP_ID . '/scores';
//
//$result = CURL_DELETE($scoredel_url,'access_token=' . $oauth_token);
//var_dump($result);


/*
 * ------------Reset AI scores
 */
$M = new MySql();
$M->Update('player',array('totalPoints' => 0),array('AI' => 1));
var_dump($M);

/*
 * ------------Reset ALL scores
 */
//$M = new MySql();
//$M->Update('player',array('totalPoints' => 0));
//var_dump($M);
?>
