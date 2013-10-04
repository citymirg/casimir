<?php




/*
 * Include the needed classes
 */
include_once 'class.Song.server.php';
include_once 'class.User.server.php';

/**
 * Triplet data models on server side
 *
 */
class Triplet {
    /** @var int */
    public $id = Null;  
    
    /** 
     * Array of song that is sent to the client.
     * IT IS NOT SORTED BY ID AS IN THE DB.
     * @var Song[] 
     */
    public $aSong = array();    
    
    /** @var int */
    public $permId = Null;   
    
    
    //****************************
    // USEFUL METHODS FOR GAME 
    //****************************
    
    /**
     * Gives the most probable voted song Id considering this Triplet.
     * It can't has a probabilty as argument and take user details to
     * specify who would answer that way.
     * 
     * This method lead to the use of AI.
     * 
     * 
     * 
     * @param boolean $isStochastic 
     * @param float $p It is a Noise rate, if $p gets to 0 the probabilty
     * for the song to be choosen the voted rate of the song in that Triplet
     * If it gets to $p=1 each song has a probabilty of 1/3 to be taken.
     * 
     * @param array $userDetails 
     */
    
    public function mostProbableSongChosen($isStochastic = false,$p = 0.,$userDetails = Null){

        
        $M = new MySql();
        $M->Select('triplet',array('id' => $this->id));
        $total = $M->aArrayedResults[0]['counter'];
        
        $query = '
            SELECT v.songChosenId, COUNT(v.id) 
            FROM vote AS v
            LEFT JOIN `permutation` AS p
            ON (v.permId = p.id)';
        //THESE LINES TAKES CARE OF THE userDetails
        if(isset($userDetails)){
            $query .= '
                LEFT JOIN `session` AS sess
                ON (v.sessionId = sess.id) 
                LEFT JOIN user
                ON (sess.userId = user.id) 

                WHERE ';
            foreach($userDetails as $key => $val){
                $query .= '
                    (user.'.$key.'= "'.$val.'") AND';
            }

            $query .= '
                p.tripletId = '.$this->id.'
                GROUP BY `songChosenId` '; 
        } else
        
        $query .= '
            
            WHERE p.tripletId = '.$this->id.'
            GROUP BY `songChosenId` ';
        
        if(!$isStochastic){
            //RETURN THE SONG WITH THE MAXIMUM OF VOTES
            $query .= '
                 ORDER BY COUNT(v.id) DESC
                 LIMIT 0,1';
            $result = mysql_query($query) or die(mysql_error());
            $row = mysql_fetch_array($result);
            return $row['songChosenId'];
        } else {
            $rate = array();
            $distrib = array();
            
            //CALC THE RATE OF VOTED TIMES OF EACH SONG OF THE TRIPLET
            $result = mysql_query($query) or die(mysql_error());
            while($row = mysql_fetch_array($result))
                $rate[$row[0]] = $row[1]/$total; //rate(id) = nbVote / totalVote
                
            //CALC A DISTRIBUTION OF PROBABILITIES
            foreach($rate as $id => $rate)
                $distrib[$id] = 1/3*$p + $rate*(1-$p);
            
            //TAKE A RANDOM NUMBER 0 < $X < 1 to pick the song
            $X = rand()/getrandmax();
            
            foreach($distrib as $id => $prob){
                $X = $X - $prob;
                if($X<=0)
                    return $id;
            }
            
            
        }
        
        

        
            
    }
    
    
    //****************************
    // OBJECT MANIPULATION METHODS 
    //****************************
    
