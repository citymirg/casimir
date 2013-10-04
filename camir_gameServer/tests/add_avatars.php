<?php

/*
 *
 *
 */

// @todo: REMOVE DEBUG before production allow for php error reporting
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once '../config.php';
include_once '../includes.php';

require_once('FirePHPCore/FirePHP.class.php');
ob_start();


$src_folder = "../../camir_gameClient/img/avatars/lvl3";
$file_prefix = "lvl3/";
$costPoints = 1500;
$levelRequired = 3;
add_avatars($src_folder, $file_prefix, $costPoints, $levelRequired);

function add_avatars($src_folder, $file_prefix, $costPoints = 0, $levelRequired = 0)
{
    $M = new MySql();
    foreach (new DirectoryIterator($src_folder) as $fileInfo) {
        if($fileInfo->isDot()) continue;

        /*
         * if its a graphic, add it to the database
         */
        if(substr ( $fileInfo->getFilename() , -3,3) == 'png'){
            
            $aSet = array('fileName' => $file_prefix.$fileInfo->getFilename(),
                          'costPoints' => $costPoints,
                          'levelRequired' => $levelRequired);

            $M->Insert($aSet,'ui_avatars');
            echo ($file_prefix.$fileInfo->getFilename()." added.\n");
        }
    }
} 

?>
