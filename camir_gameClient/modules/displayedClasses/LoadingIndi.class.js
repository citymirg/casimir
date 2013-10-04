goog.provide('LoadingIndi');


goog.require('ProgressTimer');

goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Sprite');
goog.require('lime.Circle');


goog.require('lime.animation.RotateTo');
goog.require('lime.animation.Loop');
goog.require('lime.animation.Sequence');

/**
 * LoadingIndi visualising the loadingin between modules
 * 
 * @todo: make this a child of Timer
 */
LoadingIndi = function(pollInterval, runOutCallback){
    
    lime.Layer.call(this);
    
    var d = new Date();
    this.lastPollTime = d.getTime();    
    this.pollInterval = pollInterval;
    
    this.timer = new ProgressTimer(this.lastPollTime, this.pollInterval, runOutCallback) ;

    // animated circle
    this.img = new lime.Circle()
        .setSize(30,30)
        .setFill('./img/skin1/membrane_color.png')
        .setAnchorPoint(0.5,0.5)
        .setPosition(20,0);
    this.appendChild(this.img);
        
    this.lbl = new lime.Label();
    setMediumFont(this.lbl)
        .setAnchorPoint(0, 0.5)
        .setPosition(0,0)
        .setAlign("left")
        .setText(_('Loading'));
    this.appendChild(this.lbl);
    
    this.animation = new lime.animation.Loop(
                        new lime.animation.RotateTo(360)
                            .setDuration(this.pollInterval).setEasing(lime.animation.Easing.LINEAR)
                    );
    //    this.animation.play() ;
    this.img.runAction(this.animation);
};
goog.inherits(LoadingIndi, lime.Layer);

/**
 * Synch current progress value
 */
LoadingIndi.prototype.renew = function(){
    
    // get current time
    var d = new Date();
    this.lastPollTime = d.getTime();
    
    // give another bit of polling
    this.timer.serverStartTime = this.lastPollTime;
    this.timer.serverSync(this.lastPollTime);
    
    //@todo: make progresstimer easier
    this.timer.curTime = this.pollInterval;
    this.animation.play();

};

LoadingIndi.prototype.start = function(){
    
    // give another bit of polling
    this.renew();
    this.timer.start();
};


LoadingIndi.prototype.stop = function(){
    
    // give another bit of polling
    this.animation.stop();
    this.timer.stop();
};

LoadingIndi.prototype.show = function(){
    
    this.start();
    this.setOpacity(1);
};

LoadingIndi.prototype.hide = function(){
    
    this.setOpacity(0);
    this.stop();
};


goog.exportSymbol('LoadingIndi', LoadingIndi);