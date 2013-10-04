<?php


/*
 * Returns a column's values of an Array
 * @param array $array the array to slice
 * @param string $column the name of the column
 * @return array An array containing all the values of the specific Column
 */
function array_column($array, $column)
{
    foreach ($array as $row) $ret[] = $row[$column];
    return $ret;
}

 /* Returns a stdObject including the specified properties
 * @param Object $object the object to strip
 * @param array $keepProps names of properties to keep
 * @return stdObj stdObject including the specified properties
 */
function strip_object($object, $keepProps)
{
    $ret = new stdClass();
    foreach ($keepProps as $prop) $ret->$prop = $object->$prop;
    return $ret;
}

 /* Returns an array including the specified properties
 * @param array array  to strip
 * @param array $keepProps names of fields to keep
 * @param array  $removeProp names of fields to keep
 * @return array array including the specified properties
  * use either $keepprops or removeprops
 */
function strip_array($array, $keepProps = null, $removeProps = null)
{
    $ret = array();
    if(isset($keepProps)){
        // positive stripping
        
        foreach ($keepProps as $prop) $ret[$prop] = $array[$prop];
    }
    elseif(isset($removeProps)){
        // proper stripping
        
        foreach ($array as $prop => $value) {
            if(!array_search($prop, $removeProps)){
                $ret[$prop] = $array[$prop];
            }
        }
    }
    return $ret;
}

 /* Returns a stdObject including the specified properties
 * @param Object $object the object to strip
 * @param array $keepProps names of properties to keep
 * @return stdObj stdObject including the specified properties
 */
function utf8_encode_array (&$array, $key) {
    if(is_array($array)) {
      array_walk ($array, 'utf8_encode_array');
    } else {
      $array = utf8_encode($array);
    }
}

function utf8_decode_array (&$array, $key) {
    if(is_array($array)) {
      array_walk ($array, 'utf8_decode_array');
    } else {
      $array = utf8_decode($array);
    }
}

function CURL($url, $post = null, $retries = 3)
{
    $curl = curl_init($url);

    if (is_resource($curl) === true)
    {
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
       // curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        if (isset($post) === true)
        {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, (is_array($post) === true) ? http_build_query($post, '', '&') : $post);
        }

        $result = false;

        while (($result === false) && (--$retries > 0))
        {
            $result = curl_exec($curl);
        }

        curl_close($curl);
    }

    return $result;
}
function CURL_DELETE($url, $data = null, $retries = 3){

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = false;
        while (($result === false) && (--$retries > 0))
        {
            $result = curl_exec($ch);
        }

        curl_close($ch);
        return $result;
}

//error handler function
function customError($errno, $errstr, $errfile, $errline)
  {
  $log = Logger::getLogger('Main');// Logger::getLogger(__CLASS__);
  $log->fatal("[$errno] $errstr");
  $log->fatal(" Error on line $errline in $errfile");
  $log->fatal($_SERVER['REQUEST_URI']);
  $log->fatal("Post Data:" . file_get_contents('php://input'));
  $log->fatal("MySqlError:" . mysql_error());
  //die();
  }
?>
