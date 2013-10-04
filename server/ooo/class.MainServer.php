<?php

/**
 * Public Methods are server requests
 *
 */
class MainServer {
    
    //******************************
    //
    //   CLIENT -> SERVER : REQUESTS
    // 
    //*******************************
    
    /**
    * Send the whole vote rquest to the server. Returns true if that works fine.
    * 
    * @param GenericVote $vote
    * @return boolean
    *
     * @todo: DISCUSS clarify concept how genericVote will be used to get AI votes.
     *                DW thinks it is necessary to be able to SELECT votes at the db
     *                  level. THis is not possible if all information is at the 
     *                  JSON-encoded details struct
     *                  GenericVote should be an internal datatype with derived types
     *                  like taptempovote accessible from outside
    */
    public function sendGenericVote(GenericVote $vote){
            
        $res = $vote->toDB();
        
            /*
             * @todo: schedule Song and Triplet adding
             * indepedent of user interaction
             */
        
            if($vote->voteType == 'taptempo'){
                if($res){
                    /*
                     * @todo: STRUCTURE
                     *  here we assume a songId, but this is not defined in the
                     *  genericvote php file
                     */
                    $song = new Song($vote->details['songId']);
                    $song->incrSongCounter($vote->voteType);
                }

            }else{
                
            }
            $this->genericAddingSongStrategy($vote);
        
        return $res;
     
    }   
    
    
   /**
    * Send the whole vote rquest to the server. Returns true if that works fine.
    * 
    * @todo Redefine sendVote based on sendGenericVote after Making Vote heritated from GenericVote
    * 
    * @param Vote $vote
    * @return boolean
    *
    */
    public function sendVote(Vote $vote){
            
        $res = $vote->toDB();
        if($res){

            /*
             * @todo: schedule Song and Triplet adding
             * indepedent of user interaction
             */
            $this->genericAddingSongStrategy($vote);
            $this->addingTripletStrategy($vote);
            
        }
        
        return $res;
     
    }
    
   /**
    * The triplet selection for the next Triplet to send would be done in four steps.
    * 
    * Send it is time to send back the array of chosen songs.
    * 
    * @param array $filter Fields that should be met by returned triplet,
    *                       e.g. array['genreId'] = 55
    * @todo: STRUCTURE the param filter should be becoming an php array
    * @return Triplet
    *
    */
    public function getTriplet($filter = null){

        //@todo: DEBUG: if no genre specified this is supposed to break stuff        
        $triplet = $this->tripletSelectionStrategy($filter);
        
        
        return $triplet;
    }   
    
    
       /**
    * The song selection for the next Song to send would be done in four steps.
    * 
    * Send it is time to send back the array of chosen songs.
    * 
    * @param string moduleType
    * @param array $filter Fields that should be met by returned triplet,
    *                       e.g. array['genreId'] = 55
    * @todo: STRUCTURE the param filter should be becoming an php array
    * @return Song
    *
    */
    public function getSong($moduleType,$filter = null){

        //@todo: DEBUG: if no genre specified this is supposed to break stuff        
        $song = $this->genericSongSelectionStrategy($moduleType,$filter);
        
        return $song;
    }   
    
    
    
    
    /**
    * Given an array of clipIds or a Triplet, this function returns 
    * 
    * a Vote which is detemined by the Voting data existent for the triplet
    * 
    * @param array $triplet triplet to be answered
    * @return Vote
    *
    */
    public function getAIVote(array $triarray){
        
        $vote = $this->voteSelectionStrategy($triarray);
        
        return $vote;
    }   
    
    
    
    /**
    * Begin the game, we create a session in the databse and we store session
    * information in the user object.
    * Then we send back the object and the game can begin. 
    * 
    * @param array $userDetails
    * @return User 
    *
    */
    public function startSession($userDetails){
       
        $userObj = new User();
        $userObj->details = $userDetails;
        
        $userObj->toDB();
        
//        //Don't send back to much information
//        $userObj->details = array();
        
        return $userObj;
    }
    
    
   /**
    * TEST FUNCTION TO TRY ON SERVER
    *  NO MYSQL CONNEXION
    * 
    * @param array $userDetails
    * @return User 
    *
    */
    public function startSessionNoMySql($userDetails){
       
        $userObj = new User();
        $userObj->details = $userDetails;
        return $userObj;
    }   
    
    //********************************
    //
    // THE NEXT METHODS DEFINES THE
    //  STRATEGY WE USE TO GET THE 
    //      BEST STATISTICS
    //
    //*******************************
    
    
    /**
     * Strategy to follow to ADD a new Triplet in the effective database.
     * 
     * 1. Set a condition for adding a Triplet
     * 2. Check the condition
     *  3. Select the Triplet
     *  4. Add it to Database
     *
     *
     * @todo: make same-genre adding  configurable / callable
     *        from the random song / by genre selection process
     *
     * @param Vote $vote 
     * @return boolean 
     */
    private function addingTripletStrategy(Vote $vote){
        $adder = new TripletAdder();
        
        //1. Only check for new triplets every 4 times
        $adder->condition = $adder->oneOutOf(4);
        if($adder->condition)
            $adder->condition = $adder->XpercentVotedNTimes(70,2);
       

        //2. have 70 percent of the triplets been voted 2 times?
       if($adder->condition){
           
           /*
            * Every second added triplet should be a same genre triplet:
            * 50 percent chance
            */
           $adder->condition = $adder->oneOutOf(2);
           
           if($adder->condition){
                $adder->setTripletOfOneGenre();
           }  else {
                $adder->setTripletToAddByRandom();
           }

           return $adder->addSelectedTriplet();
           
       }
       return false;
    }
    
  
    
