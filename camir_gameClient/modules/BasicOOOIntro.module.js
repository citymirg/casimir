goog.provide('basicoooIntro');

// entrypoint
goog.require('basicooo');
goog.require('TutorialCanvas');



/*
 * Basicooo takes a list of song ids and urls
 * @param array args
 */
basicoooIntro = function(args){
    
    basicooo.call(this,args);

    var self = this;


    // tutorial bubbles
    this.bubblePlay = this.tutCanvas.popBubble('Click on the speaker symbols to listen to each clip',
                              600,30,0,100,'s', 1000, 20000);

    this.bubbleTl = this.tutCanvas.popBubble('Time left',
                              115,30,-350,450,'s', 5000, 40000);


    /*
    * hack: we overwrite the callback for the audiobuttons
    */
    for(i=0; i<3; i++){
         
        // create audio and check button
        self.audiobutton[i].onClickFunction = function(){
            self.audioPressed();
            if (array_unique(listenIdSeq).length == 3){
                self.bubblePlay.hide();
                self.bubbleChoose = self.tutCanvas.popBubble('2. Choose the clip which seems the most different to the others',
                                  400,70,0,350,'', 0, 10000);
            }
        };
     }


    // @todo: DEBUG disable extra layers
    return;
    
}
goog.inherits(basicoooIntro,basicooo);

//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('basicoooIntro', basicoooIntro);
