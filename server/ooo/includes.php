<?php
include_once('log4php/Logger.php');
include_once("class.MySql.php");
include_once 'class.dbMappedObject.php';
include_once 'Zend/Loader/Autoloader.php';

include_once 'helpers.php';
include_once("class.MainServer.php");
//DATA MODELS CLASSES
include_once 'data_models/class.Song.server.php';
include_once 'data_models/class.User.server.php';
include_once 'data_models/class.Triplet.server.php';
include_once 'data_models/class.GenericVote.server.php';
include_once 'data_models/class.Vote.server.php';


//GETTING STATISTICS STRATEGY CLASSES
include_once 'stat_songSelection/class.SongSelector.php';
include_once 'stat_tripletSelection/class.PermSelector.php';
include_once 'stat_tripletAddition/class.TripletAdder.php';
include_once 'stat_songAddition/class.SongAdder.php';

include_once 'stat_voteSelection/class.VoteSubsetDistribution.php';
include_once 'stat_voteSelection/class.VoteSelector.php';

?>
