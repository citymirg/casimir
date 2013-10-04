goog.provide('CamirHerdMatch');

// Modules
goog.require('basicooo');
goog.require('basicooo_result');
goog.require('basicoooIntro');
goog.require('basicooo_resultIntro');

goog.require('taptempo');
goog.require('taptempo_result');
goog.require('taptempoIntro');
goog.require('TapTempo_resultIntro');

goog.require('TapRythm');
goog.require('TapRythmIntro');
goog.require('TapRythm_result');
goog.require('TapRythm_resultIntro');

goog.require('closer');


//get requirements
goog.require('QuickMenu');
goog.require('TutorialCanvas');
goog.require('PlayerHighScore');
goog.require('GeneralPurposeButton');
goog.require('PlayerAvatar');
goog.require('TutorialCanvas');

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
 * This is the Match Scene from the CamirHerd Game
 */

CamirHerdMatch = function(game, matchDetails) {

    lime.Scene.call(this);

    var self = this;


    /*
     * String-indexed array map containing all players in-game data
     */
    this.players = new Array();

    // have we shown intro modules?
    this.introShown = new Object();
    
    
    // have we shown intro modules?
    var dd = new Date();
    this.lastLoadedModuleDate = dd.getTime();
    
    // have we shown intro modules?
    this.lastAudioPlayedDate = dd.getTime();
    
    
    
    
    /*
     * Note: the module layer is centered, this is not.
     */
    this.mainLayer = new lime.Layer();
       // .setPosition(moduleWidth/2,50)
       // .setOpacity(1);

    /*
     *  Background.
     *  they overlap so the picture can be stretched 
     */
    this.bgtop = new lime.Sprite().setSize(960,640).
        setFill('./img/skin1/bg_behindsp.jpg').setAnchorPoint(0,0);
        this.mainLayer.appendChild(this.bgtop);
        
    this.bgbot = new lime.Sprite().setSize(960,640-370).
        setFill('./img/skin1/bg_behindpl.jpg').setAnchorPoint(0,0)
        .setPosition(0,370);
        this.mainLayer.appendChild(this.bgbot);


    /*
     *  Title Label
     *  @todo: add style elements to text. for 
     *      a. translation
     *      b. 
     */
    this.titleLbl = new lime.Label();
    setLargeFont(this.titleLbl)
        .setText(_('Welcome'))
        .setAnchorPoint(0.5, 0.5)
        .setPosition(moduleWidth/2,50)
        .setSize(moduleWidth,40);
    this.mainLayer.appendChild(this.titleLbl);
    
     /*
     *  Title Label
     *  @todo: add style elements to text. for 
     *      a. translation
     *      b. 
     */
    this.stepLbl = new lime.Label();
    setLargeFont(this.stepLbl)
        .setText('')
        .setAnchorPoint(0.5, 0.5)
        .setPosition(50,50)
        .setOpacity(0.2)
        .setFontColor('#000000');
        
    this.mainLayer.appendChild(this.stepLbl);

    /*
     * stops polling and kicks playe3r
     * also updates facebook scores
     * @todo: reset stuff in the client?
     */
    this.stopMatch = function(){
                
        // stop polling
        game.client.stop(); 
        
        

        //kick player
        game.client.asyncConnection.setPlayerState('out');    
        
        /*
         * 
         *   send score update
         *   
         */
        var player = self.players["_" + game.client.userAuth.playerid.toString()];
        console.log(player);
        if (!isEmpty(player) && fbUserDetails.loggedIn){
            
            fbUserDetails.publishScore(player.totalPoints);
        }
    }

    
    /*
     * Quick menu
     */
    this.quickMenu = new QuickMenu(game)
                     .setPosition(moduleWidth-55,50);
    this.mainLayer.appendChild(this.quickMenu);
    
      /*
     * Loading indicator kicks player with bad connection
     * (10*polling interval) = 50 seconds
     */
    this.loading = new LoadingIndi(interval/1000 * 10, function(){
                             console.log('loading indicator timed out');
                            //self.stopMatch();
                           // game.showMainMenu();
                        });
    this.mainLayer.appendChild(this.loading);
    this.loading.setPosition(moduleWidth/2,moduleHeight/2);


    /**
     * Every lime object of this module is appened to this frame.
     */
    // show main Layer
    this.appendChild(this.mainLayer);


    /*
     * Define Polling behaviour
     *
     */

     /**
     * What to do when poll successes.
     */
    this.pollsuccess = function (ret,id,met){
        console.log('pollsuccess :' + ret);

        console.log('ret[0] Player Table:');
        console.log(ret[0]);
        console.log('ret[1] Server Info:');
        console.log(ret[1]);
        console.log('ret[2] Optional Module:');
        console.log(ret[2]);

        /*
         * update the matchs players
         */
        delete self.players;
        self.players = new Array();
        for( var k=0; k < ret[0].length; k++ ) {
            self.players["_" + ret[0][k].id.toString()] = ret[0][k];
        }
       
         
         
        /*
         *  Save step number.
         */       
        
        
        
        /*
         * @todo: remove gameServerTime. make serverStartTime a module variable
         *  otherwise below synchs a new module to old servertime
         * 
         * update the server Time
         */
        if(game.match.serverTime == undefined || ret[1].serverTime > game.match.serverTime)
            game.match.serverTime = ret[1].serverTime;
        
        // STEPS INFOS
        if(game.match.step == undefined || ret[1].step > game.match.step)
            game.match.step = ret[1].step;
        if(game.match.totalSteps == undefined)
            game.match.totalSteps = ret[1].totalSteps;
        
        
        if( game.match.module != undefined &&  game.match.module.time_left != undefined ){
            game.match.module.time_left.serverSync(game.match.serverTime);
        }
        self.loading.renew();
        
         /*
         * Update the step Lbl
         */
        var stepNo =   parseInt (game.match.step);
        stepNo = stepNo +1;
        self.stepLbl.setText(stepNo + '/' + (game.match.totalSteps - 1));


        /*
         * Start the returned module
         * @todo: better control of what can happen here
         *        e.g. check if a module is already running
         */
        if(ret[2] != undefined && ret[2] != null ){
                
            // Save last module date
            var dd = new Date();
            game.match.lastLoadedModuleDate = dd.getTime();
            
            /*
             * We check if there is a Intro module unshown and available
             */
            var mod;
            console.log(self.introShown[ret[2].type]);
            
            if (self.introShown[ret[2].type] == undefined && (typeof window[ret[2].type+ "Intro"] === 'function')){

                // start intro module
                mod = new this[ret[2].type + "Intro"](ret[2].args);

                // log intro as shown
                self.introShown[ret[2].type] = true;
            }else{
                mod = new this[ret[2].type](ret[2].args);
            }
            mod.run();
        }
        
          
        /**
         * Kick Timeout Players
         */ 
        
        var stepNum =   parseInt (game.match.step);
        dd = new Date();
//        if(((dd.getTime() - game.match.lastAudioPlayedDate) > 120000) && stepNum >0){
//            console.log('KICKED for audio player action time out');
//            game.match.stopMatch();
//            game.showMainMenu();
//        }
        
        if((dd.getTime() - game.match.lastLoadedModuleDate) > 100000){
            console.log('KICKED for module time out');
            game.match.stopMatch();
            game.showMainMenu();
        }
    }

     //---------------------
    // Module   Management
    //---------------------
    this.module = null;

    this.closeModule = function () {

        this.removeChild(this.module);
        delete this.module;

        this.module = null;
    }


    //-------------------------
    // Interaction with Server
    //-------------------------

    /**
    * There are six possible states :
    *  'ready' -> Step has just begun
    *  'onModule' -> A Module is launched on the user client.
    *  'moduleDone' -> The module is finished on the client side.
    *
    *  'onResultModule' -> A Result Module is launched on the user client.
    *  'done' -> Ready for match going to next step
    *
    *  'out' -> The user has left the game.
    *
    *  Possible Auto update :
    *  From 'ready' TO 'moduleDone' -> if not concerned by the module
    *  From 'moduleDone' TO 'done' -> if not concerned by the results
    *
    *
    */

    /*
     * Create Player HUD
     */


    //----------------------------
    //Start Module Loading
    //----------------------------

     /**
     *  Create Ajax-JsonRPC client
     *  this also starts the module
     */

    game.client.startMatch('STOSO',matchDetails);
    game.client.start(interval, this.pollsuccess);
    
    // start loading animation
    this.loading.start();

}
goog.inherits(CamirHerdMatch,lime.Scene);


//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('CamirHerdMatch', CamirHerdMatch);