<?php
//----------------------
// AUTH AUTHENTIFICATION
//----------------------


/**
 * Methods definition for the client/server object.
 */
Class MainServer {
    
    /** 
     * Authentifacation object.
     * @access private
     * @var Auth 
     */
    public $auth;
    
    /** 
     * Create the Auth Object attribute of the server class.
     * The authentification is based on GET request.
     * 
     * CAUTION: match declaration needed auth declaration. S
     * It may have problem because the game is here created with
     * missing information, on auth object. Yet I think it does not
     * use it.
     * 
     * @access private 
     */
    private function authUser() {
        if(isset($this->auth))
            return $this->auth;


        /*
         * Match Auth Case
         */
        if( isset($_GET['playerid']) &&
            isset($_GET['matchid']) &&
            isset($_GET['sessionid'])){
            
            
            $player = Player::getFromAuth(
                    $_GET['playerid'],
                    $_GET['matchid'],
                    $_GET['sessionid']);
            
            if(!$player->isValidAuth()){
                $this->auth = new Auth();
                $this->auth->player = Player::getFromAuth($_GET['playerid']);
                $this->auth->match = null; // no match joined yet
                return $this->auth;
            }
                
            
            $this->auth = new Auth();
            $this->auth->player = $player;
            
            //@todo: STRUCTURE: updateTimeStamp of the player should go sw. else
            $this->auth->player->updateTimeStamp();

            /*
             * assign match to auth
             */
            $this->auth->match = 
                    new Match($this->auth,$this->auth->player->matchId);
            /*
             * @todo: test if match is still running
             *          if not, we have to reset the player
             */
           // if ($this->auth->match->state != 'running')
           //         return false;
             //    $this->auth->match = Match::findMatch ($this->auth);
            return $this->auth;
            
        }elseif(isset($_GET['playerid']) ){ //&& isset($_GET['uniqueExtId'])
        /*
         * Menu / Score / Data Auth Case
         */
            $this->auth = new Auth();
            $this->auth->player = Player::getFromAuth($_GET['playerid']);
            $this->auth->match = null; // no match joined yet
        }
        return null;
    } 

    
    /**
     * This is the players interface to get the data etc.
     *      The player is identified with the uniqueExtId.
     *      For security, the game should also get atime-dependent signature,
     *      approving that this very user is also just logged into facebook
     *
     *      Maybe that can already be solved in using a salted hash when
     *      generating the uniqueExtId
     * 
     * @param array $userDetails array with all the sescription of the user to be stored by the API
     * @param array $fbSession optional facebook id to derive the uniquext identifier
     * @return array 
     */
    public function authenticate($userDetails, $fbSession = null) {
        
        $player = new Player($userDetails,$fbSession);
        
        /*
         * Return the game Player identity to the game / js client
         * @todo: return temporally valid key for accessing data
         */
        $aAuth = array();
        $aAuth['playerid'] = $player->id;
        $aAuth['uniqueExtId'] = $player->uniqueExtId;
        return $aAuth;
    }


    
    /**
     * To build a new Player Object :
     * 1. Ask the API to recognize the user.
     * 2. Link the APIuserId to the last corresponding instance of the Player DB
     * 3. Find a Game
     * 4. Create the session.
     *
     * @todo: after using authenticate above, $userdetails should not
     *        need to be transferred again.
     * 
     * @param string $matchType
     * @param stdClass $matchDetails
     * 
     * @return array $auth 
     */
    public function startMatch($matchType = Null, $matchDetails = Null){

        /*
         * create the player and give user details to api
         */
        $this->authUser();
        if(!isset($this->auth)) return array();
        $player = $this->auth->player;
      
        /*
         * We find or create a match for the player and attach him
         * to the match
         */
        $match = Match::findMatch($this->auth, $matchDetails);
        
        // this also creates the gamesession
        $player->joinMatch($match);
        
        /*
         * Return the game Player identity to the game / js client
         */
        $aAuth = array();
        $aAuth['playerid'] = $player->id;
        $aAuth['matchid'] =  $player->matchId;
        $aAuth['sessionid'] =  $player->sessionId;
        $aAuth['uniqueExtId'] =  $player->uniqueExtId;

        return $aAuth;
    }
    

    /**
     * This is the MAIN asynchronous poll which updates the client variables
     * 
     * Return a module if every player is ready. 
     * Return a player table anyway.
     * @todo: update client state
     * 
     * @todo nextModule
     * @todo do we have to do all of this (getmodule etc)
     *       all the time?
     *
     * 
     * @return array 
     */
    public function poll(){

        /*
         *  Validation of user, send closer if anything goes wrong
         */
        $this->authUser();
        if(!isset($_GET['matchid']) || !isset($this->auth) || !isset($this->auth->match)){
            $ret[0] = array();
            // Return server time
            $ret[1] = array('serverTime' => date('U'),
                        'step' => 0,
                        'totalSteps' => 0);
            $ret[2] = array('args'=>array('No valid gamesession found, maybe a duplicate player?'), 'type' => 'closer');
        
            return $ret;
        }

        
        /*/*
         * Ignore users that have already left
         */
        if(!($this->auth->player->state === 'out'))
        {
            $this->auth->match->kickTimedOutSessions();
            
            /*
             * Handle all the AI players before
             * the user information is processed!
             */
            $this->auth->match->updateAIPlayers();
            
            /*
             * We Don't use this for now
             * $this->auth->match->playerStateAutoUpdate();
             */

            $this->auth->match->matchStepAutoUpdate();
            /*
             * @todo: cleanup matches
             *  e.g.: delete matches wich have no remaining plaers
             * 
             */
        }
        
        /*
         */
        $ret = array();
        // Return Player Table
        $ret[0] = $this->auth->match->getPlayerTable();
            
        // Return server time
        $ret[1] = array('serverTime' => date('U'),
                        'step' => $this->auth->match->step,
                        'totalSteps' => count($this->auth->match->moduleLauncherStack));

        /*
         * Return the next Module if appropriate ( e.g. Players are Ready)
         * 
         */
            
            
        if(!($this->auth->player->state === 'out')){
            $ret[2] = $this->auth->match->getModuleData();
        }
        else{
            $ret[2] = array('args'=>array('User is already setted out. A sent closer should not matter.'), 'type' => 'closer');
        }
        // Return Player State ( not necessary ?)
        // $ret[] = $this->auth->player;

        /*
         *  check achievements. 
         * @todo: PERFORMANCE STRUCTURE this should be done directly after
         *        computing results, andfor all players at once
         */
        if($this->auth->match->currentModule->launchConditionForRes()){
            
            $ach = $this->auth->match->currentModule->checkAchievements($this->auth->player);
            if (!empty($ach)){
                $ret[2]['args']['achievements']= $ach;
            }
        }
        /*
         * Utf8 encoding
         * @todo: STRUCTURE this should be at a central point when the server
         * sends the data
         */
        array_walk($ret[0], 'utf8_encode_array');

        return $ret;
    }



    /**
     * Set player state to state needed.
     * @param string $state : Player state to set
     * @return boolean 
     */
    public function setPlayerState($state){
        
        $this->authUser();
        if(!isset($_GET['matchid']) || !isset($this->auth)|| !isset($this->auth->match)) return false;

        $earlyState = $this->auth->player->state;
        $res = $this->auth->player->setState($state);

        if ($state == 'out')
            
            /*
            * We close the match if this is the last player
            */
            $this->auth->match->updateNbPlayers();
            if(!$this->auth->match->isAnyPlayerLeft()){
                $this->auth->match->close();
            }

        return $res;
    }

    /**
     * get player state
     *
     * @todo: FIX: this does not transfer the data well :(
     * @return string
     */
    public function getPlayerState(){

        // get player state
        $this->authUser();

        return $this->auth->player->getState();
    }


    /**
    * Register a bit of user data (e.g. votes) into database
    * Returns true if that works fine.
    *
    * @param array $aVote
    * @return boolean
    *
    */
    public function sendData($aVote){

        // get player state
        $this->authUser();
        if(!isset($_GET['matchid']) || !isset($this->auth)) return false;

        /*
         * Get the current module
         */
        $curMod = $this->auth->match->currentModule;

        if ($curMod->provideModuleResult($aVote)){
         /*
          *  save result into module result db
          *  The function above also builds the Vote object and sends the result to API
         */
            return true;
        }
        return false;
    }

     /**
     * Return the highscore list and optional extra player information
    * @param int $firstRank
    * @param int $lastRank
     * @return array
     */
    public function getHighscore($firstRank = 0, $lastRank = 8){
        $this->authUser();

        $M = new MySql();

        /*
         * get all the info from the player table
         */

         $query = 'SELECT player.id, player.totalPoints, player.name,
                   player.uiAvatarId, avatars.fileName AS uiAvatarFileName

                    FROM
                        `player` AS player
                        JOIN `ui_avatars` AS avatars
                            ON (avatars.id = player.uiAvatarId)
                    WHERE
                        (player.AI = 0) 

                    ORDER BY player.totalPoints DESC
                    LIMIT '.$firstRank.', '.$lastRank;

        $M->ExecuteSQL($query);

        $table = array();
        if ($M->iAffected > 0){
           $M->ArrayResults();

           // count the ranks
           $rank = $firstRank +1;
           foreach ($M->aArrayedResults as $row){
                  $row['rank'] = $rank++;
                  $table[] = $row;
           }
        }

        /*
         * get actual player data if id provided
         */
        if(isset($this->auth)){

        /*
         * get Player Description
         * @todo: PERFORMANCE this should be cached client-wise

         */
            $table[] = $this->auth->player->getDetailsTable();
        }
        /*
         * Utf8 encoding
         * @todo: STRUCTURE this should be at a central point when the server
         * sends the dataa
         */
        array_walk($table, 'utf8_encode_array');
        return $table;
    }
    

     /**
     * Return Avatar Table
     * @return array
     */
    public function getAvatarTable(){
        $this->authUser();
        if(!isset($this->auth)) return array();
        
        return $this->auth->player->getAvatarTable();
    }
    
    /**
     * Set Avatar
     * @param integer $avatarId
     * @return boolean
     */
    public function buyAvatar($avatarId){
        $this->authUser();
        if(!isset($this->auth)) return false;
        
        $res = $this->auth->player->buyAvatar($avatarId);
        if($res && $avatarId >= 27 ) $this->auth->player->addAchievement('firstVIPAvatar');
        
        return $res ;
    }

    /**
     * Return Genre Match Table
     * @return array
     */
    public function getGenreTable(){
        $this->authUser();
        if(!isset($this->auth)) return array();

        return $this->auth->player->getGenreTable();
    }

    /**
     * Set Genre
     * @param integer $genreId
     * @return boolean
     */
    public function buyGenre($genreId){
        $this->authUser();
        if(!isset($this->auth)) return false;
        
        $res = $this->auth->player->buyGenre($genreId);
        
        // add firstGenre achievement
        if($res) $this->auth->player->addAchievement('firstGenre');
        return $res;
    }
    
     /**
     * Get Player Details
     * @return array
     */
    public function getPlayerDetails(){
        $this->authUser();
        if(!isset($this->auth)) return array();

        return $this->auth->player->getDetailsTable();
    }


     /**
     * Insert feedback into table
     * @param string $feedback
     * @return boolean
     */
    public function provideFeedback($feedback){
        $this->authUser();

        $M = new MySql();

        /*
         * copy the feedback data into the feedback table
         */
        $pinfo = $this->auth;
        unset($pinfo->player->log);
        unset($pinfo->match->auth);
        $M->Insert(array('text' => $feedback,
                                    'playerId' => $this->auth->player->id,
                                    'details' => json_encode($pinfo)), 
                'feedback');
        return $M->iAffected > 0;
    }
    
     /**
     * Return the current states and points of the player in the game.
     * ONLY FOR DEBUG PURPOSES
     * @return array 
     */
    public function getPlayerTable(){
        
        $this->authUser();
        if(!isset($this->auth)) return array();
        
        return $this->auth->match->getPlayerTable();
    }

     /**
     * Returns Remote Time
     *  ONLY FOR DEBUG PURPOSES
     * @return int
     */
    public function getRemoteTime() {
        if(isset($test))
            $ret = $test;
        else
            $ret = date('U');
        return $ret;
    }
      /**
     * Returns Remote Time
     *  ONLY FOR DEBUG PURPOSES
     * @return string
     */
    public function addAchievement(){
        $this->authUser();
        if(!isset($this->auth)) return array();

        $ret = $this->auth->player->addAchievement("first100");
        return $ret;
    }



}
?>
