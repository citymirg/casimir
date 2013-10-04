<?php

/*
 *
 * this script adds achievements to the
 * game database and registers them in faccebook
 */

// @todo: REMOVE DEBUG before production allow for php error reporting
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once '../config.php';
include_once '../includes.php';

require_once('FirePHPCore/FirePHP.class.php');
ob_start();


$src_folder = "../../camir_gameClient/achievements";
$file_prefix = "/";


/*
 * get the achievements from folder and into our db
 */
add_achievements($src_folder, $file_prefix);

/*
 * register the achievements with facebook
 */

//$N = new MySQL();
//$N->Select('fb_session', array( playerId => 1));
//$fb_id = $N->aArrayedResults[0]['id'];
//$oauth_token = $N->aArrayedResults[0]['oauth_token'];

 //Get an App Access Token


$token_url = 'https://graph.facebook.com/oauth/access_token?'
. 'client_id=' . APP_ID
. '&client_secret=' . APP_SECRET
. '&grant_type=client_credentials';
 echo ('<a href="'.$token_url .'" > tokenUrl </a>');

$token_response = CURL($token_url);
var_dump($token_response);

$params = array();
parse_str($token_response, $params);

$oauth_token = $params['access_token'];
var_dump($oauth_token);


$M = new MySQL();
// get achievements from db
$M->Select('fb_achievements');
foreach ($M->aArrayedResults as $achievement) {
    
    // the url of the achievement
    $achievement_url = FINAL_GAME_CLIENT_PATH. "achieve.php?name=". $achievement['name'];
 
    // get facebook session of daniel from db
    // CARE: this has to be refreshed by visiting the game every 5 minutes
    
    // Register the achievement
    $achievement_registration_URL = 'https://graph.facebook.com/'
                                    . APP_ID . '/achievements';
    $achievement_registration_result = CURL($achievement_registration_URL,
                                    'achievement=' . $achievement_url
                                    . '&display_order=' . $achievement['order']
                                    . '&access_token=' . $oauth_token
                                     );
    echo ('<a href="'.$achievement_registration_URL .'?'.
                                    'achievement=' . $achievement_url
                                    . '&display_order=' . $achievement['order']
                                    . '&access_token=' . $oauth_token .'" > regurl </a>');
    var_dump($achievement_url);
    var_dump($achievement_registration_result);
    echo '<br />';
}

function add_achievements($src_folder, $file_prefix)
{
    $M = new MySql();
    foreach (new DirectoryIterator($src_folder) as $fileInfo) {
        if($fileInfo->isDot()) continue;

        /*
         * if its a graphic, add it to the database
         */
        if(substr ($fileInfo->getFilename(), -4,4) == 'html'){

            $aSet = array('name' => substr($fileInfo->getFilename(),0,-5));
            
            $M->Select('fb_achievements',$aSet);
            if ($M->iAffected < 1){
                $M->Insert($aSet,'fb_achievements');
            
                echo (substr($fileInfo->getFilename(),0,-5)." added.\n");
            }
            
        }
    }
}

?>
