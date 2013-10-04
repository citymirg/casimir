<?php


/**
 * Description of class
 *
 * @author Daniel Wolff
 */
class AIPlayer extends Player {
//put your code here
    
/**
     * Return AIPlayer when authentification
     * @param integer $AINum number of AI Player
     * @param integer $mId match Id.
     * @param integer $sessId match session Id.
     * 
     * @return Player
     */
     
     public function __construct(Player $player = NULL) {
        
        if (!isset($player)){
            
            // do we have an Idling AI player to reuse?
            $idlingPDetails = AIPlayer::getIdlingDetails();
            if (isset($idlingPDetails)){
                
                /*
                 * Instantiate an idling Player if possible
                 */

                parent::__construct($idlingPDetails);

            }else{
                /*
                 * No, all AI players are active
                * Create user details for new AI player
                */
               $userDetails = array('uniqueExtId' => 'AI_' . date('U'),
                                    //'uiAvatarId' => 0,
                                    'AI' => 1); 
               $moreDetails = self::getRandomDetails();
               
               // backup for guillaume
               if($moreDetails != null){
                    $userDetails = array_merge($userDetails, $moreDetails);
               }else
                   $userDetails['name'] = 'AI Player';
               
               // construct AI player
                parent::__construct($userDetails);
            }
            
         }
         else{
             // copy all fields from the player
             foreach(($allvars = get_object_vars($player)) as $varname => $value){
                 $this->$varname = $player->$varname;
             }
         }
     }
     
     /*
      * This function searches for AIPlayers Idling around
      * @return array details of an idling AI Player
      */
     public static function getIdlingDetails(){
         
            $M = new MySql();

            /*
             * This gets all palyers which are not in the gamesession table
             * @todo: PERFORMANCE this join statement is slow. 
             *                    we could organize AI in a separate table
             */
            $M->ExecuteSQL(
                        'SELECT DISTINCT
                        player.id,player.uniqueExtId, player.APIUserId,player.totalPoints,player.AI
                        FROM  player LEFT JOIN gamesession ON gamesession.playerId = player.id
                        WHERE (gamesession.playerId IS NULL) AND (player.AI = 1)');

            if ($M->iAffected > 0){
                $M->arrayResults();

                $userDetails = array(
                    'APIUserId' => $M->aArrayedResults[0]['APIUserId'],
                    'uniqueExtId' => $M->aArrayedResults[0]['uniqueExtId'],
                    //'AI' => $M->aArrayedResults[0]['AI']
                        );
                return $userDetails;
            }else {
                return NULL;
            }
     }
     
      /*
      * This function gets new User information from an AI generation table
      * @return array details of an idling AI Player
      */
     public static function getRandomDetails(){
         
            $M = new MySql();

            /*
             * 
             */
            $M->ExecuteSQL('SELECT * FROM ai_userdetails 
                            ORDER BY RAND()
                            LIMIT 1');

            if ($M->iAffected > 0){
                $M->arrayResults();

                return $M->aArrayedResults[0];
            }else {
                return NULL;
            }
     }    
}
?>
