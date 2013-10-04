<?php


include_once 'class.TripletSelector.php';

class PermSelector extends TripletSelector {
    
    /** @var Triplet */
    public $selectedPerm;
    
    
    /** @var int */
    public $selectedPermTypeId;
    
    
    /**
     * This function finally build the perm object.
     * If supposes that subset, distribution, and selected permId
     * are already defined. 
     */
    public function buildSelectedPermObject(){
        $aSongId = Triplet::aSongIdFromTripletIdAndPermType($this->selectedTripletId,$this->selectedPermTypeId);
        
        $this->selectedPerm = new Triplet($aSongId);
        
    }
    
    /**
     * It returns the songId array that has to be given to the
     * client. It is not needed to build the whole object.
     * 
     * @return int[] 
     */
    public function selectedASong(){
        $aSongId = Triplet::aSongIdFromTripletIdAndPermType($this->selectedTripletId,$this->selectedPermTypeId);
        
        $aSong = array();
        foreach($aSongId as $key => $songId){
            $aSong[$key] = new Song($songId);
        }
        return $aSong;
    }
       
    /**
     * This selects a permutation type from the database by random.
     * It will fix the selectedPermType attribute to the final value. 
     */
    public function setPermTypeByRandom(){
        $this->selectedPerm = new Triplet(array());
        
        $M = new MySql();
        
        $M->Select('permutationtype');
        
        $randomKey = array_rand($M->aArrayedResults);
        $this->selectedPermTypeId = $M->aArrayedResults[$randomKey]['id'];
    }
    
    
}

?>
