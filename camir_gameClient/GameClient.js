goog.provide('GameClient');

//get requirements

GameClient = function () {
    var self = this;    
    var url = config.GAME_SERVER_PATH;

    // keep track of registration status
    this.isRegistered = false;
    //--------------------
    // Initial connection
    //--------------------
    var initConfiguration = {
			url:  url,
                        async: false
                    };
                    
                    
    //INITIALISE CONNECTION
    this.syncConnection = new jQuery.Zend.jsonrpc(initConfiguration);

   /*
    * Transmits the user details to server and creates a new player
    */
    this.registerPlayer = function(fbSession){
        console.log(fbSession);
        if(this.isRegistered)
            return;
        
        // register Player in database
        if(isEmpty(fbSession)){
            this.userAuth = this.syncConnection.authenticate(userDetails);
        }else{
            delete userDetails.uniqueExtId;
            this.userAuth = this.syncConnection.authenticate(userDetails,fbSession);
        }

        // update uniqueExtId in userDetails
        userDetails.uniqueExtId = this.userAuth.uniqueExtId;

        // update SynchConnection
        var registeredConfiguration = {
			url:  url + '?' +
                                'playerid=' + this.userAuth.playerid + '&' +
                                'uniqueExtId=' + this.userAuth.uniqueExtId 
                               // + '&' + 'XDEBUG_SESSION_START=netbeans-xdebug'
                            ,
                        async: false
                    };
        delete(this.syncConnection);
        this.syncConnection = new jQuery.Zend.jsonrpc(registeredConfiguration);

        // update registration status
        this.isRegistered = true;
    }
    
    this.reset = function(){
        // keep track of registration status
        self.isRegistered = false;
        //--------------------
        // Initial connection
        //--------------------
        var initConfiguration = {
                            url:  url,
                            async: false
                        };


        //INITIALISE CONNECTION
        self.syncConnection = new jQuery.Zend.jsonrpc(initConfiguration);
    }

    /*
     *
     * Starts a match for the player.
     * @todo: minimize transported information (userdetails are already known)
     * @todo: account for reloads of the page (best to strictly restart new match)
     */
    this.startMatch = function(matchType, matchDetails){
        
        // also update syncconnection
        delete(this.syncConnection);
        this.syncConnection = new jQuery.Zend.jsonrpc( {
			url: url + '?' +
                                'playerid=' + this.userAuth.playerid + '&' +
                                'uniqueExtId=' + this.userAuth.uniqueExtId,
                        async: false
                    });
                    
        //Methods to initialise
        self.userAuth = this.syncConnection.startMatch(matchType, matchDetails);
        console.log('Auth :' + self.userAuth);


        //----------------------
        // Permanent Connection
        //----------------------
        this.authRequest = '?' +
                    'playerid=' + this.userAuth.playerid + '&' +
                    'matchid=' + this.userAuth.matchid + '&' +
                    'sessionid=' + this.userAuth.sessionid + '&' +
                    'uniqueExtId=' + this.userAuth.uniqueExtId ;

        var configuration = {
                            url: url + this.authRequest,
                            async: true
                        };

        // make this a jsonrpc client
        this.asyncConnection = new jQuery.Zend.jsonrpc(configuration);

        // also update syncconnection
        delete(this.syncConnection);
        this.syncConnection = new jQuery.Zend.jsonrpc( {
			url: url + this.authRequest,
                        async: false
                    });
    }
    // starts the client polling
    this.start = function (interval, sucFun){
        this.pollsuccess = sucFun;
        lime.scheduleManager.scheduleWithDelay(this.poller,this, interval);
    }

    // stops the polling
    this.stop = function (){
        lime.scheduleManager.unschedule(this.poller,this);
    }

    // get the highscore
    this.getHighscore = function (){
        lime.scheduleManager.scheduleWithDelay(this.poller,this, interval);
    }

    // poll function
    this.poller = function (dt) {

        var res = this.asyncConnection.poll({
            success: self.pollsuccess,
            error: pollerror}
        )

        //console.log('Polling: ' + res);
    }

    function pollerror (a,b,c) {
        console.log('Polling failed.');
        console.log(a);
        console.log(b);
        console.log(c);
    }
    //console.log(this);
}


//this is required for outside access after code is compiled in ADVANCED_COMPILATIONS mode
goog.exportSymbol('GameClient', GameClient);