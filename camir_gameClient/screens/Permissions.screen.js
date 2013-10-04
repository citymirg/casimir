goog.provide('Permissions');

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
 * Permissions Screen
 * @constructor
 * @extends lime.Layer
 * @param parent 
 * @param game 
 * @todo: make this a child of Timer
 */
Permissions = function(parent, game, width, height){
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
                    
    // Advanced details
    /*
     * @todo: split earlier
     */
    this.allowAdvancedData1 = function(){
         fbUserDetails.requestPermissions(config.permissions_adv.split(","), function(){
             window.top.location.href = config.CLIENT_PATH;
         });
    };
    
    this.removeAdvancedData1 = function(){
         fbUserDetails.removePermissions(config.permissions_adv.split(","), function(){
             window.top.location.href = config.CLIENT_PATH;
         });
    };
    
    this.advancedData1 = new GeneralPurposeButton(this,this.allowAdvancedData1,'',_('Post Results on Facebook'))
                    .setPosition(width/2, rowHeight/2)
                    .setSize(width,rowHeight);
    setLargeFont(this.advancedData1.text)
                .setSize(width,40);
                    
    this.advancedData1.disable();
    this.appendChild(this.advancedData1);

    
//    // Show Highscore
//    // @todo: make screen
//    this.toHighscore = new GeneralPurposeButton(game,game.showHighscore,'','Highscore')
//                .setPosition(width/2,rowHeight*2-rowHeight/2)
//                .setSize(width,rowHeight);
//    setLargeFont(this.toHighscore.text)
//                .setSize(width,40);
//    this.appendChild(this.toHighscore);
//    
//   
//    
    this.toMainMenu = new GeneralPurposeButton(parent,parent.showMainMenu,'','Main Menu')
                .setPosition(width/2,rowHeight*4-rowHeight/2)
                .setSize(width,rowHeight);
    setLargeFont(this.toMainMenu.text)
                .setSize(width,40);
    this.appendChild(this.toMainMenu);



    this.reload = function(){
        /*
         * Check if we got all the details to actually register
         */
        if(this.active){
            if(fbUserDetails.loggedIn){
                if(fbUserDetails.userDetailsFilled && game.client.isRegistered){
                    
                    // update facebook options
                    if (!fbUserDetails.validPermissions(config.permissions_adv.split(","))){
                        this.advancedData1.onClickFunction = this.allowAdvancedData1;
                        this.advancedData1.text.setText(_('Post Results on FB'));
                    }else{
                        this.advancedData1.onClickFunction = this.removeAdvancedData1;
                        this.advancedData1.text.setText(_('Stop Posting Results'));
                    }
                    this.advancedData1.enable();
                    
                }else{
                    this.advancedData1.disable();
                }
            }
        }
    }

    this.enable = function(){
        this.active = true;
        if(!fbUserDetails.loggedIn){
            this.advancedData1.disable();
            this.toMainMenu.enable();
        }else{
            // see if we can enable the game stuff
            this.reload.call(this);
            this.toMainMenu.enable();
        }
        // here enable stuff that is not user-dependent
    }
    
    this.disable = function(){
        this.active = false;
        this.advancedData1.disable();
        this.toMainMenu.disable();
    }
    
    this.hide();
    
}
goog.inherits(Permissions, ScreenMod);



//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('Permissions', Permissions);