<?php
class TapTempoCaller extends ModuleCaller {
    public function __construct($auth){
        parent::__construct($auth);
        
        //@todo: treat recursion
        $this->auth = $auth;
          
        $this->type = 'taptempo';
        
        $this->getModuleArgs();
        
    }
    
    
    
    
    /**
     * No argument to send. Return true.
     * @return boolean
     */
    public function setModuleArgs(){
        parent::setModuleArgs();
        /*
         * Get songs from SOAP API
         * @todo: save these in SQL table (current args)
         */
        $API = new MainClient();
        $song = $API->getSong('taptempo');


        $o = array();
        $o['id'] = $song->id;
        $o['url'] = $song->url;
        $o['details'] = strip_array($song->details, array(
                                                "title",
                                                "artist",
                                                "tempo",
                                                "buySongUrl"));
       
        $this->args['aSong'] = $o;
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
         */
        $vote = new GenericVote('taptempo');
        
        $vote->user = new User();
        $vote->user->id = $this->auth->player->APIUserId;
        $vote->user->sessionId = $this->auth->player->APISessionId;
          

        $vote->details = $aVote;
        
        $API = new MainClient();
        $res = $API->sendGenericVote($vote);
        
            /*
             * @todo: send vote to API
             *
             */  
        // Check if match and step correpond.
        $res = $res && ($this->auth->match->id == $vote->details['matchid']);
        $res = $res && ($this->auth->match->step == $vote->details['step']); // Step is sent with +1 to the client
      
        if($res){
            /*
             * Send Result to Game Database
             */

            $res = $this->resultToDB($vote);
        }
        else
            return $res;
    }
    
