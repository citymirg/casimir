goog.provide('ScreenMod');

//get requirements

goog.require('lime.Layer');


/**
 * Screens are Layers which can be shown at the same time or not 
 * We use them for efficiency, as it allows for one scene to remain with 
 * common background and title graphics
 * 
 * @constructor
 * @extends lime.Layer
 *
 */
ScreenMod = function () {
    lime.Layer.call(this);

    /**
     * Variable useful in function definition.
     */
    var self = this;

    this.active = true;
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
    this.show = function (){
        this.setOpacity(1);
        this.enable();
    }
    
    this.hide = function(){
        /*
         * todo: replace by layer.setHidden(true)
         */
        this.setOpacity(0);
        this.disable();
    }
    
    this.enable = function(){
        this.active = true;
    }
    
    this.disable = function(){
        this.active = false;
    }
  
}
goog.inherits(ScreenMod,lime.Layer);



//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('ScreenMod', ScreenMod);