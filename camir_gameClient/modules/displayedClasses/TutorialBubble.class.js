goog.provide('TutorialBubble');

goog.require('lime.Label');
goog.require('lime.Label');
goog.require('lime.Circle');
goog.require('lime.animation.FadeTo');
goog.require('lime.animation.ScaleTo');

/*
 * shows a speechbubble
 * @param text text to be shown
 * @param width
 * @param height
 * @param posx position of the bubble
 *  @param posy position of the bubble
 * @param orientation "n" "s" "e" "w"
 * @param delay delay in ms to display the bubble
 * @param length in ms to display the bubble
 * @param imgUrl in ms to display the bubble
 */
TutorialBubble = function(canvas,lbltext,width,height,posx,posy,orientation,imgUrl){
    lime.Layer.call(this);
    
    if(imgUrl == undefined || imgUrl == ''){
        var imgUrl = '#FFFFFF';
    }
    
    this.setPosition(posx,posy)
        .setOpacity(0)
        .setScale(0.2);

    var self = this;

    this.rect = new lime.RoundedRect()
        .setSize(width,height)
        .setFill(imgUrl)
        .setStroke(2,'#000000');
      
    
    this.appendChild(this.rect);

    this.text = new lime.Label();
    setMediumFont(this.text)
        .setSize(width,height)
        .setText(lbltext);
    this.rect.appendChild(this.text);

    /*
     * Create and positiobn pointerbubbles
     */
    this.pointerb1 = new lime.Circle().setSize(22,22);
    this.pointerb2 = new lime.Circle().setSize(15,15);
    switch(orientation){
        case 'n':
            this.pointerb1.setPosition(0,-height/2-width/10);
            this.pointerb2.setPosition(0,-height/2-2*width/10);
            break;
            //format text
            //this.text.setAlign("center");
        case 's':
            this.pointerb1.setPosition(0,height/2+22);
            this.pointerb2.setPosition(0,height/2+2*22);
            break;
        case 'e':
            this.pointerb1.setPosition(width/2+22,0);
            this.pointerb2.setPosition(width/2+2*22,0);
            break;
            //format text
            //this.text.setAlign("right");
        case 'w':
            this.pointerb1.setPosition(-width/2-22,0);
            this.pointerb2.setPosition(-width/2-2*22,0);
            break;
        default:
            this.pointerb1.setOpacity(0);
            this.pointerb2.setOpacity(0);
            break;
    }

    // set other pointer properties
    this.pointerb2.setFill('#FFFFFF')
        .setStroke(2,'#000000');
    this.appendChild(this.pointerb2);

    this.pointerb1.setFill('#FFFFFF')
                .setStroke(2,'#000000');
    this.appendChild(this.pointerb1);

    // hide and show implementation
    // @todo: use animation for popup
    this.show = function (){
        self.runAction(new lime.animation.FadeTo(1).setDuration(1.0));
        self.runAction(new lime.animation.ScaleTo(1).setDuration(1.0));
        //this.enable();
    }

    this.hide = function(){
        self.runAction(new lime.animation.FadeTo(0).setDuration(1.0));
        self.runAction(new lime.animation.ScaleTo(0.2).setDuration(1.0));
        //this.disable();
    }
    
}
goog.inherits(TutorialBubble,lime.Layer);

goog.exportSymbol('TutorialBubble', TutorialBubble);