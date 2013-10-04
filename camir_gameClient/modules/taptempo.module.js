goog.provide('taptempo');

// entrypoint
goog.require('Module');

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

goog.require('goog.math.Vec2');
goog.require('goog.events');
goog.require('goog.events.KeyCodes');
goog.require('goog.events.KeyHandler');

goog.require('lime.scheduleManager');

goog.require('PlayerPanel');
goog.require('TutorialCanvas');
goog.require('GeneralPurposeButton');
goog.require('AudioPlayerButton');
goog.require('AudioPlayerBanner');
goog.require('CheckButton');
goog.require('ProgressTimer');
goog.require('RecordingIndicator');

goog.require('VolumeControl');

/*
 * Taptempo takes a list of song ids and urls
 * @param array args.aSong [id1, id2, id3, url1, url2 url3]
 */
taptempo = function(args){
    Module.call(this);
    
    /* Set Music on local for tests */
    this.data = args.aSong;
    var self = this;
    
    
    /** remember if this is a result module*/
    isResult = false;
   
    // tutorial canvas
    this.tutCanvas = new TutorialCanvas(this);

    /*
     * Set up background and title
     */
    game.match.bgbot.runAction(new lime.animation.FadeTo(1).setDuration(.4));
    game.match.titleLbl.setText('Tap Tempo');
    game.match.titleLbl.runAction(new lime.animation.FadeTo(1).setDuration(.4));
   
   
       /** have we collected a vote already?**/
    this.hasVoted = false;


    this.audioPressed = function(){
        
        var d = new Date();
        self.startPlayerTime = d.getTime();
        game.match.lastAudioPlayedDate = d.getTime();
            
        self.volumeControl.runAction(new lime.animation.FadeTo(1).setDuration(.4));
        self.recordingIndicator.runAction(new lime.animation.FadeTo(1).setDuration(.4));
        
        //goog.events.listen(self.recordingIndicator,['mousedown','touchstart'],self.onTap);

        self.recordingIndicator.enable();
            
        self.keyEventsListener = new goog.events.KeyHandler(window);
        self.keyForKeyPressedListen = goog.events.listen(self.keyEventsListener,'key' ,function(e){self.onTap(e)},false,self);
        
        self.audiobutton.onClickFunction = null;
        self.recordingIndicator.enabled = true;
        
            
    };

    //  ------------ DISPLAY
    
    var speakerSpace = 225;
    var speakerHeight = 370-232; // start of white - soeaker heught
    
    
    var layer = new lime.Layer();
    var layerPos = new goog.math.Vec2(0-(196/2), speakerHeight);
    
    // Image in the background :
    
    var bgImg = new lime.Sprite().setFill('img/metronome.png');
    
    layer.appendChild(bgImg);
    
    bgImg.setSize(speakerHeight*2,speakerHeight*2)
        .setPosition(- speakerHeight, speakerHeight/2)
        .setOpacity(0.1);
    
    // Changing Label to display BPM :
    
    this.BPMRealTimeLabel = new lime.Label().setText('-- BPM');
    setMediumFont(this.BPMRealTimeLabel);
    this.errorRealTimeLabel = new lime.Label().setText('-- %');
    setMediumFont(this.errorRealTimeLabel);
    
    layer.appendChild(this.BPMRealTimeLabel);
    layer.appendChild(this.errorRealTimeLabel);
    
    this.BPMRealTimeLabel
        .setPosition(2*speakerHeight, speakerHeight)
        .setOpacity(0.2);
    this.errorRealTimeLabel
        .setPosition(speakerHeight*2.8, speakerHeight)
        .setOpacity(0.2);
    
    // FadeIn a red gradiented circle when Tapping
    
    var tappingDisplaylayer = new lime.Circle()
        .setSize(speakerHeight*4,speakerHeight*4)
        .setFill('#c00')
        .setPosition(speakerSpace/2,speakerHeight*1)
        .setOpacity(0.);
    
    layer.setPosition(layerPos);
    self.mainLayer.appendChild(layer);
    layer.appendChild(tappingDisplaylayer);
    
    /** create an audio button */
    this.audiobutton = new AudioPlayerButton(args.aSong,this.audioPressed);
    this.recordingIndicator = new RecordingIndicator(this,function(e){this.onTap(e)},"Tap Here");
    
    
    layer.appendChild(this.audiobutton);
    layer.appendChild(this.recordingIndicator);
    
    // AUDIO BANNER 
    this.audiobanner = new AudioPlayerBanner(args.aSong);
        self.audiobanner.setAnchorPoint(0,0);
        self.audiobanner.setPosition(8,-60);
        self.audiobanner.hide();
    layer.appendChild(this.audiobanner);
    
    
    self.audiobutton.setAnchorPoint(0,0);
    self.audiobutton.setPosition(0,0);
    self.recordingIndicator.setAnchorPoint(0,0);
    self.recordingIndicator.setPosition(100,295);
    self.recordingIndicator.setOpacity(0);

    
    // make sure the timing is reset from earlier rounds
    AudioPlayerButton.prototype.reset();
   
   
    // ---------------- VOLUME CONTROL
    
    this.volumeControl = new VolumeControl();
    this.volumeControl
        .setPosition(moduleWidth/2-55,160)
        .setOpacity(0);
    this.mainLayer.appendChild(this.volumeControl);
            
    // ---------------- PROGRESS BAR
    
    /**
     * The great Progress bar
     */
    this.progresslayer = new lime.Layer().setPosition(-moduleWidth/2 + 30, 525);
    this.time_left = new ProgressTimer(args.serverTime,args.givenTime, function(){
        
                    AudioPlayerButton.prototype.stopPlayingAudio();
                    if (!this.hasVoted){
                    
                    tempoRecorder.averageTime = -1;
                    tempoRecorder.stdDevTime = -1;
                    tempoRecorder.maxDevTime = -1;
                    
                    tempoRecorder.averageBPM = -1;
                    tempoRecorder.stdDevBPM = -1;
                    tempoRecorder.maxDevBPM = -1;
                    tempoRecorder.beatTimes = new Array();
                    
                    self.recordingIndicator.timeOut();
                    self.closeTempoRecorder();
                    }
                    
                    
                });

    this.time_left.setPosition(0, 0);
    this.progresslayer.appendChild(this.time_left);
    this.time_left.start();
    
    this.mainLayer.appendChild(this.progresslayer);
    
    // ---------------- PLAYER PANEL
    // 
    // playerPanel
    this.pPanel = new PlayerPanel(-moduleWidth/2,540);
    this.pPanel.update();
    this.mainLayer.appendChild(this.pPanel); 
   
    // ---------------- FINISHING METHODS
    
    this.finished = function(){
         // send "module done" player state                 
        game.client.asyncConnection.setPlayerState('moduleDone');

        // make sure the audio stops
        AudioPlayerButton.prototype.stopPlayingAudio();
        
        //Show Audio banner
        self.audiobanner.show();
        
        if(self.bubbleTl != undefined) self.bubbleTl.hide();
        self.bubbleTl = self.tutCanvas.popBubble('Waiting for other players',
                      150,60,-350,450,'s', 0, self.time_left.timeRunning());
        /*
         * Close the module               
         */                                             
        lime.scheduleManager.callAfter(function(){

            //self.close();
        },self,1000);
    }
    
    /*
     * This is the module-specific close function which closes all 
     * schedules etc specific to this very module
     * @todo: make proper destructor (.destroy) functions for modules and objects 
     */
    this.terminate = function(){
        
        this.recordingIndicator.disable();
        goog.events.unlistenByKey(self.keyForKeyPressedListen);
        
        // stop timers
        self.time_left.stop();
        lime.scheduleManager.unschedule(self.updatePlayerPanel,self);
    }

    //---------------------------- TEMPO RECORDER DESCRIPTION
    

    var tempoRecorder = new Object();


    tempoRecorder.beatTimes = new Array();

    tempoRecorder.averageTime = 0;
    tempoRecorder.stdDevTime = 0;
    tempoRecorder.maxDevTime = 0;

    tempoRecorder.averageBPM = 0;
    tempoRecorder.stdDevBPM = 0;
    tempoRecorder.maxDevBPM = 0;





    tempoRecorder.computeStats = function (){


        var n = tempoRecorder.beatTimes.length ;
        var lastTimeDif = (tempoRecorder.beatTimes[n-1] - tempoRecorder.beatTimes[n-2]) ;
        var lastBPM = 60000/lastTimeDif;

        // ------------------ Average Time difference

        var totTime = tempoRecorder.beatTimes[n-1] - tempoRecorder.beatTimes[0];

        tempoRecorder.averageTime = totTime /(n-1);

        // ------------------ Average BPM

        tempoRecorder.averageBPM = 60000 / tempoRecorder.averageTime;
        var lastDev = Math.abs(lastBPM - tempoRecorder.averageBPM);

        // ------------------ Std Deviation in Time

        var timeVariance = 0;
        var iDif = 0;
        var iDev = 0;

        for (var i=1;i<n;i++)
        {
            iDif = tempoRecorder.beatTimes[i] - tempoRecorder.beatTimes[i-1];
            iDev = Math.abs(tempoRecorder.averageTime - iDif);
            timeVariance = timeVariance + Math.pow(iDev,2);
        }
        timeVariance = timeVariance / (n-1);

        tempoRecorder.stdDevTime = Math.sqrt(timeVariance);

        // ------------------ Std Deviation in BPM

        var variance = 0;
        var iDif = 0;
        var iDifBPM = 0;
        var iDev = 0;
        var iBPM = 0;

        for (var i=1;i<n;i++)
        {
            iDif = tempoRecorder.beatTimes[i] - tempoRecorder.beatTimes[i-1];
            iBPM = 60000/iDif;
            iDev = Math.abs(tempoRecorder.averageBPM - iBPM);
            variance = variance + Math.pow(iDev,2);
        }
        variance = variance / (n-1);


        tempoRecorder.stdDevBPM = Math.sqrt(variance);
        

        // ------------------ Max Deviation Time

        var lastTimeDev = Math.abs(lastTimeDif - tempoRecorder.averageTime);
        tempoRecorder.maxDevTime = Math.max(
            tempoRecorder.maxDevTime,
           lastTimeDev);

        // ------------------ Max Deviation BPM

        tempoRecorder.maxDevBPM = Math.max(
            tempoRecorder.maxDevBPM,
            lastDev);



        // ------------------ Error

        tempoRecorder.error = Math.round(tempoRecorder.stdDevTime / tempoRecorder.averageTime *100);



    }
    
    // First click initialise the tempoRecorder, after 8 Click the tempoRecorder is closed
    self.onClickFunction = function (e) {
        
        var d = new Date();
        var n = d.getTime(); 
        
        var len = tempoRecorder.beatTimes.length;
        var bpm = tempoRecorder.averageBPM;
        
        if(  len == 0){
            self.initTempoRecorder();
          
        } else if(d - tempoRecorder.beatTimes[len-1] < 100){
           // DO NOTHING IF FOO MUCH FOLLOWING PRESSING.
            
        }else if( 
            (bpm < 70 && len > 7)
            || (bpm < 100 && len > 11)
            || (len > 15)
            ){
           // ENDING CASE
            
           
            self.updateTempoRecorder();
            goog.events.unlistenByKey(self.keyForKeyPressedListen);
            self.recordingIndicator.stopRecord();
            this.tapAnimation(e);
            AudioPlayerButton.prototype.stopPlayingAudio();
            
             lime.scheduleManager.callAfter(function(){
                self.closeTempoRecorder();
            },self,600);
            
        } else {
            self.updateTempoRecorder();
            this.tapAnimation(e);
        }
        
        return true;
        
    };
    
    
    
    // Initialise TemporRecorder
    this.initTempoRecorder = function () {
        var d = new Date();
        var n = d.getTime(); 
        
        tempoRecorder.beatTimes.push(n);
        this.recordingIndicator.startRecord();
    };
    
    // Update tempoRecorder : Add new time compute statistics
    this.updateTempoRecorder = function () {
        var d = new Date();
        var n = d.getTime(); 
        
        tempoRecorder.beatTimes.push(n);
        tempoRecorder.computeStats();
        
        
        this.BPMRealTimeLabel.setText(Math.round(tempoRecorder.averageBPM) + ' BPM ');
        this.errorRealTimeLabel.setText(tempoRecorder.error + ' %');
    };
    
    // Close and finish TemporRecording then send Data
    this.closeTempoRecorder = function () {
                            
        if(this.hasVoted){
            console.log('ERROR: Trying to vote a second time.')
            return;
        }
        
        goog.events.unlistenByKey(self.keyForKeyPressedListen);
        
        
        var result = {
            
                            // Coherency check
                            'songId': args.aSong.id,
                            
                            'matchid': game.client.userAuth.matchid,
                            'step': game.match.step,
                            'voteType': 'taptempo',
                            
                            // Proper results values
                            'averageTime':  tempoRecorder.averageTime,
                            'stdDevTime':  tempoRecorder.stdDevTime,
                            'maxDevTime':  tempoRecorder.maxDevTime,
                            'averageBPM':  tempoRecorder.averageBPM,
                            'stdDevBPM':  tempoRecorder.stdDevBPM,
                            'maxDevBPM':  tempoRecorder.maxDevBPM,
                            'totalTime': self.time_left.timeRunning(),
                            'startPlayerTime': self.startPlayerTime,
                            'jPlayerFirstTimeUpdateTime': self.audiobutton.getJPlayerFirstTimeUpdateTime(),
                            'jPlayerPlayTime': self.audiobutton.getJPlayerPlayTime(),
                            'beatTimes': tempoRecorder.beatTimes
                        };
                    
        game.client.syncConnection.sendData(result);
        this.hasVoted = true;
        
        self.finished();
        
    };
        
    
    //---------------------------- EVENTS AND SCEDULE MANAGER
    
    // ---------------- PLAYER PANEL UPDATE
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
    
    // ---------------- Listen to taping
    
    // ON TAP ANIMATION
    
    this.fadeCircle = function (e){
        var tofadeCircle = new lime.animation.Sequence(
                    new lime.animation.FadeTo(.3).setDuration(.1),
                    new lime.animation.FadeTo(0.).setDuration(.1)
                    );
        tappingDisplaylayer.runAction(tofadeCircle);  
    }
    
    
    this.tapAnimation = function(e){
        
                // Circle in the back
                self.fadeCircle();
                // Tilt button
                self.recordingIndicator.tilt();
    }
    
    
    // ON TAP FUNCTION
    this.onTap = function(e){
               
                
                // Compute stats
                self.onClickFunction(e);
                
                
                return true;
    };
                
                
                
    
    
    
            
            
            
    // ---------------- Enable animations
    self.audiobutton.enabled = true;


     /*
     * Tell the Game that Module has been Loaded
     */
    game.client.asyncConnection.setPlayerState('onModule');


}
goog.inherits(taptempo,Module);

//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('taptempo', taptempo);
