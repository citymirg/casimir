<?php
// allow for php error reporting
error_reporting(E_ALL);
ini_set("display_errors", 1);
include_once "../configServer.php";

// Add toolbox folder to include , ZEND Framework
$newIncludePath = array();
$newIncludePath[] = '.';
$newIncludePath[] = '../../../common';
$newIncludePath[] = '../../../common/data_models';
$newIncludePath[] = '../../../toolboxes';
// new position of ZEND and LimeJs Framework
$newIncludePath[] = LOCAL_WEB_PATH . '_toolboxes';
$newIncludePath[] = get_include_path();
$newIncludePath = implode(PATH_SEPARATOR, $newIncludePath);
set_include_path($newIncludePath);

include_once "../includes.php";



include_once '../class.MainServer.php';
include_once 'chronometer.php';
include_once "class.MySql.php";



/*
 * cool debug framework for firefox "FirePHP" plugin
 */
require_once('../../../toolboxes/FirePHPCore/FirePHP.class.php');
ob_start();
global $firephp;
$firephp = FirePHP::getInstance(true);

$server = new MainServer();


$details = array();
$details['name'] = 'StatisticsTestsStilltesting123';
$details['bla'] = 'TestForUnexpectedDetails';


$user = $server->startSession($details);
out('User',$user);


$matchDetails = array();
//$matchDetails['genreId'] = 1;
$triplet = $server->getTriplet($matchDetails);
out('Triplet',$triplet);


//$vote = new Vote();
$vote = new Vote();
$vote->user = $user;
$vote->totalTime = 1;
$vote->songChosenId = $triplet->aSong[2]->id;
$vote->aSongId = array();
$vote->aSongId[0] = $triplet->aSong[0]->id;
$vote->aSongId[1] = $triplet->aSong[1]->id;
$vote->aSongId[2] = $triplet->aSong[2]->id;
$vote->details['totalTime'] = 10.5;
$vote->details['test'] = 'wuwuwuwu';
$vote->voteType = 'basicooo';

out ('GenericVote',$vote);

out ('sendVote',$server->sendVote($vote));


/*
 * Testing Vote AI
 */
$triplet = array(8853,4486,4496);
out ('loaded triplet',$triplet);
$aiVote = $server->getAIVote($triplet);
out ('aivote',$aiVote);


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
