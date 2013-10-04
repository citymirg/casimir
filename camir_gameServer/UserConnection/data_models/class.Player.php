<?php


/**
 * Player class description.
 *
 */
class Player extends dbMappedObject{
    //---------------------------
    // Static Authentification variables:
    // These must not change for the lifetime of the
    // player entry in the database
    //
    // The GameClient has more precise User Information than the Triplet API:
    //   We can have multiple sessions for the same user at the API level,
    //   But not at the Game server, as there are different matches
    //---------------------------
    
    /** @var integer  */
    public $id;

    /**
     * @var string external Unique property (username/hash of fb id/ systemtime)
     *      we need this in order to avoid double log-ins of users, but
     *      may not store this in the long-term CASIMIR API
     */
    public $uniqueExtId;
    
    /** @var integer  */
    public $APIUserId;
    
    /** @var integer  */
    public $APISessionId;

    //---------------------------
    // Dynamic Player Variables
    //---------------------------

      /** @var integer */
    public $matchId;

    /** @var integer  */
    public $sessionId;
    
    /** @var integer */
    public $totalPoints;
    
    /** @var integer
     *  Not used yet but for temporary game pts
     *  */
    public $points;
    
    /** @var string */
    public $state;
    
    /** @var integer */
    public $lastConnectionTime;
    
    /** @var integer 
     *  true If this is an AI player
     */
    public $AI;
    
    /** @var string */
    public $name;
    
    /*
     * The following are ui-specific elementss
     */
    /** @var integer */
    public $uiLanguage;
    
    /** @var integer */
    public $uiAvatarId;

    /** @var integer */
    public $uiTheme;
    
    /** @var Logger */
    public $log;

    
    //------------------------------------------
    // Player Class in SERVER - User Interaction
    //------------------------------------------
    /*
     * Main constructor of the player class
     * @constructor
     * @param array $userDetails
     * @param array $fbSession optional facebook session information
     */

    function __construct($userDetails = array(),$fbSession = null) {

        $this->table = 'Player';
        $this->log = Logger::getLogger(__CLASS__);// Logger::getLogger(__CLASS__);
        
        // empty constructor
        if (empty($userDetails))
            return;
       
        //2.
        /*
         * The Player Id is determined using the unique Identifier and API ID
         */
        if (!isset($userDetails['uniqueExtId'])){
            if (!empty($fbSession)){
                $userDetails['uniqueExtId'] = sha1($fbSession['user_id']);
            }else{
                $userDetails['uniqueExtId'] = $userDetails['ipHash'];

                // append the name to avoid collisions when using the same IP
                if (isset($userDetails['name'])){
                   $userDetails['uniqueExtId'] .= substr($userDetails['name'], 0,20);
                }
            }
        }

        // apply all other user details
        $this->applyDBDetails($userDetails);
        
        //1.
        /*
         * We look up the user in the API (and overwrite)
         * @todo: take API session as game session
         * @todo: unable to transport any user details
         */
        $this->getAPIUserId($userDetails);
        
        
        /*
         * The User is added to the player DB, if not existent
         * @todo: PERFORMANCE: some calls here are repeated by guarantee
         */
        if(!$this->getPlayerId()){
            
            // set this to be a human player
            if(!isset($this->AI))
                $this->AI = 0;
            
            /*
            * Give random avatar
            */
            if (!isset($this->uiAvatarId))
                $this->uiAvatarId = rand(0, 3);
            
            // initialise further values
            $this->totalPoints = 0;

            // Create new Player if we haven't seen this before
            $this->dbWrite();
         
        }else{

            // update player information
            $this->dbWrite(); 
            
            // load remaining information ?
            $this->fullData();

            /*
             * @todo: check if the player is still active in a game. if so
             *        a. send back? (timing will be reset) or 
             *        b. kick from old match and join new
             * use getMatch Function?
             */
        }
        
        /*
         * register facebookId and oauth token
         */
         if (!empty($fbSession)){
            $M = new MySQL();
            $aVars = array(
                'id' => $fbSession['user_id'],
                'oauth_token' => $fbSession['oauth_token'],
                'uniqueExtId' => $userDetails['uniqueExtId'],
                'playerId' => $this->id
            );
            $M->Replace($aVars, 'fb_session');
         }
    }



