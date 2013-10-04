goog.provide('VolumeControl');

goog.require('lime.Layer');
goog.require('lime.Sprite');
goog.require('lime.RoundedRect');
goog.require('goog.events');

// global volume variable
var volume = 0.8;

/**
 * Progressbar to show time left value
 * @constructor
 * @extends lime.Layer
 * @param song: song
 * @param position: number of song chosen (0-2)
 * @todo: make this a child of Timer
 */
VolumeControl = function() {
    lime.Layer.call(this);

    var self = this;
/*
 * This is an actual state of a Player table
 * id "71", points "0", sessionId "72", state "moduleDone", totalPoints "0"
 *
 */
  this.stepSize = 0.2;

  this.height = 200;

  this.speakerSmall = new lime.Sprite().
        setAnchorPoint(0.5,1).setOpacity(1)
        .setSize(35,48)
        .setFill('./img/skin1/speaker_small_results.png')
        .setPosition(0,this.height)
        .setOpacity(0.4);
  this.appendChild(this.speakerSmall);

  this.speakerBig = new lime.Sprite().
        setAnchorPoint(0.5,0).setOpacity(1)
        .setSize(35,48)
        .setFill('./img/skin1/speaker_small_results.png')
        .setPosition(0,0);
  this.appendChild(this.speakerBig);

  this.panel = new lime.RoundedRect()
        .setSize(30, (volume)*(this.height-(2*58)))
        .setAnchorPoint(.5,1)
        .setFill('#f2f2f2')
        .setRadius(8)
        .setPosition(0,(this.height-58));
  this.appendChild(this.panel);


  /*
   * Instance Functions
   * this standard callback uses isPlayinGStack to live control the
   * volume of running jplayers
   */
  this.volumeCallback = function(){
        self.panel .setSize(30, (volume)*(this.height-(2*58)))
      AudioPlayerButton.prototype.updateAllVolume();
  }

/*
 * @todo: use generalpurposebuttons here!
 */
  this.clickListenerSmall =
    goog.events.listen(this.speakerSmall,['mousedown','touchstart'],function(e){
            if (volume > 0){
               volume -= self.stepSize;
            }
            self.volumeCallback();
        }
    );

  this.clickListenerBig =
    goog.events.listen(this.speakerBig,['mousedown','touchstart'],function(e){
            if (volume < 1){
               volume += self.stepSize;
            }
            self.volumeCallback();
        }
    );


}
goog.inherits(VolumeControl, lime.Layer);



goog.exportSymbol('VolumeControl', VolumeControl);