goog.provide('SpendPoints');

//get requirements

goog.require('ScreenMod');
goog.require('GeneralPurposeButton');
goog.require('GeneralPurposeSelector');
goog.require('TutorialCanvas');


goog.require('lime.Scene');
goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Sprite');
goog.require('lime.Circle');
goog.require('lime.RoundedRect');
/**
 * Main Menu Screen
 * @constructor
 * @extends lime.Layer
 * @param parent 
 * @param game 
 */
SpendPoints = function(parent, game, width, height){
    ScreenMod.call(this);
    
    /*
     * Menu Buttons
     * 
     */
    this.setPosition(-width/2,-height/2);
    
    
    
    /*
     * Label for Lines at Bottom
     */       
     var rowHeight = height/4;
     
    // cycle through results and plot them into the songs
    for(i=1; i < 4; i++){
        this.appendChild( new lime.Sprite()
                            .setSize(width,2)
                            .setPosition(0,i*rowHeight)
                            .setFill("#CCCCCC")
                            .setAnchorPoint(0,0)
                            );
    } 
     
      // Start Match
    this.selectAvatar = new GeneralPurposeButton(parent,parent.showBuyAvatar,'','Change Avatar')
                .setPosition(width/2, rowHeight/2)
                .setSize(width,rowHeight);
    setLargeFont(this.selectAvatar.text)
                .setSize(width,40);
    this.appendChild(this.selectAvatar);
    
    // Show Highscore
    // @todo: make screen
    this.buyGenres = new GeneralPurposeButton(parent,parent.showBuyGenre,'','Buy Genres')
                .setPosition(width/2,rowHeight*2-rowHeight/2)
                .setSize(width,rowHeight);
    setLargeFont(this.buyGenres.text)
                .setSize(width,40);
    this.appendChild(this.buyGenres);
    

    
    // Show Options
    // @todo: make screen
    this.toMainMenu = new GeneralPurposeButton(parent,parent.showMainMenu,'','Main Menu')
                .setPosition(width/2,rowHeight*4-rowHeight/2)
                .setSize(width,rowHeight);
    setLargeFont(this.toMainMenu.text)
                .setSize(width,40);
    this.appendChild(this.toMainMenu);


    this.enable = function(){
        this.active = true;
        this.selectAvatar.enable();
        this.buyGenres.enable();
//        this.toOptions.enable();
        this.toMainMenu.enable();
    }
    
    this.disable = function(){
        this.active = false;
        this.selectAvatar.disable();
        this.buyGenres.disable();
//        this.toOptions.disable();
        this.toMainMenu.disable();
    }
    
    this.hide();
    
}
goog.inherits(SpendPoints, ScreenMod);



//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('SpendPoints', SpendPoints);