    /**
     * Return player when authentification
     * @param integer $pId player Id.
     * @param integer $mId match Id.
     * @param integer $sessId match session Id.
     * 
     * @return Player
     */
    static public function getFromAuth($pId, $mId = null, $sessId = null) {
        
        $player = new Player();
        
        $player->id = $pId;
        $player->matchId = $mId;
        $player->sessionId = $sessId;

        if($player->fullData()){
            if ($player->AI){
                return new AIPlayer($player);
            }
            else {
                return $player;
            }
        }
        return Null;   
    }
    

    /**
     * Full data of the object from Authentification information.
     */
    public function fullData(){
        $M = new MySql();

        // have we found a player ?
        if ($this->dbLoad($this->id)){
            
            /*
             * get the match / session data from the session table if match has been assigned
             * this is very similar to getSessionId
             */
            if (!empty($this->sessionId)){
                $aWhere = array('id' => $this->sessionId );
                $M->Select('gameSession',$aWhere);

                $this->lastConnectionTime = $M->aArrayedResults[0]['lastConnectionTime'];
               // $this->points = $M->aArrayedResults[0]['points'];
                $this->APISessionId = $M->aArrayedResults[0]['APISessionId'];
                $this->state = $M->aArrayedResults[0]['state'];
            }
            return true;
        } else {
            return false;
        }

    }

    
    /*
     * Returns the session Id if the player is in tha match
     */
    static public function getSessionId($pId,$mId){
        $query = '
                    SELECT sess.id
                    
                    FROM `gameSession` AS sess
                    
                    WHERE 
                        (sess.playerId = '.$pId.') 
                    AND (sess.matchId = '.$mId.')';
        
        $result = mysql_query($query);
        $res = mysql_fetch_array($result);
        return $res[0];
    }
    
    
    /*
     * Returns the session if the player is in any match
     */
    public function getSession(){
        
        $M = new MySQL();
        $M->Select('gameSession', array('playerId' => $this->id));
        return $M->aArrayedResults[0];
    }
    
     /**
     * Return validity of provided auth
     * @return boolean
     */
    public function isValidAuth() {
        
        $M = new MySQL();
        $M->Select('gameSession', array('playerId' => $this->id,
                                        'matchId' => $this->matchId,
                                        'id' => $this->sessionId));
        return $M->iAffected > 0; 
    }
    
    //------------------------------------------
    // Player Class in SERVER - API Interaction
    // 
    //------------------------------------------
    
    /**
     * Check number Id in data base corresponding to the APIUserId.
     * @return integer
     */
    public function getPlayerId(){
        if(isset($this->id))
            return $this->id;
        
        $M = new MySql();
        $aWhere = array(
            //'APIUserId' => $this->APIUserId,   now uniqueExtId is unique in our table
            'uniqueExtId' => $this->uniqueExtId
                );
        $M->Select('player',$aWhere);
        
        if(!empty($M->aArrayedResults[0])){
            // found player in db
            $this->id = $M->aArrayedResults[0]['id'];
            return $this->id;
            }
        else // player Not in DB
            return 0;
    }
    
    /**
     * Create a new User from User Client information.
     * Still need to join a match afterwards.
     * 
     * @param array $userDetails
     */
    public function getAPIUserId($userDetails) {
        
        $cl = new MainClient();
        $APIUser = $cl->startSession($userDetails);

        $this->APIUserId = $APIUser->id;
        $this->APISessionId = $APIUser->sessionId;
    }
    

