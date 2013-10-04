<?php


class SongSubset {
    
    /** 
     * This is an array of SongId. It will be set as the array of 
     * all the possible Songs to select.
     * @var int[] 
     */
    public $subset = array();
    
    /** @var int */
    public $size = 0;
    
    
    /** @var string songTable */
    public $songTable = Null;
    
    /**
     * Song Table has to be precised.
     * @param type $songTable
     */
    public function __construct ($songTable) {
        $this->songTable = $songTable;
    }
    
    /**
     * Set the subset  
     * @param int $limit
     */
    public function setAllSongSubset($limit = Null) {
        $M = new MySql();
        if($limit == Null)
            $M->select('song.'.$this->songTable);
        else
            $M->Select('song.'.$this->songTable,array(),'RAND()','0,'.$limit);
        
        
        foreach($M->aArrayedResults as $sqlSong){
            $this->subset[] = $sqlSong['globalId'];
            $this->size = $this->size +1;
        }
    }
    
        
    /**
     * Set the subset  
     * @param int $limit
     */
    public function setSqlRestrictedSubset($whereClause = '1',$orderClause = 'RAND()',$limit = null) {
        $M = new MySql();
        
        $query =
                'SELECT * FROM '. $this->songTable .
                'WHERE ' .$whereClause .
                'ORDER BY ' . $orderClause ;
        
        if(! $limit != null)
        $query .=
                'LIMIT 0,' . $limit;
        
        $M->ExecuteSQL($query);
        
        
        if(! isempty($M->aArrayedResults)){
            foreach($M->aArrayedResults as $sqlSong){
                $this->subset[] = $sqlSong['globalId'];
                $this->size = $this->size +1;
            }
        }
        else{
           $this->setAllSongSubset();
        }
    }


}

?>
