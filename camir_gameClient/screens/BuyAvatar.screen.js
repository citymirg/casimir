goog.provide('BuyAvatar');

//get requirements
goog.require('ScreenMod');
goog.require('GeneralPurposeButton');
goog.require('PlayerAvatar');
goog.require('TutorialCanvas');
goog.require('SelectionAvatar');


// Facebook Stuff -->
goog.require('FaceBookLikeButton');
goog.require('FaceBookButton');


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
BuyAvatar = function(parent, game, width, height){
    ScreenMod.call(this);
    
    var self = this;
    /*
     * layout constants
     */
    this.setPosition(-width/2,-height/2);
    
    var rowNum = 2;
    var rowHeight = 130;
    var colWidth = 130;
    
    this.isInitialised = false;

    this.reload = function(){

        if (this.isInitialised){
            this.removeChild(this.scroll);
            this.isInitialised = false;
        }
        /*
         * Get this players Avatar data
         */
        this.avatarTable = game.client.syncConnection.getAvatarTable();

        // main avatar display
        this.scroll = new lime.ui.Scroller()
            .setSize(width-60,(height - height/4))
            .setPosition(30,10)
            .setAnchorPoint(0,0)
            .setDirection(lime.ui.Scroller.Direction.HORIZONTAL);

        this.scrollPosition = 0;

        /*
         * We fill the scrollbars with all the avatars 
         * maybe use ajax to load the step by step? at least not all at the start
         * 
         */
        this.avatars = new Array();
        var row;
        var column;
        var avPic;
        // loop over all avatars
        for(var i=0; i < this.avatarTable.length; i++){

            column = Math.floor(i / rowNum);
            row = i % rowNum;

           // create avatar pic
           this.avatars[i] = new SelectionAvatar(this,this.avatarTable[i],function(i){
               // test if the avatar is already bought
               if (this.avatarTable[i]['playerId'] != null){
                    this.buyBtn.text.setText(_('Select Owned'));
               }else{
                    this.buyBtn.text.setText(_('Buy') + ': ' + this.avatarTable[i]['costPoints'] + ' ' + _('Points'));
               }
            },i)
            .setPosition(colWidth + column*colWidth,row*rowHeight+rowHeight/2)
            .setSize(100,100);

           // Append to scrollbar
           this.scroll.appendChild(this.avatars[i]);
        }
        this.appendChild(this.scroll);
        
        this.isInitialised = true;
    }
    
    
    this.buyAvatar = function(){
        var result = game.client.syncConnection.buyAvatar(SelectionAvatar.prototype.getSelected().avatar.uiAvatarId);
        if (result){
            // cool it worked, go back to main menu then
            parent.tutCanvas.popBubble(_('Bought avatar') ,
                      200,60,0,-150,'s', 0, 3000);
            console.log('Bought Avatar ' + SelectionAvatar.prototype.getSelected().avatar.uiAvatarId);
            
            parent.plUpdate();
            lime.scheduleManager.callAfter( function(){
                    parent.showMainMenu();
                },this,3300);
            
        }else{
            parent.tutCanvas.popBubble(_('Not enough points'),
                    200,60,0,-150,'s', 0, 3000);
            console.log(SelectionAvatar.prototype.getSelected());

        }
    }
    
    /*
     * scroll buttons
     * rewinds the displayed value by one
    */ 
    this.goBack = function(){
        if (!this.active)
            return;
        this.scrollPosition = Math.max(this.scrollPosition - 260,0) ;
        this.scroll.scrollTo(this.scrollPosition,1);
    }
    this.goFwd = function(){
        if (!this.active)
            return;
        this.scrollPosition = Math.min(this.scrollPosition + 260,
                                130 * Math.floor(this.avatars.length / rowNum - 1)) ;
        this.scroll.scrollTo(this.scrollPosition,1);
    }

    this.backBtn = new GeneralPurposeButton(this,this.goBack,'','<<<')
                .setPosition(width/16,7*height/8)
                .setSize(width/8,height/8);
    this.appendChild(this.backBtn);

    this.fwdBtn = new GeneralPurposeButton(this,this.goFwd,'','>>>')
                .setPosition(width-width/16,7*height/8)
                .setSize(width/8,height/8);
    this.appendChild(this.fwdBtn);

    this.buyBtn = new GeneralPurposeButton(this,this.buyAvatar,'','Select avatar above')
                .setPosition(3*width/8,7*height/8)
                .setSize(width/4,height/8);
    this.appendChild(this.buyBtn);

    this.toMainMenu = new GeneralPurposeButton(parent,parent.showMainMenu,'','Cancel')
                .setPosition(6*width/8,7*height/8)
                .setSize(width/8,height/8);
    this.appendChild(this.toMainMenu);

   
    this.enable = function(){
        this.reload.call(this);
        this.active = true;
        this.buyBtn.enable();
        this.toMainMenu.enable();
        /*
         * todo: check if avatars loaded and load them if not
         */
        for(i=0; i < this.avatars.length; i++) this.avatars[i].btn.enable();
    }
    
    this.disable = function(){
        this.active = false;
        if(this.isInitialised){
            for(i=0; i < this.avatars.length; i++) this.avatars[i].btn.disable();
        }
        this.buyBtn.disable();
        this.toMainMenu.disable();
    }
    
    this.hide();
    
}
goog.inherits(BuyAvatar, ScreenMod);


//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('BuyAvatar', BuyAvatar);