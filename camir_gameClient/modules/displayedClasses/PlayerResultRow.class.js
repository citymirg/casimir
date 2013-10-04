goog.provide('PlayerResultRow');

goog.require('LimitedSizeLabel');
goog.require('PlayerScoreIndicator');

goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Sprite');
goog.require('lime.Circle');
goog.require('lime.RoundedRect');

/**
 * Progressbar to show time left value
 * @constructor
 * @extends lime.RoundedRect
 * @param result player description should be similar to PhP class
 *
 * @todo: make this a child of Timer
 */
PlayerResultRow = function(result, player, args) {
    lime.Layer.call(this);
/*
 * This is an actual state of a Player table
 * id "71", points "0", sessionId "72", state "moduleDone", totalPoints "0"
 * 
 */

    // save this for indexing 
    this.result = result;
    this.player = player;
    this.args = args;

    this.picAvatar = new lime.RoundedRect().
        setAnchorPoint(0, 0.5).setOpacity(1)
        .setSize(56,56)
        .setFill('./img/avatars/' + player.uiAvatarFileName)
        .setPosition(20,0)
        .setStroke(1,'#EEEEEE');
    this.appendChild(this.picAvatar);
    this.picAvatar.getDeepestDomElement().setAttribute("style", "-moz-box-shadow: 2px 2px 3px #888; -webkit-box-shadow:2px 2px 3px #888; box-shadow: 2px 2px 3px #888;"); 

        

    this.lblName = new LimitedSizeLabel(15);

    if(!(isEmpty(player.name))){
        this.lblName.setShortText(player.name);
    }else
    {
        this.lblName.setShortText(player.id);
    }
    
    if (player.id == game.client.userAuth.playerid){
            this.lblName.setFontColor('#ff6c00');
        }           
    setMediumFont(this.lblName)
        .setPosition(20+56+20,-20)
        .setAnchorPoint(0, 0.5)
        .setAlign("left");
    this.appendChild(this.lblName);
    
    
    // total points texted label
    this.lblPtsText = new lime.Label();
    setMediumFont(this.lblPtsText)
        .setFontSize(18)
        .setPosition(20+56+20,5)
        .setAnchorPoint(0, 0.5)
        .setAlign("left")
        .setText(player.totalPoints);
        
    this.appendChild(this.lblPtsText);
        
        
    // total points
    this.lblPts = new PlayerScoreIndicator(player.totalPoints)
        .setPosition(20+56+20,25);
    this.appendChild(this.lblPts);
    
    
    
    // points earned
     this.earnedLbl = new lime.Label();
     setMediumFont(this.earnedLbl)
         .setAnchorPoint(0, 0.5)
         .setPosition(500,0)
         .setText('+' + result.earnedPoints);
     this.appendChild(this.earnedLbl);
     
     
     /*+
      * Result speakers: Get the song
      */
     this.resultDetails = new lime.Layer();
     this.resultDetails.setPosition(340,0);
     
     this.appendChild(this.resultDetails);

/*
 * @todo: DEBUG remove LBLSTATE
 */ 
}
goog.inherits(PlayerResultRow, lime.Layer);


goog.exportSymbol('PlayerResultRow', PlayerResultRow);