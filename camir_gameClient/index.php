<?php header('Content-type: text/html; charset=utf-8'); ?>

<?php
//******************
// Compulsory header
//******************

include_once "configClient.php";
include_once('TinyButStrong/tbs_class.php');
include_once "../common/helpers.php";

include("../toolboxes/GeoIP/geoip.inc");
include("../toolboxes/GeoIP/geoipcity.inc");

$TBS = new clsTinyButStrong;
$error = array();

/*
 * 1. First open the user input form
 * 2. if that is submitted open the template
 */
$permissions = "user_birthday,user_hometown,user_location,".
        "user_education_history,user_interests,".
        "friends_hometown,user_groups".
        //",user_events,user_religion_politics,user_work_history,friends_location,".
        "";
// in fb menu removed: religionpolitics, workhistory, friendslocation,
// userevents !!! (put back soon if enough users)
// user_actions.music,friends_actions.music -> /me/music.listens

$permissions_adv = "publish_actions";

// the open graph url for this page: the community page
$graph_url = "http://www.facebook.com/pages/Spot-the-Odd-Song-Out/320976971339231";
//$graph_url = "http://apps.facebook.com/".APP_ID."/";

$app_id = APP_ID;
$canvas_page = EXTERNAL_CLIENT_PATH;

$auth_url = "https://www.facebook.com/dialog/oauth?client_id="
    . $app_id . "&redirect_uri=" . urlencode($canvas_page).
        "&scope=". $permissions;

// did we already gather the details and provide them in the form?
if(!isset($_POST['userDetails'])){

     /*
     * Case where we got a signed request from faceboox (either canvas)
     */
    // this only works without http redirection from mi
    if ($_REQUEST && isset($_REQUEST['signed_request'])) {

    //    http://www.facebook.com/dialog/oauth?client_id=427976707257845&redirect_uri=http://chivm.soi.city.ac.uk/_sandbox/casimir/camir_gameClient/
       
        /*
         * Set facebook params
         * NOTE: Permissions cannot be revoked in the Login Dialog during the login flow, 
         * meaning they are non-optional for Users when logging into your app.
         *
         * If you want them to be optional, you should structure 
         * your app to only request them when absolutely 
         * necessary and not upon initial login.
         */

        // get user basic details
        $signed_request = $_REQUEST['signed_request'];
        $data = FacebookHelper::parse_signed_request($signed_request);
        

        if (empty($data["user_id"])) {
               echo("<script> top.location.href='" . $auth_url . "'</script>");
        }else{
                    
            // convert signed_request to userDetails Structure
           $userDetails = FacebookHelper::toUserData($data);
        }

     /*
     * ok we got a code from facebook e.g. via the mobuile web app
     */  
    }elseif ($_REQUEST && isset($_REQUEST["code"])){
        
        $code = $_REQUEST["code"];
        
        // if still not authorised go to auth dialog
        if(empty($code))
            echo("<script> top.location.href='" . $auth_url . "'</script>");
        
        // get auth_token url
        $token_url = "https://graph.facebook.com/oauth/access_token?"
        . "client_id=" . $app_id . "&redirect_uri=" .  urlencode($canvas_page)
        . "&client_secret=" . APP_SECRET . "&code=" . $code;
                
        //use curl to get auth token
        $token_response = CURL($token_url);
        
        // redirect to login if doesnt work
         if($token_response  ==false)
            echo("<script> top.location.href='" . $auth_url . "'</script>");
         
        // parse auth token
        $params = array();
        parse_str($token_response, $params);
        $data['oauth_token'] = $params['access_token'];
        $data['user_id'] = 0;
        
        /*
         * @todo: get user id and basic details to match the first
         * case when getting a signed request
         */
        

    }elseif (isset($_POST['error_reason'])){
        
        $error = $_POST['error_reason'];
        return;
    }elseif (!isset($_GET['uniqueExtId']) && !(isset($_GET['embedder']) && strcmp($_GET['embedder'],"fangnowait") == 0)) {  //  exception for no form
        // load the form for User Detail Input
        
        if (isset($_GET['embedder']) && strcmp($_GET['embedder'],"fang") == 0) { //  exception for special form
            $TBS->LoadTemplate('templates/form_fang.html');
        }else{
            
            $TBS->LoadTemplate('templates/form.html');
        }
        $TBS->MergeField('CLIENT_PATH',EXTERNAL_CLIENT_PATH);
        $TBS->MergeField('permissions', $permissions);
        $TBS->MergeField('privacypolicy',EXTERNAL_CLIENT_PATH . "templates/privacypolicy.html");
        $TBS->MergeField('app_id', APP_ID);
        $TBS->MergeField('auth_url', $auth_url);

        $TBS->MergeField('graph_url', $graph_url);
        $TBS->Show();

        return;
    }
}else{
    /*
     *  Non-Facebook case
     *  copy the posted user details to the php variable
     */
    $userDetails = new stdClass();

    foreach($_POST['userDetails'] as $key => $value){
            $userDetails->$key = $value;
    }
}



