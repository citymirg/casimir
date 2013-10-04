goog.provide('CamirHerdFeedback');


goog.require('QuickMenu');
goog.require('TutorialCanvas');
goog.require('PlayerHighScore');
goog.require('GeneralPurposeButton');
goog.require('CreditsBanner');
//get requirements

goog.require('lime.Scene');

goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Sprite');
goog.require('lime.Circle');
goog.require('lime.RoundedRect');



/*
 * This is the Feedback Scene from the CamirHerd Game
 */
CamirHerdFeedback = function(game) {

    lime.Scene.call(this);

    var self = this;

   
    this.mainLayer = new lime.Layer();
    /*
     *  Background.
     *  they overlap so the picture can be stretched
     */
    this.bgtop = new lime.Sprite().setSize(960,640).
        setFill('./img/skin1/bg_behindsp.jpg').setAnchorPoint(0,0);
        this.mainLayer.appendChild(this.bgtop);


    this.titleLbl = new lime.Label();
    setLargeFont(this.titleLbl)
        .setText(_('Feedback'))
        .setAnchorPoint(0.5, 0.5)
        .setPosition(moduleWidth/2,50)
        .setSize(moduleWidth,40);
    this.mainLayer.appendChild(this.titleLbl);

    this.toMainMenu = new GeneralPurposeButton(game,game.showMainMenu,'','')
                .setPosition(moduleWidth/2,50)
                .setSize(moduleWidth/2,40);
    this.mainLayer.appendChild(this.toMainMenu);

    /*
     * Quick menu
     */
    this.quickMenu = new QuickMenu(game)
                     .setPosition(moduleWidth-55,50);
    this.mainLayer.appendChild(this.quickMenu);


    var frameWidth = 615;
    var frameHeight = 400;

    // create lime layer
    this.mainFrame = new lime.RoundedRect()
        .setAnchorPoint(0, 0).setOpacity(1)
        .setSize(frameWidth,frameHeight) // centred
        .setFill('#f2f2f2')
        .setPosition(moduleWidth/2-frameWidth/2,moduleHeight/2-frameHeight/2)
        .setStroke(1,'#EEEEEE');
    this.mainLayer.appendChild(this.mainFrame);
    this.mainFrame.getDeepestDomElement().setAttribute("style", "-moz-box-shadow: 2px 2px 3px #888; -webkit-box-shadow:2px 2px 3px #888; box-shadow: 2px 2px 3px #888;");


    /*
     * Labels for Points at Bottom
     */
//   simple label
//   var input1 = document.createElement('input');
//   this.mainFrame.appendChild(input1);

//    iFrame
//    var iframe = goog.dom.createDom('iframe', { 'src': 'form.html' });
//    this.mainFrame.appendChild(iframe);

    this.textarea = goog.dom.createDom('textarea', {'class':'bugs'});
    this.mainFrame.appendChild(this.textarea);
    //input1.setPosition(-frameWidth/2,-frameHeight/2);
    goog.style.setStyle(this.textarea, {'margin': '10px',
                                 'width': frameWidth-20+'px',
                                 'height': frameHeight-20+'px',
                                 'font-size':'18px'});

    this.mainLayer.appendChild(this.mainFrame);
    
    /*
     *  submit button:
    *   This is a checkbutton replicate
    */
    this.checkbutton = new GeneralPurposeButton(this, function(){

        var text = self.textarea.value;
        if (!isEmpty(text)){
            var sent = game.client.syncConnection.provideFeedback(text);
            console.log('Feedback: ' + sent + text);
        }

        // show main menu
        game.showMainMenu.call(game)}
    , './img/skin1/choose.png',_('submit'));
    

    // submit button
    this.checkbutton.text.
        setFontFamily('Helvetica').setFontColor('#FFFFFF').setFontSize(22).
        setFontWeight(200).setPosition(-10,10);
    this.checkbutton.text.getDeepestDomElement().setAttribute('style','letter-spacing:3pt;');
    
    this.checkbutton.setPosition(moduleWidth/2,moduleHeight/2+frameHeight/2+40);
    this.checkbutton.setSize(150,56);
    this.mainLayer.appendChild(this.checkbutton);

    // like button
    this.fbLikeButton = new FaceBookLikeButton(70,27,false);
    this.fbLikeButton.setPosition(moduleWidth/4 ,moduleHeight/4 + 25);
    this.mainLayer.appendChild(this.fbLikeButton);

    /*
     * creditsbanner
     */
    this.credits = new CreditsBanner();
    this.credits.setPosition(moduleWidth/2+frameWidth/2+80,200);
    this.mainLayer.appendChild(this.credits);

    
    
/*
 * Explanation Bubbles
 */
    this.tutCanvas = new TutorialCanvas(this);
    this.mainLayer.appendChild(this.tutCanvas);

    this.tutCanvas.popBubble(_('Please type here:'),
         200,30,moduleWidth/2,100,'s', 0, 0);

    this.tutCanvas.popBubble(_('How did you like the game?'),
         150,100,90,200,'e', 500, 0);

    this.tutCanvas.popBubble(_('Were the tasks easy to understand?'),
         150,100,90,330,'e', 2000, 0);

    this.tutCanvas.popBubble(_('Any problems playing?'),
         150,100,90,460,'e', 3000, 0);

        // show main Layer
    this.appendChild(this.mainLayer);

}
goog.inherits(CamirHerdFeedback,lime.Scene);


//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('CamirHerdFeedback', CamirHerdFeedback);