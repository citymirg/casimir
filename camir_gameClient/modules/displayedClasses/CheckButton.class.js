goog.provide('CheckButton');

//get requirements

goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Sprite');


goog.require('lime.animation.FadeTo');
goog.require('lime.animation.ScaleTo');
goog.require('lime.animation.MoveTo');
goog.require('lime.animation.MoveBy');


/**
 * Global array of checkbuttons
 */
var checkButtonStack = new Array();


/**
 * Check button, information senders.
 */
CheckButton= function(songId){
    
    lime.Sprite.call(this);
    checkButtonStack.push(this);
    
    var self = this;
    
    this.enabled = true;
    
    // save associated song id
    this.songId = songId;
    
    this.but = this.setSize(150,56).setFill('./img/skin1/choose.png');

    this.lbl = new lime.Label().
        setFontFamily('Helvetica').setFontColor('#FFFFFF').setFontSize(22).
        setText(_('choose')).setAnchorPoint(0.5, 0.5).setFontWeight(200)
        .setPosition(75,55).setSize(moduleWidth,60);
        
    this.lbl.getDeepestDomElement().setAttribute('style','letter-spacing:3pt;');

    // set pointer
    goog.style.setStyle(this.getDeepestDomElement(), {'cursor': 'pointer'});

    this.appendChild(this.lbl);
    
    //-------------
    // Animations 
    //-------------
    
    
//    this.mouseOver = game.match.listenOverOut(this,function(e){
//          //  this.but.setFill('./img/choosetransp.png');
//        },
//        function(e){
//         //   this.but.setFill('./img/choosetranspnoarrow.png');
//        });
//
    /**
     * Animation of a button when clicked
     */
    this.scaleOnClick = function () {
        this.but.runAction(
            new lime.animation.Sequence(
                new lime.animation.ScaleTo(.5).setDuration(.2),
                new lime.animation.ScaleTo(1).setDuration(.2)
            )
        );
        
    }
            
    /**
     * Animation of vanishment of other check button when clicked
     */   
    this.vanish = function () {
        this.but.runAction(new lime.animation.FadeTo(.0).setDuration(.4));
    }
    
    this.disable = function(){
        this.enabled = false;
        goog.style.setStyle(this.getDeepestDomElement(), {'cursor': 'default'});

    }
    

}
goog.inherits(CheckButton,lime.Sprite);
goog.exportSymbol('CheckButton', CheckButton);