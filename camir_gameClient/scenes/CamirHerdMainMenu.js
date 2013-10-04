goog.provide('CamirHerdMainMenu');

//get requirements

goog.require('QuickMenu');
goog.require('TutorialCanvas');
goog.require('PlayerHighScore');
goog.require('GeneralPurposeButton');
goog.require('PlayerAvatar');
goog.require('TutorialCanvas');

// Screens -->
goog.require('ScreenMod');
goog.require('MainMenu');
goog.require('GenreSelection');
goog.require('BuyAvatar');
goog.require('BuyGenre');
goog.require('SpendPoints');
goog.require('Permissions');

// Facebook Stuff -->
goog.require('FaceBookLikeButton');
goog.require('FaceBookButton');


goog.require('lime.Scene');

goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Sprite');
goog.require('lime.Circle');
goog.require('lime.RoundedRect');


/*
 * MAIN MENU SCENE
 * First Navigation Test
 */

CamirHerdMainMenu = function(game) {

    lime.Scene.call(this);

    var self = this;

    this.mainLayer = new lime.Layer();

    /*  Background.
     *  they overlap so the picture can be stretched 
     */
    this.bgtop = new lime.Sprite().setSize(960,640).
        setFill('./img/skin1/bg_behindsp.jpg').setAnchorPoint(0,0);
        this.mainLayer.appendChild(this.bgtop);
        

     /*
     *  Title Label
     */
    this.titleLbl = new lime.Label();
    setLargeFont(this.titleLbl)
        .setText('Spot The Odd Song Out')
        .setAnchorPoint(0.5, 0.5)
        .setPosition(moduleWidth/2,50)
        .setSize(moduleWidth,40);
    this.mainLayer.appendChild(this.titleLbl);
    /**
     * @todo: Background design of the frame of the module.
     */

     /*
      * The main Frame. 
      * @todo: make code of title and fram reuseable
      */
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
     * Menu Logic, like scene logic in game js file
     */
    this.showGenreSel = function(){
        this.mainMenu.hide();
        this.genreSelection.show();
    }
   
    this.showMainMenu = function(){
        this.genreSelection.hide();
        this.spendPoints.hide();
        this.buyAvatar.hide();
        this.buyGenre.hide();
        this.options.hide();
        this.mainMenu.show();
    }
   
    this.showSpendPoints = function(){
        this.mainMenu.hide();
        this.spendPoints.show();
    }

    this.showBuyAvatar = function(){
        this.spendPoints.hide();
        this.buyAvatar.show();
    }
    
    this.showBuyGenre = function(){
        this.genreSelection.hide();
        this.spendPoints.hide();
        this.buyGenre.show();
    }
    this.showOptions = function(){
        this.mainMenu.hide();
        this.options.show();
    }


    /*
     * All Menus and selection screens
     */
    this.mainMenu = new MainMenu(this,game,frameWidth,frameHeight);
    this.mainFrame.appendChild(this.mainMenu);

    this.genreSelection = new GenreSelection(this,game,frameWidth,frameHeight);
    this.mainFrame.appendChild(this.genreSelection);

    this.spendPoints = new SpendPoints(this,game,frameWidth,frameHeight);
    this.mainFrame.appendChild(this.spendPoints);

    // todo: get avatars on demand
    this.buyAvatar = new BuyAvatar(this,game,frameWidth,frameHeight);
    this.mainFrame.appendChild(this.buyAvatar);
    
    this.buyGenre = new BuyGenre(this,game,frameWidth,frameHeight);
    this.mainFrame.appendChild(this.buyGenre);
    
    // atm there's only permissions in options
    this.options = new Permissions(this,game,frameWidth,frameHeight);
    this.mainFrame.appendChild(this.options);
    
    /*,
    * Feedback Button
    */
    this.toFeedback = new GeneralPurposeButton(game,game.showFeedback,
                                                'img/menu_icons/mail-reply-sender-2.png',
                                                '     ' + _('Feedback'))
                .setPosition(moduleWidth/2+ 165,580+ 5)
                .setSize(50,50);
    this.toFeedback.text.setAnchorPoint(0,0.5).setPosition(10,-5);
    goog.style.setStyle(this.toFeedback.text.getDeepestDomElement(), {'cursor': 'default'});
    this.mainLayer.appendChild(this.toFeedback);


    /*,
    * Facebook Button
    */
    this.fbButton = new FaceBookButton(game,null,null);
    this.fbButton.setPosition(moduleWidth/2,580);
    this.mainLayer.appendChild(this.fbButton);
    
     // like button
    this.fbLikeButton = new FaceBookLikeButton(70,23,false);
    this.fbLikeButton.setPosition(moduleWidth/2 + 350,33);
    this.mainLayer.appendChild(this.fbLikeButton);
    
     // invite button
    this.fbInviteButton = new GeneralPurposeButton(this,function(){
                    FB.ui({method: 'apprequests',
                      filters: new Array('app_non_users'),
                      message: 'Join me at Spot The Odd Song Out'
                    }, null);
                 }, 'img/fb_addfriends.gif','')
                .setPosition(moduleWidth/2 + 330+ 70,33 + 55)
                .setSize(100,40);
    this.fbInviteButton.hide();           
    this.mainLayer.appendChild(this.fbInviteButton);
    
    
    /*
     * Info canvas
     */
    this.tutCanvas = new TutorialCanvas(this);
    this.mainFrame.appendChild(this.tutCanvas);

    this.appendChild(this.mainLayer);

    // start with main menu
    this.mainMenu.show();
    
    /*
     * Logos
     */
    var pic4 = new lime.Sprite()
        .setFill('./img/' + 'mirg_logo.png')
        .setPosition(15,33 )
        .setScale(0.6)
        .setAnchorPoint(0,0);
    this.mainLayer.appendChild(pic4);
    
    /*
     *
     * update cycle
     * 
     * 
     */
    this.active = false;
     
    /*
     * Loads Player Information if called into
     * Array containing the player data
     */
    this.player = new Array();
    this.hasFaceBookData = false;
    
    this.plUpdate = function(){
        /*
         * @todo: maybe get this asynchronously
         */
        this.player = game.client.syncConnection.getPlayerDetails();
        
        this.hasFaceBookData = false;

        console.log(this.player);
    }
    
     /*
     * update Player State
     */
    if(!fbUserDetails.loggedIn){
        if(!game.client.isRegistered){
            /*
             * @Todo: show loading splash
             */
            game.client.registerPlayer();
        } 
       // this.plUpdate(); done in showMainMenu
    }
   
    this.fbUpdate = function(){

        // try late registering
        if(fbUserDetails.loggedIn && fbUserDetails.userDetailsFilled){
            if(!game.client.isRegistered || !this.hasFaceBookData){
                /*
                 * @Todo: show loading splash
                 */
                game.client.reset();
                game.client.registerPlayer(config);
                this.plUpdate();
                
                this.hasFaceBookData = true;
            }
        }  
        self.fbButton.setText();

        if(fbUserDetails.loggedIn){
            this.fbInviteButton.show();  
        }else{
            this.fbInviteButton.hide()
        }
        /*
         *  Status Avatar
         */
        if(!isEmpty(this.player)){
            
            if(this.playerStatus != undefined){

                // remove old status
                this.mainLayer.removeChild(this.playerStatus);
                delete(this.playerStatus);
            }
            // create new avatar
            this.playerStatus = new PlayerAvatar(this.player);
            this.mainLayer.appendChild(this.playerStatus);
            this.playerStatus.setPosition(moduleWidth/2- 300 ,550);
            this.playerStatus.setScale(0.8);
            
        }
        
        // update children
        self.mainMenu.fbUpdate();
    }
    this.fbuStart = function(){
        if (self.active) return;
        self.active = true;
        
        // 1 second update interval
        lime.scheduleManager.scheduleWithDelay(self.fbUpdate,self,1000);
    }
    this.fbuStop = function(){
        if (!self.active) return;
        self.active = false;
        /*
         * @todo: FIXME this creates an error when called from show-highscore
         */
        lime.scheduleManager.unschedule(self.fbUpdate,self);
    }
    //this.fbuStart(); done in main menu
}
goog.inherits(CamirHerdMainMenu,lime.Scene);


//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('CamirHerdMainMenu', CamirHerdMainMenu);