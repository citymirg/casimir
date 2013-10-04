goog.provide('PlayerPanel');

goog.require('lime.fill.LinearGradient');
goog.require('PlayerAvatar');
goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Sprite');
goog.require('lime.Circle');
goog.require('lime.RoundedRect');


PlayerPanel = function(xpos,ypos){
    
    lime.Layer.call(this);
    var self = this;
    
    
    this.xpos = xpos;
    this.ypos = ypos;
/*
 * This is an actual state of a Player table
 * id "71", points "0", sessionId "72", state "moduleDone", totalPoints "0"
 */
    var gradient = new lime.fill.LinearGradient().
        setDirection(0.5,0,0.5,1). // 45' angle 
        addColorStop(0,'#CCCCCC'). // start from red color
        addColorStop(1,'#D0D0D0'); // end with transparent blue
    
    //this.avatars[0] = new PlayerAvatar( 'Herbert', 'onModule', 22).setPosition(100,100);
    /**
     * Update all Avatars
     * @param playerTbl
     */
    this.update = function(){
        
        var playerTbl = game.match.players;

        self.avatars = new Array();
        
        var thispos = 0;
        var playerPos = 1;
       //loop through all players and create avatars
        for(var playerId in playerTbl) {
            
            // for( var k=0; k< 4; k++ ) { //DEBUG
           
            // @todo: STRUCTURE this is the only way positioning works :(
            // in a better world the PlayerAvatar would be a Layer

            // determine f this is the current player
            if (playerTbl[playerId].id == game.client.userAuth.playerid){
                
                thispos = 1;
                self.avatars[thispos] = new PlayerAvatar(playerTbl[playerId])
                    .setPosition(self.xpos + 30,self.ypos);
                
            }else{
                thispos = playerPos++;
                self.avatars[thispos] = new PlayerAvatar(playerTbl[playerId])
                    .setPosition(self.xpos + (playerPos-1)*(237) + 30,self.ypos);
            }
            
            if (thispos < (maxPlayers)){
                self.avatars[thispos].appendChild( new lime.Sprite()
                                    .setSize(2,110)
                                    .setPosition(210,0)
                                    .setFill(gradient)
                                    .setAnchorPoint(0,0)
                                    );
            }

            self.appendChild(self.avatars[thispos]);
        }
    };
    //this.update(playerTbl);
   

}
goog.inherits(PlayerPanel, lime.Layer);

goog.exportSymbol('PlayerPanel', PlayerPanel);