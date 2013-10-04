<?php

// INCLUDES
include_once('log4php/Logger.php');

include_once 'class.MySql.php';
include_once 'class.dbMappedObject.php';
include_once 'helpers.php';

include_once 'APIconnection/class.MainClient.php';

include_once 'APIconnection/class.MainClient.php';
include_once 'UserConnection/class.MainServer.php';


include_once 'APIconnection/data_models/class.Song.client.php';
include_once 'APIconnection/data_models/class.User.client.php';
include_once 'APIconnection/data_models/class.Triplet.client.php';
include_once 'APIconnection/data_models/class.Vote.client.php';
include_once 'APIconnection/data_models/class.GenericVote.client.php';

include_once 'UserConnection/data_models/class.Auth.php';
include_once 'UserConnection/data_models/class.ModuleCaller.php';
include_once 'UserConnection/data_models/class.Player.php';
include_once 'UserConnection/data_models/class.AIPlayer.php';
include_once 'UserConnection/data_models/class.Match.php';

// the order is important
include_once 'UserConnection/moduleCallerDefinitions/moduleCaller.closer.php';
include_once 'UserConnection/moduleCallerDefinitions/moduleCaller.basicooo.php';
include_once 'UserConnection/moduleCallerDefinitions/moduleCaller.BasicOOOIntro.php';
include_once 'UserConnection/moduleCallerDefinitions/moduleCaller.taprythm.php';
include_once 'UserConnection/moduleCallerDefinitions/moduleCaller.taprythmIntro.php';
include_once 'UserConnection/moduleCallerDefinitions/moduleCaller.taptempo.php';
include_once 'UserConnection/moduleCallerDefinitions/moduleCaller.taptempoIntro.php';



?>
