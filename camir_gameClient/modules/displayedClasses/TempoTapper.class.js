goog.provide('TempoTapper');

goog.require('lime.Sprite');
goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('GeneralPurposeButton');

goog.require('goog.events');
goog.require('goog.events.KeyCodes');
goog.require('goog.events.KeyHandler');
/**
 * Check button, information senders.
 */
TempoTapper = function  () {
    
    GeneralPurposeButton.call(this);
    
    var self = this;
    
    // Label that Write the status of the tempo tapper
    this.label = new lime.Label().
        setFontFamily('Trebuchet MS').setFontColor('#4f4f4f').setFontSize(20)
        .setFontWeight(100).setOpacity(1).
        setText("Tap to start recording tempo");
    
    this.appendChild(this.label);
    
    
    //The tempo tapper object has three stats : waiting, recording, done
    this.status = "waiting";
    
    
    // Computation variables
    this.beatTimes = new Array(); // Store absolute time of each tapped beat
    this.beatTempo = new Array(); // Store absolute time of each tapped beat
    
    this.stats = new Object();
    this.stats.total = 0; // Total taping duration
    this.stats.average = 0; // Average BPM tapped
    this.stats.max = 0; // Maximum deviation tapped
    
    //Tapping Event Listening
    
    //Statistics updating on beat
    this.updateTimeStats = function () {
        var d = new Date();
        var n = d.getTime(); 
        
        this.beatTimes.push(n);
        var nbB = this.beatTimes.length;
        
        
        this.timeStats.total = this.beatTimes[nbB] - this.beatTimes[0]; // Total tapping duration
        
        this.timeStats.average = (nbB-1)*this.timeStats.average; (this.beatTimes[nbB] - this.beatTimes[nbB-1]); // Average BPM tapped
        this.timeStats.average +=  this.beatTimes[nbB] - this.beatTimes[nbB-1];
        this.timeStats.average *= nbB;
        
        
        this.timeStats.max = max(this.timeStats.max,this.beatTimes[nbB] - this.beatTimes[nbB-1]); // Maximum deviation tapped
        
       
    }
    
    this.init = function () {
        var d = new Date();
        var n = d.getTime(); 
        
        this.beatTimes.push(n);
        
        this.status = "recording";
        this.label.setText("Recording tempo..." + this.stats.total );
        
    }
    
    this.close = function () {
        this.clickListener = 
        goog.events.listen(self,['mousedown','touchstart','keypress'],function(e){
            }
        );
        
        this.status = "done";
        this.label.setText("Tempo registred : " + this.stats.total );
    }
    //Key tapped function
    this.onBeat = function () {
        if(this.beatTimes.length == 0){
            this.init();
        } else if(this.beatTimes.length < 8){
            this.updateTimeStats();
        } else {
            this.close();
        }
        
        
        
    }
    
    this.onSpacePressedFunction = this.onBeat;
    this.onClickFunction = this.onBeat;
    //Method to close en the listenning of tempo
    
}
goog.inherits(TempoTapper,GeneralPurposeButton);

goog.exportSymbol('TempoTapper', TempoTapper);