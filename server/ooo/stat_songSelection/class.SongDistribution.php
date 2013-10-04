<?php

include_once 'class.SongSubset.php';

class SongDistribution extends SongSubset {

    
     /** @var float[] */
    public $distrib = array(); 

    
    /**
     * Song Table has to be precised.
     * @param type $songTable
     */
    public function __construct($songTable) {
        parent::__construct($songTable);
    }    
    

    /** 
     * Main method for the calcul
     * @param string $method
     * @param array $args
     */
    public function calcDistribution($method,$args = Null){
        
        $prob = (float) 0 ;
        
        foreach($this->subset as $songId){
            
            $prob = $this->$method($songId,$args);
            $i = (int) $songId;
            $this->distrib[$i] = $prob;
        }

    }
    
    
    
    
    //*************************************
    // SONG PROBABILITY CALCULUS METHODS
    // 
    //      All the next methods are 
    //      applied to only one Song
    //      the method calcDistrubition
    //      call those method for each
    //      Song from the subset.
    //      
    //************************************
    
    /**
     * @param int
     * @return float
     */
    private function identDistrib($songId,$args){
        return (float) 1/$this->size;
    }
    
    

}

?>
