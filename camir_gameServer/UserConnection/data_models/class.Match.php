<?php
/*
* Camirherd MAIN Game Class.
* Contains Server functionality
*/


class Match extends dbMappedObject{
    
    /** @var Auth */
    public $auth;

    //----------------------
    // CONSTANT OF THE GAME
    //----------------------
    
    /**
     * Type
     * @var string 
     */
    public $type;
    
    /**
     * details
     * @var array
     */
    public $details;
    
    
    /** 
     * 
     * @var array 
     */
    public $moduleLauncherStack = array();
    
    
    /** 
     * 
     * @static integer 
     * @todo: make number of players variable
     */
    static public $maxPlayers = 4;
    
    static public $minPlayers = 4;

    // player timeout in seconds
    static public $playerTimeout = 15;
    
    //-----------------------------------
    //@todo: SAVE PLAYER TABLE FOR EFFICIENCY
    //----------------------------------
    
//    private $playerTable;
//
//    private $playerList;

    //--------------------------
    // Ongoing Match attributes
    //--------------------------

    /** @var integer */
    public $id;

    /** @var string */
    public $state;

    /** @var integer */
    public $step;

    /** @var integer */
//    public $nbPlayers;

    /** @var integer: ONLY AI Players */
    public $nbAIPlayers;

    /** @var integer: ONLY AI Players */
    public $nbHumanPlayers;
    
    /**@var ModuleCaller : current module */
    public $currentModule;
    
        
    /** @var Logger */
    public $log;
    //-----------------------------
    // GAME CONSTRUCTION: CAMHERD
    //----------------------------
    
    /**
     * Camirherd basic Game construction.
     * Give an ID to get DB information on construction. 
     * Don't to create a new Match in DB.
     * 
     * @param Auth $auth Authentificator
     * @param integer $id 
     * @param stdObj $details
     */
    public function __construct ($auth, $id = NULL, $details = NULL) {
            
            // CAUTION : if the match is currently creating $auth->match is NULL
            // 
            // @todo: STRUCTURE: resolve the circular dependency of auth->match->auth!

            $this->table = 'match';
            $this->log = Logger::getLogger(__CLASS__);// Logger::getLogger(__CLASS__);
            
            /*
             * @todo: STRUCTURE: put gametype in an inherited class with variables etc
             */
            $this->type = 'STOSO';
            $this->details = $details;

            //initialise gamesession table details
            $this->nbAIPlayers = 0;
            $this->nbHumanPlayers = 0;
            $this->state = 'running';
            $this->step = 0;
            
            $this->auth = $auth;
            $this->auth->match = $this; // RECURSION!!!

            if(isset($id)){
                $this->dbLoad($id);
            }
            else{
                $this->createDBInformation();
            }

            /*
              * 3 games for start
             * @todo: make number of games variable
             */




            
            $this->moduleLauncherStack[] = 'BasicOOOCaller';
            $this->moduleLauncherStack[] = 'TapTempoCaller';
            $this->moduleLauncherStack[] = 'BasicOOOCaller';
            $this->moduleLauncherStack[] = 'TapTempoCaller';
            $this->moduleLauncherStack[] = 'BasicOOOCaller';
            $this->moduleLauncherStack[] = 'TapRythmCaller';


            // add game close after 10 guesses
            $this->moduleLauncherStack[] = 'CloserCaller';
    

            $modname = $this->moduleLauncherStack[$this->step];
            
            $this->currentModule = 
                    new $modname($this->auth);
    }

    
    //----------------------
    // Construction tools
    //----------------------

    /**
     * Create match in DB. And set information.
     * @param integer $id 
     */
    public function createDBInformation(){
        $M = new MySql();
        $aInsert = array(
            'nbAIPlayers' => 0,
            'nbHumanPlayers' => 0,
            'state' => 'running',
            'type' => $this->type , 
            'step' => 0);
        if(isset($this->details))
            $aInsert['details'] = json_encode($this->details);
        
        $M->Insert($aInsert,'match');
        
        $this->nbAIPlayers = 0;
        $this->nbHumanPlayers = 0;
        $this->state = 'running';
        $this->step = 0;
        $this->id = mysql_insert_id($M->sDBLink);

        return $this->id; 
    }
    
