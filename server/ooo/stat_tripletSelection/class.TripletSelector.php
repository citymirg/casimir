<?php


include_once 'class.TripletDistribution.php';

class TripletSelector extends TripletDistribution {
   
    /** @var int */
    public $selectedTripletId = Null;
    
    /**
     * We select the triplet using the input distribution
     * 
     * iterate over the list of probablilities, 
     * every time subtracting the prob from 
     * the random number chosen above
     * return when value < 0
     * 
     * @return boolean 
     */
    
    public function setTripletIdByRandom(){
        
        // Random number between 0 and 1
        $X = mt_rand() / mt_getrandmax(); 
        

        $lastKey = (int) 0;
        foreach($this->distrib as $key => $prob){
            
           // We go throuw the array and X while be negative as soon 
           $X = $X - $prob;
           
           if($X < 0){
               $this->selectedTripletId = $key;
               return true;
           }
        }
        return false;
    }
}

?>
