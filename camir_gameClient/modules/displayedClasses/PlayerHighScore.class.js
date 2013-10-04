goog.provide('PlayerHighScore');

goog.require('PlayerScoreIndicator');
goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Sprite');
goog.require('lime.Circle');
goog.require('lime.RoundedRect');

/**
 * Row in High Score depicting a Player
 * @constructor
 * @extends lime.RoundedRect
 * @param player description should be similar to PhP class
 *
 * @todo: make this a child of Timer
 */
PlayerHighScore = function(player) {
    lime.Layer.call(this);
/*
 */
    /*
     * Avatar
     */
    this.picAvatar = new lime.RoundedRect().
        setAnchorPoint(0, 0.5).setOpacity(1)
        .setSize(40,40)
        .setFill('./img/avatars/' + player.uiAvatarFileName)
        .setPosition(20+40,0)
        .setStroke(1,'#EEEEEE');
    this.appendChild(this.picAvatar);
    this.picAvatar.getDeepestDomElement().setAttribute("style", "-moz-box-shadow: 2px 2px 3px #888; -webkit-box-shadow:2px 2px 3px #888; box-shadow: 2px 2px 3px #888;");


    /*
     * Rank label
     */
    this.lblRank = new lime.Label();
    setLargeFont(this.lblRank)
        .setPosition(20,0)
        .setAnchorPoint(0, 0.5)
        .setAlign("left")
        .setText(player.rank);
    this.appendChild(this.lblRank);

    

    /*
     *Name label
     */
    this.lblName = new lime.Label();
    setMediumFont(this.lblName)
        .setPosition(20+80+20,-5)
        .setAnchorPoint(0, 0.5)
        .setAlign("left");

    // set the name label depending on the player 
    if (player.id == game.client.userAuth.playerid){
            this.lblName.setFontColor('#ff6c00');
        }
    if(!(isEmpty(player.name))){
        this.lblName.setText(player.name);
    }else
    {
        this.lblName.setText(player.id);
    }

    this.appendChild(this.lblName);


     /*
     * total points
     */
    this.lblPts = new PlayerScoreIndicator(player.totalPoints)
        .setPosition(20+80+20,15);
    this.appendChild(this.lblPts);

    // points earned
     this.pointsLbl = new lime.Label();
     setMediumFont(this.pointsLbl)
         .setAnchorPoint(0, 0.5)
         .setPosition(550,0)
         .setText(player.totalPoints);
     this.appendChild(this.pointsLbl);


/*
 * @todo: DEBUG remove LBLSTATE
 */
}
goog.inherits(PlayerHighScore, lime.Layer);



goog.exportSymbol('PlayerHighScore', PlayerHighScore);