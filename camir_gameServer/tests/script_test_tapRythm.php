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
$userDetails = array('name' => 'testFromGameServersupernew',
        'ipHash' => 'testFromGameServer3');




// test authentication
$auth = $server->authenticate($userDetails);
out('Authenticate :',$auth);

// save / reflect auth as get variable
$_GET = $auth ;




// start match
$auth = $server->startMatch('testFromScriptForTapRythm',"");
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

$module = $serverdata[2];
out('Module :',$module);
$args = $serverdata[2]['args'];
out('Module :',$module);


out('PlayerState: onModule',$server->setPlayerState('onModule'));

// poll
$server = new MainServer();
out('POLL :',$server->poll());


$tapTimes = array();
$tempo = $args['tempo'];
if($tempo == 0)
    $tempo = 120;
$beatTime = 60000/$tempo;
$t = 0;
        while($t<9000){
            $hit = array();
            $hit['time'] = $t;
            $hit['keyCode'] = 0;
            $hit['charCode'] = 0;
            
            $tapTimes[] = $hit;
            $t = $t + $beatTime;
        }

        out('taptimes',$tapTimes);
        
$fMax = array();
$fVal = array(
            rand(10,40) /1000,
            rand(10,40) /1000,
            rand(10,40) /1000
        );
$fRef = array(
            rand(5,20) /100,
            rand(5,20) /100,
            rand(5,20) /100,
        );
        
$fMax['fRelative'] = $fRef;
$fMax['val'] = $fVal;

        
$result = array(
                            'songId' => $args['id'],
                            'startPlayerTime' => 0,
                            'totalTime' => 0,
                            'ismobile' => false,
                            'fMax' => $fMax,
                            'tapTimes' => null
                            //'tapTimes' => $tapTimes
                                            );



// send voting data
$server = new MainServer();
out('SendData: ',$server->sendData($result));   

// tell we have finished the module
out('PlayerState: moduleDone',$server->setPlayerState('moduleDone'));

// this should return the module
$server = new MainServer();
$serverdata = $server->poll();
out('POLL :',$serverdata);

$playerTable = $serverdata[0];
out('Player Table :',$playerTable);
$module = $serverdata[2];
out('Module :',$module);
$args = $serverdata[2]['args'];
out('Args :',$module);

// tell we have the RESULT module
out('PlayerState: onResultModule',$server->setPlayerState('onResultModule'));
$server = new MainServer();
out('POLL :',$server->poll());


// tell we have finished the module
out('PlayerState: done',$server->setPlayerState('done'));

// poll
$server = new MainServer();
out('POLL :',$server->poll());

//$server = new MainServer();
//out('POLL :',$server->poll());





out('Player Table :',$server->getPlayerTable());



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

echo 'love';

?>
