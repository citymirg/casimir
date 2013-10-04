<?php

/**
 * Triplet data models on client side
 *
 */
class Triplet {
    /** @var int */
    public $id = Null;  
    
    /** 
     * Array of song that is sent to the client
     * @var Song[] 
     */
    public $aSong = array();    
    
    /** @var int */
    public $permId = Null;   
}

?>
