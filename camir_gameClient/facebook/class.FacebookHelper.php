<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author Daniel Wolff
 */
class FacebookHelper {
//put your code here
    
    
    public static function toUserData($userdata) {
       
        /*
         * These seem to be the smalles amount of data we get,
         * we should collect more data before registering the user
         */
        $userDetails->uniqueExtId = sha1($userdata['user_id']);
        $userDetails->countryLiving = $userdata['user']['country'];
        $userDetails->locale = $userdata['user']['locale'];
        
        //$userDetails->name = $userdata['name'];
        
        // todo: onvert into age
        //$userDetails->birthday = $userdata['birthday'];
        //$userDetails->fbLocation = $userdata['live']['name'];
        //$userDetails->placeLiving = $userdata['location']['name'];

        
        //$userDetails->placeLivingId = $userdata['location']['id'];
        
    }  
    
    public static function parse_signed_request($signed_request) {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

        // decode the data

        $data = json_decode(FacebookHelper::base64_url_decode($payload), true);

        return $data;
        //
        // Adding the verification of the signed_request below
        //    
        //$sig = base64_url_decode($encoded_sig);
        //  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        //  if ($sig !== $expected_sig) {
        //    error_log('Bad Signed JSON signature!');
        //    return null;
        //  
    }

    private static function base64_url_decode($input) {
        return base64_decode(strtr($input, '-_', '+/'));
    }
    
}
?>
