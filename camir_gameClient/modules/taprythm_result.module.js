goog.provide('TapRythm_result');
goog.require('Module_result');

//get requirements
goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Circle');
goog.require('lime.RoundedRect');
goog.require('lime.Polygon');
goog.require('lime.fill.LinearGradient');
goog.require('lime.ui.Scroller');

goog.require('lime.animation.Resize');
goog.require('lime.animation.Spawn');
goog.require('lime.animation.FadeTo');
goog.require('lime.animation.ScaleTo');
goog.require('lime.animation.MoveTo');
goog.require('lime.animation.MoveBy');
goog.require('lime.animation.RotateTo');
goog.require('lime.animation.Loop');

goog.require('PlayerScoreIndicator');
goog.require('TutorialCanvas');

goog.require('GeneralPurposeButton');
goog.require('BarDiagram');


// entrypoint
TapRythm_result = function(args) {
    
    Module_result.call(this,args);
    
    var detailsDiagram = new Array();
    for(i=0; i < this.playerRow.length; i++){
         
        var detailsLayer = this.playerRow[i].resultDetails;
        
            
       var frac0 = this.playerRow[i].result.relComp;
       var frac1 = this.playerRow[i].result.accuracy;
        
        console.log(frac0);
        detailsDiagram[i] = new BarDiagram('./img/barDiagram/2D',
        11,75,8,12,2,[7,7],[24,24],[69,59],[24,24],[7,7],[24,24],[0,0],[0,28],[frac0,frac1],[1,1]);
        //detailsLayer.label.setText('Complex:  '+ this.playerRow[i].result.complex);
        detailsDiagram[i].setPosition(60,-25);
        detailsLayer.appendChild(detailsDiagram[i]); 
        
        
        detailsLayer.labelComp = new lime.Label().setPosition(18,-10);
        detailsLayer.labelAcc = new lime.Label().setPosition(20,20);
        setSmallFont(detailsLayer.labelComp);
        setSmallFont(detailsLayer.labelAcc);
        
        detailsLayer.labelComp.setText(' Comp. ');
        detailsLayer.labelAcc.setText(' Acc. ');
        
        detailsLayer.appendChild(detailsLayer.labelComp);
        detailsLayer.appendChild(detailsLayer.labelAcc);
     }
     
         
    // kth logo for modules
    var pic5 = new lime.Sprite()
        .setFill('./img/' + 'kth-logo.png')
        .setPosition(-moduleWidth/2 + 30,moduleHeight/2)
        .setScale(0.7)
        .setAnchorPoint(0,0.5);
    this.mainLayer.appendChild(pic5);
}
goog.inherits(TapRythm_result,Module_result);


//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('TapRythm_result', TapRythm_result);