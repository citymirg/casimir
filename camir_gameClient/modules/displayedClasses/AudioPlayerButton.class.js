/**
 * Static array of all the audio anim ation running.
 * As one player is playing, this stack has a length between 1 and 0. 
 *
 */
goog.provide('AudioPlayerButton');

// General UI scripts -->

goog.require('Timer');

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

    





var playingAudioStack = new Array();

    
/*
 * Keep track of the playing sequence
 */
var listenIdSeq = new Array();
var listenTimeSeq = new Array();
var audioBtnSeq = new Array();
/**
 * AudioPlayerButton class definition.
 */
AudioPlayerButton = function(song, onClickFunction, identifier){
    lime.Sprite.call(this);
    
    var self = this;
                
    if(identifier != undefined) 
        this.identifier = identifier;
    else this.identifier = 0;

    
    this.songId = song.id;
    
    this.isPlaying = false;
    
    this.onClickFunction = onClickFunction;
   
    this.jPlayerFirstTimeUpdateTime = new Array();
    this.jPlayerPlayTime = new Array();
    
    /**
     * LimeJS Circle object that is going to be animated.
     * 
     * Requires : lime.Circle
     */
    this.setSize(196,253);
    this.setFill('./img/skin1/speaker.png');
    
    /**
     * The object is an hidden jPlayer in a div.
     **/
    this.hiddenPlayer = new hiddenjPlayer(this,song.url);
    
    this.enabled = true;

    // timer to count the time this sound is running
    this.timer = new Timer(0.01);
    
    /*
     * Further Graphics
     */
    this.topCircle = new lime.Circle()
        .setSize(52,52)
        .setFill('./img/skin1/circle_color.png')
        .setAnchorPoint(0.5,0.5)
        .setPosition((196/2),60);
        
    this.appendChild(this.topCircle);
    
    // play indication
    this.playIndi = new lime.Circle()
        .setSize(75,75)
        .setFill('./img/skin1/membrane_color.png')
        .setAnchorPoint(0.5,0.5)
        .setPosition((196/2),165);
    this.appendChild(this.playIndi);
 
    //-------------
    // PLAY Animations
    //-------------

    /**
     * This define the animation of the audio button while playing.
     */
    this.animation=  new lime.animation.Loop(
                        new lime.animation.RotateTo(-360)
                            .setDuration(5).setEasing(lime.animation.Easing.LINEAR)
                    );

    
    /**
     * Play the Animation and the audio with the jPlayer.
     */
    this.play = function () {
        this.hiddenPlayer.setVolume(volume);
        this.hiddenPlayer.play();
        this.isPlaying = true;

        /*
         * Record timing for Song and append to general list
         */ 
        this.timer.start();
        listenTimeSeq.push(game.match.module.time_left.timeRunning());
        listenIdSeq.push(this.songId);
        
        audioBtnSeq.push(this.identifier);
              
        // start the audio
        playingAudioStack.push(this);
        
        
        //animation;
        
        this.playIndi.runAction(
                    new lime.animation.Sequence(
                        new lime.animation.ScaleTo(.9).setDuration(.2),
                        new lime.animation.ScaleTo(1).setDuration(.2)
                    )
                );
                    
        this.playIndi.runAction(this.animation);
        
    }
    
    
    /**
     * Animation of vanishment of other check button when clicked
     */   
    this.vanish = function(){
        this.disable();
        this.runAction(new lime.animation.FadeTo(.0).setDuration(.4));
    }
    
     /**
     * Animation of vanishment of other check button when clicked
     */   
    this.disable = function () {
        this.stop();
        this.enabled = false;
    }
    
         /**
     * Animation of vanishment of other check button when clicked
     */   
    this.enable = function () {
        this.enabled = true;
    }
    
    
    /**
     * Stop jPlayer, Timer and Animation.
     */
    this.stop = function () {
        this.hiddenPlayer.stop();
        
        this.timer.stop();

        
        this.isPlaying = false;
        
        
        // animation
        this.animation.stop();   
        
        // return to starting position
        this.playIndi.runAction(new lime.animation.ScaleTo(1).setDuration(0.3));
        this.playIndi.runAction(new lime.animation.RotateTo(360).setDuration(0.5));
        
    }
    
    this.clickListener = 
        goog.events.listen(this,['mousedown','touchstart'],function(){
                        
                if (!self.enabled){
                    return 1;
                }
                
                
                if(self.isPlaying){
                    AudioPlayerButton.prototype.stopPlayingAudio();
                } else {
                    AudioPlayerButton.prototype.stopPlayingAudio();
                    self.play(); 
                }
                
                if (!(this.onClickFunction == null)){
                    this.onClickFunction();
                }
                
            }
        );

    // set cursor pointer
    goog.style.setStyle(this.getDeepestDomElement(), {'cursor': 'pointer'});

}
goog.inherits(AudioPlayerButton,lime.Sprite);

/**
 * Static.
 * Stop all audio animation that exist
 */
AudioPlayerButton.prototype.stopPlayingAudio = function () {
        if(playingAudioStack.length > 0){
            playingAudioStack[0].stop();
            playingAudioStack.shift();
        }
    }
    
/**
 * Sets all playing Volumes to actual volume
 */
AudioPlayerButton.prototype.updateAllVolume = function() {
    for(var i=0; i < playingAudioStack.length; i++){
        playingAudioStack[i].hiddenPlayer.setVolume(volume);
    }
}


/*
 * @return array sequence of song ids and absolute time they were started 
 */
AudioPlayerButton.prototype.getListenSeq = function () {
    
    return {'listenIdSeq': listenIdSeq,'listenTimeSeq': listenTimeSeq};
}

AudioPlayerButton.prototype.reset = function () {
    audioBtnSeq.length = 0;
    listenIdSeq.length = 0;
    listenTimeSeq.length = 0;
    playingAudioStack.length = 0;
    
}

AudioPlayerButton.prototype.getJPlayerFirstTimeUpdateTime = function () {
    
    return this.hiddenPlayer.firstUpdateTime ;
    
}

AudioPlayerButton.prototype.getJPlayerPlayTime = function () {
    
    return this.hiddenPlayer.playTime ;
    
}

goog.exportSymbol('AudioPlayerButton', AudioPlayerButton);