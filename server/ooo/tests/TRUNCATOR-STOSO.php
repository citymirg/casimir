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



require_once('../../../toolboxes/FirePHPCore/FirePHP.class.php');
ob_start();

/*
 * cool debug framework for firefox "FirePHP" plugin
 */
global $firephp;
$firephp = FirePHP::getInstance(true);


$M = new MySql();

/*
 * First: delete all users, votes, songs and triplets after "userId=136, 2012-12-13 02:58:11"
 * this includes data from a. the survey, b. the ismir demo session, c. the people reacting 
 * to a post on music-IR
 * 
 * corresponding vote id: SELECT MAX(id) FROM `vote` WHERE userId=136 returns voteId=319
 *  corresponding session id: SELECT MAX(id) FROM `session` WHERE userId=136  returns 302, taking 304
 * corresponding song.basicooo.gamedependingId=32
 * corresponding tripletId=72
 */ 
//$M->ExecuteSQL('DELETE FROM user WHERE id > 136');
//out("user",$M);
//$M->ExecuteSQL('DELETE FROM session WHERE id > 304');
//out("session",$M);
//$M->ExecuteSQL('DELETE FROM vote WHERE id > 319');
//out("vote",$M);
//$M->ExecuteSQL('DELETE FROM `song.basicooo` WHERE gamedependingId > 32');
//out("song",$M);
//$M->ExecuteSQL('DELETE FROM triplet WHERE id > 72');
//out("triplet",$M);
//
///*
// * Delete Track 159835
// * SELECT * FROM `songlibrary` WHERE url = "1569835" returns id 4481
// * gamedependingid = 163 globalid 4481  counter	15  date 2012-12-18 09:05:09
// */
//$M->ExecuteSQL('DELETE FROM `song.basicooo` WHERE globalid=4481');
//out("song 1569835",$M);
//
//
//// update same genre triplet cache
//$adder = new TripletAdder();
//$adder->updateOneGenreTripletCache();


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
