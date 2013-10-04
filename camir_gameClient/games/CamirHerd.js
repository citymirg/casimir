/**
 * This is the main game interface
 * It is built on the Game Frame and contains game logic constants
 * @constructor 
 *
 */
goog.provide('CamirHerd');

// main
goog.require('GameClient');
goog.require('GameFrame');

// scenes
goog.require('CamirHerdMatch');
goog.require('CamirHerdHighScore');
goog.require('CamirHerdMainMenu');
goog.require('CamirHerdFeedback');
goog.require('CamirHerdFacebookComment');

    
var maxPlayers = 4;

var maxScore = 1500;

// time for OOO module
//var totalOOOTime = 60; now server-side

// time for result modules
var totalResultTime = 10; // not server side yet because of very short time interval
var maxDecongestTime = 2;
/*
 * Layout Functions
 */
setLargeFont = function(label){
     label.setFontFamily('Helvetica');
     label.setFontSize(40);
     label.setFontColor('#4f4f4f');
     label.setFontWeight(100);
     
     /*
     *  set letter spacing
     *  @todo: check if this goes with all browsers. can be used for fb translation
     */
     label.getDeepestDomElement().setAttribute('style','letter-spacing:5pt; cursor:default;');
     //goog.style.setStyle(label, 'cursor', 'default');
     return label;
} 

setMediumFont = function(label){
     label.setFontFamily('Helvetica');
     label.setFontColor('#4f4f4f');
     label.setFontSize(25);
     label.setFontWeight(100);
     label.getDeepestDomElement().setAttribute('style','cursor:default;');
     
     return label;
}

setSmallFont = function(label){
     label.setFontFamily('Helvetica');
     label.setFontColor('#4f4f4f');
     label.setFontSize(18);
     label.setFontWeight("bold");
     label.getDeepestDomElement().setAttribute('style','cursor:default;');
     return label;
}
    



/**
 * This is the main game interface
 * It is built on the Game Frame and contains game logic constants
 * @constructor 
 *
 */
CamirHerd = function() {
    /**
     * make this extend the layer
     */
    GameFrame.call(this);

    var self = this;

    /*
     * server Time in seconds from the creation of Unix
     */
    this.serverTime = null;

    /*
     * create  client
     */
    this.client = new GameClient();
    
    /*
     * @todo: make the following scenes:
     * Choose Language
     * Adapt_Permissions
     * HighScore
     * SpendPoints
     *      Avatar Choice
     *      Genre Selection
     */
    this.startMatch = function(matchDetails){
        // This starts a new match
        this.menu.fbuStop();
        this.match = new CamirHerdMatch(this,matchDetails);
        this.director.replaceScene(this.match);
        this.scene = this.match;
    };

    this.showHighscore = function(){
        // This shows the highscore table
        /*
         * @todo: FIXME 
         */
        //this.menu.fbuStop();
        this.highscore = new CamirHerdHighScore(this);
        this.director.replaceScene(this.highscore);
        this.scene = this.match;
    };
    
    this.showMainMenu = function(){
        // show the main menu
        if (this.menu != undefined) this.menu.fbuStop();
        this.menu = new CamirHerdMainMenu(this);
        this.director.replaceScene(this.menu);
        this.scene = this.menu;
        
        this.menu.fbuStart();
        this.menu.plUpdate();

    };

    this.showFeedback = function(){
        // This shows the highscore table
        /*
         * @todo: FIXME
         */
        //this.menu.fbuStop();
        // temporarily disabled until find a solution without fbxml
        if(false && fbUserDetails.loggedIn){
           this.feedback = new CamirHerdFacebookComment(this);
        }else{
           this.feedback = new CamirHerdFeedback(this);
        }
        this.director.replaceScene(this.feedback);
        this.scene = this.feedback;
    };
    
  
    //  this.startMatch(); for starting directly, but will give authentication / facebook problems
    this.showMainMenu();

    
}
goog.inherits(CamirHerd,GameFrame);

//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('CamirHerd', CamirHerd);
goog.exportSymbol('setSmallFont',setSmallFont);
goog.exportSymbol('setMediumFont',setMediumFont);
goog.exportSymbol('setLargeFont',setLargeFont);