    /**
     * Generates new AI result
     * @return boolean
     */
    public function insertAIResult(){
        
        $vote = new GenericVote('taptempo');
        
        //$this->getModuleArgs();
        $tempo = $this->getTempo();
        
        //  Give some possibility to answer the doubled tempo and the tempo divided by two.
        $X = rand() / getrandmax();
        if($X < 1/6 && $tempo > 90)
            $tempo = $tempo/2;
        if($X > 3/4 && $tempo < 140)
            $tempo = $tempo*2;
        
        
        
        $vote->details['averageBPM'] = $tempo + $tempo /10 * (2 * rand() / getrandmax()-1);
        $vote->details['averageTime'] = 1/$vote->details['averageBPM'] *60000;
        
        $stdDev = $vote->details['averageTime'] /8;
        $vote->details['stdDevTime'] = $stdDev + $stdDev /1.5 * (2* rand() /  getrandmax()-1);
        
        
        
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
     * Get the tempo of the song currently in the args of the module.
     * @return int
     */
    public function getTempo(){
         // Compute the beat a simple beat spectrum.
        //$this->getModuleArgs();
        $tempo = $this->args['aSong']['details']['tempo'];
        
        if($tempo == 0)
            $tempo = 120;
        return $tempo;
    }
    
    /**
     * Checks and saves the result for the module
     * @return array
     * @todo make this return boolean
     */
    public function computeProcessedResults() {
        
        // get the vote object from db
        $results = $this->results;
        $processedResults = array();
        
        
       
        // Compute regularity Points
        $aRegulatiryPts = array();
        foreach($results as $res){
            
            $stdDevTime = $res['result']->details['stdDevTime'];
            $averageTime = $res['result']->details['averageTime'];
            
            if($averageTime > 0){
                $error = $stdDevTime /$averageTime;
            }
            else{
                $error = 100;
            }
            
            $stdError = 15/100;
            
            $aRegulatiryPts[' ' . $res['playerId']] = $error / $stdError;
            
        }    
        
        // Compute agreement points
        $aAgreementPts = array();
        foreach($results as $res){
            if($res['result']->details['averageBPM'] < 30)
                $aAgreementPts[' ' . $res['playerId']] = 100;
            elseif($res['result']->details['averageBPM'] > 300)
                $aAgreementPts[' ' . $res['playerId']] = 100;
            else{
                    
            $stdDevTime = $res['result']->details['stdDevTime'];
            $averageBPM = $res['result']->details['averageBPM'];
            $averageTime = $res['result']->details['averageTime'];
            
            $tempo = $this->getTempo();
            $ratio = $averageBPM / $tempo;
            if($ratio <1)
                $ratio = 1/$ratio;
            
            $octaveInteger = round($ratio);
            $decimalPart = abs($ratio - $octaveInteger);
            
            $error = $decimalPart;
            
            $stdError = 10/100;
            
            $aAgreementPts[' ' . $res['playerId']] = $error / $stdError;
            }
            
        }            
         //Rank the players
         
        $toRank = array();
        foreach($results as $res){
            $toRank[' ' . $res['playerId']] = $aAgreementPts[' ' . $res['playerId']] + $aRegulatiryPts[' ' . $res['playerId']];
        
        }
       
        asort($toRank);
        $i = 0;
        $rank = array();
        $points = array();
        foreach($toRank as $iid => $pts){
            $i++;
            $rank[$iid] = $i;
            if($i == 1)
                $points[$iid] = 20; 
            if($i == 2)
                $points[$iid] = 10; 
            if($i == 3)
                $points[$iid] = 5; 
            if($i == 4)
                $points[$iid] = 0; 
        }    
        
        
        /**
         * Send back the result to DB.
         */
        foreach($results as $res){
            $stdDevTime = $res['result']->details['stdDevTime'];
            $averageBPM = $res['result']->details['averageBPM'];
            $averageTime = $res['result']->details['averageTime'];

            
            
         
           if($averageTime > 0){
            $pR = $stdDevTime / $averageTime * 100;
           }
           else{
            $pR = 100;
            $averageBPM = 0;
           }
               
           // SEt the values of the earned points
           $processedResults[] = array(
                           
                
                'averageBPM' => round($averageBPM),
                'averageTime' => round($averageTime),
                'percentRegularity' => round($pR),
                'regularityPts' => $aRegulatiryPts[' ' . $res['playerId']],
                'agreementPts' => $aAgreementPts[' ' . $res['playerId']],
                'pointsForRank' => $toRank[' ' . $res['playerId']],
                'earnedPoints' => $points[' ' . $res['playerId']],
                'rank' => $rank[' ' . $res['playerId']],
                // old vote
                'result' => $res['result'],
                // playerId
                'playerId' => $res['playerId']//strip_object($player, array("id","name","points","uiAvatarId"))
           );
           
           // we create a temporary player
           $player = Player::getFromAuth($res['playerId'],
                            $this->auth->player->matchId,  
                            Player::getSessionId($res['playerId'], 
                            $this->auth->player->matchId));
            $player->addPoints($points[' ' . $res['playerId']]);
            
        }
        
        return $processedResults;
    }


    /**
     * Specific Achievements for this module
     * @param Player $player player for which che results should be chekced
     * @return array
     *
     * @todo: make this efficient to run once for all players
     */
   public function checkAchievements($player){
        $res = parent::checkAchievements($player);

        // secureity check if results exist
        if (!isset($this->args['results'])) return $res;

        foreach($this->args['results'] as $result){
            if((int)$result['playerId'] == $player->id && $result['averageBPM'] != -1){

                // tapping Accuracy Acheivements
                if ($result['percentRegularity'] <= 10){

                    $tmpres = $player->addAchievement("tappingAccuracy10");
                    if (!empty($tmpres)){
                        $res[] = $tmpres;
                    }
                }elseif ($result['percentRegularity'] <= 5){

                    $tmpres = $player->addAchievement("tappingAccuracy5");
                    if (!empty($tmpres)){
                        $res[] = $tmpres;
                    }
                }elseif ($result['percentRegularity'] <= 2){

                    $tmpres = $player->addAchievement("tappingAccuracy2");
                    if (!empty($tmpres)){
                        $res[] = $tmpres;
                    }
                }elseif ($result['percentRegularity'] <= 1){

                    $tmpres = $player->addAchievement("tappingAccuracy1");
                    if (!empty($tmpres)){
                        $res[] = $tmpres;
                    }
                }
            }
        }
        return $res;
    }
}

?>
