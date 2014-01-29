<?php


/**
 * Songs data model on server side.
 *
 */
class Song extends dbMappedObject{
    /** @var int */
    public $id = Null; 
   
    /** @var string */
    //public $urlType = Null;
    
    /** @var string */
    public $url = Null;

    /** @var array */
    public $details = array();
    
    /**
     * Construct the object with the ID and the Database
     * @param integer $sql_id Database ID of the song
     */
    public function __construct($sql_id){
        
        $M = new MySql();

        $sql = 'SELECT '.
                'id, url, musicDb, '.   // essential!
                'title, artist, tempo, buySongUrl' .       // go to details
                ' FROM songlibrary WHERE ' .
                'id ='. $sql_id;
        // get song from library
        $M->executeSql($sql);
        $M->arrayResults();
        $sql_song = $M->aArrayedResults;

        //
        // get song ID and other object details
        // 
        //$this->id = $sql_song[0]['id'];
        $this->applyDBDetails($sql_song[0]);

        /*
        * get relative url path from db
        */

        // determine database of song
        $M->select('musicdb', array('id' => $sql_song[0]['musicDb']) );
        
        
        if (strcmp($sql_song[0]['musicDb'],'1') == 0 OR $sql_song[0]['musicDb'] == '4'){

            // magnatagatune OR Ring Tone for tap tempo study
            $prefix = $M->aArrayedResults[0]['relUrlPath'];
            $this->url = $prefix . $sql_song[0]['url'];
        }else{

            // million song dataset

            $prefix = $M->aArrayedResults[0]['relUrlPath'];
            $sloadapi = $M->aArrayedResults[0]['auth'];
            $sloadid = $sql_song[0]['url'];
            $this->url = $prefix . '?trackid=' . $sloadid . '&oauth_consumer_key=' . $sloadapi;
            }
    }
    
    
    
        
    /**
     * Build a triplet to sent back to the client
     * 
     * @return array
     */
    static public function randomSongIdArray($songTable){
               
        
        //Choose randomly three different songs row number
        $M = new MySql();
        $M->select('song.'.$songTable);
        
        $nn = $M->iRecords;
        $id1 = rand(1,$nn-1);
        $id2 = rand(1,$nn-1);
        $id3 = rand(1,$nn-1);
        
        while($id1 == $id2 OR $id1 == $id3 OR $id2 == $id3){
        $id1 = rand(1,$nn-1);
        $id2 = rand(1,$nn-1);
        $id3 = rand(1,$nn-1);
        }
        
        $aSongId = array($id1,$id2,$id3);
        
        return $aSongId;
            
    }
    
    
    
   /**
    * Return the ID of the song is the game depending DB from the one given
    * in the song master database 
    *  
    */
    public function isRegistred($songTable){
        $M = new MySql();
        $M->Select('song.'.$songTable,array('globalId' => $this->id));
        
        return ($M->iRecords >= 1);
    }
    
    /**
     * Put the song into the song game depending database.
     * @todo: make type checks if object properties are
     *        reflected by database structure
     * 
     * @param string $songTable
     * @return boolean
     * 
     */
    public function toDB($songTable){
        
        if($this->isRegistred($songTable) || empty($this->id)){
            return false;
        } else {
            
            // save temporary copy of song, and blacklist if it fails
//            if (!$this->saveAudio()){
//                return false;
//            } 
           
            $M = new MySQL();
            $a = array('globalId' => $this->id);
            $M->insert($a,'song.'.$songTable);
            return ($M->iAffected == 1) ;
        }
    }
    
    /**
     * Blacklist this song as not available
     * 
     * @return boolean
     * 
     */
    public function blacklist(){
        
        $M = new MySQL();
        $a = array('songId' => $this->id);
        $M->Insert($a,'songlibrary_blacklist');

        return ($M->iAffected >= 1) ;
    }
    
    /**
     * @param string $songTable
     * @return boolean 
     */
    public function incrSongCounter($voteType){
        $M = new MySql();
        $M->ExecuteSQL('UPDATE `song.'.$voteType.'` SET counter = counter+1 WHERE globalId ='.$this->id);
        
        return ($M->iAffected == 1);
    }

    /**
     * Saves a copy of the played song in a temporary folder
     * @return boolean
     */
    public function saveAudio(){

        $file = RUNTIME_DATA_PATH . 'audio/' . $this->id . '.mp3';

        if(file_exists($file)) return true;

        // get sound
        $image = file_get_contents($this->url);

        //@todo: chekc usual strlen of downloaded files
        if ($image == false || strlen($image) < 10000){
            $this->blacklist();
            return false;

        }else{
            
            // save file
            $res = file_put_contents($file, $image);
        }
        return ($res > 0);
    }
}

?>
