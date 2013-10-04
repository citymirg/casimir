<?php

/**
 * In this server the authentification object is an attribute of every class.
 * Every constructor then has to have :
 * 
 *  $this->auth = $auth and $auth as argument to work correctly.
 *
 */
class Auth {
    
    
    public function __construct(Player $player = Null, Match $match = Null) {
        
        if (!isset($player))
            return;
        
        $this->player = $player;
        $this->match = $match;
    }
    
    /**
     * Player authenticated.
     * @var Player 
     */
    public $player;
    
    
    
    /**
     * Match that plays the Player authenticated.
     * @var Match 
     */
    public $match;
}
?>
