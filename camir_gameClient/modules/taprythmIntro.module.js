goog.provide('TapRythmIntro');
goog.require('TapRythm');

goog.require('TutorialCanvas');

/*
 * Basicooo takes a list of song ids and urls
 * @param array args [id1, id2, id3, url1, url2 url3]
 */
TapRythmIntro = function(args){
    
    TapRythm.call(this,args);

    var self = this;

    // tutorial canvas
    this.tutCanvas = new TutorialCanvas(this);
    this.appendChild(this.tutCanvas);
    
   

    var bubble = self.tutCanvas.popBubble('Reproduce the main rhythmic pattern. Repeat it during 9`. Use four fingers.',
                                  256,192,0,150,'s', 1000, 10000,'img/help_taprythm.gif');
    ///var image = new lime.Sprite().setFill('../img/help_taptempo.png');                    
    //bubble.appendChild(image);
        
        //return self.audiobutton.onClickFunction();
    
       

}
goog.inherits(TapRythmIntro,TapRythm);

//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('TapRythmIntro', TapRythmIntro);
