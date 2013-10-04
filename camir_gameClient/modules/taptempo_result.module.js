goog.provide('taptempo_result');
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


// entrypoint
taptempo_result = function(args) {
    
    Module_result.call(this,args);
    

    for(i=0; i < this.playerRow.length; i++){
         
        var detailsLayer = this.playerRow[i].resultDetails;
        
        detailsLayer.labelBPM = new lime.Label().setPosition(15,0);
        detailsLayer.labelError = new lime.Label().setPosition(20,20);
        setSmallFont(detailsLayer.labelBPM);
        setSmallFont(detailsLayer.labelError);
        
        
        var aColor = new Array();
        aColor.push('#EB0037');
        aColor.push('#D01B3B');
        aColor.push('#B53640');
        
        aColor.push('#9A5245');
        aColor.push('#806D4A');
        aColor.push('#65884F');
        
        aColor.push('#4AA454');
        aColor.push('#2FBF59');
        aColor.push('#15DB5E');
        
        var x = this.playerRow[i].result.regularityPts;
        if(x > 1.99)
            x = 1.99;
        x = x * 9 /2;
        var color = aColor[8 - Math.floor(x)];
        detailsLayer.labelError.setFontColor(color);
        
        
        x = this.playerRow[i].result.agreementPts ;
        if(x > 1.99)
            x = 1.99;
        x = x * 9 /2;
        
        color = aColor[8 - Math.floor(x)];
        detailsLayer.labelBPM.setFontColor(color);
        
        
        detailsLayer.labelBPM.setText(this.playerRow[i].result.averageBPM + ' BPM ');
        detailsLayer.labelError.setText(this.playerRow[i].result.percentRegularity + ' % ');
        
        detailsLayer.appendChild(detailsLayer.labelBPM);
        detailsLayer.appendChild(detailsLayer.labelError);
     } 
     
    // kth logo for modules
    var pic5 = new lime.Sprite()
        .setFill('./img/' + 'kth-logo.png')
        .setPosition(-moduleWidth/2 + 30,moduleHeight/2)
        .setScale(0.7)
        .setAnchorPoint(0,0.5);
    this.mainLayer.appendChild(pic5);
}
goog.inherits(taptempo_result,Module_result);


//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('taptempo_result', taptempo_result);