<?php


/**
 * Votes on the client side. 
 *
 * Note: for the SOAP connection it is ESSENTIAL to type right 
 * (dont forget the @)
 */
class GenericVote extends dbMappedObject{
    /** @var int */
    public $id = Null;

    /** @var User */
    public $user;
    
    
    /** @var string voteType */
    public $voteType;

    /*
     * The following are optional components
     */
    /** @var array */
    public $details;



    public function __construct ($table) {

        if(empty($table))
            unset($this);
        else {
            $this->table = 'vote.'.$table;
            $this->voteType = $table;
        }
    }
}
