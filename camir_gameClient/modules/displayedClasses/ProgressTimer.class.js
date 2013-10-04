goog.provide('ProgressTimer');

goog.require('lime.fill.LinearGradient');
goog.require('lime.RoundedRect');
/**
 * Progressbar to show time left value
 * @constructor
 * @extends lime.RoundedRect
 * @param serverStartTime the time on server when the module was started
 * @param maxTime the time to count down in seconds
 * @param  callbackFun function to call when time is up
 * 
 * @todo: make this a child of Timer
 * @todo: CAUTION: this class has the flaw that timeLeft() actually returns the 
 *          positively increasing time running, while timeRunning() returns the 
 *          decreasing timeLeft
 */
ProgressTimer = function(serverStartTime, maxTime, callbackFun) {
    lime.RoundedRect.call(this);

    this.callbackFun = callbackFun;
    var WIDTH = 185,
        HEIGHT = 18,
        RADIUS = 5,
        BORDER = 3;

    // current state
    this.running = false;

    /*
     * To avoid calling the server exactly at the same time we introduce some jitter here
     */
    this.maxTime = maxTime + Math.random()*maxDecongestTime; // maximal time
    this.curTime = maxTime;
    
    this.maxServerCurTime = 0;
    
    this.serverStartTime = serverStartTime; // set Server time in second
    this.serverTime = serverStartTime; // set Server time in second
    
    this.progress = 0; // progress between 0 - 1

    this.setSize(WIDTH, HEIGHT).setRadius(RADIUS).setAnchorPoint(0, .5);
    this.setFill(new lime.fill.LinearGradient().addColorStop(0, 0x15, 0x15, 0x15, .6).addColorStop(1, 0x1e, 0x1e, 0x1e, .4));

    // inner balue var
    var inner = new lime.RoundedRect().setRadius(RADIUS).setSize(WIDTH- 2 * BORDER, HEIGHT- 2 * BORDER).setFill('#000ca8').
        setAnchorPoint(0, .5).setPosition(BORDER, 0);
    this.appendChild(inner);

    inner.setFill(new lime.fill.LinearGradient().addColorStop(0, '#ff6c00').addColorStop(1, '#ffa461'));

    this.width = WIDTH;
    this.inner = inner;

};
goog.inherits(ProgressTimer, lime.RoundedRect);

/**
 * Set current progress value
 * @param {number} value Current progress value.
 */
ProgressTimer.prototype.setProgress = function(value) {
    this.progress = value;
    this.inner.runAction(new lime.animation.Resize(this.width * value, this.inner.getSize().height).setDuration(.4));
};

/**
 * Return current progress value
 * @return {number} value.
 */
ProgressTimer.prototype.getProgress = function() {
    return this.progress;
};

/**
 * Subtract one second from left time in timed mode
 */
ProgressTimer.prototype.decreaseTime = function() {
    this.curTime--;
    if (this.curTime <= 0) {
        console.log('Module Time left <= 0');
        /** 
         * BANG
         * @todo: end module
         * 
         */
        this.stop();
        this.callbackFun();
    }
    // update progressbar
    this.setProgress(this.curTime / this.maxTime);
};

ProgressTimer.prototype.start = function() {
    if(this.running) return;

    //decrease time on every second
    lime.scheduleManager.scheduleWithDelay(this.decreaseTime, this, 1000);
    this.running = true;
};

ProgressTimer.prototype.stop = function() {
    if(!this.running) return;
    
    //decrease time on every second
    lime.scheduleManager.unschedule(this.decreaseTime, this);
    this.running = false;
};

ProgressTimer.prototype.timeRunning = function() {
    //decrease time on every second
    return this.maxTime - this.curTime;
};

ProgressTimer.prototype.maxServerTimeLeft = function() {
    //decrease time on every second
    return this.maxServerCurTime;
};


ProgressTimer.prototype.timeLeft = function() {
    //decrease time on every second
    return this.curTime;
};

ProgressTimer.prototype.serverSync = function(serverTime) {
    // Stor the server Time given
    this.serverTime = serverTime;
    
    // Update the client time values 
    /*
     * @todo: this could be cleaner. e.g. always update the 
     * curtime to servercurtime and get rid of parallel time observation
     */
    
    var serverCurTime = this.maxTime - (this.serverTime - this.serverStartTime);
    
    console.log('time left :' +  this.curTime + ' .---.  With sync on server :' + serverCurTime);
    
    // update to server time only if server is faster
    if(serverCurTime < this.curTime){
        this.curTime = serverCurTime;
        
    if (this.maxServerCurTime == 0)
        this.maxServerCurTime = serverCurTime;
    
    }
        
    
    // set the progress bar to the right value.
    this.setProgress(this.curTime / this.maxTime);
    
    return this.curTime;
};

goog.exportSymbol('ProgressTimer', ProgressTimer);