<?php


class TripletSubset {
    
    /** 
     * This is an array of TripletId. It will be set as the array of 
     * all the possible Triplet to select.
     * @var int[] 
     */
    public $subset = array();
    
    /** @var int */
    public $size = 0;
    
    
    /**
     * Set the subset  
     * @param int $limit
     */
    public function setAllTripletSubset($limit = Null) {
        $M = new MySql();
        
        $sql = 'SELECT * FROM `triplet`
                WHERE id NOT IN
                    (SELECT tripletId AS id FROM (`triplet_blacklist`)) 
                ORDER BY RAND() ';

        if($limit != Null)
            $sql .= ' LIMIT 0,'.$limit;
        
        $M->ExecuteSQL($sql);
        $M->ArrayResults();
            
        foreach($M->aArrayedResults as $sqlTriplet){
            $this->subset[] = $sqlTriplet['id'];
            $this->size = $this->size +1;
        }
    }


    /**
     * Set a subet of Triplet that have only Triplet from the same genre.
     *
     * @param int $genreId
     * @param int $limit Number of Tripletin the subset
     */
    public function setOneGenreTripletSubset($genreId = Null ,$limit = Null){
        $M = new MySql();
        
        $query = 'SELECT tripletId FROM `triplet_onegenre_cache`
                     WHERE tripletId NOT IN
                        (SELECT tripletId FROM (`triplet_blacklist`)) ';

        if($genreId != Null)
            $query .= ' AND (genreId = '.$genreId.')';

        if($limit != Null)
            $query .= '  ORDER BY RAND() LIMIT 0,'.$limit;

        $result = mysql_query($query);
        
        
        while($triplet = mysql_fetch_array($result)){
            $this->subset[] = $triplet['tripletId'];
            $this->size = $this->size +1;
        }
    }

    /**
     * Set a subet of Triplet that have only Triplet from the same genre.
     *
     * @param int $genreId
     * @param int $limit Number of Tripletin the subset
     */
    public function setDifferentGenreTripletSubset($limit = Null){
        $M = new MySql();

        /**
         *  This SQL query select all the TripletId of the Triplet
         *  that have three songs from the same genre. The idea is
         *  to take the complementary or the subset of Triplet that
         *  have at least two genreId that are the same.
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
         *  We select only the lines with two times the same genre
         *  And then we finally take the complementary
         */

        //Take the completary
        $query = '  SELECT DISTINCT id
                    FROM triplet
                    WHERE id NOT
                    IN (';

        // Of a triplet table
        $query .= '     SELECT triplet.id
                        FROM triplet';

        // Joined to the linksonggenre table
        $query .= '         LEFT JOIN  `linksonggenre` AS lsg1 ON ( lsg1.songId =  `triplet`.song1 )
                                LEFT JOIN  `linksonggenre` AS lsg2 ON ( lsg2.songId =  `triplet`.song2 )
                                    LEFT JOIN  `linksonggenre` AS lsg3 ON ( lsg3.songId =  `triplet`.song3 )';
        // With at least two same genre
        $query .= '    WHERE lsg1.genreId = lsg2.genreId
                        OR lsg2.genreId = lsg3.genreId
                        OR lsg3.genreId = lsg1.genreId
                        )';

        if($limit != Null)
            $query .= '  ORDER BY RAND() LIMIT 0,'.$limit;

        $result = mysql_query($query);
        while($triplet = mysql_fetch_array($result)){
            $this->subset[] = $triplet['id'];
            $this->size = $this->size +1;

        }
    }

    /**
     * This methods gives only red Herring Triplet with
     * two times the same song.
     *
     * @param int $limit
     */
    public function setRedHerringSubset($limit = Null){
        $M = new MySql();

         $query = ' SELECT DISTINCT id
                    FROM triplet 
                    WHERE (song1=song2 OR song1=song3 OR song2=song3) 
                    AND id NOT IN
                        (SELECT tripletId AS id FROM (`triplet_blacklist`))';
        if(isset($limit))
             $query .= ' LIMIT = 0,'.$limit;
        
        $query .= ' ORDER BY RAND()';

        $M->ExecuteSql($query);
        $M->ArrayResults();
        
         foreach($M->aArrayedResults as $sqlTriplet){
            $this->subset[] = $sqlTriplet['id'];
            $this->size = $this->size +1;
        }
    }
}

?>
