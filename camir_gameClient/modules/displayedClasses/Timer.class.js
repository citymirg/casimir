goog.provide('Timer');


/**
 * Timer to count time running 
 * @constructor 
 * @param double accuracy of the counting process in seconds
 */
Timer = function(accuracy) {
   
    // accuracy in seconds
    this.accuracy = accuracy;
        
    this.curTime = 0.0;

/**
 * Subtract one second from left time in timed mode
 */
    this.increaseTime = function() {
        
        this.curTime+=accuracy;
    };

    this.start = function() {
        //decrease time on every second
        lime.scheduleManager.scheduleWithDelay(this.increaseTime, this, accuracy/1000.0);
    };

    this.stop = function() {
        //decrease time on every second
        lime.scheduleManager.unschedule(this.increaseTime, this);
    };
    
    this.reset = function() {
        //decrease time on every second
        this.stop();
        this.curTime = 0;
    };


    this.timeRunning = function() {
        //decrease time on every second
        return this.curTime;
    };

};

goog.exportSymbol('Timer', Timer);