<?php


class ModuleCaller {
    /** 
     * @var Auth 
     * @todo hasAnswered
     */
    protected $auth;
    

    /**
     * Type of the module. Correspond to the request sent to client 
     * to send module.
     * @var string 
     */
    public $type;
    
    /**
     * Arguments sent to the client.
     * @var array 
     */
    public $args;
    
    
    
    /**
     * Results sent to the client.
     * @var array 
     */
    public $results;
       
    
     /**
     * Time in seconds for the module to be completed
     * @var integer 
     */
    public $givenTime = 60;
    
     /**
     * Time in seconds when the latest player can still see the module
     * @var integer 
     */
    public $latestEntryTime = 45;


    //---------------------------------
    // Functions
    //---------------------------------
    public function __construct ($auth) {
        $this->auth = $auth;

        /*
         * @todo: FIX there is a recursion in auth :(
         */
    }

    /*
     * can this module be launched?
     * @return boolean
     */
    public function launchCondition($auth = null){

       if (isset($auth)) $this->auth = $auth;

       return $this->auth->player->isReady()
               && $this->auth->match->isEveryPlayerReadyForModule();
    }

     /*
     * can this module be launched?
     * @return boolean
     */
    public function launchConditionForRes($auth = null){

       if (isset($auth)) $this->auth = $auth;
       return $this->auth->player->getState() == 'moduleDone'
               && $this->auth->match->isEveryPlayerReadyForResults();
    }


    /**
     * Returns the module's Arguments for this match step
     *
     * First we search in the database for any data
     *  we use base64_encode and serialize to store the data
     * If the data is outdated we get a new triplet from the API
     * @return boolean
     */
    

    public function getModuleArgs(){     
        
        // ok, is there already the module data in the db
        $M = new MySql();
        $aWhere = array('matchId' => $this->auth->match->id);
        $M->Select('curmoduleargs',$aWhere);
        
        // no, its not there
        if ($M->iRecords < 1) {

            //create new module arguments
            $this->setModuleArgs();
            
            /*
             * Great, now we have a new triplet, lets save it
             * using the php serialise function
             * @todo: SAFETY test if this did not fail!
             */
            
            /*/* check again!
             *  This is done because setting the Args can take long and 
             *  still another module might have saved them already by 
             *  the time they have been computed 
             */
            $M->Select('curmoduleargs',$aWhere);
            if ($M->iRecords < 1) {
                
                $aInsert = array(
                'matchId' => $this->auth->match->id,
                'step' => $this->auth->match->step,
                'args' => serialize($this->args));

                $success = $M->Insert($aInsert,'curmoduleargs');
                return $success;
            }
            
        }
        // no only old stuff
        elseif ($M->aArrayedResults[0]['step'] != $this->auth->match->step) {

            //create new module arguments
            $this->setModuleArgs();

             /*
             *  save it using the php serialise function
             * @todo: SAFETY test if this did not fail!
             */
            // check again!
            $M->Select('curmoduleargs',$aWhere);
            if ($M->aArrayedResults[0]['step'] != $this->auth->match->step) {
                
                $aInsert = array(
                'matchId' => $this->auth->match->id,
                'step' => $this->auth->match->step,
                'args' => serialize($this->args));

                $aWhere = array('matchId' => $this->auth->match->id);

                return $M->Update('curmoduleargs', $aInsert,$aWhere);
            }
        }
        // Yes Yes its there
        $this->args = unserialize($M->aArrayedResults[0]['args']);
        return true;
    }
    
     /**
     * creates the module's new arguments
     *@return boolean
     */
    public function setModuleArgs(){

        $this->args = array();
        
        // Send serverTime when the module starts
        $this->args['serverTime'] = date('U');
        
        // Send serverTime when the module starts
        $this->args['givenTime'] = $this->givenTime;

        return true;
    }

     /**
     * creates the module's new arguments
     * @array array to merge
     * @return boolean
     */
    public function appendToModuleArgs($key,$array){

        $this->getModuleArgs();
        
        $this->args[$key] = $array;
             /*
             *  save it using the php serialise function
             * @todo: SAFETY test if this did not fail!
             */
       $M = new MySql();

       $aInsert = array(
        'matchId' => $this->auth->match->id,
        'step' => $this->auth->match->step,
        'args' => serialize($this->args));

       $aWhere = array('matchId' => $this->auth->match->id);

       $res = $M->Update('curmoduleargs', $aInsert,$aWhere);
      
       return $res;

    }