    //------------------------
    // Playing player Methods
    //------------------------

    
    /**
     * Register player in Match via the gamesession Table
     * @param Game $match
     *
     * @todo: STRUCTURE this belongs into the match class!!!
     * @param Match $match
     * @return boolean
     */
    public function joinMatch($match) {
        
        $M = new MySql();

        /*
         * Test if the current module is already progressed to result
         * then, show results as well (or set $this->state = 'done' for security)
         * 
         * @todo: STRUCTURE convert ModuleLaunchConditions into functions and implement this there!
         *                  especially the call for isTooLate
         */

        // start done if any player already ended
        // save state options to set are ready and moduledone, as they change
        // automatically with the module delivered
        if($match->nbHumanPlayers == 0){
            $this->state = 'ready';
        }elseif($match->nbHumanPlayers > 0 && ($match->isAnyPlayer('done')
                                            || $match->isAnyPlayer('onResultModule'))){
            $this->state = 'ready';
        }elseif($match->nbHumanPlayers > 0
                && ($match->currentModule->isTooLate()
                    || $match->isAnyPlayer('moduleDone'))){
            // show result if result is displayed

            $this->state = 'moduleDone';
        }else{
            // otherwise start ready
            $this->state = 'ready';
        }
        $this->log->trace("Player " . $this->id .$this->name . "starts with " .  $this->state ." in Match " .$match->id);  

        /*
         * we make sure the player has no session running , and 
         * deal with the session otherwise
         */
        $sess = $this->getSession();
        $sId = (integer) $sess['id'];
        $mid = (integer) $sess['matchId'];
        
        // is the player already added to this match?
        if (!empty($sess)) {
            /*
             * THe player is already in a different game, we delete the old session
             */
            $aWhere = array(
                'playerId' => $this->id,
                'matchId' => $mid,
                'id' => $sId);

            $M->Delete('gameSession',  $aWhere);
        }

        /*
         * Add this player to the match
         */
        $aInsert = array(
            'state' => $this->state,
            'playerId' => $this->id,
            'APISessionId' => $this->APISessionId,
            'lastConnectionTime' => date('U'),
            'points' => 0,
            'matchId' => $match->id);

        $M->Insert($aInsert, 'gameSession');
        $this->sessionId = mysql_insert_id($M->sDBLink);

        /*
         * Kick an AI player if the game is full
         */
        if ($match->nbPlayers() >= match::$maxPlayers) {
            $aiplay = $match->getAIPlayerList();
            if (count($aiplay) > 0)
                $aiplay[0]->kick();
        }
        
        /*
         * update match player number and
         * Check again if match is not too full, and kick player otherwise
         */
        $match->updateNbPlayers();
         if ($match->nbPlayers() > match::$maxPlayers){
             $this->kick();
             $match->updateNbPlayers();
         }
        
        // only fill AI players if this is a human player in the first round
        if (!$this->AI && $match->nbHumanPlayers == 1)
            $match->fillAIPlayers();

        $this->points = 0;
        $this->matchId = $match->id;
        $this->lastConnectionTime = date('U');

        return true;
    }
    
    
    /**
     * Change the player's state in DB.
     * @var string $state
     * @return boolean
     */
   public function setState($state){
       if($this->state == $state)
           return false;
       
       $M = new MySql();
       $aSet = array('state' => $state);
       $aWhere = array('id' => $this->sessionId);
       if($M->Update('gameSession',$aSet,$aWhere)){

           /*
            * Update gamesession if this is a kick
            */
           $res = true;
           if ($state == 'out' && ($this->state == "moduleDone" || $this->state == "done")){
                $auth = new Auth();
                $auth->player = $this;
                $auth->match->id = $this->matchId;

                // create temporary match
                $match = new Match($auth, $this->matchId);

                // remove module result
                $res = $match->currentModule->removeResult($this);
                /*
                 * @todo: updatenbPlayers?
                 */
            }

            // update state in memory
           $this->state = $state;
           $this->updateTimeStamp();
           
           $this->log->trace("Player " . $this->id .$this->name .", session ". $this->sessionId ." changed state to " .$state ." in Match " .$this->matchId);  
           return $res;
       }
       $this->log->debug("Player " . $this->id .$this->name .", session ". $this->sessionId ." STATECHANGE FAILED " .$state ." in Match " .$this->matchId);  
       return false;
   }


