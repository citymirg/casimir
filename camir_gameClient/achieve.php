<?php header('Content-type: text/html; charset=utf-8'); ?>

<?php
/*
 * Facebook Achievements Templater
 */
if(!$_GET['name']) return;

include_once "configClient.php";
include_once('TinyButStrong/tbs_class.php');

// load template
$TBS = new clsTinyButStrong;
$error = array();

$TBS->LoadTemplate('achievements/'. $_GET['name'].'.html');

// put details in template
$TBS->MergeField('CLIENT_PATH',EXTERNAL_CLIENT_PATH);
$TBS->MergeField('app_id', APP_ID);

$TBS->MergeField('graph_url', EXTERNAL_CLIENT_PATH."achieve.php?name=". $_GET['name']);

$TBS->show();
?>