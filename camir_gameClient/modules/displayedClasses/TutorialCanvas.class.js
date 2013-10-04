 goog.provide('TutorialCanvas');
 
goog.require('TutorialBubble');
goog.require('lime.Layer');
goog.require('lime.scheduleManager');
/*
 * Canvas For tutorial Speechbuubles
 *
 */
TutorialCanvas = function(parent){
    lime.Layer.call(this);
    
    
    this.activeBubbles = new Array();
    
    /*
     * shows a speechbubble
     * @param text text to be shown
     * @param width
     * @param height
     * @param posx position of the bubble
     *  @param posy position of the bubble
     * @param orientation "n" "s" "e" "w" 
     * @param delay delay in ms to display the bubble
     * @param length in ms to display the bubble
     * @param imgUrl string url of an image inserted in the background
     * @returns bubble id
     */
    this.popBubble = function(text,width,height,posx,posy,orientation, delay, length, urlImg){

        //create bubble
        var bubble = new TutorialBubble(this,text,width,height,posx,posy,orientation,urlImg);
        this.activeBubbles.push(bubble);
           

        // append to this layer
        this.appendChild(bubble);

        /*
         * timed popup / goaway
         */
        if(delay > 0){
            lime.scheduleManager.callAfter(bubble.show,bubble,delay);
        }else{
             bubble.show();
        }

        if(length > 0){
            lime.scheduleManager.callAfter(bubble.hide,bubble,delay+length);
            //@todo: remove bubble from stack as well.
            // use "hasbeenShown, in bubble for testing in remobveall---
        }
        return bubble;
    }


    this.removeAllBubbles = function(){

        /*
         * @todo: use bubble array to hide and unschedule all leftover bubbles
         */
        //this.activeBubbles.forEach(lime.scheduleManager.unschedule,bubble.hide,bubble);
    }

}
goog.inherits(TutorialCanvas,lime.Layer);

goog.exportSymbol('TutorialCanvas', TutorialCanvas);