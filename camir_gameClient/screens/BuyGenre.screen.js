goog.provide('BuyGenre');

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
BuyGenre = function(parent, game, width, height){
    ScreenMod.call(this);
    
    /*
     * layout constants
     */
    this.setPosition(-width/2,-height/2);
    
    
    /*
     * Label for Lines at Bottom
     */
     var rowHeight = height/4;
     
    // Show Highscore
    // @todo: make screen

    this.isInitialised = false;

    this.reload = function(){

        if (this.isInitialised){
            this.removeChild(this.toGenreGame);
            this.isInitialised = false;
        }
        /*
         *@todo: this should be done only once when facebook has finished
         */
        this.genreTbl = game.client.syncConnection.getGenreTable();
        
        this.genres = new Array();
        for(var i = 0; i < this.genreTbl.length; i++){

            // only display genres not owned
            if (this.genreTbl[i].playerId == null){
                this.genres.push({value:this.genreTbl[i].genreId,
                                  label:this.genreTbl[i].name});
            }
        }
        if(isEmpty(this.genres)){
            this.genres.push({value:-1, label:_('You own all genres')});
        }


        this.updateBuyBtn = function(){

            // collect match details
            //this.toGenreGame.actLabel()
            this.buyBtn.text.setText(_("Buy") + ': 100 ' +_("Pts"));
        };
        this.toGenreGame = new GeneralPurposeSelector(this,this.updateBuyBtn,this.genres,width,rowHeight,this.updateBuyBtn)
                  .setPosition(0,rowHeight*2-rowHeight/2)
                  .setSize(width,rowHeight);
        setLargeFont(this.toGenreGame.btn.text)
                  .setSize(width,40);
        this.appendChild(this.toGenreGame);

        //show its initialised
        this.isInitialised = true;
    }


    this.buyGenre = function(){
         if(this.toGenreGame.actValue() != -1){
            var result = game.client.syncConnection.buyGenre(this.toGenreGame.actValue());
            if (result){
                // cool it worked, go back to main menu then
                parent.tutCanvas.popBubble(_('Bought') + ' ' + this.toGenreGame.actLabel(),
                      200,60,0,-150,'s', 0, 3000);

                console.log('Bought Genre ' + this.toGenreGame.actLabel());

                parent.plUpdate();
                lime.scheduleManager.callAfter( function(){
                    parent.showMainMenu();
                },this,3300);

            }else{
                parent.tutCanvas.popBubble(_('Not enough points'),
                    200,60,0,-150,'s', 0, 3000);
                console.log(this.toGenreGame.actValue());
            }
         }else{
             // no genres left
                parent.tutCanvas.popBubble(_('No genres left to buy'),
                    200,60,0,-150,'s', 0, 4000);
            }
    }
    /*
     * Buy / Cancel
     */
    this.buyBtn = new GeneralPurposeButton(this,this.buyGenre,'','Select genre above')
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
        this.toGenreGame.enable();
    }
    
    this.disable = function(){
        this.active = false;
        this.buyBtn.disable();
        this.toMainMenu.disable();
        if(this.isInitialised) this.toGenreGame.disable();
    }
    
    this.hide();
    
}
goog.inherits(BuyGenre, ScreenMod);


//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('BuyGenre', BuyGenre);