goog.provide('BarDiagram');

goog.require('lime.Layer');
goog.require('lime.Sprite');
goog.require('lime.Label');
goog.require('lime.Circle');

goog.require('goog.math.Vec2');
goog.require('goog.events');
goog.require('goog.events.KeyCodes');
goog.require('goog.events.KeyHandler');

goog.require('lime.scheduleManager');
    

/**
 * This class display a bar diagram.
 * All the dimension should be given taking the first corner of the first bar as reference.
 * The idea it to get the number of pixel when drawing the jpg and then LimeJs will adapt the siwes to the page. 
 * 
 * @todo: make this a child of Timer
 */
BarDiagram = function(url,baseSizeX,baseSizeY,baseAnchPtPixelX,baseAnchPixelPtY,barNumber,sizeBigX,sizeBigY,sizeBarsX,sizeBarsY,sizeEndX,sizeEndY,posBarsX,posBarsY,fracX,fracY){
    
    lime.Layer.call(this);
    
    // Base drawing
   this.base = new lime.Sprite();
   this.base
        .setSize(baseSizeX,baseSizeY)
        .setFill(url + '_' + 'base.png')
        .setAnchorPoint(baseAnchPtPixelX/baseSizeX,baseAnchPixelPtY/baseSizeY)
        .setPosition(0,0);
   this.appendChild(this.base,4); 
    
   
   this.bigs = new Array();
   this.bars = new Array();
   this.ends = new Array();
   
   
   for(var k=0; k<barNumber; k++){
       this.bigs[k] = new lime.Sprite();
       this.bigs[k]
        .setSize(sizeBigX[k],sizeBigY[k])
        .setFill(url + '_big' + k + '.png')
        .setPosition(posBarsX[k]+1,posBarsY[k])
        .setAnchorPoint(1,0);
       
       this.bars[k] = new lime.Sprite();
       this.bars[k]
        .setSize(sizeBarsX[k]*fracX[k],sizeBarsY[k]*fracY[k])
        .setFill(url + '_mid' + k + '.png')
        .setPosition(posBarsX[k],posBarsY[k])
        .setAnchorPoint(0,0);
        
        
       this.ends[k] = new lime.Sprite();
       this.ends[k]
        .setSize(sizeEndX[k],sizeEndY[k])
        .setFill(url + '_end' + k + '.png')
        .setAnchorPoint(0,0)
        .setPosition(posBarsX[k] + sizeBarsX[k]*fracX[k] -1,posBarsY[k]*fracY[k]);
   

   this.appendChild(this.bigs[k]);
   this.appendChild(this.bars[k]);
   this.appendChild(this.ends[k]);
}

};
goog.inherits(BarDiagram, lime.Layer);


BarDiagram.prototype.load = function(){
};


goog.exportSymbol('BarDiagram', BarDiagram);