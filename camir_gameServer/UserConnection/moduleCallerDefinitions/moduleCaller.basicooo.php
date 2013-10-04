<?php

class BasicoooCaller extends ModuleCaller {
    
    /**
     * @param type $auth 
     */
    public function __construct($auth) {
        parent::__construct($auth);

        // ok, we want all players to be ready
        /*
         * @todo: The AND here does actually not work as it should
         *        this may lead to asynchronous games
         */

        // module type
        $this->type = 'basicooo';
        
        // module maximal time left
        $this->givenTime = 60;
        $this->latestEntryTime = 45;
        
        $this->getModuleArgs();
        }
        
    
    /**
     * creates the module's new arguments
     *  [id1, id2, id3, url1, url2, url3]
     * @return boolean
     */
    public function setModuleArgs(){
        parent::setModuleArgs();
        
        /*
         * Get songs from SOAP API
         * If the match details contain genre data forward it to the API!
         */

        $API = new MainClient();
        
        
        if (!empty($this->auth->match->details) && isset($this->auth->match->details['triplet'])){
            
            //@todo: DEBUG remove json_encode
            $triplet = $API->getTriplet($this->auth->match->details['triplet']);
        }else{
            $triplet = $API->getTriplet();
        }
        
       
        // Send the triplet
        //$args['triplet'] = array();
        foreach($triplet->aSong as $song){
            // don't return all details

            $o = array();
            $o['id'] = $song->id;
            $o['url'] = $song->url;
            $o['details'] = strip_array($song->details, array(
                                                "title",
                                                "artist",
                                                "tempo", // not needed
                                                "buySongUrl"));
            $args[] = $o;
           
        }

        $this->args['aSong'] = $args;
        return true;
    }

     /**
     * Checks and saves the result for the module
     * @param array $aVote generic type for user vote/data
     * 
     * @return boolean true if vote is valid for the provided module
     */
    public function provideModuleResult($aVote = array()){

        /*
         * Construct Vote object and fill Data
         * @todo: STRUCTURE: use dbmappedobject to map array / object 
         *                   use strip_array
         */
        $vote = new Vote();
        
        $vote->voteType = $aVote['voteType']; //Be sure that the right dB is assigned.
        
        $vote->songChosenId = $aVote['songChosenId'];
        $vote->aSongId = array($aVote['aSong'][0]['id'], 
                                $aVote['aSong'][1]['id'], 
                                $aVote['aSong'][2]['id']);
        
        $vote->user = new User();
        $vote->user->id = $this->auth->player->APIUserId;
        $vote->user->sessionId = $this->auth->player->APISessionId;
        
        /*
         * Todo send/recieve the following as userDetails subarray
         */
        
        // append all the extra vote information
        $vote->details = strip_array($aVote, null, array('songChosenId',
                                                   'aSong'
                                                  ));
        
        /*
         * We also save all the players in the game
         */
        $playerTbl = $this->auth->match->getPlayerList();
        foreach ($playerTbl as $player) {
            $o = array();
            $o['id'] = $player->APIUserId;
            $o['AI'] = $player->AI;
            $vote->details['team'][] = $o;
        }
        

        /*
         * Check if the user is cheating
         * 1. check if chosen song is part of triplet
         * 2. compare triplets
         */
        // 1.
        $savedTriplet = array($this->args['aSong'][0]['id'],$this->args['aSong'][1]['id'],$this->args['aSong'][2]['id']);
        $res = ($vote->songChosenId == -1
                || in_array( $vote->songChosenId , $vote->aSongId)
                 ); // allow for empty vote
        
        // 2.
        $res = $res && ($vote->aSongId == $savedTriplet);
            
        // Save in DB and check if saved
         $API = new MainClient();
         $res = $res && $API->sendVote($vote);
            
         
         // Check if match and step correpond.
         $res = $res && ($this->auth->match->id == $vote->details['matchid']);
         $res = $res && ($this->auth->match->step == $vote->details['step']); // Step is sent with +1 to the client
            
         if($res){
            $this->resultToDB($vote);
            return true;
         }
         return $res;

    }


