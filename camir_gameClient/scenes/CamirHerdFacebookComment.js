goog.provide('CamirHerdFacebookComment');

goog.require('QuickMenu');
goog.require('TutorialCanvas');

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
CamirHerdFacebookComment = function(game) {

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

    // don't load the facebook sdk again!' its loaded in facebookhelper.
    // unfortunately this doent work because fbxml destroys the application on some
    // android phones and other deveices :(
    this.textarea = goog.dom.createDom('div', 
        {'class':'fb-comments' ,
        'data-href':'https://www.facebook.com/pages/Spot-the-Odd-Song-Out/320976971339231' ,
        'data-width': frameWidth-20+'px' ,
        'data-num-posts':'10' });

//    this.textarea =goog.dom.htmlToDocumentFragment('<div class="fb-comments" data-href="https://www.facebook.com/pages/Spot-the-Odd-Song-Out/320976971339231" data-width="' + (frameWidth-20).toString()+'px"' +'data-num-posts="10"></div>');
    this.mainFrame.appendChild(this.textarea);

    this.mainLayer.appendChild(this.mainFrame);
    
    
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
goog.inherits(CamirHerdFacebookComment,lime.Scene);


//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('CamirHerdFacebookComment', CamirHerdFacebookComment);