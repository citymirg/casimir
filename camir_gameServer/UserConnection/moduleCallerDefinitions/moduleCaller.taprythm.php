<?php
class TapRythmCaller extends ModuleCaller {
    public function __construct($auth){
        parent::__construct($auth);
        
        //@todo: treat recursion
        $this->auth = $auth;
          
        $this->type = 'TapRythm';
        
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
        $vote = new GenericVote($aVote['voteType']);
        
        $vote->user = new User();
        $vote->user->id = $this->auth->player->APIUserId;
        $vote->user->sessionId = $this->auth->player->APISessionId;
         
        
        
        $vote->details = $aVote;
        
        if(isset($aVote['fMax']))
            $vote->details['complex'] = $this->computeComplexFromfMax($aVote['fMax']['fRelative'], $aVote['fMax']['val']);
        else
            $vote->details['complex'] = 0;
        
        
            /*
             * Send vote to API
             */
        
        $API = new MainClient();
       $res =  $API->sendGenericVote($vote);
         // Check if match and step correpond.
         $res = $res && ($this->auth->match->id == $vote->details['matchid']);
         $res = $res && ($this->auth->match->step == $vote->details['step']); // Step is sent with +1 to the client
      
       
        unset($vote->details['tapTimes']);
            /*
             * Send Result to Game Database
             */
        $res = $res && $this->resultToDB($vote);
                
        return $res;
            
            
    }
    
    /**
     * Generates new AI result
     * @return boolean
     */
    public function insertAIResult(){
        
        $vote = new GenericVote('taprythm');
        
        
        $fVal = array(
            rand(17,33) /1000,
            rand(11,26) /1000,
            rand(9,20) /1000
        );
        $fRef = array(
            rand(65,135) /100,
            rand(5,20) /100,
            rand(5,20) /100,
        );
        
        $vote->details['fMax']['fRelative'] = $fRef;
        $vote->details['fMax']['val'] = $fVal;
        $vote->details['timedOut'] = false;
        
        $vote->details['complex'] = $this->computeComplexFromfMax($fRef,$fVal);
       
        
        
        
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
     * Checks and saves the result for the module
     * @return array
     * @todo make this return boolean
     */
    public function computeProcessedResults() {
        
        $results = $this->results;
        $processedResults = array();
        
        // Compute the sum of the complex
        $sumComplex = 0;
        foreach($results as $res){
            $sumComplex +=  $res['result']->details['complex'];
            
        }
        
        
        // Points related to complexity
        $aRelComp = array();
        foreach($results as $res){
            
            if($res['result']->details['timedOut'] == true)
                $aRelComp[' ' . $res['playerId']] = 0;
            else
            $aRelComp[' ' . $res['playerId']] = $res['result']->details['complex'];
            
        }
        
        // Points related to accuracy
        $aAccuracy = array();
        foreach($results as $res){
                
            if($res['result']->details['timedOut'] == true)
                $aAccuracy[' ' . $res['playerId']] = 0;
            else{
                
            $fRel0 = $res['result']->details['fMax']['fRelative'][0];
            
            $ratio = $fRel0;
            if($ratio <1)
                $ratio = 1/$ratio;
            
            $octaveInteger = min(4,round($ratio));
            $decimalPart = abs($ratio - $octaveInteger);
            
            $error = $decimalPart ;
            
            $stdError = 20/100;
            
            $aAccuracy[' ' . $res['playerId']] = 1/ (1+ $error/$stdError);
            
            
            }
        }  
            
        
        // Ranking
        $toRank = array();
        foreach($results as $res){
            $toRank[ ' ' . $res['playerId']] = $aRelComp[' ' . $res['playerId']] + 59/69 * $aAccuracy[' ' . $res['playerId']];
        }
        
        asort($toRank);
        $i = 0;
        $rank = array();
        $points = array();
        foreach($toRank as $iid => $comp){
            $i++;
            $rank[$iid] = $i;
            if($i == 4)
                $points[$iid] = 20; 
            if($i == 3)
                $points[$iid] = 10; 
            if($i == 2)
                $points[$iid] = 5; 
            if($i == 1)
                $points[$iid] = 0; 
        }
        
        
        /*
         * @todo: calculate the players points and 
         * agreement with other players
         * 
         * and a rewarding method (ideally in match)
         */
        foreach($results as $res){
                 

           
           // SEt the values of the earned points
           $processedResults[] = array(
                           
               
                'complex' => $res['result']->details['complex'],
                'fRelative' => $res['result']->details['fMax']['fRelative'],
                'fVal' => $res['result']->details['fMax']['val'],
                'relComp' => $aRelComp[' ' . $res['playerId']],
                'accuracy' => $aAccuracy[' ' . $res['playerId']],
                'rank' => $rank[' ' . $res['playerId']],
                'earnedPoints' => $points[' ' . $res['playerId']],
               
                // playerId
                'playerId' => $res['playerId']//strip_object($player, array("id","name","points","uiAvatarId"))
           );
           
           
            // we create a temporary player
            $player = Player::getFromAuth($res['playerId']);
            $player->addPoints($points[' ' . $res['playerId']]);

        }
        
        return $processedResults;
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
     * 
     */
    public function computeComplexFromfMax($fRel,$fVal){
        
        $error = 0;
        $bruitDft = 0;
        foreach($fRel as $a){
            if($a < 0.2)
                $error = $error +2;
            if($a < 0.15)
                $error = $error +4;
            if($a < 0.1)
                $error = $error +8;
        }
        
        
        
        foreach($fVal as $a){
            if($a >= 0.025 && $a < 0.027)
                $bruitDft = $bruitDft +2;
            else if($a >= 0.022 && $a < 0.025)
                $bruitDft = $bruitDft +4;
            else if($a >= 0.018 && $a < 0.022)
                $bruitDft = $bruitDft +6;
            else if($a < 0.018)
                $bruitDft = $bruitDft +8;
            else if($a < 0.016)
                $bruitDft = $bruitDft +10;
            else if($a < 0.013)
                $bruitDft = $bruitDft +12;
        }
        
        $stdErrors = 20;
        $complex = 1/ (1 + ($error + $bruitDft)/$stdErrors);
        
        return $complex;
    }
    
}

?>
