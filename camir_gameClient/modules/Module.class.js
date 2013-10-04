goog.provide('Module');


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

goog.require('goog.math.Vec2');
goog.require('goog.events');
goog.require('goog.events.KeyCodes');
goog.require('goog.events.KeyHandler');

goog.require('lime.scheduleManager');
    
goog.require('GeneralPurposeButton');
goog.require('ProgressTimer');
goog.require('PlayerScoreIndicator');

goog.require('TutorialBubble');
goog.require('TutorialCanvas');

goog.require('GeneralPurposeButton');
goog.require('GeneralPurposeSelector');
goog.require('AudioPlayerButton');
goog.require('AudioPlayerBanner');
goog.require('CheckButton');
goog.require('ProgressTimer');
goog.require('LoadingIndi');
goog.require('SelectionAvatar');
goog.require('RecordingIndicator');

goog.require('PlayerScoreIndicator');
goog.require('VolumeControl');
goog.require('ResultSpeakers');
goog.require('QuickMenu');



    
    
/**
 * Modules are mutuallly exclusive screens which are invoked by the server
 * on demand
 * @constructor
 * @extends lime.Layer
 *
 */
Module = function() {
    lime.Layer.call(this);

    /**
     * Variable useful in function definition.
     */
    var self = this;

    var isResult = false;

    var data;
    
    this.closing = false;

    /**
     * Every lime object of this module is appened to the frame child of this layer.
     * @note: the module is centered horizontally
     */
    this.mainLayer = this.setPosition(moduleWidth/2,0);
    
    /**
     * Background design of the frame of the module.
     */

    /**
     * Every lime object of this module is appened to this frame.
     */
//    this.mainFrame = new lime.RoundedRect()
//        .setSize(2/4*moduleWidth, 1.5/4*moduleHeight)
//        .setRadius(50,1)
//        .setStroke(2,50,0,0)
//        .setFill('#FFFFFF')
//        .setOpacity(1);
   
    //------------------
    // Display functions.
    //------------------
//    this.mainLayer.appendChild(this.mainFrame);
    
    this.mainLayer.setOpacity(0.);

    //-----------------------
    // RUN and CLOSE METHODS
    //-----------------------
    
    /**
     * Time spent of module running animation and closing animation.
     * 
     * @todo: STRUCTURE: put this in a general settings file
     */
    var animTime = 0.5;
    
    /**
     * Running method of the module.
     */
    this.run = function (){
        if (game.hidden){
            console.log('Trying to change module while out of focus');
            if(!(game.match == undefined)){game.match.stopMatch();}

            // @todo: DEBUG resetting the focus here;
            game.resetFocus();
            
            game.showMainMenu();
        }

        if(game.match.module !=  null){
            
            /* @todo: FIX THIS IT CAUSES A HANG WHEN CLOSING-*/
            //console.log('Trying to launch module but module is running');
            game.match.module.close();
            
            lime.scheduleManager.callAfter( function(){
                
                this.instantiate();
                
                // dont show "loading"
                game.match.loading.hide();
                
            },this,(2*animTime)*1000.0);
        
        } else {
            
        this.instantiate();
        
        // dont show "loading"
        game.match.loading.hide();
        }
    }

    
    this.instantiate = function(){
        game.match.module = this;
        
        game.match.appendChild(this.mainLayer);
        
        var fadeIn = new lime.animation.FadeTo(1).setDuration(animTime);
        fadeIn.addTarget(this.mainLayer);   
        fadeIn.play();
    }
    

    /**
     * Closes the module.
     */
    this.close = function (){
        
        // check if we are already closing the module
        if (this.closing == true){
            return;
        }
        // MUTEX: is this already closing?
        this.closing = true;
        
        // call modules close function
        this.terminate();
        
        var fadeOut = new lime.animation.FadeTo(0).setDuration(animTime);
        fadeOut.addTarget(self.mainLayer);   
        fadeOut.play();
        

        /*
         * inform server that we can receive the next module
         * @todo: What If KICKED ?decide if to restart or show end screen when out
         */
//        var res = game.client.asyncConnection.getPlayerState({
//            success: setstate,
//            error: null
//            });
            
        lime.scheduleManager.callAfter(function(){
            game.match.closeModule();
            
             // show "loading"
            game.match.loading.show();
        },this,animTime);
        }
//    /**
//     * @destructor
//     **/
//    this.destroy = function () {
//
//        for(t in audioStack){
//            t.destroy();
//        }
//        for(t in checkButtonStack){
//            t.destroy();
//        }
//        delete this.mainLayer;
//        delete this;
//    }
}
goog.inherits(Module,lime.Layer);

//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('Module', Module);