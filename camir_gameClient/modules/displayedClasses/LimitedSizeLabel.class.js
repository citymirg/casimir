goog.provide('LimitedSizeLabel');

goog.require('lime.Label');
/**
 * Little class to clean shorten long label that don't feet in their boxes.
 */
LimitedSizeLabel = function (sizeMax,threeDots){
    lime.Label.call(this);
    
    // Optionnal argument
    if(threeDots == null)
        threeDots = false;
    
    /* --- Constants --- */
    
    // Characters before cutting string
    this.sizeMax = sizeMax;
    
    // Three dots or one dots at the end.
    // Three dots qre more likely to be fore long strings.
    this.threeDots = threeDots;
    
    
    // Characters before cutting string
    this.string = "";
    
    // String at the end of the label
    this.endingString = "";
    
   
}
goog.inherits(LimitedSizeLabel,lime.Label);

/**
 * Static.
 * Stop all audio animation that exist
 */
LimitedSizeLabel.prototype.setEndingString = function (st) {
        
        this.endingString = st;
        
        this.setText(st);
        
    }
    
/**
 * Static.
 * Stop all audio animation that exist
 */
LimitedSizeLabel.prototype.setShortText = function (st) {
        var s = st;
        if(s == null || s == undefined)
            s = "Unknown";
        
        var len = s.length;
        
        // Shorten the string if too long.
        if(len > this.sizeMax){
            if(this.threeDots){
                s = s.substr(0,this.sizeMax -2);
                s = s + "...";
            }
            else {
                s = s.substr(0,this.sizeMax -1);
                s = s + ".";
            }
        }
        this.string = s + this.endingString;
        
        this.setText(this.string);
    }
    
    
    
goog.exportSymbol('LimitedSizeLabel', LimitedSizeLabel);