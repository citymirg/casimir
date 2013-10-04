goog.provide('PlayerScoreIndicator');

goog.require('lime.Layer');
goog.require('lime.Circle');


/**
 * Progressbar to show time left value
 * @constructor
 * @extends lime.Layer
 * @param song: song 
 * @param position: number of song chosen (0-2)
 * @todo: make this a child of Timer
 */
PlayerScoreIndicator = function(score) {
    lime.Layer.call(this);
/*
 * This is an actual state of a Player table
 * id "71", points "0", sessionId "72", state "moduleDone", totalPoints "0"
 * 
 */
  this.circles = new Array();
  
  var circledist = 32; 
  var circlecolors = new Array("#ff431a","#ff9000", "#fffc00", "#b6ff1a", "#1ac1ff", "#e71aff");
  
  var numCircles = circlecolors.length;
  var pointsPerCircle = maxScore / numCircles;
  
  for(var i = 0; i < numCircles; i++) {
   
    this.circles[i] = new lime.Circle().
        setAnchorPoint(0, 0.5)
        .setSize(16,16)
        .setPosition(i*circledist,0)
        .setStroke(2,"#b9b9b9");
        
    if(score >= (i+1) * pointsPerCircle){
        this.circles[i].setFill(circlecolors[i]);
    } else{
        this.circles[i].setFill("#d8d8d8");
    }
        
    this.appendChild(this.circles[i]);
  }

}
goog.inherits(PlayerScoreIndicator, lime.Layer);/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



goog.exportSymbol('PlayerScoreIndicator', PlayerScoreIndicator);