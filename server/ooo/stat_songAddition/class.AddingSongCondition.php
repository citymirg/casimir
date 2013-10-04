<?php

/**
 * This class defines at which condition will a new Song be added is 
 * the effective database depending on the game Type.
 * Each method define a new condition.
 *
 * 
 */
class AddingSongCondition {

    /** 
     * The Triplet will be added as soon as condition is set to true.
     * @var bool 
     */
    public $condition = false;
    
    
    /**
     * Name of the table where the song is added. (The DB Name should be song.$songTable)
     * @var string 
     */
    public $songTable = NULL;
    
    
    /**
     * Song Table has to be precised.
     * @param type $songTable
     */
    public function __construct ($songTable) {
        $this->songTable = $songTable;
        
    }
    
    /**
     * Add a new triplet to DB with a probability of 1/n (n is the argument)
     * 
     */
    public function oneOutOf($n){
        $X = rand(1,$n);
        if($X == 1)
            return true;
        return false;
    }
    

    
    /**
     * Add a Song once X percent of the Triplet have been
     * voted N times.
     *  
     */
    public function XpercentVotedNTimes($X,$N){
        
        $M = new MySql();
        $query = 'SELECT COUNT(*) FROM `song.'.$this->songTable.'` WHERE 1';
        $total = mysql_result(mysql_query($query),0);
        
        $query = 'SELECT COUNT(*) FROM `song.'.$this->songTable.'` WHERE counter >= '.$N;
        $nbFulled = mysql_result(mysql_query($query),0);
        
        $rate = $nbFulled/$total;
        if($rate >= $X/100)
           return true;
        return false;
    }
}
?>
