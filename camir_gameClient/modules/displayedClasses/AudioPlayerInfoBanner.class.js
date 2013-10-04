goog.provide('AudioPlayerBanner');


// General UI scripts -->
goog.require('LimitedSizeLabel');


goog.require('GeneralPurposeButton');


goog.require('lime.Layer');
goog.require('lime.Label');
goog.require('lime.Circle');
goog.require('lime.RoundedRect');

goog.require('lime.animation.Resize');
goog.require('lime.animation.Spawn');
goog.require('lime.animation.FadeTo');
goog.require('lime.animation.ScaleTo');
goog.require('lime.animation.MoveTo');
goog.require('lime.animation.MoveBy');
goog.require('lime.animation.RotateTo');


/**
 * Banner to show song info after has been chosen
 * @constructor
 * @extends lime.RoundedRect
 * @param song
 *
 */
AudioPlayerBanner = function(song) {
    lime.RoundedRect.call(this);

    var self = this;

    this.setSize(180,135);
    this.setFill('#f2f2f2');
    this.setStroke(1,'#EEEEEE');
    this.buySongUrl = '';


    // title and artist indicator visible after choosing
    this.lblArtist = new LimitedSizeLabel(27,true);
    this.lblArtist
        .setEndingString(':');
    this.lblArtist
        .setShortText(song.details.artist);
    
    setSmallFont(this.lblArtist)
        .setAnchorPoint(0,0)
        .setSize(180,40)
        .setPosition(0,10)
        .setAlign("center");
        
        
    this.appendChild(this.lblArtist);
    
    this.lblTitle = new LimitedSizeLabel(27,true);
    this.lblTitle.setShortText(song.details.title );
    
    setSmallFont(this.lblTitle)
        .setAnchorPoint(0,0)
        .setPosition(0,50)
        .setSize(180,40)
        .setAlign("center");
        
    this.appendChild(this.lblTitle);

    /*
     * Buy stuff
     *
     */
    this.lblBuy = new lime.Label();
    setSmallFont(this.lblBuy)
        .setAnchorPoint(0,0)
        .setPosition(10,110)
        .setText(_('Info:'));
    this.appendChild(this.lblBuy);

    this.buySong = function(){
        window.open(self.buySongUrl, '_blank');
        window.focus();
    }

    // get the track buy url from sevenload
    var trackid = getURLParameter(song.url,'trackid');
    /*
    * we use the trackid to see if this is sevenload or magnatagatune
    * @todo: get db from server? 
    */
    if (!isEmpty(trackid))
        this.buyButton = new GeneralPurposeButton(this,this.buySong ,'img/7digital-Logo-Master.png','');
    else
        this.buyButton = new GeneralPurposeButton(this,this.buySong ,'img/magnatune_logo.gif','');
    
    this.buyButton.setPosition(40+60+10,120)
    this.buyButton.setSize(100,25);
    this.buyButton.disable();
    this.appendChild(this.buyButton);


    this.setUrl = function(data){
        var url = $(data).find('track').children('url').text();
        if (!isEmpty(url)){
            console.log(url);
            self.buySongUrl = url;
            self.buyButton.enable();
        }
    }


    
    this.hide = function(){
        this.buyButton.disable();
        this.setOpacity(0.0);
    }

    this.show = function(){
        this.runAction(new lime.animation.FadeTo(.8).setDuration(.4));
        if (!isEmpty(song.details.buySongUrl)){
            self.buySongUrl = song.details.buySongUrl;
            self.buyButton.enable();
        }
        else if (!isEmpty(trackid)){
            var key = getURLParameter(song.url,'oauth_consumer_key');

            jQuery.ajax({
                url: 'http://api.7digital.com/1.2/track/details',
                type: 'get',
                data: 'trackid=' + trackid + '&oauth_consumer_key='+ key + '&country=GB',
                success: this.setUrl
            });
        }
    }

}
goog.inherits(AudioPlayerBanner, lime.RoundedRect);

goog.exportSymbol('AudioPlayerBanner', AudioPlayerBanner);