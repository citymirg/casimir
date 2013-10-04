<?php

include_once 'class.User.server.php';

/**
 * Inherited class for the votes on the server side. 
 *
 */
class Vote extends GenericVote{
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
    public $details = array();



    public function __construct () {

        /** @var string
        * the object table in SQL DB */
        $this->table = 'vote';
        $this->voteType == 'basicooo';
    }
    
    /**
     * @return boolean 
     */
    
    public function isRegistred(){
        
        if($this->id  == Null)
            return false;
        
        $M = new MySQL();
        
        $M->select('vote',array('id' =>  $this->id));
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
        * Fill db array with the vote-details fields
        * correspondoing to db fields
        * @todo: STRUCTURE use the general routine in USer for this
        */
        $splitDet = $this->splitObjectDetails();

       /*
        * Encode the remaining Properties and attach them to the information
        */
        $dbArray = $splitDet['dbRow'];
        $dbArray['details'] = json_encode($splitDet['toDetails']);

        /*
         * Attach the  standard vote values
         */     
        $triplet = new Triplet($this->aSongId);
        $data = array(  'songChosenId' =>  $this->songChosenId,
                        'sessionId'    =>  $this->user->sessionId,
                        'permId'    =>  $triplet->permId,
                        'userId'    =>  $this->user->id);
        
        /*
         * Save vote db object to database
         */
        $M = new MySQL();   
        $M->insert(array_merge($dbArray, $data),
                                'vote');
        if ($M->iAffected > 0)
            $this->id = mysql_insert_id($M->sDBLink);

      /*
       * Incrrease conters in triplet and permutations!
       */
       foreach($triplet->aSong as $song){
           $song->incrSongCounter('basicooo');
       }
       $triplet->incrTripletCounter();
       $triplet->incrPermCounter();

       // this does not work
       return !($this->id  == Null) ;
    }

}

?>