    /**
     * Build the triplet object from array of Songs ID
     * as it would be given to the client.
     * It will add the Triplet and the Permutation to 
     * the DB if it doesn't exist.
     * 
     * 
     * @param int[] $aSongId
     */
    public function __construct(array $aSongId = Null){
        if(sizeof($aSongId) != 3){
            $this->id = Null;
        } else {
            $this->aSong[0] = new Song ($aSongId[0]);
            $this->aSong[1] = new Song ($aSongId[1]);
            $this->aSong[2] = new Song ($aSongId[2]);


            $this->setId();
            $this->toDB();
            
            
            $this->setPermId();
            $this->permToDB();
        }
    }
      
    
    /**
     * This returns the array of song Id as given to the client
     * which is also the main attribut of the class and the argument
     * of the constructor2. 
     * @param int
     * @param int
     * 
     * @return int[]
     */
    static public function aSongIdFromTripletIdAndPermType($tripletId,$permType){
        $M = new MySql();
        $M->Select('triplet',array('id' => $tripletId));
        $tripl = $M->aArrayedResults[0];
        
        $aSongSortedId = array( 1 => $tripl['song1'],
                                2 => $tripl['song2'],
                                3 => $tripl['song3']);
        
        
        $M->Select('permutationtype',array('id' => $permType));
        $pType = $M->aArrayedResults[0];
        
        foreach($aSongSortedId as $key => $songId){
            $aSongId[$pType['positionOfSong'.$key] -1] = $songId;
        }
        
        ksort($aSongId);
        
        return $aSongId;
                
    }
    
    /**
     * The method gives the array of song Id that would correpond
     * to the mysql colomns to get the to the triplet table. The
     * song are sorted by id from low to high.
     * 
     * @return int[]
     */
    
    public function aSongIdSortedWithKeys(){
        if(empty($this->aSong))
            return array();
        
        $a = array($this->aSong[0]->id, 
                   $this->aSong[1]->id, 
                   $this->aSong[2]->id) ;
        
        sort($a);
        $aWithKeys = array();
        foreach($a as $key => $val){
            $aWithKeys['song'.($key+1)] = $val;
        }
        
        return $aWithKeys;
    }
    
    
    /**
     * This function gives the permutationtype of the Triplet object
     * as soon as the aSong attribute is defined. aSong is the array
     * of songs that would be given to the client.
     * 
     * @return int 
     */
    public function permType(){
        $songId1 = (integer) $this->aSong[0]->id;
        $songId2 = (integer) $this->aSong[1]->id;
        $songId3 = (integer) $this->aSong[2]->id;
        
        $aNormal = array(1 => $songId1,2 => $songId2,3 => $songId3);
        
        $aSorted = array(1 => $songId1,2 => $songId2,3 => $songId3);
        sort($aSorted);

        /*
         * Because this lipping odes not work with 2 identical numbers
          * as in a red herring triplet, a string is attached and then not saved
         */
        $aPositions = array();
        foreach($aSorted as $key => $value){
            
            // remember the actual position of the sorted triplets songs
            $aPositions['positionOfSong'.($key +1)] = array_search($value,$aNormal);
            
            // remove the song/position just processed
            $aNormal[$aPositions['positionOfSong'.($key +1)]] = -1;
        }
        
        $M = new MySql();
        $M->select('permutationtype',$aPositions);
        $permId = $M->aArrayedResults[0]['id'];

        return $permId;
    }

    /**
     * Array of song id with the keys corresponding to the permutation DB colomns
     * @return array $aSongIdGiven 
     */
    public function aSongIdWithKeys(){
        $a = array();
        foreach($this->aSong as $key => $song){
            $a['songGivenId'.($key+1)] = $song->id;
        }
       
        return $a;
    }
    
    
    
    
    //************************************
    // TRIPLET: IS REGISTRED IM DB METHODS
    //************************************

    /**
     * isRegistred function that set ID if it exists
     * @return boolean 
     */
    public function setId() {
        $id = $this->returnId();
        if($id > 0){
            $this->id = $this->returnId();
            return true;
            
        } else
            return false;
    }

    /**
     * isRegistred function
     * @return boolean 
     */ 
    public function isRegistred() {
        return ($this->returnId() > 0);
    }
    
    
    /**
     * Check if the triplet is registred in the database.
     * @return boolean¦int 
     */
    
