goog.provide('Module_result');



//get requirements
goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Circle');
goog.require('lime.RoundedRect');
goog.require('lime.Polygon');
goog.require('lime.fill.LinearGradient');

goog.require('lime.animation.Resize');
goog.require('lime.animation.Spawn');
goog.require('lime.animation.FadeTo');
goog.require('lime.animation.ScaleTo');
goog.require('lime.animation.MoveTo');
goog.require('lime.animation.MoveBy');
goog.require('lime.animation.RotateTo');
goog.require('lime.animation.Loop');

goog.require('goog.events');

goog.require('lime.scheduleManager');
    
goog.require('GeneralPurposeButton');
goog.require('ProgressTimer');
goog.require('PlayerScoreIndicator');

goog.require('PlayerResultRow');
goog.require('TutorialCanvas');

goog.require('GeneralPurposeButton');
goog.require('AudioPlayerButton');
goog.require('ProgressTimer');

goog.require('PlayerScoreIndicator');
goog.require('ResultSpeakers');
goog.require('QuickMenu');


// entrypoint
Module_result = function(args) {
    Module.call(this);
    var self = this;

    /** remember if this is a result module*/
    isResult = true;
    
    /*
     * Save Results in Module
     */
    this.data = args;
    
    /*
     * Set up background and title
     */
    game.match.bgbot.runAction(new lime.animation.FadeTo(.0).setDuration(.4));
    game.match.titleLbl.runAction(new lime.animation.FadeTo(.0).setDuration(.4));
    
    
    /**
     * The great Progress bar
     */
    this.progresslayer = new lime.Layer().setPosition(-185/2, 540);
    this.time_left = new ProgressTimer(game.match.serverTime,totalResultTime, function(){
                    
                    AudioPlayerButton.prototype.stopPlayingAudio();
                    game.client.asyncConnection.setPlayerState('done');
                    //self.close();
                });
                
    this.time_left.setPosition(0, 0);
    this.progresslayer.appendChild(this.time_left);
    this.time_left.start();

   
    // create exit button
    // var checkbutton;
    var frameWidth = 615;
    var frameHeight = 380;

    // create lime layer 
    this.mainFrame = new lime.RoundedRect()
        .setAnchorPoint(0.5, 0.5).setOpacity(1)
        .setSize(frameWidth,frameHeight) // centred
        .setFill('#f2f2f2')
        .setPosition(0,moduleHeight/2)
        .setStroke(1,'#EEEEEE');
    this.mainLayer.appendChild(this.mainFrame);
    this.mainFrame.getDeepestDomElement().setAttribute("style", "-moz-box-shadow: 2px 2px 3px #888; -webkit-box-shadow:2px 2px 3px #888; box-shadow: 2px 2px 3px #888;"); 

         
    /*
     * Labels for Points at Bottom
     */       
     this.playerRow = new Array();
     var rowHeight = 95;
     
     console.log(args);
     
     var player;
     var idx_ctr = 1;
     var idx;
     // cycle through results and plot them into the songs
      for(i=0; i < args.results.length; i++){
        // for debug urposes get the player here
        player = game.match.players["_" + args.results[i].playerId.toString()];
         if(player != undefined && args.results[i].earnedPoints != null){
             
            // is this the actual player?
            if (player.id == game.client.userAuth.playerid){
                idx = 0;
            }else{
                idx = idx_ctr;
            }
            this.playerRow[idx] = new PlayerResultRow(args.results[i], player, args.aSong);

            // create a label representing the player
            this.playerRow[idx].setPosition(-frameWidth/2,-frameHeight/2 + rowHeight/2 + idx*rowHeight);

            if (i < (maxPlayers)){
                this.playerRow[idx].appendChild( new lime.Sprite()
                                    .setSize(frameWidth,2)
                                    .setPosition(0,rowHeight/2)
                                    .setFill("#CCCCCC")
                                    .setAnchorPoint(0,0)
                                    );
                }
            this.mainFrame.appendChild(this.playerRow[idx]);
            
            if (!idx==0) idx_ctr++;
        }else{
            // there was a player which left the game before the results were shown
            // atm we just dont show their results
        }
    }

    /*
     * fix for player which has left:
     * copy the last row to the first row
     */
     if(this.playerRow[0] == undefined){
         var len = this.playerRow.length;

         this.playerRow[0] = this.playerRow[len-1];
         this.playerRow[0].setPosition(-frameWidth/2,-frameHeight/2 + rowHeight/2 + 0);

         this.playerRow.splice(len-1,1);
     }
     
    // hook all the main content
    this.mainLayer.appendChild(this.mainFrame);
    this.mainLayer.appendChild(this.progresslayer);

    /*
     * Achievement display
     *
     */
    this.tutCanvas = new TutorialCanvas(this);
    this.appendChild(this.tutCanvas);
    
  
    // show bubles one after another
    if (args.achievements != undefined){
        // how long to display achievements?
        var achTime = totalResultTime / args.achievements.length;
    
        
        for(i=0; i < args.achievements.length; i++){
            this.bubbleAch = self.tutCanvas.popBubble(args.achievements[i].description,
                 400,70,200,600,'n', i*achTime, achTime);
            setSmallFont(this.bubbleAch.text);
        }
        if (fbUserDetails.loggedIn && !fbUserDetails.validPermissions(config.permissions_adv.split(","))){

           this.publish = new GeneralPurposeButton(this,
                function(){
                    fbUserDetails.requestPermissions(config.permissions_adv.split(","),function(){});
               } ,'',_('Post this'))
                       .setPosition(-270, 0)
                       .setSize(70,50);
           this.bubbleAch.appendChild(this.publish);
        }
        }
    
    /*
     * Tell the game.match that Module has been Loaded
     */
    game.client.asyncConnection.setPlayerState('onResultModule');
    
    this.terminate = function(){
        
        // stop timers
        self.time_left.stop();
    }


}
goog.inherits(Module_result,Module);


//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('Module_result', Module_result);

