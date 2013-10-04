<?php


/**
 * Votes on the client side. 
 *
 * Note: for the SOAP connection it is ESSENTIAL to type right 
 * (dont forget the @)
 */
class Vote extends dbMappedObject{
    /** @var int */
    public $id = Null;

    /** @var User */
    public $user;

    /** @var int */
    public $songChosenId;

    /** @var array */
    public $aSongId;


    /*
     * The following are optional components
     */
    /** @var array */
    public $details;



    public function __construct () {

        /** @var string
        * the object table in SQL DB */
        $this->table = 'vote';
    }
}
