goog.provide('TapRythm_resultIntro');
goog.require('TapRythm_result');

goog.require('TutorialCanvas');

// entrypoint
TapRythm_resultIntro = function(args) {
    TapRythm_result.call(this,args);
    self = this;

    /** remember if this is a result module
     * @todo: make this a module variable
     **/
    isResult = true;

    this.tutCanvas = new TutorialCanvas(this);
    this.appendChild(this.tutCanvas);
    
    this.tutCanvas.popBubble('Improve complexity and accuracy to get more points.',
         350,80,100,90,'s', 1000, 20000);

    
    //self.btnLbl.setText('Round 1 of 3. Get ready!');
}
goog.inherits(TapRythm_resultIntro,TapRythm_result);

//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('TapRythm_resultIntro', TapRythm_resultIntro);
