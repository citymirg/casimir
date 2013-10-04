<?php


include_once 'class.SongDistribution.php';

class SongSelector extends SongDistribution {
   
    /** @var int */
    public $selectedSongId = Null;
 
    
    /** @var Song */
    public $selectedSong = Null;
    
    
    /**
     * Song Table has to be precised.
     * @param type $songTable
     */
    public function __construct($songTable) {
        parent::__construct($songTable);
        
    }    
    
    /**
     * We select the Song using the input distribution
     * 
     * iterate over the list of probablilities, 
     * every time subtracting the prob from 
     * the random number chosen above
     * return when value < 0
     * 
     * @return boolean 
     */
    
    public function setSongIdByRandom(){
        
        // Random number between 0 and 1
        $X = mt_rand() / mt_getrandmax(); 
        

        $lastKey = (int) 0;
        foreach($this->distrib as $key => $prob){
            
           // We go throuw the array and X while be negative as soon 
           $X = $X - $prob;
           
           if($X < 0){
               $this->selectedSongId = $key;
               return true;
           }
        }
        return false;
    }
    
    /**
     * This function finally build the Song object.
     * If supposes that subset, distribution, and selected permId
     * are already defined. 
     */
    public function buildSelectedSongObject(){
         
        $this->selectedSong = new Song($this->selectedSongId);
        
    }
}

?>
