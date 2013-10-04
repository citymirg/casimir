<?php

class VoteSubsetDistribution {

    /** @var Triplet */
    public $triplet;

    /**
     * This is an array of Vote Ids. It will contain the vote
     * ids elegible for the task
     * @var int[]
     */
    public $subset = array();
    
    /**
     * This is an array of the songChosen property of each vote
     */
    public $songChosen = array();

    /**
     * record all songs chosen and their histogram
     * @var int[]
     */
    public $songChosenHisto = array();

    /** @var int */
    public $size = 0;

    /** @var float[] */
    public $distrib = array();

    /**
     *
     * @param Triplet $aSongId
     */
    public function __construct(Triplet $triplet){

        // save triplet
        $this->triplet = $triplet;
    }

    /**
     * This sets the subset to the set of answers were users have
     * listened to each of the songs at least for a short time
     */
    public function setUnrelatedMinListenVote() {

        /*
         * Define eligible votes:
         * totalTime has to be greater zero
         * all songs have to be listened longer than 1 second
         */
        $aSelect = 'SELECT id, songChosenId FROM VOTE ';
        $aWhere = 'WHERE '.
            '(totalTime >= 3) AND '.
            '(song1PlayTime >= 1) AND ' .
            '(song2PlayTime >= 1) AND '.
            '(song3PlayTime >= 1) AND ' .
            '(songChosenId <> -1)' .        // we allow for non-votes to be saved
            'ORDER BY RAND() LIMIT 1';

        /*
         * Get votes from db
         */
        $M = new MySql();
        $M->ExecuteSQL($aSelect.$aWhere);
        $M->ArrayResults();

        $this->subset[] = $M->aArrayedResults[0]['id'];

         // save songChosen in Array map
        $this->songChosen[$M->aArrayedResults[0]['id']] = $M->aArrayedResults[0]['songChosenId'];
        $this->size++;

        return true;

    }

    /**
     * This sets the subset to the set of answers were users have
     * listened to each of the songs at least for a short time
     */
    public function setMinListenVotes($permId = Null) {

        if($permId == Null)
            $permId = $this->triplet->permId;
        /*
         * Define eligible votes:
         * totalTime has to be greater zero
         * all songs have to be listened longer than 1 second
         */
        $aSelect = 'SELECT id, songChosenId FROM VOTE ';
        $aWhere = 'WHERE (permId = ' . $permId . ') AND '.
            '(totalTime >= 3) AND '.
            '(song1PlayTime >= 1) AND ' .
            '(song2PlayTime >= 1) AND '.
            '(song3PlayTime >= 1)';

        /*
         * Get votes from db
         */
        $M = new MySql();
        $M->ExecuteSQL($aSelect.$aWhere);

        // as we use ExecuteSql the results have to be postprocessed
        $M->ArrayResults();

        /*
         * Save all vote ids into the subset array.
         * Furthermore, we save the chosen Song to get a histogram
         * @todo: STRUCTURE the histogram is actually part of the dis
         *        tribution class
         * @return boolean
         */
        foreach($M->aArrayedResults as $sqlVote){

            // save vote id
            $this->subset[] = $sqlVote['id'];

            // save songChosen in Array map
            $this->songChosen[$sqlVote['id']] = $sqlVote['songChosenId'];
            $this->size++;
        }

        // check if we actually got any data
        if ($this->size >0 ){
            return true;
        }else return false;
    }

     /**
     * This sets the subset of answers were users have
     * listened to each of the songs at least for a short time
     * but regardless of the permutation of order presented
     * @return boolean
     */
    public function setAllPermsMLVotes() {

        // get all valid permutatuins for this triplet
        $aWhere = array('tripletId' =>$this->triplet->id);
        
        $M = new MySql();
        $M->Select('permutation',$aWhere);

        $ret = false;
        // cycle through all permutations and accumulate subset
        foreach($M->aArrayedResults as $res){
            $ret = $ret | $this->setMinListenVotes($res['id']);
        }
        return $ret;
    }
    
    /**
     * Calculate the histogram for the votes in the triplet
     * @param int $limit
     */
    private function getsongChosenHisto(){

        /*
         *  initialise array position
         * @todo: structure: this should be done according to the triplet
         */
        foreach($this->triplet->aSong as $sc) {
                $this->songChosenHisto[$sc->id]= 0;
        }
        
         /*
         * @todo: BUGFIX: code below throws Undefined index with corrupted
          *               database
         */
        // get histogram for the songChosen
        foreach($this->songChosen as $sc){
            $this->songChosenHisto[$sc]++;
        }
    }

    /**
     * Main method for the calcul
     * @param string $method
     * @param array $args
     */
    public function calcDistribution($method,$args = Null){

        $this->getsongChosenHisto();

        $prob = (float) 0 ;
        foreach($this->subset as $voteId){

            $prob = $this->$method($voteId,$args);
            $this->distrib[$voteId] = $prob;
        }

        /*
         * Normalise the distribution:
         * we sum up the entries and then divide each by that
         */
        $distSum = array_sum($this->distrib);
        foreach($this->distrib as $key => $value){
            $this->distrib[$key] /= $distSum;
        }
    }

    //*************************************
    // Vote PROBABILITY CALCULUS METHODS
    //
    //      All the next methods are
    //      applied to only one Triplet
    //      the method calcDistrubition
    //      call those method for each
    //      Triplet from the subset.
    //
    //************************************

    /**
     * Returns the uniform distribution-based probability of the given voteId
     * @param int
     * @return float
     * @todo: classify these
     */
    private function identDistrib($voteId){
        return (float) 1.0 ; // /$this->size;
    }

    /**
     * Returns the histogram-based probability of the given voteId
     * @param int
     * @return float
     * @todo: classify these
     */
    private function histoDistrib($voteId){
        return ((float) $this->songChosenHisto[$this->songChosen[$voteId]]); // /$this->size;
    }
   
}

?>