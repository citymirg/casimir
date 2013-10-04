<?php

include_once 'class.TripletSubset.php';

class TripletDistribution extends TripletSubset {

    
     /** @var float[] */
    public $distrib = array(); 
    

    /** 
     * Main method for the calcul
     * @param string $method
     * @param array $args
     */
    public function calcDistribution($method,$args = Null){
        
        $prob = (float) 0 ;
        
        foreach($this->subset as $tripletId){
            
            $prob = $this->$method($tripletId,$args);
            $this->distrib[$tripletId] = $prob;
        }

    }
    
    
    
    
    //*************************************
    // TRIPLET PROBABILITY CALCULUS METHODS
    // 
    //      All the next methods are 
    //      applied to only one Triplet
    //      the method calcDistrubition
    //      call those method for each
    //      Triplet from the subset.
    //      
    //************************************
    
    /**
     * @param int
     * @return float
     */
    private function identDistrib($tripletId){
        return (float) 1/$this->size;
    }
}

?>
