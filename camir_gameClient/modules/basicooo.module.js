goog.provide('basicooo');
goog.require('Module');


//get requirements
goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Circle');
goog.require('lime.RoundedRect');
goog.require('lime.Polygon');
goog.require('lime.fill.LinearGradient');
goog.require('lime.ui.Scroller');

goog.require('lime.animation.Resize');
goog.require('lime.animation.Spawn');
goog.require('lime.animation.FadeTo');
goog.require('lime.animation.ScaleTo');
goog.require('lime.animation.MoveTo');
goog.require('lime.animation.MoveBy');
goog.require('lime.animation.RotateTo');
goog.require('lime.animation.Loop');
goog.require('lime.scheduleManager');

goog.require('goog.math.Vec2');
goog.require('goog.events');
goog.require('goog.events.KeyCodes');
goog.require('goog.events.KeyHandler');

    
goog.require('GeneralPurposeButton');
goog.require('ProgressTimer');
goog.require('PlayerScoreIndicator');

goog.require('PlayerPanel');
goog.require('TutorialCanvas');

goog.require('GeneralPurposeSelector');
goog.require('AudioPlayerButton');
goog.require('AudioPlayerBanner');
goog.require('CheckButton');
goog.require('ProgressTimer');
goog.require('SelectionAvatar');
goog.require('RecordingIndicator');

goog.require('PlayerScoreIndicator');
goog.require('VolumeControl');
goog.require('ResultSpeakers');
goog.require('QuickMenu');



/*
 * Basicooo takes a list of song ids and urls
 * @param args [id1, id2, id3, url1, url2 url3]
 */