    /** 
     * Return a RANDOM match if one is available and not full.
     * Create one if none exists.
     *
     * @todo ADD in where clause : (play.id <> '.$auth->player->id.') AND
     *  
     * @static
     * 
     * @param string $type type (todo: subclass) of the match
     * @param stdClass $details preferences of the match
     * @return Match
     */    
    static public function findMatch($auth, $details = NULL){

        // @todo: use this when multiple match classes are in operation,
        //        see constructor as well
        $type = 'STOSO';

        //*
        // close matches where nobody's playing
        //
        Match::updateGameSessionDb();
        
        Match::updateMatchDb($auth);

        $M = new MySql();

        /*
         * todo: look at match and throw out AI players if
         * new human player arrives
         */
         $query =   'Select id 
                    from `match` 
                    Where 
                        (nbHumanPlayers < '.self::$maxPlayers.') AND
                        (state = "running")';
         
            if(!empty($type))
                $query .='
                    AND (type LIKE "'.$type.'")';
            
            if(!empty($details))
                $query .='
                    AND (details LIKE "'.mysql_real_escape_string(json_encode($details),$M->sDBLink).'")';
            
                $query .='
        
                    ORDER BY RAND()
                    
                    LIMIT 0,1';
        
        $result = mysql_query($query);
        if($result && (mysql_num_rows($result) >= 1)){
            $match = mysql_fetch_array($result);
            
            return new Match($auth,$match[0]);
        } else {
            return new Match($auth,null,$details);
        }
        
    }


