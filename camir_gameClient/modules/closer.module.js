goog.provide('closer');
goog.require('Module');

// entrypoint

/*
 * Basicooo takes a list of song ids and urls
 * @param array args [id1, id2, id3, url1, url2 url3]
 */
closer = function(){
    Module.call(this);
    this.MOD = this;
    
    // stop all audio playing
    AudioPlayerButton.prototype.stopPlayingAudio();

    // stop Match
    game.match.stopMatch();
    
    this.isDisplayFinished = true; // game.director.removeDomElement();
    
    //delete game.client;

    game.showFeedback();
    
    
    //this.close ???
    /*
     * Make the start div appear.
     */
    //this.JQueryUnivers = $("#univers").fadeIn(1000, function () {


    //game.director.removeAllChildren();

    //game.director.removeDomElement();

    //document.body.setAttribute("style","");

    //game = undefined;
    //});

}
goog.inherits(closer,Module);

//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('closer', closer);
