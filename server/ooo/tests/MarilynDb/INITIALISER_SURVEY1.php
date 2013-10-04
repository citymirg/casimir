<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
include_once("../../configServer.php");

include_once("../../class.MainServer.php");
include_once("../../../../toolboxes/class.MySql.php");

// @todo: REMOVE DEBUG before production allow for php error reporting

require_once('../../../../toolboxes/FirePHPCore/FirePHP.class.php');
ob_start();

/*
 * cool debug framework for firefox "FirePHP" plugin
 */
global $firephp;
$firephp = FirePHP::getInstance(true);

$song_ids = array(1145,1377,2419,2689,3207,3288,3555,3982,4486,4496,5995,6688,
    7497,7565,7946,8090,8853,9215,9859,10208);
// import songs from marilyns db

for ($i=0; $i < sizeof($song_ids); $i++){

    $adder = new SongAdder();
    $adder->selectedSong = new Song($song_ids[$i]);
    $adder->selectedSong->id = $song_ids[$i];

    echo($adder->addSelectedSong());
}


// add 5 new triplets
$adder = new TripletAdder();
for($i=1; $i<=10; $i++){
    $adder->setTripletToAddByRandom();
    $adder->addSelectedTriplet();
}



?>
