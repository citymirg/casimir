goog.provide('RecordingIndicator');

goog.require('GeneralPurposeButton');
goog.require('lime.Sprite');
goog.require('lime.animation.FadeTo');
/**
 * Check button, information senders.
 */
RecordingIndicator = function (parent,onClickFunction,text){
    
    GeneralPurposeButton.call(this,parent,onClickFunction, "", text, null, false);
    
    var self = this;
    
    this.enabled = false;
    
    this.bg = new lime.Sprite();
    
    this.bg
        .setSize(150,56)
        .setFill('./img/recordingtransp.png')
        .setAnchorPoint(0.5, 0.5)
        .setPosition(0,-20);
    this.appendChild(this.bg);

    this.lbl = this.text;
    setSmallFont(this.lbl);
    this.lbl.setAnchorPoint(0.5, 0.5)
        .setText(text)
        .setPosition(15,5).setSize(moduleWidth - 40,60);
        

    this.circle = new lime.Sprite();
    this.circle
        .setSize(150,56)
        .setFill('./img/redrec.png')
        .setAnchorPoint(0.5, 0.5)
        .setPosition(0,-20)
        .setOpacity(0.2);
    this.appendChild(this.circle);
    
    
    this.bigCircle = new lime.Sprite();
    this.bigCircle
        .setSize(128,128)
        .setFill('./img/bigRedCircle.png')
        .setAnchorPoint(0.5, 0.5)
        .setPosition(-55,-8)
        .setOpacity(0.);
    this.appendChild(this.bigCircle);
    
    
    
    this.startRecord = function (){
        

        this.lbl.setText(" REC. ");
        var appear = new lime.animation.FadeTo(1).setDuration(0.5);
        var bigAppear = new lime.animation.FadeTo(0.4).setDuration(0.5);
        
        appear.addTarget(this.circle);
        bigAppear.addTarget(this.bigCircle);
        
        appear.play();
        bigAppear.play();
    }
    
    this.stopRecord = function (){
        this.disable();
        
        var disappear = new lime.animation.FadeTo(0.2).setDuration(0.5);
        var bigdisAppear = new lime.animation.FadeTo(0).setDuration(0.5);
        
        disappear.addTarget(this.circle);
        bigdisAppear.addTarget(this.bigCircle);
        
        disappear.play();
        bigdisAppear.play();
    }


    this.timeOut = function () {
        this.lbl.setText(" TIME OUT ");
        
        this.stopRecord();
    }
    
    this.tilt = function () {
                var X1 = Math.random() - 0.5;
                var X2 = Math.random() - 0.5;
                var X3 = Math.random() - 0.5;
                
                var dL = 15;
                
                
                var tilt =  new lime.animation.Sequence(
                    new lime.animation.MoveBy(X1*dL,0).setDuration(.01),
                    new lime.animation.MoveBy(0,X2*dL).setDuration(.01),
                    new lime.animation.MoveBy(-X1*dL,0).setDuration(.01),
                    new lime.animation.MoveBy(0,-X2*dL).setDuration(.01)
                    );
               this.runAction(tilt);
               
    }
    
    this.appendChild(this.lbl);
    
    

}
goog.inherits(RecordingIndicator,lime.Layer);

goog.exportSymbol('RecordingIndicator', RecordingIndicator);