basicooo = function(args){
    Module.call(this);
    
    var self = this;
    
    this.data = args;

    /** remember if this is a result module*/
    isResult = false;
    
    /*
     * Set up background and title
     */
    game.match.bgbot.runAction(new lime.animation.FadeTo(1).setDuration(.4));
    game.match.titleLbl.runAction(new lime.animation.FadeTo(1).setDuration(.4));
    game.match.titleLbl.setText(_("Spot the Odd Song Out"));

   /** Position of each couple of buttons relativly to the rooPos. */
    var layerPos = new Array();
   /** Array of three layers */
    var layer = new Array();
   /** Array of three audio objects. */
    var jPlayer = new Array();
    
    /** Array of three audio button. */
    this.audiobutton = new Array();
   /** Array of three check button. */
    this.checkbutton = new Array();
      /** Array of three audio button. */
    this.audiobanner = new Array();
    
    // tutorial canvas
    this.tutCanvas = new TutorialCanvas(this);

    /** have we collected a vote already?**/
    this.hasVoted = false;


    this.audioPressed = function(){
        
        var d = new Date();
        self.startPlayerTime = d.getTime();
        game.match.lastAudioPlayedDate = d.getTime();
        
        self.volumeControl.runAction(new lime.animation.FadeTo(1).setDuration(.4));

        if (array_unique(audioBtnSeq).length == 3){
            for (i=0; i<3; i++){
                self.checkbutton[i].but.runAction(new lime.animation.FadeTo(1).setDuration(.4));
                self.audiobutton[i].onClickFunction = null;
            }
        }
    }

    
    var speakerSpace = 225;
    var speakerHeight = 370-232; // start of white - soeaker heught

    // fixed button positions 
    layerPos[0] = new goog.math.Vec2(-speakerSpace -(196/2),speakerHeight);
    layerPos[1] = new goog.math.Vec2(0-(196/2), speakerHeight);
    layerPos[2] = new goog.math.Vec2( speakerSpace-(196/2) ,speakerHeight);
    // create three buttons
    for(i=0; i<3; i++){

        // create lime layer for each button
        layer[i] = new lime.Layer();

        self.mainLayer.appendChild(layer[i]);
        layer[i].setPosition(layerPos[i]);

        // create audio and check button
        self.audiobutton[i] = new AudioPlayerButton(args.aSong[i],this.audioPressed,i);
        self.checkbutton[i] = new CheckButton(args.aSong[i].id);
        // banner
        self.audiobanner[i] = new AudioPlayerBanner(args.aSong[i]);
        
        layer[i].appendChild(self.audiobutton[i]);
        layer[i].appendChild(self.checkbutton[i]);
        layer[i].appendChild(self.audiobanner[i]);
        
        self.audiobutton[i].setAnchorPoint(0,0);
        self.audiobutton[i].setPosition(0,0);
        self.checkbutton[i].but.setAnchorPoint(0,0);
        self.checkbutton[i].but.setPosition(22,265);
        self.checkbutton[i].but.setOpacity(0);

        self.audiobanner[i].setAnchorPoint(0,0);
        self.audiobanner[i].setPosition(8,-60);
        self.audiobanner[i].hide();

    }
    
        
    // make sure the timing is reset from earlier rounds
    AudioPlayerButton.prototype.reset();
                
    // volume control
    this.volumeControl = new VolumeControl();
    this.volumeControl
        .setPosition(moduleWidth/2-55,160)
        .setOpacity(0);
    this.mainLayer.appendChild(this.volumeControl);

    /**
     * The great Progress bar
     */
    this.progresslayer = new lime.Layer().setPosition(-moduleWidth/2 + 30, 525);
    this.time_left = new ProgressTimer(args.serverTime,args.givenTime, function(){
                    AudioPlayerButton.prototype.stopPlayingAudio();
                        for(i=0; i<3; i++){
                            self.checkbutton[i].disable();
                        };
                    self.finalVote(-1);
                });
                
    this.time_left.setPosition(0, 0);
    this.progresslayer.appendChild(this.time_left);
    this.time_left.start();
    this.mainLayer.appendChild(this.progresslayer);
    
    // playerPanel
    this.pPanel = new PlayerPanel(-moduleWidth/2,540);
    this.pPanel.update();
    this.mainLayer.appendChild(this.pPanel); 


     this.finished = function(){
         // send "module done" player state                 
        game.client.asyncConnection.setPlayerState('moduleDone');

        // make sure the audio stops
        AudioPlayerButton.prototype.stopPlayingAudio();

        /*
         * Close the module               
         */                                             
        lime.scheduleManager.callAfter(function(){

            //self.close();
        },self,1000);
    };
    
    /*
     * This is the module-specific close function which closes all 
     * schedules etc specific to this very module
     * @todo: make proper destructor (.destroy) functions for modules and objects 
     */
    this.terminate = function(){
        
        // stop timers
        self.time_left.stop();
        lime.scheduleManager.unschedule(self.updatePlayerPanel,self);
    }


    //----------------------------
    //Events and Schedule Managing 
    //----------------------------
    
    /*
     * Update the player panel
     * @todo: PERFORMANCE using "this" does not work here :(
     *        the reason is that this function is compiled and then
     *        given forward.
     */
    this.updatePlayerPanel = function() {
        self.mainLayer.removeChild(self.pPanel);
        
        self.pPanel = null;
        delete self.pPanel;
        self.pPanel = new PlayerPanel(-moduleWidth/2,540);
        self.pPanel.update();
        self.mainLayer.appendChild(self.pPanel); 
    }
    lime.scheduleManager.scheduleWithDelay(this.updatePlayerPanel,this, 3000);
    
    /**
     * Callback DEALs with user events
     *
     * SENDS VOTES
     * 
     * Animation of speaker when music is played.
     *
     * @todo: porperly position functions at the
     *  end of the constructor or class
     */                     
    
    this.finalVote = function(butNum){
        // stop the timer
        // self.time_left.stop();
        if (self.hasVoted)
            return;
        self.hasVoted = true;
        /*
        * SEND THE VOTE HERE
        * @todo: record TIME and PLAYTIMES of the songs
        */
       var sendId;
        if (butNum >= 0) sendId = self.checkbutton[butNum].songId;
        else sendId = -1;

        var result = {
            
                    // Coherency check
                    'matchid': game.client.userAuth.matchid,
                    'step': game.match.step,
                    'voteType': 'basicooo',
                    
                    // Proper results values
                    'songChosenId': sendId,
                    'aSong': self.data.aSong,
                    'givenTime': self.time_left.maxServerTimeLeft(),
                    'totalTime': self.time_left.timeRunning(),
                    'song1PlayTime': self.audiobutton[0].timer.timeRunning(),
                    'song2PlayTime': self.audiobutton[1].timer.timeRunning(),
                    'song3PlayTime': self.audiobutton[2].timer.timeRunning(),
                    'songSequence': AudioPlayerButton.prototype.getListenSeq()};

        game.client.syncConnection.sendData(result);


        var ii;
        // vanish choose buttons
        if (butNum >= 0) {
            ii = butNum;
            self.checkbutton[ii].scaleOnClick();
        } else {
            ii = 0;
            self.checkbutton[(ii)].vanish();
        }
        
        self.checkbutton[(ii +1) %3].vanish();
        self.checkbutton[(ii +2) %3].vanish();

        //vanish audio buttons
        if (butNum < 0) self.audiobutton[(ii)].vanish();
        self.audiobutton[(ii +1) %3].vanish();
        self.audiobutton[(ii +2) %3].vanish();

        //show audio banners
        for(var iii = 0; iii < 3; iii++){
            self.audiobanner[iii].show();
        }
        self.finished();
    }

    var eventsListener = function(i){// callback association
        goog.events.listenOnce(self.checkbutton[i].but,['mousedown','touchstart'],function(e){
                // check if the button is enabled
                if (!(self.checkbutton[i].enabled))
                    return false;

                self.finalVote(i);
                
                if(self.bubbleTl != undefined) self.bubbleTl.hide();
                self.bubbleTl = self.tutCanvas.popBubble('Waiting for other players',
                              150,60,-350,450,'s', 0, self.time_left.timeRunning());
        });
    }
    
     /**
     * Compile Event Script.
     */
    for(var i=0; i<3; i++){
        eventsListener(i);
    };
 
    //---------------------
    //Animation Definitions
    //---------------------
    for (i=0; i<3; i++){
         self.checkbutton[i].enabled = true;
         self.audiobutton[i].enabled = true;
     }
     
    // tutorial canvas
    this.appendChild(this.tutCanvas);

     
     /*
     * Tell the game.match that Module has been Loaded
     */
    game.client.asyncConnection.setPlayerState('onModule');


}
goog.inherits(basicooo,Module);

//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('basicooo', basicooo);
