/**
 * Count the number of jPlayers created to set, jPlayers Ids.
 */
var njPlayers = 0;
incr_njPlayers = function () {
    njPlayers += 1;
    return  njPlayers
}

function hiddenjPlayer(googParent,url)
    {
        // counts the number of jplayers
        this.id = incr_njPlayers();
        
        var self = this;

        // describe the id and class of html object
        this.el = new Object();
        this.el.id = 'jp_jplayer_'+this.id;
 //       this.el.class = 'jPlayerHiddenDiv';
        
        // create a div in the html body
        this.jQueryDomEl = jQuery('<div/>', this.el).addClass('jPlayerHiddenDiv').appendTo(document.body);
        
           
        this.jQueryDomEl = jQuery("#"+this.el.id);    
        // console.log('jQ: '+ jQuery("#"+this.el.id));
        
        /**
         * Stores the times when the player is laumching the music.
         */
        this.firstUpdateTime = new Array();
        this.playTime = new Array();
        
        
        /*
         * Create ajPlayer within the div we have just created
         */
        this.player = 
            
            this.jQueryDomEl.jPlayer({
		ready: function () {
                        $(this).jPlayer("setMedia", {
                            mp3: url
                        });
                    
                },
		swfPath: "../toolboxes/jPlayer/",
                solution: "html,flash",
                loop: "false"
            }); 
        
        // console.log('player: '+this.player);


        /*
         * The html object is created
         * Define member functions
         */

        this.stop = function () {
           // console.log("player paused");
            this.player.jPlayer("pause");
        }
        
        this.play = function () {
            this.player.jPlayer("play", 0);
        }
        /*
         * @param vol Volume of this player in % (0-100)
         */
        this.setVolume = function (vol) {
            this.player.jPlayer("volume", vol);
        }
        this.setVolume(80);
        
        this.googDomEl = goog.dom.getElement('jp_jplayer_'+this.id);

        
        //
        // this moves the class as a child to the googParent class specified
        //
        this.appended = goog.dom.appendChild(googParent, this.googDomEl);
        this.jQueryDomEl = jQuery("#"+this.el.id);    
        
        this.listenToFirstTimeUpdate = function () {
                this.player.one(jQuery.jPlayer.event.timeupdate,
                    function(event) {
                        var t = new Date().getTime();
                        console.log('firstUpdateTime :' + t);
                        self.firstUpdateTime.push(t) ;
                    });
                var t = new Date().getTime();
                console.log('Listening :' + t);
        }
        
        
        this.player.bind(jQuery.jPlayer.event.play,
                function(event) {
                     var t = new Date().getTime();
                     console.log('playTime :' + t);
                     self.playTime.push(t) ;
                    
                    self.listenToFirstTimeUpdate();
                });
  
    }