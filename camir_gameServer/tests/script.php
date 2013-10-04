<?php header('Content-type: text/html; charset=utf-8'); ?>

<?php

/*
 * @todo: why do I only get modules at the Gameclient?
 * 
 */


// @todo: REMOVE DEBUG before production allow for php error reporting
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once '../config.php';
include_once '../includes.php';


// configure log4php
Logger::configure('../conf_log4php.xml');

//require_once('FirePHPCore/FirePHP.class.php');
ob_start();

/*
 * cool debug framework for firefox "FirePHP" plugin
 */
global $firephp;
//$firephp = FirePHP::getInstance(true);

/*
 * Encoding
 */
echo '<?xml version="1.0" encoding="UTF-8"?>';

// open server connection:
//identify User
$server = new MainServer();
$userDetails = array('name' => 'testFromGameServersupernew',
        'ipHash' => 'testFromGameServer3');

$matchDetails = array('triplet' => array('genreId' => 45));


// test authentication
$auth = $server->authenticate($userDetails);
out('Authenticate :',$auth);

// save / reflect auth as get variable
$_GET = $auth ;


//out('GetHighScore :',$server->getHighscore(0,10));
//out('GetAvatarTable :',$server->getAvatarTable());
//out('BuyAvatar 1 :',$server->buyAvatar(1));

out('GetGenreTable :',$server->getGenreTable());
out('BuyAvatar 1 :',$server->buyGenre(54));

// start match
$auth = $server->startMatch('STOSO',$matchDetails);
out('StartMatch :',$auth);
$_GET = $auth;

//get player ids
out('getFromAuth ROOT :',Player::getFromAuth(
        $auth['playerid'],
        $auth['matchid'],
        $auth['sessionid']
        ));

//out('PlayerState: ready',$server->setPlayerState('ready'));


// this should return the module
$server = new MainServer();
$serverdata = $server->poll();
out('POLL :',$serverdata);


$Ntests = 4;
for($n=0; $n <= 0;$n++){
  
// poll
$triplet = $serverdata[2]['args']['aSong'];
// tell we have the module
out('PlayerState: onModule',$server->setPlayerState('onModule'));

// poll
$server = new MainServer();
out('POLL :',$server->poll());

// send voting data
$server = new MainServer();
$vote->details['totalPlayTime'] = 10.5;
$vote->details['test'] = 'wuwuwuwu';
out('SendTriplet: ',$server->sendData(array(
                                        'songChosenId' => $triplet[0]['id'],
                                        'aSong' =>$triplet,
                                        'totalTime' => 10.5,
                                        'song1PlayTime'=> 10.5,
                                        'song2PlayTime'=> 10.5,
                                        'song3PlayTime'=> 10.5,
                                        'songSequence'=> array(2)
                                            )));   

// tell we have finished the module
out('PlayerState: moduleDone',$server->setPlayerState('moduleDone'));


//// test quitting
//out('Player Table :',$server->getPlayerTable());
//out('PlayerState: out',$server->setPlayerState('out'));
//out('Player Table :',$server->getPlayerTable());

// poll
$server = new MainServer();
out('POLL :',$server->poll());

// tell we have the RESULT module
out('PlayerState: onResultModule',$server->setPlayerState('onResultModule'));
$server = new MainServer();
out('POLL :',$server->poll());


// tell we have finished the module
out('PlayerState: done',$server->setPlayerState('done'));

// poll
$server = new MainServer();
out('POLL :',$server->poll());

}


function out ($titre,$var) {
    echo '<br/>---------------------<br/>
        <p> <h5>'.$titre.'</h5> <br/>
        ';
    var_dump($var);
    echo '</p>
        <br/>--------------------<br/>';
    //global $firephp;
    // firephp
    //$firephp->log($var, $titre);
};

echo 'love';

?>