    /**
     * Get the player's state from DB
     * @var string $state
     * @return string
     */
   public function getState(){
       if($this->state != Null)
           return $this->state;

       $M = new MySql();
       $aSet = 'state';
       $aWhere = array('id' => $this->sessionId);
       if($M->Select('gameSession',$aSet,$aWhere)){
           $this->state = $state;
       }
       return $this->state;
   }
   
    /**
     * Change the player's state in DB to out like if it has gone.
     * @return boolean
     *
     * @todo:  STRUCTURE: it is very difficult to kick players / reach the match
       from the player class
     *  @todo: should be moved to match alongside JoinMatch
     */
    public function kick(){
        return $this->setState('out');
   }
   
   /**
    * Check if ready.
    * @return boolean 
    */
     public function isReady(){
      return ($this->getState() === 'ready');
    }
   
   /**
    * Update Last connection time. 
    */
   public function updateTimeStamp(){
       $M = new MySql();
       $this->lastConnectionTime = date('U');
       $aSet = array('lastConnectionTime' => $this->lastConnectionTime );
       $aWhere = array('id' => $this->sessionId );
       $M->Update('gameSession',$aSet,$aWhere);
   }
   
    /**
    * add points to the players score
    * save them into the database 
    * @param integer $npoints Number of Points to add
    * @return boolean 
    */
   public function addPoints($npoints){
       if ($npoints <= 0) 
           return true;
       
       $this->totalPoints += $npoints;
       
       // update database with players points
        $M = new MySql();
        $aWhere = array(
            //'APIUserId' => $this->APIUserId,
            'id' => $this->id
                );
        
        $aSet = array(
            'totalPoints' => $this->totalPoints
                );
        
        // execute sql statement        
        $M->Update('player', $aSet, $aWhere);
        return $M->aResult;
   }
    /**
    * -----------------------------------------------------------
    *  The following functions are not necessary for the match
    */

   /**
    * add points to the players score
    * save them into the database
    * @param integer $npoints Number of Points to add
    * @return boolean
    */
   public function substractPoints($npoints){
       if ($npoints <= 0)
           return true;

       $this->totalPoints -= $npoints;

       // update database with players points
        $M = new MySql();
        $aWhere = array(
            //'APIUserId' => $this->APIUserId,
            'id' => $this->id
                );

        $aSet = array(
            'totalPoints' => $this->totalPoints
                );

        // execute sql statement
        $M->Update('player', $aSet, $aWhere);
        return $M->aResult;
   }
   
   
   

   /**
    * get current player rank if id provided
    * @param integer $npoints Number of Points to add
    * @return integer
    */
   public function getRank(){
        $M = new MySql();
            $query = 'SELECT COUNT(*)
                        FROM `player`
                        WHERE (AI = 0) AND
                        (totalPoints >=' .$this->totalPoints. ')';
            $M->ExecuteSQL($query);
            $M->ArrayResults();

            return $M->aArrayedResults[0]["COUNT(*)"];
   }
   
