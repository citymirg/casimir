goog.provide('GenreSelection');

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
 * Genre Selection Screen
 * @constructor
 * @extends lime.Layer
 * @param parent
 * @param game
 * @todo: make this a child of Timer
 */
GenreSelection = function(parent, game, width, height){
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
    this.toGame = new GeneralPurposeButton(game,game.startMatch,'','Start Random Match')
                .setPosition(width/2, rowHeight/2)
                .setSize(width,rowHeight);
    setLargeFont(this.toGame.text)
                .setSize(width,40);
    this.appendChild(this.toGame);

    this.isInitialised = false;

    this.reload = function(){

            if (this.isInitialised){
                this.removeChild(this.toGenreGame);
                this.isInitialised = false;
            }
        // Show Highscore
        // @todo: make screen
        this.genres = new Array();

        if(parent.player.genres.length == 0){

            // at the moment we don't show anything without genres'
            return;
            
            //this.genres.push({value:-1,
            //                  label:_('No genres bought')});
        }

        for(var i = 0; i < parent.player.genres.length; i++){

            // only display genres  owned
            this.genres.push({value:parent.player.genres[i].genreId,
                              label:parent.player.genres[i].name + ' ' + _('Match')});
        }

        this.toGenreGame = new GeneralPurposeSelector(this,function(){
            if(this.toGenreGame.actValue() > -1){
                this.hide();
                // collect match details
                var matchDetails = {triplet:{genreId:this.toGenreGame.actValue()}}
                game.startMatch(matchDetails);
            }else{
                // go Buy genres
                parent.showBuyGenre();
            }

        }, this.genres,width,rowHeight)
                    .setPosition(0,rowHeight*2-rowHeight/2)
                    .setSize(width,rowHeight);
        setLargeFont(this.toGenreGame.btn.text)
                    .setSize(width,40);
        this.appendChild(this.toGenreGame);


        // save it this has been initialised
        this.isInitialised = true;
    }


    // Show Options
    // @todo: make screen
    this.toMainMenu = new GeneralPurposeButton(parent,parent.showMainMenu,'','Main Menu')
                .setPosition(width/2,rowHeight*4-rowHeight/2)
                .setSize(width,rowHeight);
    setLargeFont(this.toMainMenu.text)
                .setSize(width,40);
    this.appendChild(this.toMainMenu);
    
    
    this.enable = function(){
        this.reload.call(this);
        this.active = true;
        this.toMainMenu.enable();
        this.toGame.enable();
        if(this.isInitialised) this.toGenreGame.enable();
       
    }
    
    this.disable = function(){
        this.active = false;
        this.toMainMenu.disable();
        this.toGame.disable();
        if(this.isInitialised) this.toGenreGame.disable();
    }


    this.hide();
}
goog.inherits(GenreSelection, ScreenMod);


//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('GenreSelection', GenreSelection);