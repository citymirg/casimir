
/*
 * This is the GameFrame Class. It contains
 * the basic setup of limejs and general Graphic constants
 *
 * @todo: STRUCTURE Move the communication functions to the Game client.
 *
 */


//set main namespace
goog.provide('GameFrame');

//get requirements
goog.require('lime.Director');
goog.require('lime.Scene');

goog.require('lime.Layer');
goog.require('lime.Label');

goog.require('goog.events');
goog.require('goog.events.KeyCodes');
goog.require('goog.events.KeyHandler');

goog.require('lime.scheduleManager');
    


    //-----------
    //CONSTANTS
    //-----------
   /** Whole module Width. */
    var moduleWidth = 960;
   /** Whole module Height. */
    var moduleHeight = 640;
    
   /** Duration of Click animation. */
    var animClickTime = .2;
   /** Button sizes. */
    var logoSize = 150;
    


    // Polling interval
    // @todo: put this in a cionfiguration file
    var interval = 5000;
    
    
GameFrame = function (){
    var self = this;
    
    //------------------
    // LimeJS   Objects
    //------------------
    this.director = new lime.Director(document.body,moduleWidth,moduleHeight);
   
    this.director.setDisplayFPS(false);

    /**
     * Create and activate scene
     */
    this.scene = new lime.Scene();
    // set current scene active
    this.director.replaceScene(this.scene);
   
    
   
    /*
     *Function called at the closing of the game
     * Temporarily commented out as it requires the user to confirm
     * "go away from this page"
     */
//    window.onbeforeunload = function (e) {
//        e = e || window.event;
//
//        // For IE<8 and Firefox prior to version 4
//        if (e) {
//            self.client.setPlayerState('out');
//            e.returnValue = 'You have been disconnected from the server.';
//        }
//
//        // For Chrome, Safari, IE8+ and Opera 12+
//        self.client.setPlayerState('out');
//        return 'You have been disconnected from the server.';
//        };

    
this.listenOverOut = (function(){


/*
 * Handler For mouseover
 * 
 * @todo: put htis in an addon file
 * Usage: game.listenOverOut(shape,function(e){ console.log('over'); }, function(e){ console.log('out'); });
 * Advice welcome about how to have the same result with more LimeJS/Closure style API.
 */
    var moveHandler = function(e){
        for(var i in self.scene.registeredOverOut_){
            var item = self.scene.registeredOverOut_[i];
            var shape = item[0];
            if(!shape.inTree_) continue;
            var insideShape = shape.hitTest(e);
            if(!shape.insideShape_ && insideShape && goog.isFunction(item[1])){
                item[1].call(shape,e);
            }
            if(shape.insideShape_ && !insideShape && goog.isFunction(item[2])){
                item[2].call(shape,e);
            }
            shape.insideShape_ = insideShape;
        }
    };

    return function(shape,over,out){
        if(shape==self.scene) return; //scene itself is always full

        if(!self.scene.registeredOverOut_){
             self.scene.registeredOverOut_ = {};
        }

        var uuid = goog.getUid(shape);

        if(!over && !out) //clear if empty
            delete self.scene.registeredOverOut_[uuid];

        if(!self.scene.isListeningOverOut_){
            goog.events.listen(self.scene,"mousemove",moveHandler,false,self.scene);
            self.scene.isListeningOverOut_ = true;
        }

        self.scene.registeredOverOut_[uuid] = [shape,over,out];
    }
    })();

    /*
     * detect page visibility and change
     */
    this.hiddenAPI = "hidden";
    this.resetFocus = function(){
        this.hidden = false;
    }
    this.resetFocus();

     this.onchange = function(evt) {
        var body = document.body;
        evt = evt || window.event;

        if (evt.type == "focus" || evt.type == "focusin"){
            body.className = "visible";
            self.hidden = false;
        }
        else if (evt.type == "blur" || evt.type == "focusout")
            {
                body.className = "hidden";
                self.hidden = true;
            }
        else if(this[self.hiddenAPI]){
            body.className = "hidden";
            self.hidden = true;
        }else{
            body.className = "visible";
            self.hidden = false;
        }
    }

    // Standards:
    if ((self.hiddenAPI = "hidden") in document)
        document.addEventListener("visibilitychange", self.onchange);
    else if ((self.hiddenAPI = "mozHidden") in document)
        document.addEventListener("mozvisibilitychange", self.onchange);
    else if ((self.hiddenAPI = "webkitHidden") in document)
        document.addEventListener("webkitvisibilitychange", self.onchange);
    else if ((self.hiddenAPI = "msHidden") in document)
        document.addEventListener("msvisibilitychange", self.onchange);

    // IE 9 and lower:
    else if ('onfocusin' in document)
        document.onfocusin = document.onfocusout = self.onchange;
    // All others:
    else
        window.onfocus = window.onblur = self.onchange;

    // jquery alternatives
    // does the user see the module right now?
//    this.focussed = true;
//    $(window).blur(function(){
//         self.focussed = false;
//    });
//    $(window).focus(function(){
//         self.focussed = true;
//    });
}

//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('GameFrame', GameFrame);