   /**
    * add points to the players score
    * save them into the database
    * @param integer $npoints Number of Points to add
    * @return boolean
    */
   public function changeUiAvatar($avatarId){
      $this->uiAvatarId = $avatarId;
       
      $M = new MySql();
        $aWhere = array(
            //'APIUserId' => $this->APIUserId,
            'id' => $this->id
                );

        $aSet = array(
            'uiAvatarId' => $this->uiAvatarId
                );

        // execute sql statement
        $M->Update('player', $aSet, $aWhere);
        return $M->aResult;
   }
   
   
   /**
    * get current player rank if id provided
    * @return string
    */
   public function getUiAvatarFileName(){
       
       /*
        * todo: performance: Save Avatar FileNmae instead of ID in player
        */
                $M = new MySql();
                $aWhere = array('id' => $this->uiAvatarId);
                $M->Select('ui_avatars',$aWhere);
                        
                return $M->aArrayedResults[0]["fileName"];
   }
   /**
    * all player details as array
    * @return array
    */
   public function getDetailsTable(){
        $row = array();
        $row['id'] = $this->id;
        $row['name'] = $this->name;
        $row['totalPoints'] = $this->totalPoints;
        $row['uiAvatarId'] = $this->uiAvatarId;
        $row['uiAvatarFileName'] = $this->getUiAvatarFileName();
        $row['uiLanguage'] =$this->uiLanguage;
        $row['uiTheme'] = $this->uiTheme;
        $row['rank'] = $this->getRank();
        $row['genres'] = $this->getGenreTable(true);
        return $row;
   }
   
   /**
    * Avatar Table With
    * @return array
    */
   public function getAvatarTable(){
        $M = new MySql();
        
        /*
         * This query uses the right join to get a result containing all avatars,
         * indicating whether they have geen bought by the player by the playerid (id / NULL)
         * 
         *  playerId 	uiAvatarId  uiAvatarFileName 	costPoints levelRequired 
            1           2           animals-cat.png 	0           0
            1           1           animals-elephant.png0           0
            NULL	3           animals-beaver.png 	0           0
            NULL	0           animals-rabbit.png 	0           0
         */
        $query = 'SELECT link.playerId , ui_avatars.id AS uiAvatarId, ui_avatars.fileName AS uiAvatarFileName,
                        costPoints, levelRequired
                    
                    FROM 
                       (SELECT * FROM linkplayerui_avatars WHERE (playerId = '.$this->id.')) AS link
                       
                    RIGHT JOIN ui_avatars
                    ON (link.ui_avatarsId = ui_avatars.id) 

                    ORDER BY playerId DESC ,levelRequired, costPoints ASC';
        $M->ExecuteSQL($query);
        $M->ArrayResults();
        
        return $M->aArrayedResults;
   }

   
   /**
    * Avatar Table With
    * @param integer $avatarId
    * @return boolean
    */
   public function buyAvatar($avatarId){
       // test if player owns the avatar
        $M = new MySql();

       $query = 'SELECT link.playerId , avatars.id AS uiAvatarId, avatars.fileName AS uiAvatarFileName,
                        costPoints, levelRequired
                    
                    FROM 
                       (SELECT * FROM linkplayerui_avatars WHERE (playerId = '.$this->id.')) AS link
                       
                    RIGHT JOIN 
                    (SELECT * FROM ui_avatars WHERE (id = '.$avatarId.')) AS avatars
                    ON (link.ui_avatarsId = avatars.id)';
        $M->ExecuteSQL($query);        
        $M->ArrayResults();
        
        $avatar = $M->aArrayedResults[0];
        
        
       // is this a buy? replaces hasAvatar
       if ($avatar['playerId'] == null){
           // do we have enough money?
           if($this->totalPoints < $avatar['costPoints']){
               
               // the player doesnt have enough points
               return false;
           }else{
               $this->substractPoints($avatar['costPoints']);
               
               $aSet = array('playerId' => $this->id,
                            'ui_avatarsId' => $avatarId);
               $M->Insert($aSet,'linkplayerui_avatars');
           }
       }
       return $this->changeUiAvatar($avatarId);    
   }

