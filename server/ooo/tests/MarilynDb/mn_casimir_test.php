<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<body>

<?php

$user_name = 'midbstudent';
$password = 'M1db5tud';
$server = 'vega.soi.city.ac.uk';

$database = 'stud_mi';
$table = 'mn_casimir_songlibrary';
$sevenloadkey = '7drb8p23ggb7';

define('CLIENT_LONG_PASSWORD', 1);
$con =  mysql_connect($server, $user_name, $password, false, CLIENT_LONG_PASSWORD);

if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
print 'Connection to the Server opened <br/>';

// select the song database
mysql_select_db($database, $con);
echo(mysql_error());


/*
*
* Get all the artists
*
*/

/*
$result = mysql_query('SELECT DISTINCT artist FROM ' . $table);

while($row = mysql_fetch_array($result))
  {
  echo '<br />'. $row['artist'] ;
  }
*/


/* 
* make a query for some songs!!
* 
* the 10.000 million song dataset songs 
* start from song 1020 -  song 10020 
*/

$result = mysql_query('SELECT * FROM ' . $table . ' WHERE (id = 1020)');

while($row = mysql_fetch_array($result))
  {
  echo '<audio controls="controls" tabindex="0"><source type="audio/mpeg" src="' . 
	makeMp3Url($row['url'],$sevenloadkey) . '"></source></audio>';
  }
  
 
  
// close connection
mysql_close($con);

// function to build the 7load url
function makeMp3Url($track_id, $sevenloadkey){
	$mp3url = 'http://api.7digital.com/1.2/track/preview?trackid=' . $track_id . '&oauth_consumer_key=' . $sevenloadkey;

return $mp3url;
}

?>

</body>
</html>