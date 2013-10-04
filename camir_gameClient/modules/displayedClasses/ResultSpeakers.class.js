goog.provide('ResultSpeakers');

goog.require('lime.Sprite');
goog.require('lime.Layer');
/**
 * Progressbar to show time left value
 * @constructor
 * @extends lime.Layer
 * @param song: song 
 * @param position: number of song chosen (0-2)
 * @todo: make this a child of Timer
 */
ResultSpeakers = function(songId, songs) {
    lime.Layer.call(this);
/*
 * This is an actual state of a Player table
 * id "71", points "0", sessionId "72", state "moduleDone", totalPoints "0"
 * 
 */
  this.speakers = new Array();
  
  var speakerdist = 50; 
  
  for(var i = 0; i < 3; i++) {
   
    this.speakers[i] = new lime.Sprite().
        setAnchorPoint(0, 0.5).setOpacity(1)
        .setSize(35,48)
        .setFill('./img/skin1/speaker_small_results.png')
        .setPosition(i*speakerdist,0);
        
    if(songId != songs[i].id){
         this.speakers[i].setOpacity(0.3);
    }   
        
    this.appendChild(this.speakers[i]);
  }

}
goog.inherits(ResultSpeakers, lime.Layer);


goog.exportSymbol('ResultSpeakers', ResultSpeakers);