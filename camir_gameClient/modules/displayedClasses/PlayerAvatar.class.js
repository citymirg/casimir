goog.provide('PlayerAvatar');

goog.require('PlayerScoreIndicator');
goog.require('LimitedSizeLabel');

goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Sprite');
goog.require('lime.Circle');
/**
 * Progressbar to show time left value
 * @constructor
 * @extends lime.RoundedRect
 * @param player player description should be similar to PhP class
 *
 * @todo: make this a child of Timer
 */
PlayerAvatar = function(player) {
    lime.Layer.call(this);
/*
 * This is an actual state of a Player table
 * id "71", points "0", sessionId "72", state "moduleDone", totalPoints "0"
 * 
 */
    this.picAvatar = new lime.RoundedRect().
        setAnchorPoint(0, 0).setOpacity(1)
        .setSize(71,71)
        .setFill('./img/avatars/' + player.uiAvatarFileName)
        .setPosition(0,0)
        .setStroke(1,'#EEEEEE');
    this.appendChild(this.picAvatar);
    this.picAvatar.getDeepestDomElement().setAttribute("style", "-moz-box-shadow: 2px 2px 3px #888; -webkit-box-shadow:2px 2px 3px #888; box-shadow: 2px 2px 3px #888;"); 

        

    this.lblName = new LimitedSizeLabel(9);
    
    if(!(isEmpty(player.name))){
        this.lblName.setShortText(player.name);
    }else
    {
        this.lblName.setShortText(player.id);
    }
        
    setMediumFont(this.lblName)
        .setAnchorPoint(0, 0.5)
        .setPosition(80,22)
        .setAlign("left");
    
    if (player.id == game.client.userAuth.playerid){
            this.lblName.setFontColor('#ff6c00');
        }           
    this.appendChild(this.lblName);
   

    if (player.state == "moduleDone"  || player.state == "done"){
        this.picDone = new lime.Sprite().
            setAnchorPoint(0, 0).setOpacity(1)
            .setSize(71,71)
            .setFill('./img/skin1/ready1.png')
            .setPosition(0,0)
            .setOpacity(0.9);
        this.appendChild(this.picDone);      
    }

    // total points texted label
    this.lblPtsText = new lime.Label();
    setMediumFont(this.lblPtsText)
        .setFontSize(18)
        .setPosition(80,47)
        .setAnchorPoint(0, 0.5)
        .setAlign("left")
        .setText(player.totalPoints);

    this.appendChild(this.lblPtsText);

    // total pints indicator
    this.lblPts = new PlayerScoreIndicator(player.totalPoints)
        .setPosition(0,90);
    this.appendChild(this.lblPts);


/*
 * @todo: DEBUG remove LBLSTATE
 */ 
/*
    this.lblState = new lime.Label().
        setFontFamily('Trebuchet MS').setFontColor('#4f4f4f').setFontSize(10)
        .setFontWeight(100)
        .setText(player.state)
        .setAnchorPoint(0, 0);
   // this.lblName.setPosition(-200, 50);
    this.lblState.setPosition(0,80);
    this.appendChild(this.lblState); */
    
}
goog.inherits(PlayerAvatar, lime.Layer);

/**
 * Update all Avatars
 * @param {number} value Current progress value.
 */
PlayerAvatar.prototype.updatePlayer = function(playerTbl) {
   // this.progress = value;
};


goog.exportSymbol('PlayerAvatar', PlayerAvatar);