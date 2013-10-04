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

// start server
$server = new MainServer();

// get all active song ids
$M = new MySql();
$M->select('song.basicooo');

foreach($M->aArrayedResults as $res){

    $song = new Song($res['globalId']);

    out ('Song '. $song->id,$song->saveAudio());

    //sleep(5);
}


/*
 * UPDATE Triplet blacklist
 * The SONG blacklist is automatically updated during song download
 */
$sql = ' INSERT INTO `triplet_blacklist` (tripletId)
         (SELECT id FROM `triplet` WHERE
             (song1 IN (SELECT songId FROM songlibrary_blacklist))
             OR (song2 IN (SELECT songId FROM songlibrary_blacklist))
             OR (song3 IN (SELECT songId FROM songlibrary_blacklist)))';
$M->ExecuteSQL($sql);
var_dump($M);



function out ($titre,$var) {
    echo '<br/>---------------------<br/>
        <p> <h5>'.$titre.'</h5> <br/>
        ';
    var_dump($var);
    echo '</p>
        <br/>--------------------<br/>';
};
?>
