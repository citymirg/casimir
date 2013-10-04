<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dbMappedObject
 *
 * @author Daniel Wolff
 */
class dbMappedObject {
    //put your code here
    
    /** @var string */
    public $table;

    // @todo: implement
    // public function dbWrite();
    public function dbWrite(){

        $M = new MySql();
        
        // get db_keys from database for saving
        $M->ExecuteSQL('SHOW COLUMNS FROM `'. $this->table .'` ;');
         while ($row = mysql_fetch_row($M->aResult))
             $db_keys[] = $row[0];
        
        // split object details
        $splitDetails = $this->splitObjectDetails();
        $dbArray = $splitDetails['dbRow'];
        
        if (!(array_search('details', $db_keys) === FALSE) && !empty($splitDetails['toDetails'])){ 
            $dbArray['details'] = json_encode($splitDetails['toDetails']);
        }

        // insert or update ?
        /*
         * @todo: make this an insert on duplicate key update statement!
         * 
         */
        if(!isset($this->id)){
            // get details to put into the db
           
            $M->Insert($dbArray, $this->table);

            $this->id = mysql_insert_id($M->sDBLink);  

            // have we inserted a object?
            return ($this->id > 0);
        }else
        {
            $aWhere = array('id' => $this->id);
            $M->Update($this->table, $dbArray, $aWhere);
            
            if ($M->iAffected > 0){
                return true;
            }else
                return false;
        }
    }

    /*
     * this loads an object from a db if the objects id is set
     *
     * @param integer id
     * @returns boolean
     */
    public function dbLoad($id){

        $M = new MySql();
        
        //Look for a data entry with this id
        $aWhere = array('id' => $id);
        $M->Select($this->table, $aWhere);

        // have we found a player ?
        if ($M->iAffected > 0){

            // get all db details into this object
            $this->applyDBDetails($M->aArrayedResults[0]);
            return true;
        }else
            return false;
    }

    
    
    /*
    * Splits the User details into the ones defining the object (table columns exist)
    *  and additional details
    * @todo: STRUCTURE use this function for user and vote objects
    * 
    * @param array $sqlArray The SQL result to convert into this Object Type
    * 
    */
    public function applyDBDetails($sqlArray){
        /*
         * Get Keys from db and save into object
         * @todo: STRUCTURE use the general routine in user for this
         */
        $detail_keys = array_keys($sqlArray);
        $obj_keys = array_keys(get_object_vars($this));
        $tmpDetails = array();

        foreach ($detail_keys as $key){

            /*
             * Look if this is not a detail 
             * @todo: structure, we should have the same detail /
             *        object property structure in the code and db
             */
             if (!(array_search($key, $obj_keys) === FALSE)){

                    //ok, then it goes straight to the Object
                    $this->$key = $sqlArray[$key];

                }else{
                   // copy the value to details if not
                    $tmpDetails[$key] = $sqlArray[$key];
             }
        }
        /*
         * We decode the extra information and merge it into the details
         * part of the variable
         */
        if (!(array_search('details', $obj_keys) === FALSE)){
            if (!empty($this->details)){
                // ok, we got a details column in the database
                $this->details = array_merge(json_decode($this->details, true), $tmpDetails);
            }else{
                $this->details = $tmpDetails;
            }
        }
    }
    
    /*
    * Splits the User details into the ones defining the object (table columns exist)
    *  and additional details
    *  @return: array ['dbRow']: has rows in db  ['toDetails']: goes into details struct
    * 
    * @todo: STRUCTURE use this function for user and vote objects
    * 
    */
    public function splitObjectDetails(){
        $M = new MySql();

         /*
          * Check for the details to correspond to db fields and
         * Get the fields of the database
         */
         $M->ExecuteSQL('SHOW COLUMNS FROM `'. $this->table .'` ;');
         while ($row = mysql_fetch_row($M->aResult))
             $db_keys[] = $row[0];

         $canSaveDetails = array_search('details', $db_keys);
         $tmpDetails = array();
         /*
          * get corresponding keys in object
          */

         $obj_keys = array_keys(get_object_vars($this));
         foreach ($obj_keys as $key){
             
             // don't save admin fields
             if ($key == 'table' || $key == 'log')
                 continue;
             
             if (isset($this->$key) && ($key != 'details')){
                // is this field a db field?
                if (!(array_search($key, $db_keys) === FALSE)){

                    //ok, then it goes straight to the array
                    $dbArray[$key] = $this->$key;

                }else{
                    // this if statement is only for compability
                    if ($canSaveDetails){
                        // copy the value to details if not
                         $tmpDetails[$key] = $this->$key;
                    }
                }
             }
         }

          
        /*
         * get corresponding keys in details
         */
        if (isset($this->details)){
            $detail_keys = array_keys($this->details);
            foreach ($detail_keys as $key){

                // is this field a db field?
                if (!(array_search($key, $db_keys) === FALSE)){

                    //ok, then it goes straight to the array
                    $dbArray[$key] = $this->details[$key];

                }else{
                   // copy the value to details if not
                   //
                   // this if statement is only for compability
                    if ($canSaveDetails){
                        $tmpDetails[$key] = $this->details[$key];
                    }
                }
            }
        }
        return array('dbRow' => $dbArray,
                     'toDetails' => $tmpDetails);
        
    }
}

?>
