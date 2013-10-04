<?php

class VoteSelector extends VoteSubsetDistribution{

    /** @var int */
    public $selectedVoteId = Null;

    /*
     * @Todo: reconsider this implementation of a random distribution
     */
    public function setVoteIdByRandom(){
        
        // Random number between 0 and 1
        $X = mt_rand() / mt_getrandmax();

        $lastKey = (int) 0;
        foreach($this->distrib as $key => $prob){

           // We go throuw the array and X while be negative as soon
           $X = $X - $prob;

           if($X < 0){
               $this->selectedVoteId = $key;
               return true;
           }
        }
        return false;
    }


     public function setVoteId($subsetPos){
         $this->selectedVoteId = $this->subset[$subsetPos];
     }

    public function buildSelectedVote(){

        // create selected object
        $this->selectedVote = new Vote();

        // get info from db
        $this->selectedVote->fromDB($this->selectedVoteId);

        /*
         * @todo: STRUCTURE: the ids shouldbe assigned in the fromdb call
         */
        $this->selectedVote->aSongId = array( $this->triplet->aSong[0]->id,
                                              $this->triplet->aSong[1]->id,
                                              $this->triplet->aSong[2]->id);
    }

     public function buildSyntheticVote(){

        // create selected object
        $this->selectedVote = new Vote();


        // get info from db
        $this->selectedVote->fromDB($this->selectedVoteId);

        /*
         * @todo: STRUCTURE: the ids shouldbe assigned in the fromdb call
         */
        $this->selectedVote->aSongId = array( $this->triplet->aSong[0]->id,
                                              $this->triplet->aSong[1]->id,
                                              $this->triplet->aSong[2]->id);
        
        // overwrite the chosen song by random
        $chosenPos = (integer) mt_rand(0,2);
        $this->selectedVote->songChosenId = $this->selectedVote->aSongId[$chosenPos];
    }
}

?>