    /**
    * Genre Table With
    * @return array
    */
   public function getGenreTable($owned = false){
        $M = new MySql();

        /*
         * This query uses the right join to get a result containing all buyable  genres,
         * indicating whether they have geen bought by the player by the playerid (id / NULL)
         *
         */
        $query = 'SELECT link.playerId , genre.id AS genreId, genre.name, costPoints, levelRequired

                    FROM
                       (SELECT * FROM linkplayergenre WHERE (playerId = '.$this->id.')) AS link';

        if(!$owned) $query = $query .
                   ' RIGHT';

        $query = $query .
                   ' JOIN genre
                        ON (link.genreId = genre.id)
                        
                    JOIN genreCost
                        ON (genreCost.genreId = genre.id)
                    ORDER BY playerId DESC ,levelRequired, costPoints, name ASC';

        $M->ExecuteSQL($query);
        $M->ArrayResults();

        return $M->aArrayedResults;
   }

   /**
    * Buy a special Genre
    * @param integer $genreId
    * @return boolean
    */
   public function buyGenre($genreId){
       // test if player owns the avatar
        $M = new MySql();

       $query = 'SELECT link.playerId , genres.id AS genreId, genres.name, costPoints, levelRequired

                    FROM
                       (SELECT * FROM linkplayergenre WHERE (playerId = '.$this->id.')) AS link

                    RIGHT JOIN
                        (SELECT * FROM genre WHERE (id = '.$genreId.')) AS genres
                        ON (link.genreId = genres.id)
                    JOIN genreCost
                        ON (genreCost.genreId = genres.id)';
        $M->ExecuteSQL($query);
        $M->ArrayResults();
        $genre = $M->aArrayedResults[0];


       // Does the Player not own the genre already?
       if ($genre['playerId'] == null){
           // do we have enough money?
           if($this->totalPoints < $genre['costPoints']){

               // the player doesnt have enough points
               return false;
           }else{
               $this->substractPoints($genre['costPoints']);

               $aSet = array('playerId' => $this->id,
                            'genreId' => $genreId);
               $M->Insert($aSet,'linkplayergenre');
           }
       }
       return $M->aResult;
   }
   
   /*
    * Add an achievement for the player
    * @param string $a name of achievement to add
    * @return array Description of the achievement
    */
   public function addAchievement($a){
        $M = new MySQL();

        // get id for achievement
        $M->Select('fb_achievements',array('name' => $a));

        /*
         * Achievement stuff
         */
        // get achievement id and order
        $aId = $M->aArrayedResults[0]['id'];
        $order = $M->aArrayedResults[0]['order'];
        $description =  $M->aArrayedResults[0]['description'];

        // the url of the achievement
        $achievement_url = FINAL_GAME_CLIENT_PATH. "achieve.php?name=". $a;

        // does the player already have the achievement?
        $M->Select('linkplayer_fb_achievements',
                array('playerId' => $this->id,
                      'achievementId' => $aId));
        
        // register the achievement if not already achieved
        if ($M->iAffected < 1){
            
            // get facebook session  from db
            // @todo: PERFORMANCE is this a facebook player?
            $aWhere = array(
                'playerId' => $this->id
            );
            $M->Select('fb_session',$aWhere);

            $achievement_result = true;
            
            // return if this is not a facebook user
            if ($M->iAffected >= 1){

                // set facebook session
                $fb_id = $M->aArrayedResults[0]['id'];
                $oauth_token = $M->aArrayedResults[0]['oauth_token'];


                // Register an Achievement for the user, using https post
                $achievement_request_URL = 'https://graph.facebook.com/' 
                        . $fb_id
                        . '/achievements';
                $achievement_result = CURL($achievement_request_URL,
                    'achievement=' . $achievement_url
                    . '&access_token=' . $oauth_token
                );
            }

            //save achievement in db
            if ($achievement_result != false){
                $M->Insert(array('playerId' => $this->id,
                      'achievementId' => $aId),
                      'linkplayer_fb_achievements');
            }

            return array('description' => $description, 'url' => $achievement_url, 'name' => $a, 'id' => $aId);
        }else return array();
   }
   
}
?>