    /**
     * Checks and saves the result for the module
     * @return array
     * @todo make this return boolean
     */
    public function computeProcessedResults() {
        
        //$this->results is initialized already.
        $results = $this->results;


//        // @debug
//        $results[0]['result']->aSongId = array("9422","9422","5509");
//        $results[0]['result']->songChosenId = 5509;

        $processedResults = array();

        // is this a red herring triplet?
        $songs = $results[0]['result']->aSongId;
        $uniqSongs = array_unique($songs);
        sort($uniqSongs);
        $isRH = count($uniqSongs) == 2;

        /*
         * if so, find the herring song and give pointsonly to those who
         * voted for that song
         */
        if($isRH){
            if(count(array_keys($songs,$uniqSongs[0])) == 2){
                $herring = $uniqSongs[1];
            }else{
                $herring = $uniqSongs[0];
            }
        }

        /*
         * Get a histogram of votes
         */
        $voteH = array(
            '_'.$results[0]['result']->aSongId[0] => 0,
            '_'.$results[0]['result']->aSongId[1] => 0,
            '_'.$results[0]['result']->aSongId[2] => 0
                );
        
        foreach($results as $res){
            // only count valid votes
            if($res['result']->songChosenId > 0)
                $voteH['_'.$res['result']->songChosenId]++;
        }
        
        /*
         * @todo: calculate the players points and 
         * agreement with other players
         * 
         * and a rewarding method (ideally in match)
         */
        foreach($results as $res){
           
           /*
            * // get earned points
            * @todo: dependency on number of total players
            */
           if (isset($res['result']->songChosenId) && ((int) $res['result']->songChosenId > 0)){
               $agree = $voteH['_'.$res['result']->songChosenId]-1;
               $earnedPts = $agree * 10;

               // no points if people didnt get the red herring
               if($isRH){
                   if( !((int)$res['result']->songChosenId == (int)$herring)){
                       $earnedPts = 0;
                   }else{
                       $earnedPts = 3 * 10;
                   }
               }
           }else{
               $agree = 0;
               $earnedPts = 0;
           }
            
           
           // get number of agreeing users
           $processedResults[] = array(
                'agreement'=>$agree,
                'earnedPoints' => $earnedPts,
                // old vote
                'vote' => $res['result'],
                // playerId
                'playerId' => $res['playerId']//strip_object($player, array("id","name","points","uiAvatarId"))
           );
           
           
           
           // we create a temporary player and save points
            $player = Player::getFromAuth($res['playerId']);
            $player->addPoints($earnedPts);

        }
        
        return $processedResults;
    }
      
     /**
     * Generates new AI result
      * @return boolean
     */
    public function insertAIResult(){
        
        // construct triplet given for this round
        $savedTriplet = array($this->args['aSong'][0]['id'],$this->args['aSong'][1]['id'],
                                $this->args['aSong'][2]['id']);
        
        // ask the api for a vote
        $API = new MainClient();
        
        /* @var Vote */
        $vote = $API->getAIVote($savedTriplet);
        
        
        // @todo: EFFICIENCY PARADIGM: we could decide log the vote here.
        //        it is only discarded because the API detects the duplicate
        // $vote->id = Null;

        /*
        * Send Result to Game Database
        */
        $success = $this->resultToDB($vote);
        return $success;
    }
    
       
 /**
     * Specific Achievements for this module
     * @param Player $player player for which che results should be chekced
     * @return array
     * @todo: make this efficient to run once for all players
     */
    public function checkAchievements($player){
        $res = parent::checkAchievements($player);

        // security check if results exist
        if (!isset($this->args['results'])) return $res;
        
        foreach($this->args['results'] as $result){
            if((int)$result['playerId'] == $player->id){

                // firstOSO
                
                if ($result['agreement'] > 2){
                    
                    $tmpres = $player->addAchievement("firstOSO");
                    if (!empty($tmpres)){
                        $res[] = $tmpres;
                    }
                }

                /*
                 * @todo:  // threeOSO In A ROW
                if ($this->args['results']['agreement'] >= 2){
                    $player->addAchievement("firstOSO");
                }
                 */
            }
        }
        return $res;
    }
    
    
}

?>
