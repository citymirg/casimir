<?php
/*
 * This truncates some of the game tables
 *
 */
// allow for php error reporting
error_reporting(E_ALL);
ini_set("display_errors", 1);


include_once("../config.php");
include_once("../includes.php");
include_once("../../toolboxes/class.MySql.php");

$server = new MainServer();

$M = new MySql();
$M->ExecuteSQL('TRUNCATE TABLE  `gamesession`');
out("gamesession",$M);

$M->ExecuteSQL('TRUNCATE TABLE  `curmoduleargs`');
out("curmoduleargs",$M);

$M->ExecuteSQL('TRUNCATE TABLE  `curmoduleresults`');
out("curmoduleresults",$M);        


/*
 * Care wit the following data, this is player and 
 * stuff we might want to log!
 */

$M->ExecuteSQL('TRUNCATE TABLE  `match`');
out("match",$M);

$M->ExecuteSQL('TRUNCATE TABLE  `player`');
out("player",$M);

$M->ExecuteSQL('TRUNCATE TABLE  `linkplayergenre`');
out("linkplayergenre",$M);

$M->ExecuteSQL('TRUNCATE TABLE  `linkplayerui_avatars`');
out("linkplayergenre",$M);

//$M->ExecuteSQL('DELETE FROM triplet WHERE id > 72');
//out("triplet",$M);


function out ($titre,$var) {
    echo '<br/>---------------------<br/>
        <p> <h5>'.$titre.'</h5> <br/>
        ';
    var_dump($var);
    echo '</p>
        <br/>--------------------<br/>';
};
        
?>
