goog.provide('taptempoIntro');
goog.require('Module');
goog.require('TutorialCanvas');

/*
 * Basicooo takes a list of song ids and urls
 * @param array args [id1, id2, id3, url1, url2 url3]
 */
taptempoIntro = function(args){
    
    taptempo.call(this,args);

    var self = this;

    // tutorial canvas
    this.tutCanvas = new TutorialCanvas(this);
    this.appendChild(this.tutCanvas);
    
    


    var bubble = self.tutCanvas.popBubble('Listen and tap a regular pulse like a metronome.',
                                  280,200,-330,300,'e', 1000, 10000,'img/help_taptempo.gif');
    ///var image = new lime.Sprite().setFill('../img/help_taptempo.png');                    
    //bubble.appendChild(image);
        
        //return self.audiobutton.onClickFunction();
    
       

}
goog.inherits(taptempoIntro,taptempo);

//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('taptempoIntro', taptempoIntro);
