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
 * add initial songs per genres and random
 * 
 */

// 10 random songs
//for($i=1; $i<=0; $i++){
//    $adder = new SongAdder();
//
//    $adder->setSongToAddByRandom();
//
//    echo($adder->addSelectedSong());
//}

// most popular genres for both dbs: 
//1589 	Pop/Rock 	47 -
//395 	Electronic 	51 
//298 	Rap 	46         -  
//257 	Classical 	1  -    1    
//237 	Electronica 	8
//220 	New Age 	5
//215 	International 	52 -  
//209 	Rock 	10
//201 	Chamber Music 	6
//195 	R&B 	54         -    2
//182 	Latin 	45         -    3
//180 	World 	9
//179 	Jazz 	55         -    4
//155 	Baroque 	3
//151 	Religious 	60 -
//145 	Country 	49 -    5
//144 	Ambient 	4
//143 	Alt Rock 	17
//110 	Blues 	56         - combinierbar mi mtt 
//98 	Reggae 	58      - 


//$wantedGenres = array(58,46,1,54,52,45,55,49);
//$wantedGenres = array(1,54,45,55,49);
//$gSongs  = 10;
//foreach ($wantedGenres as $genre){
//    
//    // add songs for each of these genres
//
//    // 10 songs per genre
//    for($i=1; $i<=$gSongs; $i++){
//        $adder = new SongAdder('basicooo');
//
//        $adder->setSongOfOneGenre($genre);
//
//        echo($adder->addSelectedSong());
//    }
//    
//    // add triplets for each of these genres
//    for($i=1; $i<=$gSongs; $i++){
//        $adder = new TripletAdder();
//
//        $adder->setTripletOfOneGenre($genre);
//
//        echo($adder->addSelectedTriplet());
//    }
//}
////    
//// update same genre triplet cache
$adder = new TripletAdder();
$adder->updateOneGenreTripletCache();
//

/*
 * add RedHerring Triplets
 */
//$rhTriplets  = 10;
// for($i=1; $i<=$rhTriplets; $i++){
//    $adder = new TripletAdder();
//    $adder->setTripletRedHerring();
//    echo($adder->addSelectedTriplet()); 
// }


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
