<?php
// allow for php error reporting
error_reporting(E_ALL);
ini_set("display_errors", 1);
include_once "../config.php";

/*
 * @todo: why do I only get modules at the Gameclient?
 * 
 */


// @todo: REMOVE DEBUG before production allow for php error reporting
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once '../config.php';
include_once '../includes.php';

require_once('FirePHPCore/FirePHP.class.php');
ob_start();

/*
 * cool debug framework for firefox "FirePHP" plugin
 */
global $firephp;
$firephp = FirePHP::getInstance(true);

// open server connection:
//identify User
$server = new MainServer();
$userDetails = array('name' => 'testFromGameServer3',
        'ipHash' => 'testFromGameServer3');

$matchDetails = array('triplet' => array('genreId' => 1));

// test authentication
$auth = $server->authenticate($userDetails);
out('Authenticate :',$auth);

// save / reflect auth as get variable
$_GET = $auth ;

$server = new MainClient();



$triplet = $server->getTriplet(1);
out('Triplet',$triplet);


//$vote = new Vote();
$vote = new Vote();
$vote->user = $auth->user;
$vote->averageBPM = 1;
$vote->stdDevBPM = 1;
$vote->maxDevBPM = 1;
$vote->totalTime = 1;
$vote->songId = 1;


out ('GenericVote',$vote);

out ('sendGenericVote',$server->sendGenericVote($vote));


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
