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




print benchmark("Start Initialisation");

///////////////
// CONSTANTS //
///////////////
$server = new MainServer();
$Ntests = 50;


$printEachRound = false;
$printErroredRound = true;


$NsendVoteError = 0;
$NemptyASong = 0;

$Nempty1Song = 0;
$Nempty3Song = 0;

//////////////
//  SCRIPT  //
//////////////
$print = false;


$details = array();
$details['name'] = 'StatisticsTests';
$details['age'] = 0;
$details['country'] = 'TestFromClient';

$user = $server->startSession($details);
$aTriplet = $server->getTriplet();

$vote = new Vote();
$vote->user = $user;



//////////////
//   FOR    //
//////////////
print benchmark("Start for");
for($n=1; $n <= $Ntests;$n++){
    
    
    
    //set_time_limit(20);
    $vote->id = Null;
    $vote->details = array();
    $vote->songChosenId = $aTriplet->aSong[2]->id;
    $vote->aSongId = array();
    $vote->aSongId[0] = $aTriplet->aSong[0]->id;
    $vote->aSongId[1] = $aTriplet->aSong[1]->id;
    $vote->aSongId[2] = $aTriplet->aSong[2]->id;
    
    if(!$server->sendVote($vote)){
        $NsendVoteError++;
        $print = $printErroredRound;
    }
    
    
    $print = $print OR $printEachRound;
    if($print){
        echo '<br><b>Round: '.$n.'</b><br>';
        var_dump($aTriplet);
        echo '<br><br>';
        $print = false;
    }
    
    
    $aTriplet = $server->getTriplet($vote);

    if( empty($aTriplet) ){
        $NemptyASong++;
        $print = $printErroredRound;
    }elseif(
        empty($aTriplet->aSong[0]->url) AND
        empty($aTriplet->aSong[1]->url) AND
        empty($aTriplet->aSong[2]->url)){
        $Nempty3Song++;
        $print = $printErroredRound;
    }
    elseif(
        empty($aTriplet->aSong[0]->url) OR
        empty($aTriplet->aSong[1]->url) OR
        empty($aTriplet->aSong[2]->url)){
        $Nempty1Song++;
        $print = $printErroredRound;
    }
    
}
print benchmark("End for");
            

//////////////
//  RESULTS //
//////////////
echo '<b>';
echo '<br><br>Number of tests: '.$Ntests;
echo '<br><br>sendVote Errors: '.$NsendVoteError;
echo '<br><br>aSong is empty : '.$NemptyASong;
echo '<br><br>3 url of songs are missing : '.$Nempty3Song;
echo '<br>1 or 2 url of songs are missing : '.$Nempty1Song;
echo '</b>';

?>
