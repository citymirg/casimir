goog.provide('TapRythm');

goog.require('goog.positioning.ClientPosition');
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
goog.require('lime.scheduleManager');


goog.require('goog.math.Vec2');
goog.require('goog.events');
goog.require('goog.events.KeyCodes');
goog.require('goog.events.KeyHandler');

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
 * Tap Rythm takes one song ids and urls
 * @param array args.aSong id1, url1
 */
TapRythm = function(args){
    Module.call(this);
    
    /* Set Music on local for tests */
    this.data = args.aSong;
    var self = this;
    
    this.isTapListeningStarted = false;
    
    
    
    /** remember if this is a result module*/
    isResult = false;
    this.tutCanvas = new TutorialCanvas(this);

   
    /*
     * Set up background and title
     */
    game.match.bgbot.runAction(new lime.animation.FadeTo(1).setDuration(.4));
    game.match.titleLbl.setText('Tap Rhythm');
    game.match.titleLbl.runAction(new lime.animation.FadeTo(1).setDuration(.4));
   
   
    /** have we collected a vote already?**/
    this.hasVoted = false;


    this.audioPressed = function(){
        var d = new Date();
        self.startPlayerTime = d.getTime();
        game.match.lastAudioPlayedDate = d.getTime();
        
        self.recordingIndicator.enabled = true;
        self.audiobutton.onClickFunction = null;
        
        
        self.volumeControl.runAction(new lime.animation.FadeTo(1).setDuration(.4));
        
        
        
        
        
        if(! self.isTapListeningStarted){
        self.keyEventTappingCircleArray[0] = goog.events.listen(self.tappingCircleArray[0],['mousedown','touchstart'],function (e){self.onTouch(0);});
        self.keyEventTappingCircleArray[1] = goog.events.listen(self.tappingCircleArray[1],['mousedown','touchstart'],function (e){self.onTouch(1);});
        self.keyEventTappingCircleArray[2] = goog.events.listen(self.tappingCircleArray[2],['mousedown','touchstart'],function (e){self.onTouch(2);});
        self.keyEventTappingCircleArray[3] = goog.events.listen(self.tappingCircleArray[3],['mousedown','touchstart'],function (e){self.onTouch(3);});

            
            
            
        self.keyEventsListener = new goog.events.KeyHandler(window);
        self.keyForKeyPressedListen = goog.events.listen(self.keyEventsListener, 'key' , self.onTap);
        
        self.isTapListeningStarted = true;
        }
        
        
        
            
    };

    //  ------------ DISPLAY
    
    var speakerSpace = 225;
    var speakerHeight = 370-232; // start of white - soeaker heught
    
    
    var layer = new lime.Layer();
    var layerPos = new goog.math.Vec2(0-(196/2), speakerHeight);
    
    // Image in the background :
    
    var bgImg = new lime.Sprite().setFill('img/drums.png');
    
    layer.appendChild(bgImg);
    
    bgImg.setSize(speakerHeight*2,speakerHeight*2)
        .setPosition(2.2*speakerHeight,speakerHeight/2)
        .setOpacity(0.1);
    
    
   
   
    layer.setPosition(layerPos);
    self.mainLayer.appendChild(layer);
   
    // FadeIn circles when Tapping
    
    
    self.tappingCircleArray = [];
    self.keyEventTappingCircleArray = [];
    for(i=0; i<4; i++){
         self.tappingCircleArray[i] = new lime.Sprite()
            .setSize(1.5*speakerHeight,1.5*speakerHeight)
            .setOpacity(0.5);
            
        
        layer.appendChild(self.tappingCircleArray[i]);
        
    }
    
    self.tappingCircleArray[0]
        .setFill('./img/greenCircle.png')
        .setPosition(speakerSpace/2+2*speakerHeight,speakerHeight+speakerHeight)
    self.tappingCircleArray[1]
        .setFill('./img/yellowCircle.png')
        .setPosition(speakerSpace/2-2*speakerHeight,speakerHeight+speakerHeight)
    self.tappingCircleArray[2]
        .setFill('./img/blueCircle.png')
        .setPosition(speakerSpace/2+2*speakerHeight,speakerHeight-speakerHeight)
    self.tappingCircleArray[3]
        .setFill('./img/redCircle.png')
        .setPosition(speakerSpace/2-2*speakerHeight,speakerHeight-speakerHeight)
    
    
         var fadeIn = new lime.animation.FadeTo(.5).setDuration(.4);
                
        self.tappingCircleArray[0].runAction(fadeIn);
        self.tappingCircleArray[1].runAction(fadeIn);
        self.tappingCircleArray[2].runAction(fadeIn);
        self.tappingCircleArray[3].runAction(fadeIn);
    
    /** create an audio button */
    this.audiobutton = new AudioPlayerButton(args.aSong,this.audioPressed);
    this.recordingIndicator = new RecordingIndicator(this,function(e){},"Hit circles");
    this.recordingIndicator.disable();
    
    
    
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
                    
                    if(!this.hasVoted){
                    tapRecorder.tapTimes = new Array();
                    tapRecorder.timedOut = true;
                    tapRecorder.fMax = null;
                    
                    if(self.recordingIndicator.enabled == true)
                        self.recordingIndicator.timeOut();
                    self.closeTapRecorder();
                    }
                    
                });

    this.time_left.setPosition(0, 0);
    this.progresslayer.appendChild(this.time_left);
    this.time_left.start();
    /*
     *  Progress Label
     */
    //this.progLbl = new lime.Label().
    //    setFontFamily('Trebuchet MS').setFontColor('#4f4f4f').setFontSize(40).
    //    setAnchorPoint(0.5, 0.5).setFontWeight(300).
    //    setText('time left:');
    
    //this.progLbl.setPosition(-50, -20);
    //this.progLbl.setSize(200,this.progLbl.getLineHeight());
    //this.progresslayer.appendChild(this.progLbl);

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
        
        
        if(this.isTapListeningStarted){
            for(i=0; i<4; i++)
                goog.events.unlistenByKey(self.keyEventTappingCircleArray[i]);


            goog.events.unlistenByKey(self.keyForKeyPressedListen);
        }
        
        // stop timers
        self.time_left.stop();
        lime.scheduleManager.unschedule(self.updatePlayerPanel,self);
    }

    //---------------------------- TAPPING RECORDER DESCRIPTION
        
        var tapRecorder = new Object();

        tapRecorder.tapTimes = new Array();
        
    
    // First click initialise the tapRecorder, after 8 Click the rythmRecorder is closed
    self.onClickFunction = function (e) {
        var d = new Date();
        var dateU = d.getTime();
        
        
        if( tapRecorder.tapTimes[0] == undefined){
            self.initTapRecorder(e);
        } 
        // The player has 9 second to record a rythm after the first tap.
        else if (tapRecorder.firstTapClientTime + 9000 > dateU) {
            self.updateTapRecorder(e);
            this.tapAnimation(e);
        } 
        else {
            self.updateTapRecorder(e);
            for(i=0; i<4; i++)
                    goog.events.unlistenByKey(self.keyEventTappingCircleArray[i]);

            goog.events.unlistenByKey(self.keyForKeyPressedListen);
            this.tapAnimation(e);
            
            
            tapRecorder.timedOut = false;
            tapRecorder.computeStatsFinal();
            
            this.recordingIndicator.stopRecord();
			     
            AudioPlayerButton.prototype.stopPlayingAudio();
            
            lime.scheduleManager.callAfter(function(){
            self.closeTapRecorder(e);
            },self,600);
        }
        
        return true;
        
    };
    
    
    
    // Initialise rythmRecorder
    this.initTapRecorder = function (e) {
        var d = new Date();
        var n = d.getTime(); 
        
        tapRecorder.firstTapClientTime = n;
        
        var hit = new Array();
        hit.push(0);
        hit.push(e.circleId);
        hit.push(e.keyCode);
        hit.push(e.charCode);
        
        tapRecorder.tapTimes[0] = hit;
        
        self.recordingIndicator.startRecord();
        
        
    };
    
    // Update tapRecorder : Add new time compute statistics
    this.updateTapRecorder = function (e) {
        var d = new Date();
        var n = d.getTime(); 
        var t = n - tapRecorder.firstTapClientTime;
        
        var hit = new Array();
        hit.push(t);
        hit.push(e.circleId);
        hit.push(e.keyCode);
        hit.push(e.charCode);
        
        tapRecorder.tapTimes.push(hit);
        
       
    };
    
    // Close and finish rythmRecording then send Data
    tapRecorder.computeStatsFinal = function () {
        
        var tempo = args.aSong['details']['tempo'];
        if(tempo == 0 || tempo == undefined)
            tempo = 120;
        
        var win = self.windower(tapRecorder.tapTimes);
        
        var winMeanNull = self.setMeanToZero(win); 
        
        var moddft = self.moduleOfDFT(winMeanNull);
        
        
        
        var maxCouple = self.getMaxOfFirstHalf(moddft);
        
        tapRecorder.fMax = new Object();
        tapRecorder.fMax.f = new Array();
        tapRecorder.fMax.val = new Array();
        tapRecorder.fMax.fRelative = new Array();
        tapRecorder.fMax.fWindow = new Array();
        
        for(var i=0; i<3; i++){
            
            tapRecorder.fMax.fWindow.push(maxCouple[0]); //Frequence max en Hz
            tapRecorder.fMax.f.push(maxCouple[0] * 1/9000 *1000); //Frequence max en Hz
            tapRecorder.fMax.val.push(maxCouple[1]);
            tapRecorder.fMax.fRelative.push( tempo / (tapRecorder.fMax.f[i] * 60));
            
            // Sent the frequency to 0 to find the next maximum.
            // And kill also the next multiple to avoid the multiples of the beat.
            // The process is suppose to give less point to someone just tapping the beat
            var fWin0 = maxCouple[0];
            var fWin = fWin0;
            var bandWidth = 2;
            var k = 0;
            while((fWin + bandWidth < win.length) && (fWin + bandWidth > 0) && (k<3)){
                for(var ii=0; ii<bandWidth; ii++){
                    moddft[fWin-ii] = moddft[fWin-ii]*k*0.3;
                    moddft[fWin+ii] = moddft[fWin-ii]*k*0.3;
                }
                fWin = fWin + fWin0;
                k++;
            }
            
            
            maxCouple = self.getMaxOfFirstHalf(moddft);
            
        }
        
    };
    
    
    this.closeTapRecorder = function (e) {
        if(this.hasVoted){
            console.log('ERROR: Trying to vote a second time.')
            return;
        }
        
        if(this.isTapListeningStarted){
        for(i=0; i<4; i++)
            goog.events.unlistenByKey(self.keyEventTappingCircleArray[i]);
        
        
        goog.events.unlistenByKey(self.keyForKeyPressedListen);
        }
        
        
        for(var i=0; i<4; i ++){
            var anim = new lime.animation.FadeTo(.0).setDuration(1);
            lime.animation.actionManager.stopAll(self.tappingCircleArray[i]);
            //this.tappingCircleArray[i].runAction(anim);
        }

        
        
        // assemble result
    
        var result = {
                            
                            // Coherency check
                            'songId': args.aSong.id,
                            
                            'matchid': game.client.userAuth.matchid,
                            'step': game.match.step,
                            'voteType': 'taprythm',
                            
                            // Proper results values
                            'startPlayerTime': self.startPlayerTime,
                            'firstTapClientTime': tapRecorder.firstTapClientTime,
                            'totalTime': self.time_left.timeRunning(),
                            'tapTimes': tapRecorder.tapTimes,
                            'jPlayerFirstTimeUpdateTime': self.audiobutton.getJPlayerFirstTimeUpdateTime(),
                            'jPlayerPlayTime': self.audiobutton.getJPlayerPlayTime(),
                            'fMax': tapRecorder.fMax,
                            'timedOut': tapRecorder.timedOut
                        };
                                
       
       game.client.syncConnection.sendData(result);
                
        this.hasVoted = true;
        self.finished();
        // DANIEL: terminate is called from outside the module automatically, 
        //         when all the players have finished!
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
    
    // ---------------- Listen to tapping
    
    /**
     * Handle a tap animation
     */
    this.tapAnimation = function(e){
                        
                if(e.circleId != 'wrongKey'){


                    var fadeCircle = new lime.animation.Sequence(
                        new lime.animation.FadeTo(.9).setDuration(.1),
                        new lime.animation.FadeTo(.5).setDuration(.3)
                        );
                            
                    var circleId = e.circleId;
                    var circle = self.tappingCircleArray[circleId];
                    
                
                    circle.runAction(fadeCircle);
                    self.recordingIndicator.tilt();
                    return true;
                }
                return false;
    }
    
    /**
     * Handle the tap event
     */
    this.onTap = function(e){
        
            if(this.hasVoted)
                return false;
            
            if(e.keyCode == 68 || e.charCode == 100)
                e.circleId = 3; // Red circle, key D
            else if(e.keyCode == 67 || e.charCode == 99)
                e.circleId = 1; // Yellow circle, key C
            else if(e.keyCode == 74 || e.charCode == 106)
                e.circleId = 2; // Blue circle, key J
            else if(e.keyCode == 78 || e.charCode == 110)
                e.circleId = 0; // Green circle, key N
            else
                e.circleId = 'wrongKey';
                
                
                
                if(e.circleId != 'wrongKey'){

                    self.onClickFunction(e);
                    return true;
                }
                return false;
                
                
                
        };
    
    this.onTouch = function(i){
               
                if(this.hasVoted)
                    return false;
                 
                var e = new Object();
                e.circleId = i;
                e.keyCode = 0;
                e.charCode = 'click';
                
                
                self.onClickFunction(e);
                
                return true;
        };
        
        this.windower = function (tapTimes){
            var windowed = new Array();
            var dt = 60000 / 2000; // F_Nyquist = 1000 BPM, Fs = 2000 / 60000
            windowed.length = 300; // 9000 ms recording tempo. Window of 30 ms.
           
              
            var t0 = tapTimes[0][0];
            
            for( var k=0; k < windowed.length; k++ ) {
                windowed[k] = 0;
            }
            
            for(var i=0; i < tapTimes.length; i++) {
              var t = tapTimes[i][0];
              var n = Math.floor(t/dt);
              if(n >= 0 && n < 300){
                windowed[n] += 1;
              }
            }
            
            
            
            return windowed;
        }
    
        this.moduleOfDFT = function (windowed) {
            var len = windowed.length;
            var output = new Array();

            for( var k=0; k < len; k++ ) {
              var real = 0;
              var imag = 0;
              for( var n=0; n < len; n++ ) {
                real += windowed[n] * Math.cos(-2*Math.PI*k*n/len);
                imag += windowed[n] * Math.sin(-2*Math.PI*k*n/len);
              }
              var mod = Math.pow(real,2) + Math.pow(imag,2);
              output.push( mod );
            }
            
            //Normalization of the DFT
            var sum = 0;
            for( var k=0; k < len; k++ )
              sum += output[k];
            for( var k=0; k < len; k++ )
              output[k] = output[k] / sum;
            
            return output;
        }
        
        this.getMaxOfFirstHalf = function (array) {
            var output = new Array();
            var len = array.length;
            
            var ind = -1;
            var val = 0;
            for( var k=0; k < len/2; k++ )
                if(array[k] > val){
                  ind = k;
                  val = array[k];
                }
             return [ ind , val ];
             
        }
        
        this.setMeanToZero = function (array) {
            var output = new Array();
            var len = array.length;
            
            var sum = 0;
            for( var k=0; k < len; k++ )
                sum += array[k];
            
            var mean = sum / len;
            
            for( var k=0; k < len; k++ )
                output[k] = array[k] - mean;
            
             return output;
             
        }


          
    // ---------------- Enable animations
    self.audiobutton.enabled = true;


     /*
     * Tell the Game that Module has been Loaded
     */
    game.client.asyncConnection.setPlayerState('onModule');


}
goog.inherits(TapRythm,Module);

//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('TapRythm', TapRythm);