$userDetails->browser = $_SERVER['HTTP_USER_AGENT'];

/*
 * Here all browser Dropouts:
 * Check for ie 9.0 and abort if so
 */
$browser = browser();
if(($browser['name'] == 'msie') && (($browser['version'] == 9))) {
  echo 'Sorry, earlier versions of Internet Explorer are currently not supported.
      Please try another browser.
      Thank you for your patience!';
  return;
}

/*
 * This collects the user position from IP
 * @Todo: do the transfer of data in php
 * then only write the match authentification in the template
 * file
 */
$gi = geoip_open("../toolboxes/GeoIP/data/GeoLiteCity.dat",GEOIP_STANDARD);

$record = geoip_record_by_addr($gi, $_SERVER['REMOTE_ADDR']);
if (is_object($record)){
    $userDetails->ipCountryCode = $record->country_code3;
    $userDetails->ipCountry =  $record->country_name;
    $userDetails->ipRegionCode = $record->region;
    $userDetails->ipCity = $record->city;
    $userDetails->ipContinent = $record->continent_code;
    $userDetails->ipLatitude =$record->latitude;
    $userDetails->ipLongitude =$record->longitude;
}

/*
 * Only Store the IP hash, all further information has to
 * be extracted now
 *
 * We generate a new uniqueExtId if we get none
 */
$userDetails->ipHash = sha1($_SERVER['REMOTE_ADDR']);

// get the user details from get and IP and 
// transfer it to javascript
// if any facebook id present use application url

if(isset($_GET['uniqueExtId'])) {

    $userDetails->uniqueExtId = $_GET['uniqueExtId'];
        // facebook id from canvas data
}elseif(isset($data['user_id'])){
    
    $userDetails->uniqueExtId = sha1($data['user_id']);

    // facebook id from form fblogin
}elseif(isset($_POST['user_id'])){
    
    $userDetails->uniqueExtId = sha1($_POST['user_id']);
}

/*
 * TODO: session details:
 * Browser: $browser = get_browser(null, true);
 *  [platform], [browser], [version] , IP / hash
 * 
 * exact location? or keep in user details
 * 
 */


/*
 *
 *  open template and insert userdata
 *
 */
$TBS->LoadTemplate('templates/template.html');

// maybe try subversion stuff later
//$svnversion = exec('svnversion '.LOCAL_ROOT_PATH);
//$TBS->MergeField('svnversion',$svnversion);

// put details in template
$TBS->MergeField('userDetails',json_encode($userDetails));
$TBS->MergeField('CLIENT_PATH',EXTERNAL_CLIENT_PATH);
$TBS->MergeField('GAME_SERVER_PATH',GAME_SERVER_PATH);
$TBS->MergeField('EXTERNAL_WEB_PATH',EXTERNAL_WEB_PATH);
$TBS->MergeField('permissions', $permissions);
$TBS->MergeField('permissions_adv', $permissions_adv);
$TBS->MergeField('app_id', APP_ID);
$TBS->MergeField('graph_url', $graph_url);
$TBS->MergeField('auth_url', $auth_url);

// get authToken
if (isset($data['oauth_token'])){
    $TBS->MergeField('user_id',$data['user_id']);
    $TBS->MergeField('oauth_token',$data['oauth_token']);
    $TBS->MergeField('fb_login',"true");
}elseif (isset($_POST['user_id'])){
    $TBS->MergeField('user_id',$_POST['user_id']);
    $TBS->MergeField('oauth_token','');
    $TBS->MergeField('fb_login',"true");
}elseif (isset($_POST['fb_login'])){
    $TBS->MergeField('fb_login',"true");
    $TBS->MergeField('oauth_token','');
    $TBS->MergeField('user_id','');
}else{
    $TBS->MergeField('fb_login',"false");
    $TBS->MergeField('oauth_token','');
    $TBS->MergeField('user_id','');
    // the open graph url for this page
}

         
//*******************
// Compulsory footer
//*******************
$TBS->MergeBlock('error',$error);
$TBS->Show();


function browser() {
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    // you can add different browsers with the same way ..
    if(preg_match('/(chromium)[ \/]([\w.]+)/', $ua))
            $browser = 'chromium';
    elseif(preg_match('/(chrome)[ \/]([\w.]+)/', $ua))
            $browser = 'chrome';
    elseif(preg_match('/(safari)[ \/]([\w.]+)/', $ua))
            $browser = 'safari';
    elseif(preg_match('/(opera)[ \/]([\w.]+)/', $ua))
            $browser = 'opera';
    elseif(preg_match('/(msie)[ \/]([\w.]+)/', $ua))
            $browser = 'msie';
    elseif(preg_match('/(mozilla)[ \/]([\w.]+)/', $ua))
            $browser = 'mozilla';

    preg_match('/('.$browser.')[ \/]([\w]+)/', $ua, $version);

    return array($browser,$version[2], 'name'=>$browser,'version'=>$version[2]);
}


?>