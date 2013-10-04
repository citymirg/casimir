<?php

include_once 'class.User.server.php';

/**
 * Inherited class for the votes on the server side. 
 *
 */
class GenericVote extends dbMappedObject {
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



    public function __construct ($voteType) {

        if(empty($voteType))
            unset($this);
        else {
            $this->table = 'vote.'.$voteType;
            $this->voteType = $voteType;
            
        }
    }
    
    /**
     * @return boolean 
     */
    
    public function isRegistred(){
        
        if($this->id  == Null)
            return false;
        
        $M = new MySQL();
        
        $M->select($this->table,array('id' =>  $this->id));
        $res = $M->aArrayedResults;
        
        
        return !(empty($res)) ;
    }
        
    /**
     * This function saves the Vote to the DB.
     * Fields of vote->$details which correspond to
     * fields in the database are put in the corresponding field
     * the remaining data is appended to a json-encoded array
     * 
     * @return boolean 
     */
    public function toDB(){
        if($this->isRegistred())
            return false;
        /*
         * @todo: these should be defined as object variables above
         */
        $this->sessionId = $this->user->sessionId;
        $this->userId = $this->user->id;
        
        return $this->dbWrite() ;
    }

        /**
     * This function saves the Vote to the DB.
     * Fields of vote->$details which correspond to
     * fields in the database are put in the corresponding field
     * the remaining data is appended to a json-encoded array
     *
     * @todo: fill aSongId field
     * @param integer $voteId Id of the vote to retrieve
     * @return boolean
     */
    public function fromDB($voteId){

        return $this->dbLoad($voteId);
    }
}

?>
