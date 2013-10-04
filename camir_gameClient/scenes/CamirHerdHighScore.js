goog.provide('CamirHerdHighScore');


goog.require('QuickMenu');
goog.require('TutorialCanvas');
goog.require('PlayerHighScore');
goog.require('GeneralPurposeButton');

//get requirements

goog.require('lime.Scene');

goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Sprite');
goog.require('lime.Circle');
goog.require('lime.RoundedRect');
/*
 * This is the Match Scene from the CamirHerd Game
 */

CamirHerdHighScore = function(game) {

    lime.Scene.call(this);

    var self = this;

    var maxPlayers = 9;

    this.mainLayer = new lime.Layer();
    /*
     *  Background.
     *  they overlap so the picture can be stretched 
     */
    this.bgtop = new lime.Sprite().setSize(960,640).
        setFill('./img/skin1/bg_behindsp.jpg').setAnchorPoint(0,0);
        this.mainLayer.appendChild(this.bgtop);
        

    this.titleLbl = new lime.Label();
    setLargeFont(this.titleLbl)
        .setText(_('Highscore'))
        .setAnchorPoint(0.5, 0.5)
        .setPosition(moduleWidth/2,50)
        .setSize(moduleWidth,40);
    this.mainLayer.appendChild(this.titleLbl);

    this.toMainMenu = new GeneralPurposeButton(game,game.showMainMenu,'','')
                .setPosition(moduleWidth/2,50)
                .setSize(moduleWidth/2,40);
    this.mainLayer.appendChild(this.toMainMenu);
    
    /*
     * Quick menu
     */
    this.quickMenu = new QuickMenu(game)
                     .setPosition(moduleWidth-55,50);
    this.mainLayer.appendChild(this.quickMenu);
    


    /*
     * Get Highscore table of the max. ten best players
     */

    var players = game.client.syncConnection.getHighscore(0,maxPlayers-1);
//players[i].name
//players[i].totalPoints
//players[i].rank
    console.log(players);
   // create exit button
    // var checkbutton;
    var frameWidth = 615;
    var frameHeight = 400;

    // create lime layer
    this.mainFrame = new lime.RoundedRect()
        .setAnchorPoint(0.5, 0.5).setOpacity(1)
        .setSize(frameWidth,frameHeight) // centred
        .setFill('#f2f2f2')
        .setPosition(moduleWidth/2,moduleHeight/2)
        .setStroke(1,'#EEEEEE');
    this.mainLayer.appendChild(this.mainFrame);
    this.mainFrame.getDeepestDomElement().setAttribute("style", "-moz-box-shadow: 2px 2px 3px #888; -webkit-box-shadow:2px 2px 3px #888; box-shadow: 2px 2px 3px #888;");


    /*
     * Labels for Points at Bottom
     */
     var playerRow = new Array();
     var rowHeight = 50;

     var playerAppeared = false;
     // cycle through results and plot them into the songs
     for(i=0; i < (players.length-1); i++){
         
        // have we shown the player?
        if (players[i].id == game.client.userAuth.playerid){
            playerAppeared = true;
        }
        
        // if not, they will be on the last position
        if (i < (players.length-2) || playerAppeared){
            playerRow[i] = new PlayerHighScore(players[i]);
        }else{
            playerRow[i] = new PlayerHighScore(players[i+1]);
        }

        // create a label representing the player
        playerRow[i].setPosition(-frameWidth/2,-frameHeight/2 + rowHeight/2 + i*rowHeight);

        if (i < (maxPlayers-1)){
                playerRow[i].appendChild( new lime.Sprite()
                                    .setSize(frameWidth,2)
                                    .setPosition(0,rowHeight/2)
                                    .setFill("#CCCCCC")
                                    .setAnchorPoint(0,0)
                                    );
            }
        this.mainFrame.appendChild(playerRow[i]);
     }

    this.mainLayer.appendChild(this.mainFrame);
 
    /*
     * Add Navigation
     * @todo: classify and make general navigation stuff
     */
        // show main Layer
    this.appendChild(this.mainLayer);

   // game.client.syncConnection.addAchievement();

}
goog.inherits(CamirHerdHighScore,lime.Scene);


//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('CamirHerdHighScore', CamirHerdHighScore);