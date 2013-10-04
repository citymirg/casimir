<?php
class CloserCaller extends ModuleCaller {
    public function __construct($auth){
        parent::__construct($auth);
        
        //@todo: treat recursion
        $this->auth = $auth;
          
        $this->type = 'closer';
        $this->args = array();
        
    }

    /*
     * can this module be launched?
     * @return boolean
     */
    public function launchCondition($auth = null){
          // parent::launchCondition();
       return true;
    }

     /*
     * can this module be launched?
     * @return boolean
     */
    public function launchConditionForRes($auth = null){
        // parent::launchConditionForRes();
       return false;
    }
    
    
    
    
    /**
     * No argument to send. Return true.
     * @return boolean
     */
    public function setModuleArgs(){
        return true;
    }
    
    /**
     * Checks and saves the result for the module
     * @param array $aVote generic type for user vote/data
     * 
     * @return boolean true if game closed
     */
    public function provideModuleResult($aVote = array()){
        return true;
    }
}

?>
