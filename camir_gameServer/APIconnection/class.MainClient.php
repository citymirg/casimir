<?php

include_once('../config.php');

/**
 * Build the server connection.
 *
 */
class MainClient extends SoapClient {
    
    


    
    /**
     *@var string 
     */
   
    static public $wsdl;
    
    /**
     *@var array 
     */
    static public $classMap = array(    
                                'Vote' => 'Vote',
                                'Triplet' => 'Triplet',
                                'User' => 'User',
                                'Song' => 'Song',);
    
    
    /**
     *@var array 
     */
    static public $headers = array();
    
    
    public function __construct() {
        
            self::$wsdl= EXTERNAL_ROOT_PATH . 'server/ooo/server.php?wsdl';
            
            //Server connection
            $options = array(   'cache_wsdl' => 0, 
                                'classmap' => self::$classMap,
                                'encoding'=>'ISO-8859-1');
            parent::SoapClient(self::$wsdl, $options);
            
            parent::__setSoapHeaders(self::$headers);
          
            }
}


/*
 * Include the needed classes
 */



?>