    public function returnId(){
        
        if(empty($this->aSong))
            return false;
        
        if($this->id  == Null){
            $M = new MySql();
        
            $aSongIdSortedWithKeys = $this->aSongIdSortedWithKeys();
            $M->select('triplet',$aSongIdSortedWithKeys);

            $tripletSqlId = $M->aArrayedResults[0]['id'];
            
            
            if(empty($tripletSqlId)){
                return false;
            } else {
                return $tripletSqlId;
            }
        }   else {
            $M = new MySQL();
        
            $M->select('triplet',array('id' => $this->id));
            $res = $M->aArrayedResults;
        
        
            return !empty($res) ;
            
        }
        
        
    }
 
    

    /**
     * isRegistred function that set ID if it exists
     * @return boolean 
     */
    
    public function setPermId() {
        $id = $this->returnPermId();
        
        if($id > 0){
            $this->permId = $this->returnPermId();
            return true;
            
        } else
            return false;
    }

    /**
     * isRegistred function
     * @return boolean 
     */ 
    public function isPermRegistred() {
        return ($this->returnPermId() > 0);
    }
    
    /**
     * isRegistred function, returns le Id if does
     * @return boolean¦int
     */     
    public function returnPermId(){
        
        if(empty($this->aSong))
            return false;
        
        if($this->permId  == Null){
            $M = new MySql();
        
            $aSongIdWithKeys = $this->aSongIdWithKeys();
            $M->select('permutation',$aSongIdWithKeys);


            $permSqlId = $M->aArrayedResults[0]['id'];
            
            if(empty($permSqlId)){
                return false;
            } else {
                return $permSqlId;
            }
        }   else {
            $M = new MySQL();
        
            $M->select('permutation',array('id' => $this->permId));
            $res = $M->aArrayedResults;
        
        
            return !empty($res) ;
            
        }
        
        
    }
    //***********************
    // INSERT INTO DB METHODS
    //***********************
    
    
    /**
     * Insert the triplet into the DB if not registred
     * @return boolean 
     */
    public function toDB(){
        
        if($this->isRegistred() OR empty($this->aSong)){
            return false;           
        } else {
            $M = new MySQL();
            
            $a = $this->aSongIdSortedWithKeys();
            $a['counter'] = 0;

            $M->insert($a,'triplet'); 
            $this->id = mysql_insert_id();
            if(!empty($M->sLastError))
                return false;

            return !($this->id  == Null) ;
        }
    }
    
    
    
    /**
     * Insert the permutation into the DB if not registred
     * @return boolean 
     */
    public function permToDB(){
        if($this->isPermRegistred()){
            return false;           
        } else {
            $M = new MySQL();
            
            $permData = array();
            $permData['tripletId'] = $this->id;
            $permData['counter'] = 0;
            $permData['permType'] = $this->permType();
            
            foreach($this->aSongIdWithKeys() as $key => $val){
                $permData[$key] = $val;
            }
              
            /*
             * @todo STRUCTURE
             * this inserts a int as permtype, the normal routine returns a string 
             */
            $M->Insert($permData,'permutation');
            $this->permId = mysql_insert_id();

            return !empty($this->permId) ;
        }
    }  
    
    
    //***********************************
    // COUNTER INCREMENTATION AFTER VOTES
    //***********************************
    
    /**
     * @return boolean 
     */
    public function incrTripletCounter(){
        $M = new MySql();
        $M->ExecuteSQL('UPDATE triplet SET counter = counter+1 WHERE id ='.$this->id);
        
        return $M->iAffected == 1;
    }
    
    /**
     * @return boolean 
     */
    public function incrPermCounter(){
        $M = new MySql();
        $M->ExecuteSQL('UPDATE permutation SET counter = counter+1 WHERE id ='.$this->permId);
        
        return $M->iAffected == 1;
    }
    
  
}

?>
