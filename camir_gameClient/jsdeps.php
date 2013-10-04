<?php
//******************
// Compulsory header
//******************
$TBS = $this;
$compiled = CLIENT_COMPILED;
if (!empty($_GET['compiled']))
    $compiled = $_GET['compiled'];

if ($compiled){
    $TBS->LoadTemplate('templates/jsdeps_compiled.html');
}else{
    $TBS->LoadTemplate('templates/jsdeps.html');
}

$TBS->MergeField('CLIENT_PATH',EXTERNAL_CLIENT_PATH);
$TBS->MergeField('GAME_SERVER_PATH',GAME_SERVER_PATH);
$TBS->MergeField('EXTERNAL_WEB_PATH',EXTERNAL_WEB_PATH);
$TBS->MergeField('FULL_CLIENT_OFFSET',LOCAL_ROOT_OFFSET . CLIENT_OFFSET);

$TBS->Show();
?>