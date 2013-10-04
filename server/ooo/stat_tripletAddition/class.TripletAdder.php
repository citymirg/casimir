<?php

include_once 'class.AddingTripletCondition.php';

class TripletAdder extends AddingTripletCondition {
     
    /** @var Triplet */
    public $selectedTriplet;
    
    
    /**
     * Add the selected Triplet to the database if it has already been selected.
     * Return true if the Triplet is added.
     * 
     * @return bool 
     */
    public function addSelectedTriplet(){
        if(!isset($this->selectedTriplet)){
            return false;
        }
        return $this->selectedTriplet->toDB();
        
    }
    
    
    /** 
     * Select a triplet to add by random based on the whole song database 
     * and pick an other one while it has already been choosen.
     * 
     * @return boolean
     */
    public function setTripletToAddByRandom(){
        $M = new MySql();
        
        while(!isset($t) OR $t->isRegistred()){
            $sql = 'SELECT * FROM `song.basicooo`
                    WHERE globalId NOT IN
                        (SELECT songId AS globalId FROM (`songlibrary_blacklist`))
                    ORDER BY RAND() 
                    LIMIT 0,3';
            $M->ExecuteSQL($sql);
            $M->ArrayResults();
        
            $songs = $M->aArrayedResults;
            $aSongId = array($songs[0]['globalId'],$songs[1]['globalId'],$songs[2]['globalId']);
            sort($aSongId);
            
            
            $t = new Triplet();
            $t->aSong[0] = new Song ($aSongId[0]);
            $t->aSong[1] = new Song ($aSongId[1]);
            $t->aSong[2] = new Song ($aSongId[2]);
        }
        
        $this->selectedTriplet = $t;
        return true;
    }
    
    
    
    /**
     * Select a triplet of song from the same genre to add to the DB
     * 
     * The method is nearly the same that the one described in the subset selection
     * part of the tripletSelection. 
     * 
     * If NO genre is precise it will take a genre by rand and add a Triplet of
     * this genre.
     * 
     * This function is recursive in the case when genre is not specified.
     * It will call it self to change the genreId by random.
     * 
     * @param int $genreId
     * @return boolean
     */
    public function setTripletOfOneGenre($genreId = Null){
        $M = new MySql();
        
        
        /**
         * Select a Genre if it has not been choosen by the user. 
         * We use the linksonggenre DB to get the genre so that 
         * the genre genre the most represented have more chance to
         * be choosen.
         */
        $randomGenre = false;
        if(!isset($genreId)){
            $randomGenre = true;
            $M->Select('linksonggenre',array(),'RAND()','0,1');
            $genreId = $M->aArrayedResults[0]['genreId'];            
        }
                    
        $query = '  SELECT globalId
                    
                    FROM `song.basicooo` AS song
                    LEFT JOIN `linksonggenre` AS lsg1
                    
                    ON (lsg1.songId = song.globalId)
                    WHERE 
                       (lsg1.genreId = '.$genreId.')
                    AND globalId NOT IN
                        (SELECT songId AS globalId FROM (`songlibrary_blacklist`))
                    ORDER BY RAND()  
                    LIMIT 0,3';
        
        /*
         * TODO: avoid blacklisted songs 
         *   AND song.globalId NOT IN
         *   (SELECT songId AS globalId FROM (`songlibrary_blacklist`))
         */
        
        $result = mysql_query($query);
        
        // If three songs of the selected genre exist :
        if(mysql_num_rows($result) == 3){
            
            $t = new Triplet();
            $t->aSong = array();
            while($song = mysql_fetch_array($result))
                $t->aSong[] = new Song($song['globalId']);
            
            $this->selectedTriplet = $t;
            return true;
        // If there is not enough songs from this genre :
        } else
            if($randomGenre){
                return $this->setTripletOfOneGenre();
            }
            return false;       
    }
    
     /** 
     * Select a triplet to add by random based on the whole song database 
     * and pick an other one while it has already been choosen.
     * 
     * @return boolean
     */
    public function setTripletRedHerring(){
        $M = new MySql();
         
        // permutation of the triplet
        $perm = array(0,1,1);
        shuffle($perm);
        
        while(!isset($t) OR $t->isRegistred()){
           $sql = 'SELECT * FROM `song.basicooo`
                    WHERE globalId NOT IN
                        (SELECT songId AS globalId FROM (`songlibrary_blacklist`)) 
                    ORDER BY RAND() 
                    LIMIT 0,2';
            $M->ExecuteSQL($sql);
            $M->ArrayResults();
        
            
            $songs = $M->aArrayedResults;
            $aSongId = array($songs[$perm[0]]['globalId'],$songs[$perm[1]]['globalId'],$songs[$perm[2]]['globalId']);
            sort($aSongId);
            
            $t = new Triplet();
            $t->aSong[0] = new Song($aSongId[0]);
            $t->aSong[1] = new Song($aSongId[1]);
            $t->aSong[2] = new Song($aSongId[2]);
        }
        
        $this->selectedTriplet = $t;
        return true;
    }
    
    
    /**
     * Cache a subset of Triplet that have only Triplet from the same genre.
     * @note: CAUTION this usually takes about 12 seconds
     *
     * @param int $genreId
     * @param int $limit Number of Tripletin the subset
     */
    public function updateOneGenreTripletCache(){
        $M = new MySql();

        /*
         * Truncate the genretriplet cache
         */  
        $query = 'truncate table `triplet_onegenre_cache`';
        $M->ExecuteSQL($query);

        
        /**
         *  This SQL query select all the TripletId of the Triplet
         *  that have three songs from the same genre.
         *
         *  We join to the Triplet Table the linksonggenre table three times.
         *  So each row :
         *  ID / song1 / song2 / song3
         *  Become for example six lines if each song have 2 genre :
         *  g. x s. y means genre x of song y
         *  ID / g. 1 s. 1 / g. 1 s. 2 / g. 1 s. 3
         *  ID / g. 1 s. 1 / g. 1 s. 2 / g. 2 s. 3
         *  ID / g. 1 s. 1 / g. 2 s. 2 / g. 1 s. 3
         *  ID / g. 1 s. 1 / g. 2 s. 2 / g. 2 s. 3
         *  ID / g. 2 s. 1 / g. 1 s. 2 / g. 1 s. 3
         *  ID / g. 2 s. 1 / g. 2 s. 2 / g. 2 s. 3
         *
         *  And then we just pick the rows with three same genre
         */

        /**
         * @todo PERF: increase spead of this 5 seconds request
         *       ot takes 2.5-5 seconds to execute the statement below
         *       phps make a temp table / view
         *
         *  @todo do this by maintaining a samegenre_triplet table with
         *      tripletId, genreId
         */
        $query = '  INSERT INTO `triplet_onegenre_cache` (tripletId, genreId)
            
                    SELECT DISTINCT triplet.id, lsg1.genreId
                    FROM triplet

                    JOIN `linksonggenre` AS lsg1
                    ON (lsg1.songId = `triplet`.song1)
                    JOIN `linksonggenre` AS lsg2
                    ON (lsg2.songId = `triplet`.song2)
                    JOIN `linksonggenre` AS lsg3
                    ON (lsg3.songId = `triplet`.song3)
                    WHERE
                        (lsg1.genreId = lsg2.genreId) AND
                        (lsg1.genreId = lsg3.genreId)';

        $result = mysql_query($query);
    }
}
?>
