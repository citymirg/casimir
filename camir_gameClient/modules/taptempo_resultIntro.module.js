goog.provide('TapTempo_resultIntro');
goog.require('taptempo_result');
goog.require('TutorialCanvas');


// entrypoint
TapTempo_resultIntro = function(args) {
    taptempo_result.call(this,args);
    self = this;

    /** remember if this is a result module
     * @todo: make this a module variable
     **/
    isResult = true;

    this.tutCanvas = new TutorialCanvas(this);
    this.appendChild(this.tutCanvas);
    

    this.tutCanvas.popBubble('To more the pattern and regular the more points you get.',
         200,200,+380,220,'w', 1000, 20000);

    
    //self.btnLbl.setText('Round 1 of 3. Get ready!');
}
goog.inherits(TapTempo_resultIntro,taptempo_result);

//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('TapTempo_resultIntro', TapTempo_resultIntro);
