<?php


/**
 * User data model on server side.
 */
class User extends dbMappedObject{
    /** @var int */
    public $id = Null; 
    
    /** @var int */
    public $sessionId = Null;
   
    /** @var string */
    public $sessionType = Null;
    
    /** @var array */
    public $details = array(); 

    /** 
     * Construct the user object based on database information.
     * @param int $userId
     * @param int $sessionId
     */
    public function __construct ( $userId = Null , $sessionId = Null ) {

        /** @var string
        * the object table in SQL DB */
        $this->table = 'User';

        if(!empty($userId) AND !empty($sessionId)){
            $this->sessionId = $sessionId;
            $this->id = $userId;
            $M = new MySql();

            $M->Select('user',array('id' => $this->id));
            unset($M->aArrayedResults[0]['id']);
            $this->details = $M->aArrayedResults[0];    
        }
    }
    
    /**
    *  Create new session into DB and set the sessionId field of 
    * the user object to the correct value. 
    */
    private function setNewSession(){

        $M = new MySql();
        
        $M->Insert(array('userId' => $this->id), 'session');
        $this->sessionId = mysql_insert_id($M->sDBLink);
    }
 
   /**
    *  Put into DB if not already registered.
    * It does not return a bool because there is an session Id to fix anyway.
    *
    * @todo: CHECK THIS NEW ROUTINE
    * check whether all the details in the user struct correspond to
    * rows in the user db, and discard others.
    */
    public function toDB(){
        
        if(!$this->setId()){
           /*
            * @todo: implement update + correction of user data
            */
            $this->dbWrite();
        }
        $this->setNewSession(); 
    }
    
    /**
     * isRegistred function that set ID if it exists
     * @return boolean 
     */
    public function setId() {
        $id = $this->returnId();
        if($id > 0){
            $this->id = $this->returnId();
            return true;
            
        } else
            return false;
    }

    /**
     * isRegistred function
     * @return boolean 
     */ 
    public function isRegistred() {
        return ($this->returnId() > 0);
    }    
    
    /**
    *  Put into DB
    */
    public function returnId(){

        $M = new MySql();
        /*
         * Throw away the deatils not improtant for user specs
         */
        $splitDet = $this->splitObjectDetails();

        // look for the user with relevant details
        $M->Select('user',$splitDet['dbRow']);
        if($M->iRecords >= 1){
            return $M->aArrayedResults[0]['id'];
        }
            return false;

        // this is never reached !!!!
        $this->setNewSession();
    }

}

?>
