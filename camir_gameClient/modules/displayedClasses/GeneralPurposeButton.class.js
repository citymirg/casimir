goog.provide('GeneralPurposeButton');

goog.require('lime.Layer');
goog.require('lime.Label');

goog.require('lime.Sprite');

goog.require('lime.animation.Resize');
goog.require('lime.animation.Spawn');
goog.require('lime.animation.FadeTo');
goog.require('lime.animation.ScaleTo');
goog.require('lime.animation.MoveTo');
goog.require('lime.animation.MoveBy');
goog.require('lime.animation.RotateTo');
goog.require('lime.animation.Sequence');
goog.require('lime.animation.Loop');


/**
 * GeneralPurposeButton class definition.
 * @todo: structure: make checkbutton inherit from this
 * @param onClickFunction wcallback for button clicked
 * @param string img picture to display
 * @param string text text
 * @param promote click to underlying layers default = false?
 */
GeneralPurposeButton = function(parent, onClickFunction, img, text, transclickable, enableButtonAnimation){
    lime.Sprite.call(this);
    
    this.onClickFunction = onClickFunction;
    this.parent = parent;

    var self = this;
    
    if(transclickable == null)
        transclickable = false;

    if(enableButtonAnimation == null)
        enableButtonAnimation = true;
    /**
     * LimeJS Circle object that is going to be animated.
     * 
     * Requires : lime.Circle
     */
    
    this.setSize(logoSize,logoSize);
    

    if(!isEmpty(img))
       this.setFill(img);
 
    this.text = new lime.Label();
    setMediumFont(this.text).setText(text);
    this.appendChild(this.text);

    goog.style.setStyle(this.text.getDeepestDomElement(), {'cursor': 'pointer'});
    goog.style.setStyle(this.getDeepestDomElement(), {'cursor': 'pointer'});

    this.enabled = true;
    //-------------
    // Methods
    //-------------
    
    this.disable = function(){
        this.enabled = false;
        this.setOpacity(0.6);
        goog.style.setStyle(this.getDeepestDomElement(), {'cursor': 'default'});
        goog.style.setStyle(this.text.getDeepestDomElement(), {'cursor': 'default'});
    }
    
    this.enable = function(){
        this.enabled = true;
        this.setOpacity(1);
        goog.style.setStyle(this.getDeepestDomElement(), {'cursor': 'pointer'});
        goog.style.setStyle(this.text.getDeepestDomElement(), {'cursor': 'pointer'});
    }
    
    this.show = function(){
        this.enable();
    }
    
    this.hide = function(){
        this.disable();
        this.setOpacity(0);
    }
    
    /*
     * @todo: FIXME check click behaviour for gestures etc
     */
    this.clickListener = 
        goog.events.listen(this,['mousedown','touchstart'],function(e){
                
                if (this.enabled){
                    // animate the button
                    if(enableButtonAnimation){
                        
                    this.runAction(new lime.animation.Sequence(
                        new lime.animation.ScaleTo(1.2).setDuration(0.1),
                        new lime.animation.ScaleTo(1).setDuration(0.3)
                    ));
                    lime.scheduleManager.callAfter( function(){
                                    // run onclick function
                                    this.onClickFunction.call(this.parent);
                                },this,(0.4)*1000.0);
                    }
                    else{
                        this.onClickFunction.call(this.parent);
                    }
                    
                    //<- /to stop this event being processed by underlying layers
                    if(!transclickable)
                        e.event.stopPropagation(); 

                    }
                

            }
        ,false,this); // tis "this" specifies the this identity within the function            

}
goog.inherits(GeneralPurposeButton,lime.Sprite);

goog.exportSymbol('GeneralPurposeButton', GeneralPurposeButton);