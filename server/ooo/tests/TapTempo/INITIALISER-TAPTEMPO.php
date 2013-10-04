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

/*
 * cool debug framework for firefox "FirePHP" plugin
 */
global $firephp;
$firephp = FirePHP::getInstance(true);


$M = new MySql();
$query = 'SELECT id FROM songlibrary 
            WHERE 
            (musicdb = 4) 
            AND
            (musicDbLocId IN (13,14,31,33,38,45,47,66,89,94))';

$res = false;
$result = mysql_query( $query );
while ($row = mysql_fetch_array($result)) {
    $song = new Song($row['id']);
    $adder = new SongAdder('taptempo');
    $adder->selectedSong = $song;
    $res = $adder->addSelectedSong();
    out('Song '. $row['id'] .' Added ?'. $res , $song );
}




$M = new MySql();
$query = 'SELECT id FROM songlibrary 
            WHERE 
            (musicdb = 2) 
            AND
            (id IN (1714,2848,6652,5075,9633,2592,1306,1821,7340,2100,7606))';

$result = mysql_query( $query );
while ($row = mysql_fetch_array($result)) {
    $song = new Song($row['id']);
    $adder = new SongAdder('taptempo');
    $adder->selectedSong = $song;
    $res = $adder->addSelectedSong();
    out('Song Added ?'. $res , $song );
}


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
