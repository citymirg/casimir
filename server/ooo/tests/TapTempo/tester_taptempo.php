<?php
// allow for php error reporting
error_reporting(E_ALL);
ini_set("display_errors", 1);
include_once "../../configServer.php";

// Add toolbox folder to include , ZEND Framework
$newIncludePath = array();
$newIncludePath[] = '.';
$newIncludePath[] = '../../../../common';
$newIncludePath[] = '../../../../common/data_models';
$newIncludePath[] = '../../../../toolboxes';
// new position of ZEND and LimeJs Framework
$newIncludePath[] = LOCAL_WEB_PATH . '_toolboxes';
$newIncludePath[] = get_include_path();
$newIncludePath = implode(PATH_SEPARATOR, $newIncludePath);
set_include_path($newIncludePath);

include_once "../../includes.php";



include_once '../../class.MainServer.php';
include_once 'chronometer.php';
include_once "class.MySql.php";



require_once('../../../../toolboxes/FirePHPCore/FirePHP.class.php');
ob_start();
global $firephp;
$firephp = FirePHP::getInstance(true);

$server = new MainServer();


$details = array();
$details['name'] = 'StatisticsTests293';
$details['bla'] = 'TestForUnexpectedDetails';


$user = $server->startSession($details);
out('User',$user);


$song = $server->getSong('taptempo');
out('Song',$song);


//$vote = new Vote();
$vote = new GenericVote('taptempo');
$vote->user = $user;
$vote->averageBPM = 1;
$vote->stdDevBPM = 1;
$vote->maxDevBPM = 1;
$vote->totalTime = 1;
$vote->songId = 1;


out ('GenericVote',$vote);

out ('sendGenericVote',$server->sendGenericVote($vote));


function out ($titre,$var) {
    echo '<br/>---------------------<br/>
        <p> <h5>'.$titre.'</h5> <br/>
        ';
    var_dump($var);
    echo '</p>
        <br/>--------------------<br/>';
    global $firephp;
    // firephp
    $firephp->log($var, $titre);
};
?>