    /*
     * Saves the players game voting data to the game database
     * @param Object data any datatype which is serializeable
     * @return boolean
     *
     * @todo: PERF this could be replaced by a SQL REPLACE INTO
     */
     public function resultToDB($data){

             
        // ok, is there already the module data in the db?
        $M = new MySql();
        $aWhere = array(
            'matchId' => $this->auth->match->id,
            'playerId' => $this->auth->player->id
                );
        
        $M->Select('curmoduleresults',$aWhere);

        // no, its not there
        if ($M->iRecords < 1) {
            
            /*
             * @todo: SAFETY test if this did not fail!
             */
            $M = new MySql();
            $aInsert = array(
                'matchId' => $this->auth->match->id,
                'playerId' => $this->auth->player->id,
                'step' => $this->auth->match->step,
                'result' => serialize($data));
            $success = $M->Insert($aInsert,'curmoduleresults');

            return $success;
        }
        
        // found some only old stuff
        elseif ($M->aArrayedResults[0]['step'] != $this->auth->match->step) {

             /*
             *  save it using the php serialise function
             * @todo: SAFETY test if this did not fail!
             */
            $M = new MySql();

            $aInsert = array(
                'matchId' => $this->auth->match->id,
                'playerId' => $this->auth->player->id,
                'step' => $this->auth->match->step,
                'result' => serialize($data),
                'processed' => 0);
            $success =  $M->Update('curmoduleresults', $aInsert,$aWhere);

            return $success;
          
        }
            // We already have data for this module and step :(
         else {
             return false;
         }
    }

    /*
     * Get all game voting data from the game database
     * @param Object data any datatype which is serializeable
     * @return array
     *
     * @todo: PERF this could be replaced by a SQL REPLACE INTO
     */
     public function allResultsFromDB(){

        // ok, is there already the module data in the db?
        $M = new MySql();
        $aWhere = array(
            'matchId' => $this->auth->match->id,
            'step'=> $this->auth->match->step,
                );

        $M->Select('curmoduleresults',$aWhere);
        
        // unserialize the saved contents
        // if any results available
        $res = array();
        if ($M->iRecords > 0) {
            foreach ($M->aArrayedResults as $data) 
            $res[] = array('playerId' => $data['playerId'], 
                           'result' => unserialize($data['result']),
                           'processed' => $data['processed']);
        } 
        return $res;
    }


     /**
     * Checks and saves the result for the module
     * @param $aVote
     * @return boolean
     */
    public function provideModuleResult($aVote){

        return true;
    }


    
    /**
     * Checks and saves the result for the module
     * @return array
     * @todo make this return boolean
     */
    public function getAllModuleResults(){
        if(isset($this->results))
            return $this->results;
        
        $this->results = $this->allResultsFromDB();
        
        return $this->results;
    }
    
    
    /**
     * Process results.
     * As a standard we send back the results so the client as they sent it to the server.
     * 
     * Inherited modules should have a similar processing methods.
     * @return boolean
     */
    public function processResults(){
        $this->getAllModuleResults();
        
        // compute processed results for specific module
        $processedResults = $this->computeProcessedResults();
        
        if(!isset($processedResults)){
            return false;
        }
        
        
        // append processed results to moduleargs
        if(!$this->appendToModuleArgs('results', $processedResults)){
            return false;
        }
        
         // Register the result as processed. in db
        $M = new MySql();
        $aWhere = array(
                 'matchId' => $this->auth->match->id,
                 'step'=> $this->auth->match->step,
                     );
        $aSet = array('processed' => 1);
        $M->Update('curmoduleresults',$aSet, $aWhere);
            
         // Register the result as processed. in memory
        for($i = 0; $i < count($this->results); $i++)
            $this->results[$i]['processed'] = 1;

        return true;
    }
    
    /**
     * Module particular function to compute points and so on.
     */
    public function computeProcessedResults (){
        // TO DEFINE IN THE MODULE.
        return array();
    }

    
    /**
     * Process results.
     * As a standard we send back the results so the client as they sent it to the server.
     * 
     */
    public function areResultsProcessed(){
        $this->getAllModuleResults();
        
        if(!isset($this->results))
            return false;
        
        $r = true;

        foreach($this->results as $result){
            $r = $r && $result['processed'];
        }
        
        return $r;
    }
    
    
    public function insertAIResult(){
        
    }
    
    /**
     * How much time [seconds] is left for this module?
     * @return integer
     */
    public function timeRunning(){
        return (date('U') - $this->args['serverTime']);
    }
    
    
    /**
     * Can players still join the non-result part of the module ?
     * @return integer
     */
    public function isTooLate(){
        return ($this->timeRunning() > $this->latestEntryTime);
    }

    /**
     * Removes a player's result
     * @param Player $player
     * @return boolean
     */
    public function removeResult($player){
        $M = new MySql();
        $aWhere = array(
            'matchId' => $this->auth->match->id,
            'playerId' => $player->id);
        $success = $M->Delete('curmoduleresults',$aWhere);

        return $M->iAffected >0;
    }

    
 /**
     * general Achievements with points
     * @param Player $player
     * @return array
     */
    public function checkAchievements($player){

        $res = array();
        // 100 pts achievement
        if ($player->totalPoints >= 100 && $player->totalPoints < 200){

            $tmpres = $player->addAchievement("first100");
            if (!empty( $tmpres)){
                $res[] = $tmpres;
            }

            
        //500 pts achievemnet
        }elseif ($player->totalPoints >= 500 && $player->totalPoints < 600){
            
            $tmpres = $player->addAchievement("first500");
            if (!empty( $tmpres)){
                $res[] = $tmpres;
            }
        }
        return $res;
    }

}
 
?>