    /**
     * 
     * Strategy to follow to ADD a new SONG in the game depending database.
     * 
     * 1. Set a condition for adding a Song
     * 2. Check the condition
     *  3. Select the Song
     *  4. Add it to Database
     *
     * 
     * @param GenericVote $vote 
     * @return boolean 
     */
    public function genericAddingSongStrategy(GenericVote $vote){
        /*if(!isset($vote->voteType)){
            $vote->voteType = 'basicooo';
        }*/
        $adder = new SongAdder($vote->voteType);
        
        /*
         * case determining update strategy depending on module / vote tpye
         * actually this is not generic at all
         * @todo: make this more generic
         */
        if($vote->voteType == 'taptempo'){

            //1. Only check for new songs every 8 times
            $adder->condition = $adder->oneOutOf(8) 
                    && $adder->XpercentVotedNTimes(70,10);
            

            //2. have 90 percent of the songs been voted in 3 times?
            if($adder->condition){
               /*
                * we only add taptempo dataset songs,
                */
               $db = 4;
               $adder->setSongToAddByRandom($db, $vote->voteType);
               
               //Add the Song
                return $adder->addSelectedSong();
            }
        }elseif($vote->voteType == 'basicooo'){

            //1. Only check for new songs every 8 times
            $adder->condition = $adder->oneOutOf(8) 
                    && $adder->XpercentVotedNTimes(70,7);
            
            //2. have 90 percent of the songs been voted in 3 times?
            if($adder->condition){
           
               /*
                * we only add million song dataset songs,
                * bein fully genre annotated
                */
               $db = 2;
               $adder->setSongToAddDBFullAnnotated($db, $vote->voteType);

               //Add the Song
               return $adder->addSelectedSong();
            }
       }else{
           return false;
       }
    }
    
    /**
     * Strategy to follow to select the nextTriplet to give to the user.
     * 
     * 1. Choose a subset of group of three songs
     * 2. Calculate for each song of this subset as muuch as with want this triplet
     * to be chosen. This will be stored in a probability distribution array.
     * 3. Take a number by random from zero to one to choose the triplet.
     * 4. For the chosen triplet choose the right permutation
     * 
     * @param string $filter Fields that should be met by returned triplet,
    *                       e.g. array['genreId'] = 55
     * 
     * @return Triplet
     */
    private function tripletSelectionStrategy($filter = null){
        $P = new PermSelector();

        /*
         * @todo: make selection strategy configurable in a game configuration table
         */
        $prob = new SongAdder(null);
        
        //1. Chose a subset of Triplet    
        // red herring?
        if(!$prob->oneOutOf(15)){
            if (isset($filter['genreId'])){
                // specific genre asked for

                $P->setOneGenreTripletSubset($filter['genreId']);
            }else{
                $P->setAllTripletSubset();
            }
        //Red Herring every 20 games 
        }else $P->setRedHerringSubset();

        //2. Calculate the probability distributation table
        $P->calcDistribution('identDistrib');
        
        //3. Chose a triplet by random.
        $P->setTripletIdByRandom();
           
        //4. Chose the right permutation we need
        $P->setPermTypeByRandom();
        
        
        $P->buildSelectedPermObject();
        
        
         
        // Send the triplet back
        return $P->selectedPerm;
        
    }
    
    
    
    /**
     * Strategy to follow to select the next Song to give to the user.
     * 
     * 1. Choose a subset of group of three songs
     * 2. Calculate for each song of this subset as muuch as with want this song
     * to be chosen. This will be stored in a probability distribution array.
     * 3. Take a number by random from zero to one to choose the song.
     * 
     * @todo DEFINE param  $songtable ????
     * @param string $filter
     * 
     * @return Song
     */
    private function genericSongSelectionStrategy($songTable,$filter = null){
        $S = new SongSelector($songTable);

        /*
         * @todo: make selection strategy configurable in a game configuration table
         */
        
        //1. Chose a subset of Song        
        if (isset($filter['genreId'])){
            $S->setOneGenreSongSubset($filter['genreId']);
        }else{
            $S->setAllSongSubset();
        }

        //2. Calculate the probability distributation table
        $S->calcDistribution('identDistrib');
        
        //3. Chose a triplet by random.
        $S->setSongIdByRandom();
        
        
        $S->buildSelectedSongObject();
         
        
        // Send the triplet back
        return $S->selectedSong;
        
    }
    
    
    /**
     * Strategy  to generate an AI vote for a triplet
     * 
     * @param array $triarray triplet to be answered
     * @return Vote
     */
    private function voteSelectionStrategy(array $triarray){

        /*
         * Look for triplet in db
         * @todo: cave: this may insert the triplet
         * into the database if its not existing there
         */
        $triplet = new Triplet($triarray);

        $P = new VoteSelector($triplet);

        //1. Extract all the relevant votes
        /*
         * This function gets all those where
         * people listened to the triplet / permutation
         */

        //$P->setMinListenVotes() more accurate as all have the same permutation
        if ($P->setAllPermsMLVotes()){

            //2. Calculate the probability distributation table
            $P->calcDistribution('histoDistrib');

            //3. Choose an Answer by random.
            $P->setVoteIdByRandom();

            // finish up vote object
            $P->buildSelectedVote();

        }else{
         /*
         * Else we have to invent a vote / loosen the prerequesites
         * E.g. no not care about permutations
         */
            $P->setUnrelatedMinListenVote();

            $P->setVoteId(0);

            $P->buildSyntheticVote();
        }

        // Send the Vote back
        return $P->selectedVote;
    }
}
?>
