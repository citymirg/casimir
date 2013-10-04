goog.provide('MainMenu');

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
 * @todo: make this a child of Timer
 */
MainMenu = function(parent, game, width, height){
    ScreenMod.call(this);
    
    /*
     * Menu Buttons
     * 
     */
    this.setPosition(-width/2,-height/2);
    
        
    // slight bg image
    this.bgImage = new lime.Sprite()
                    .setPosition(width/2,height/2)
                    .setSize(height/2,height/2)
                    .setOpacity(0.2)
                    .setFill('img/menu_icons/go-home-2.png');
    this.appendChild(this.bgImage);

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
    this.toGame = new GeneralPurposeButton(parent,parent.showGenreSel,'','Start Match')
                .setPosition(width/2, rowHeight/2)
                .setSize(width,rowHeight);
    setLargeFont(this.toGame.text)
                .setSize(width,40);
    this.appendChild(this.toGame);
    
    // Show Highscore
    // @todo: make screen
    this.toHighscore = new GeneralPurposeButton(game,game.showHighscore,'','Highscore')
                .setPosition(width/2,rowHeight*2-rowHeight/2)
                .setSize(width,rowHeight);
    setLargeFont(this.toHighscore.text)
                .setSize(width,40);
    this.appendChild(this.toHighscore);
    
    
    // Show Spend points
    // @todo: make screen
    this.toSpendpoints = new GeneralPurposeButton(parent,parent.showSpendPoints,'','Spend Points')
                .setPosition(width/2,rowHeight*3-rowHeight/2)
                .setSize(width,rowHeight);
    setLargeFont(this.toSpendpoints.text)
                .setSize(width,40);
    this.appendChild(this.toSpendpoints);
    
    // @todo to be implemented
    this.toSpendpoints.disable();
    
    // Show Options
    // @todo: make screen
    this.toOptions = new GeneralPurposeButton(parent,parent.showOptions,'','Options')
                .setPosition(width/2,rowHeight*4-rowHeight/2)
                .setSize(width,rowHeight);
    setLargeFont(this.toOptions.text)
                .setSize(width,40);
    this.appendChild(this.toOptions);
    

    this.fbUpdate = function(){
        /*
         * Check if we got all the details to actually register
         */
        if(this.active){
            if(fbUserDetails.loggedIn){
                if(fbUserDetails.userDetailsFilled && game.client.isRegistered){
                    this.toGame.enable();
                    this.toHighscore.enable();
                    this.toSpendpoints.enable();
                    this.toOptions.enable();
                }else{
                    this.toGame.disable();
                    this.toHighscore.disable();
                    this.toSpendpoints.disable();
                    this.toOptions.disable();
                }
            }
        }
    }

    this.enable = function(){
        this.active = true;
        if(!fbUserDetails.loggedIn){
            this.toGame.enable();
            this.toHighscore.enable();
            this.toSpendpoints.enable();
            this.toOptions.disable();
        }else{
            // see if we can enable the game stuff
            this.fbUpdate(true);
        }
        // here enable stuff that is not user-dependent
    }
    
    this.disable = function(){
        this.active = false;
        this.toHighscore.disable();
        this.toSpendpoints.disable();
        this.toOptions.disable();
        this.toGame.disable();
    }
    
    this.hide();
    
}
goog.inherits(MainMenu, ScreenMod);



//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('MainMenu', MainMenu);