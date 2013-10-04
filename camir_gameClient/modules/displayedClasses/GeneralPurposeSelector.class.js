goog.provide('GeneralPurposeSelector');

goog.require('GeneralPurposeButton');

goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Sprite');

goog.require('lime.animation.FadeTo');

/**
 * GeneralPurposeButton class definition.
 * @todo: structure: make checkbutton inherit from this
 * @param parent:
 * @param optionTbl: array(object) with array[i].value, array[i].label
 */
GeneralPurposeSelector = function(parent, onClickFunction, optionTbl, width, height, sideClickFunction){
    lime.Sprite.call(this);
    var self = this;

    this.parent = parent;

    this.optionTbl = optionTbl;
    this.optionPtr = 0;

    this.enabled = true;
    /**
     * LimeJS Circle object that is going to be animated.
     *
     * Requires : lime.Circle
     */

    this.setSize(width,height);

   
    //-------------
    // FWD AND BACKWD Methods
    //-------------
    
    // rewinds the displayed value by one
    this.goBack = function(){
        if (!this.enabled)
            return;
        this.optionPtr --;
        if (this.optionPtr < 0)
            this.optionPtr = this.optionTbl.length-1;
        
        this.updateText();
        
        // execute sideClickFunction
        if(sideClickFunction != undefined){
            sideClickFunction.call(parent);
        }
    }
    this.goFwd = function(){
        if (!this.enabled)
            return;
        this.optionPtr++;
        if (this.optionPtr == this.optionTbl.length)
            this.optionPtr = 0;
        this.updateText();
        
        // execute sideClickFunction
        if(sideClickFunction != undefined){
            sideClickFunction.call(parent);
        }
    }

    this.backBtn = new GeneralPurposeButton(this,this.goBack,'','<<<')
                .setPosition(width/16,0)
                .setSize(width/8,height);
    this.appendChild(this.backBtn);

    this.fwdBtn = new GeneralPurposeButton(this,this.goFwd,'','>>>')
                .setPosition(width-width/16,0)
                .setSize(width/8,height);
    this.appendChild(this.fwdBtn);

    //-------------
    // Main button
    //-------------
    this.btn = new GeneralPurposeButton(parent,onClickFunction,'',this.optionTbl[this.optionPtr].label)
                .setPosition(width/2,0)
                .setSize(width/2,height);
    setMediumFont(this.btn.text);
    this.appendChild(this.btn);

    // animates and updates the text field
    this.updateText = function(){
        this.btn.text.runAction(new lime.animation.FadeTo(0).setDuration(animClickTime));

        // delay the change until anim is over;
        lime.scheduleManager.callAfter(function(){
            
                this.btn.text.setText(this.optionTbl[this.optionPtr].label);
                this.btn.text.runAction(new lime.animation.FadeTo(1).setDuration(animClickTime));
        },this,400);

    }

    // return the actual value
    this.actValue = function(){
        return this.optionTbl[this.optionPtr].value;
    }

    // return the actual value
    this.actLabel = function(){
        return this.optionTbl[this.optionPtr].label;
    }

    // enable and disable functions
    this.disable = function(){
        this.btn.disable();
        this.enabled = false;
        this.setOpacity(0.6);
    }
    this.enable = function(){
        this.btn.enable();
        this.enabled = true;
        this.setOpacity(1);
    }

}
goog.inherits(GeneralPurposeSelector,lime.Sprite);

goog.exportSymbol('GeneralPurposeSelector', GeneralPurposeSelector);