    /*
     * @TODO: COMPLETE
     *
     */
    static public function updateGameSessionDb(){
        $M = new MySql();
        $var = date('U') - (self::$playerTimeout);

        $aStatem = 'DELETE FROM gamesession
                    WHERE (lastConnectionTime < ' .  
                    $var
                     . ' ) ';

        $success = $M->ExecuteSQL($aStatem);
        
        return $success;
    }

    
    /** 
     * Closes Matches where all players are confirmed out
     * @static
     * 
     * @param string $type
     * @return boolean
     */    
    static public function updateMatchDb($auth){

        /*
         * get all running matches
         */
        $M = new MySql();
        $aWhere = array('state' => 'running');
        $M->Select('match', $aWhere);

         /*
         * Check player numbers on all matches
         * and update / close
         */
        if ($M->iRecords < 1)
                return false;
        
        foreach($M->aArrayedResults as $tbl)
        {
            $oldauth = $auth;
            // instantiate temporary match
            $tmpMatch = new Match($auth, $tbl['id']);

            // get players
            $tmpMatch->updateNbPlayers();

            // close the match if orphaned
            if ($tmpMatch->isAnyPlayerLeft() == 0)
                $tmpMatch->close();
        }

         $auth = $oldauth;
         return true;
    }

    //--------------------------
    // Ongoing Match Methods
    //--------------------------
            /**
     * Change the match state in DB.
     * @var string $state : State of the Game
     * @return boolean
     */

    public function setState($state){

        if($this->state == $state)
           return false;
       
        // Update Object
        $this->state = $state;

        //Update Mysql db
        $M = New MySql();
        $aWhere = array('id' => $this->id); 
        $aSet = array('state' => $state);
        $M->Update('match', $aSet, $aWhere);
    }
    
    /** 
     * Return a Array table containing information about all the players of the
     * game with the id asked.
     * 
     * @todo: structure: use getPlayerList and dbMappedObject, but will be slower
     * 
     * @param string $type
     * @return array
     */  
    public function getPlayerTable(){
//        if(isset($playerTable))
//            return $playerTable;
        
        
        
        $M = new MySql();

        /*
         * @todo: PERF increase performance of this JOIN statement
         *        Easily Doable by doing subqueries with WHERE
         * 
         * 
         */
        
         $query = 'SELECT player.id,sess.id AS sessionId,sess.state ,sess.points,
                   player.totalPoints, player.AI, player.name, 
                   player.uiLanguage, player.uiAvatarId, player.uiTheme,
                   avatars.fileName AS uiAvatarFileName 
                    
                    FROM 
                        `gameSession` AS sess
                        JOIN `player` AS player
                            ON (player.id = sess.playerId)
                        JOIN `ui_avatars` AS avatars
                            ON (avatars.id = player.uiAvatarId)
                    WHERE 
                        (sess.state <> "out") AND 
                        (sess.matchId = '.$this->id.') 
        
                    ORDER BY sess.points DESC';
        
        $M->ExecuteSQL($query);

        $table = array();
        if ($M->iAffected > 0){
           $M->ArrayResults();
           foreach ($M->aArrayedResults as $row){
                  $table[] = $row;
           }
        }

        return $table;
    }
    
    /**
     * True if the state of every player from the game is ready or out.
     * @param string $state State to check evry player for
     * @return boolean
     */
    public function isEveryPlayer($state){
        $tab = $this->getPlayerTable();
        
        $stateCheck = true;
        foreach($tab as $row){
            if(!($row['state'] === $state))
                $stateCheck = false;
        }
        return $stateCheck;
    }

     /**
     * True if the state of every player from the game is ready or out.
     * @param string $state State to check evry player for
     * @return boolean
     */
    public function isAnyPlayer($state){
        $tab = $this->getPlayerTable();

        foreach($tab as $row){
            if(($row['state'] === $state))
                return true;
        }
        return false;
    }
    
    
    /**
     * True if the state of every player from the game is ready or out.
     * @return boolean
     */
    public function isEveryPlayerReady(){
        return $this->isEveryPlayer('ready');
    }
    /**
     * True if the state of every player from the game is ready or out.
     * @return boolean
     */
    public function isEveryPlayerDone(){

        return $this->isEveryPlayer('done');
    }


    /**
     * True if the state of every player from the game is ready or on module
     * @return boolean
     */
    public function isEveryPlayerReadyForModule(){

        $tab = $this->getPlayerTable();
        
        $stateCheck = true;
        foreach($tab as $row){
            if(!(($row['state'] === 'ready') || ($row['state'] === 'onModule') ))
                $stateCheck = false;
        }
        return $stateCheck;
    }

     /**
     * True if the state of every player from the game is ready or on module
     * @return boolean
     */
    public function isEveryPlayerReadyForNextStep(){

        $tab = $this->getPlayerTable();

        $stateCheck = true;
        foreach($tab as $row){
            if(!(($row['state'] === 'done') || ($row['state'] === 'ready') ))
                $stateCheck = false;
        }
        return $stateCheck;
    }


    /**
     * True if the state of every player from the game is moduleDone or onResultModule.
     * @return boolean
     */
    public function isEveryPlayerReadyForResults(){

        $tab = $this->getPlayerTable();
        
        $stateCheck = true;
        foreach($tab as $row){
            if(!(($row['state'] === 'moduleDone') || ($row['state'] === 'onResultModule') ))
                $stateCheck = false;
        }
        return $stateCheck;
    }
    
    
    /**
     * True if one or more players are left in the game.
     * @return boolean
     */
    public function isAnyPlayerLeft(){
       
        if ($this->nbHumanPlayers > 0 )
            return true;
        else return false;
    }
    
    

    
    /**
     * Returns the number of ALL Players in game
     * @param boolean $countAI count AI players
     * @return boolean
     * @todo: synchronise nbPlayers $nbPlayers (s.u.)
     */
//    public function getNbPlayers(){
//
//         $tab = $this->getPlayerTable();
//         return sizeof($tab);
//    }
    
//    public function updateNbPlayers(){
//        
//         $tab = $this->getPlayerTable();
//         return sizeof($tab);
//    }
    
    /**
     * Returns the number of AI Players in game
     * @param boolean $countAI count AI players
     * @return boolean
     * @todo: synchronise nbPlayers $nbPlayers via forceUpdate param
     */
    public function getNbAIPlayers($forceUpdate = false){
        
         if (!isset($this->nbAIPlayers) || $forceUpdate){
            $tmpCount = 0;
            $tab = $this->getPlayerTable();
            foreach ($tab as $pTab) {
                if ($pTab['AI'] == 1)
                    $tmpCount++;
            }
            $this->nbAIPlayers = $tmpCount;
            
         }
         return $this->nbAIPlayers;
         
    }

    /**
     * Returns the number of AI Players in game
     * @param boolean $countAI count AI players
     * @return boolean
     * @todo: synchronise nbPlayers $nbPlayers  via forceUpdate param
     */
    public function getNbHumanPlayers($forceUpdate = false){

         if (!isset($this->nbHumanPlayers) || $forceUpdate){
            $tmpCount = 0;
            $tab = $this->getPlayerTable();
            foreach ($tab as $pTab) {
                if ($pTab['AI'] == 0)
                    $tmpCount++;
            }
            $this->nbHumanPlayers = $tmpCount;

         }
         return $this->nbHumanPlayers;

    }

  
    /**
     * Function that return the StdClass obj to return to user as a Module.
     * @return array 
     */
    public function getModuleData() {

        $mod = $this->currentModule;
        $retMod = array();

        if($mod->launchCondition()){

             /*
             * Return the relevant module
             */
            
            $retMod['type'] = $mod->type;
            $retMod['args'] = $mod->args;

        } elseif($mod->launchConditionForRes()) {
            /*
             * Return the relevant RESULTS module
             */
            $retMod['type'] = $mod->type . '_result';

            if(!$mod->areResultsProcessed()){
                $mod->processResults();
            }
            /*
             * The results are are now a row of the array args
             */
            $retMod['args'] = $mod->args;
            //$retMod['args']['results'] = $mod->getAllModuleResults();
            
            
        } else {
            $retMod = NULL;
        }
        return $retMod ;
    }
        
   /**
    * There are six possible states :
    *  'ready' -> Step has just begun
    *  'onModule' -> A Module is launched on the user client.
    *  'moduleDone' -> The module is finished on the client side.
    * 
    *  'onResultModule' -> A Result Module is launched on the user client.
    *  'done' -> Ready for match going to next step
    * 
    *  'out' -> The user has left the game.
    * 
    *  Possible Auto update :
    *  From 'ready' TO 'moduleDone' -> if not concerned by the module
    *  From 'moduleDone' TO 'done' -> if not concerned by the results
    * 
    * @todo: make fixPlayerState method which can also be used for player->joinmatch
    */


    /*
     * Adds or kicks AI players according to human player numbers
     */
   public function fillAIPlayers(){
       /*
        *
        *  --- Manage Number of AI Players---
        *
        * 1. Check player number in match
        */
       $pNum = $this->nbPlayers();

        /*
        * add AI players until match full
        * only at the start of each match
         * @todo: IsEveryHumanPlayerReady
        */
       if ( $pNum < self::$minPlayers){
            for ($i = $pNum; $i < self::$minPlayers; $i++){
                // create new player
                $aiPlayer = new AIPlayer();

                // join this Match
                $aiPlayer->joinMatch($this);
            }
            $this->updateNbPlayers();
        }
   }
   /*
    * Updates the status and number of AI players
    * if has not been done yet
    */
   public function updateAIPlayers() {

        // $this->fillAIPlayers();

       // kick players which are too many
       $aiplay = $this->getAIPlayerList();

        //@todo: this shouldnt be necessary, as each incoming player kicks AI
        // but somehow it still happens
       
        // kick AI players if the game is too full
       if(count($aiplay) > 0){
            for($i = $this->nbPlayers() - self::$maxPlayers ; $i > 0; $i--){
                $aiplay[$i-1]->kick();
            }
       }
       
       // update match player numbers
       $this->updateNbPlayers();

       // 4. Get all AI players (match)
       $aiplay = $this->getAIPlayerList();
        
        
       /*
        *  --- This is the main update Part ---
        * 
        *  ATM we just follow the player's state and 
        *  geerate the vote on the fly when the player's moduledone
        */
       foreach ($aiplay as $aiPlayer ){
           
           // care for the player not to timeout
           $aiPlayer->updateTimeStamp();
           
           $state = $aiPlayer->getState();
           switch($this->auth->player->getState()){
               case 'ready':
                   if(!( $state == 'onModule'))
                        $aiPlayer->setState('ready');
                   break;
               case 'onModule':
                   /*
                    *   @todo: If Real players are "ready" or On Module 
                    *   Get votes for AI Players(module) 
                    *   build in timer for moduleDone (optional?)
                    */
                   
                   if(!( $state == 'onModule' || $state == 'moduleDone'))
                   {
                        /*
                        * Tell the module to get an AI answer for thsi player
                        * Log the AI answer for this player
                        */
                       $AIAuth = new Auth($aiPlayer, $this);

                       // create AI persona Module
                       $modname = $this->moduleLauncherStack[$this->step];
                       $AIModule = new $modname($AIAuth);

                       // Get and save AI result
                       $AIModule->insertAIResult();
                   }
                   if($state != 'moduleDone') $aiPlayer->setState('onModule');
                   
                   break;
               case 'moduleDone':
                   // Tell we're done here
                   if(!( $state == 'onResultModule'))
                        $aiPlayer->setState('moduleDone');
                   
                   break;
               case 'onResultModule':
                   
                   // note: we skip 'onResultModule'
                   if(!( $state == 'done'))
                        $aiPlayer->setState('onResultModule');
                   break;
               case 'done':
                   $aiPlayer->setState('done');
                   break;
               default:
                   // this should never be reached
           }
       }
   }
   
    /**
     * Refresh the state of the Match and the step number. 
     */
    /**
     *
     * Go to nextStep in Match
     * There are 2 states:
     *  running,
     *  closed
     *
     * @todo Code set EveryplayerState
     */
    public function matchStepAutoUpdate() {

        /*
         * Check if the match is over
         */
        if (($this->step + 1) >= sizeof($this->moduleLauncherStack)){

            /*
             * @todo:close finished matches / open new match
             * This is only a backup routine
             */
            $this->close();
            return false;
        }

        
         /*
         * Check if anybody's left
         */
        if(!$this->isAnyPlayerLeft()){
            
            $this->close();
            return false;
        }


        /*
         * Check if everybody's done, some but not all people are allowed to be ready
         */
        if(!$this->isEveryPlayerReadyForNextStep() || !$this->isAnyPlayer('done'))
            return false;

        /*
         * Go to next Step
         */
        $this->step = $this->step + 1;

        $M = new MySql();
        
        $aWhere = array('id' => $this->id);
        $aSet = array('step' => $this->step);
        $M->Update('match', $aSet,$aWhere );
        

        /*
         * Set all players From Done to Ready Again
         */
        $this->setEveryPlayerState('ready');

        /*
         * @todo: STRUCTURE CONSISTENCY make auth global !!!
         * the player state is incsonsistent with module and mainServer
         */
        $this->auth->player->state = 'ready';
        /*
         * Prepare next module for launch
         */
        $modname = $this->moduleLauncherStack[$this->step];
        $this->currentModule = 
                new $modname($this->auth);

        // update AI players at start of module
        $this->fillAIPlayers();
        return true;
    }
    
    /**
     * Player Id List.
     * 
     * @return integer[]
     */
    public function getPlayerIdList(){
        $query = '  SELECT sess.playerId
                    
                    FROM `gameSession` AS sess
                   
                    WHERE 
                        (sess.state <> "out") AND 
                        (sess.matchId = '.$this->id.') 
                    ';
        
        $result = mysql_query($query);
        $table = array();
        while($row = mysql_fetch_row($result)){
            $table[] = $row[0];
        }
        
        return $table;
    }

     /*
     * updates the player numbers
     */
    public function updateNbPlayers(){
        $tmpPTbl = $this->getPlayerTable();

        /*
         * Update match table
         */
        if (count($tmpPTbl) != $this->nbPlayers()){
                $M = new MySql();
                $M->ExecuteSQL(
                    'UPDATE
                    `match`
                    SET nbAIPlayers =' . $this->getNbAIPlayers(true).
                    ', nbHumanPlayers =' . $this->getNbHumanPlayers(true).
                    ' WHERE id ='. $this->id);
        }
    }

    /*
     * returns total number of players
     */
    public function nbPlayers(){
        return $this->nbAIPlayers + $this->nbHumanPlayers;
    }


    /**
     * Player List.
     * 
     * @return Player[]
     */
    public function getPlayerList(){
        $idList = $this->getPlayerIdList();
        $tab = array();
        $mId = $this->auth->match->id;
        foreach($idList as $pId)
            $tab[] = Player::getFromAuth($pId, $mId, Player::getSessionId($pId, $mId));
        
        return $tab;
        
    }
    
    /**
     * AI Player List.
     * 
     * @return Player[]
     */
    public function getAIPlayerList(){
        // @var Player[]
        $tmpList = $this->getPlayerList();
        $tab = array();

        // we only keep the AI players
        foreach($tmpList as $pl){
            if ($pl->AI) $tab[] = $pl;
        }
        return $tab;
    }
    
    /**
     * Give a state to all the player. 
     * @param string
     *
     * @todo: STABILITY: create a match variable "leastEveryPlayerState"
     *        in db which "warps bad clients to the current state"
     */
    public function setEveryPlayerState($state){
        $playerList = $this->getPlayerList();

        foreach($playerList as $p)
            $p->setState($state);
    }
    
    
    /**
     * Kick every user that is timedout. 
     */
    public function kickTimedOutSessions(){
        $list = $this->getPlayerList();
        
        $anyKicked = false;
        foreach($list as $player){
            $dt = date('U') - $player->lastConnectionTime;
            /*
             * This is the player kick timeout in seconds
             * @todo: put setting in table
             */
            if($dt > self::$playerTimeout){
                $player->kick();
                $anyKicked = true;
            }
        }
        // update this matches number of players
        if($anyKicked){
            $this->updateNbPlayers();
        }
            
    }

    /**
     * Kick all users
     */
    public function kickAllPlayers(){
        $list = $this->getPlayerList();

        foreach($list as $player){
                $player->kick();
        }
        // update match state
        $this->updateNbPlayers();
    }
    
    /**
     * Close the Mach and Cleanup
     */
    public function close(){

       // remove players from match
       $this->kickAllPlayers();
       
       $this->setState('closed');
       
       // update gamesessions
       Match::updateGameSessionDb();
    }


    
}
?>
