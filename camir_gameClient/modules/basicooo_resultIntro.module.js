goog.provide('basicooo_resultIntro');
goog.require('basicooo_result');

// entrypoint
basicooo_resultIntro = function(args) {
    basicooo_result.call(this,args);
    self = this;

    /** remember if this is a result module
     * @todo: make this a module variable
     **/
    isResult = true;
    
    this.tutCanvas.popBubble('Chosen songs:',
         200,30,100,90,'s', 1000, 20000);

    this.tutCanvas.popBubble('Have you earned some points?',
         100,150,380,285,'w', 1000, 20000);

    
    //self.btnLbl.setText('Round 1 of 3. Get ready!');
}
goog.inherits(basicooo_resultIntro,basicooo_result);

//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('basicooo_resultIntro', basicooo_resultIntro);
