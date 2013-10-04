goog.provide('basicooo_result');
goog.require('Module_result');

goog.require('ResultSpeakers');

// entrypoint
basicooo_result = function(args) {
    
    Module_result.call(this,args);
     for(i=0; i < this.playerRow.length; i++){
         
        /*+
         * Result speakers: Get the song
         */
        this.playerRow[i].resultDetails.appendChild(
             new ResultSpeakers(this.playerRow[i].result.vote.songChosenId, this.playerRow[i].args)
            );
     }

}
goog.inherits(basicooo_result,Module_result);

//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('basicooo_result', basicooo_result);
