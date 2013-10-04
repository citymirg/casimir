<?php

include_once 'class.AddingSongCondition.php';

class SongAdder extends AddingSongCondition {
     
    /** @var Song */
    public $selectedSong;
    
    /**
     * Song Table has to be precised.
     * @param type $songTable
     */
    public function __construct($songTable) {
        parent::__construct($songTable);
        
    }
    
    
    
    /**
     * Add the selected Song to the database if it has already been selected.
     * Return true if the Song is added.
     * 
     * @return boolean
     */
    public function addSelectedSong(){
        if(!isset($this->selectedSong))
            return false;            
        return $this->selectedSong->toDB($this->songTable);
    }
    
    /** 
     * Select a song to add by random based on the whole song database 
     * and pick an other one while it has already been choosen.
     *
     * @todo: limit added songs to those annotated with genre
     * @todo: implement function to add same genre songs and triplets
     */
    public function setSongToAddByRandom($db = null,$type = null){
        $M = new MySql();
        
        $query = ' SELECT id FROM songlibrary
            
                        WHERE songlibrary.id NOT IN
                            (select globalId FROM (`song.'.$type.'`)) 
                        AND songlibrary.id NOT IN
                         (SELECT songId AS id FROM (`songlibrary_blacklist`))';
        if($db != Null)
           $query .= '  AND (songlibrary.musicdb = '.$db.')';

        $query .= '     ORDER BY RAND()
                        LIMIT 0,1';
        
        /* SQL OPTIMISATION NOTICE
         *
         * Alternative SQL: (both 0.23s)
            SELECT id FROM songlibrary 
                     WHERE songlibrary.id NOT IN 
                     (select globalId FROM (`song.'.$this->songTable.'`))
                     ORDER BY RAND()
                     LIMIT 0,1
         
        
        $query = 
                    'SELECT id
                    FROM songlibrary 
                    LEFT JOIN `song.'.$this->songTable.'` 
                    ON (songlibrary.id = `song.'.$this->songTable.'`.globalId)
                    WHERE `song.'.$this->songTable.'`.globalId IS NULL
                    
                    ORDER BY RAND()
                    
                    LIMIT 0,1'; */
            
        $result = mysql_query( $query );
        $row = mysql_fetch_array($result);
        if(mysql_num_rows($result) == 1)
            $song = new Song ($row['id']);
        
        $this->selectedSong = $song;
    }
    
    /**
     * Select song of specific genre to add to the DB
     * music database can be specified. 
     * only fully annotated songs are chosen
     * 
     * @todo: Change the mysql query to put a for loop.
     * 
     * @param int $db music database to get music from
     * @param string $type type of match the song is destined for
     * @return boolean
     */
    public function setSongToAddDBFullAnnotated($db = null,$type = null){
        $M = new MySql();
        $query = ' SELECT id FROM songlibrary
                        JOIN linksonggenre ON
                            (songlibrary.id = linksonggenre.songId)
                        WHERE songlibrary.id NOT IN
                            (select globalId FROM (`song.'.$type.'`)) 
                        AND songlibrary.id NOT IN
                         (SELECT songId AS id FROM (`songlibrary_blacklist`))';
        if($db != Null)
           $query .= '  AND (songlibrary.musicdb = '.$db.')';

        $query .= '     ORDER BY RAND()
                        LIMIT 0,1';
        
        $result = mysql_query( $query );
        $row = mysql_fetch_array($result);
        
        if(mysql_num_rows($result) == 1){

           $song = new Song ($row['id']);
           $this->selectedSong = $song;
           return TRUE;
        }else
            return FALSE;
        
    }
    
    /**
     * Select song of specific genre to add to the DB
     *
     * 
     * @param int $genreId
     * @return boolean
     */
    public function setSongOfOneGenre($genreId){
        $M = new MySql();
        
        $query = ' SELECT id FROM songlibrary 
                        JOIN linksonggenre ON
                            (songlibrary.id = linksonggenre.songId)
                        WHERE songlibrary.id NOT IN 
                            (select globalId FROM (`song.'.$this->songTable.'`))
                        AND songlibrary.id NOT IN
                         (SELECT songId AS id FROM (`songlibrary_blacklist`))
                            AND 
                            linksonggenre.genreId ='.$genreId.'
                    ORDER BY RAND()
                    LIMIT 0,1';
            
        $result = mysql_query( $query );
        $row = mysql_fetch_array($result);
        if(mysql_num_rows($result) == 1)
            $song = new Song ($row['id']);
        
        $this->selectedSong = $song;
    }
    
}
?>
