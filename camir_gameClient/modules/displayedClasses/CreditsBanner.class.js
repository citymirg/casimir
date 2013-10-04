goog.provide('CreditsBanner');

goog.require('lime.Label');
goog.require('lime.Sprite');
goog.require('lime.RoundedRect');

/**
 * Banner to show credits images
 * @constructor
 * @extends lime.Layer
 *
 * @todo: make this a child of Timer
 */
CreditsBanner = function(player) {
    lime.RoundedRect.call(this);
    
    this.setSize(130,380);
    this.setFill('#FFFFFF');
    this.setAnchorPoint(0.5,0);

    var ph = 25;
    
   
    var pic4 = new lime.Sprite()
        .setFill('./img/' + 'city-logo.png')
        .setPosition(0,ph)
        .setScale(0.5);
    this.appendChild(pic4);
    
    
    ph += 90;
    var pic5 = new lime.Sprite()
        .setFill('./img/' + 'kth-logo.png')
        .setPosition(0,ph)
        .setScale(0.5);
    this.appendChild(pic5);
    
    ph += 90;
    var pic1 = new lime.Sprite()
        .setFill('./img/' + '7digital-Logo-Master.png')
        .setPosition(0,ph)
        .setScale(0.4);
    this.appendChild(pic1);
    
    ph += 50;
    var pic2 = new lime.Sprite()
        .setFill('./img/' + 'magnatune_logo.gif')
        .setPosition(0,ph)
        .setScale(0.5);
    this.appendChild(pic2);
    
    ph += 50;
    var pic3 = new lime.Sprite()
        .setFill('./img/' + 'rovi_allmusic_logo.png')
        .setPosition(0,ph)
        .setScale(0.08);
    this.appendChild(pic3);
    
    ph += 50;
     var pic6 = new lime.Sprite()
        .setFill('./img/' + 'limejs-150x50.png')
        .setPosition(0,ph)
        .setScale(0.5);
    this.appendChild(pic6);


}
goog.inherits(CreditsBanner, lime.RoundedRect);


goog.exportSymbol('CreditsBanner', CreditsBanner);