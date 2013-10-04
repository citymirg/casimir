/**
 * FaceBookHelper containing fucntionality to get user details and post stuff
 * @constructor
 * @extends lime.RoundedRect
 * @param 
 *
 * @todo: make this a child of Timer
 */

/*
 * "id": "1442898510",
   "name": "Daniel Wolff",
   "first_name": "Daniel",
   "last_name": "Wolff",
   "username": "juppiemusic",
   "gender": "male",
   "locale": "en_GB",
   "metadata": {
      "connections": {
         "home": "https://graph.facebook.com/juppiemusic/home",
         "feed": "https://graph.facebook.com/juppiemusic/feed",
         "friends": "https://graph.facebook.com/juppiemusic/friends",
         "mutualfriends": "https://graph.facebook.com/juppiemusic/mutualfriends",
         "family": "https://graph.facebook.com/juppiemusic/family",
         "payments": "https://graph.facebook.com/juppiemusic/payments",
         "activities": "https://graph.facebook.com/juppiemusic/activities",
         "interests": "https://graph.facebook.com/juppiemusic/interests",
         "music": "https://graph.facebook.com/juppiemusic/music",
         "books": "https://graph.facebook.com/juppiemusic/books",
         "movies": "https://graph.facebook.com/juppiemusic/movies",
         "television": "https://graph.facebook.com/juppiemusic/television",
         "games": "https://graph.facebook.com/juppiemusic/games",
         "questions": "https://graph.facebook.com/juppiemusic/questions",
         "adaccounts": "https://graph.facebook.com/juppiemusic/adaccounts",
         "likes": "https://graph.facebook.com/juppiemusic/likes",
         "posts": "https://graph.facebook.com/juppiemusic/posts",
         "tagged": "https://graph.facebook.com/juppiemusic/tagged",
         "statuses": "https://graph.facebook.com/juppiemusic/statuses",
         "links": "https://graph.facebook.com/juppiemusic/links",
         "notes": "https://graph.facebook.com/juppiemusic/notes",
         "photos": "https://graph.facebook.com/juppiemusic/photos",
         "albums": "https://graph.facebook.com/juppiemusic/albums",
         "events": "https://graph.facebook.com/juppiemusic/events",
         "groups": "https://graph.facebook.com/juppiemusic/groups",
         "videos": "https://graph.facebook.com/juppiemusic/videos",
         "picture": "https://graph.facebook.com/juppiemusic/picture",
         "inbox": "https://graph.facebook.com/juppiemusic/inbox",
         "outbox": "https://graph.facebook.com/juppiemusic/outbox",
         "updates": "https://graph.facebook.com/juppiemusic/updates",
         "accounts": "https://graph.facebook.com/juppiemusic/accounts",
         "checkins": "https://graph.facebook.com/juppiemusic/checkins",
         "apprequests": "https://graph.facebook.com/juppiemusic/apprequests",
         "friendlists": "https://graph.facebook.com/juppiemusic/friendlists",
         "friendrequests": "https://graph.facebook.com/juppiemusic/friendrequests",
         "permissions": "https://graph.facebook.com/juppiemusic/permissions",
         "notifications": "https://graph.facebook.com/juppiemusic/notifications",
         "scores": "https://graph.facebook.com/juppiemusic/scores",
         "locations": "https://graph.facebook.com/juppiemusic/locations",
         "subscribers": "https://graph.facebook.com/juppiemusic/subscribers",
         "subscribedto": "https://graph.facebook.com/juppiemusic/subscribedto"
      },
*/
function FaceBookHelper (){
    
    
     /*
     * Initialisation
     */
    var self = this;

    // have we logged in already?
    this.loggedIn = false;

    /*
     * initialise callback position
     */
    this.gatherUserCallback = function(){};

    /*
     * variables indication the acutal user permissions
     */
    this.hasPermissions = new Object();
    
    /*
     * This is all the data we try to get and process
     */
    this.user = {
        basic: new Object(),
        music: new Object(),
        albums: new Object(), // newly added
        // todo: add music.listens
        
        locations: new Object(),
        
        activities: new Object(),
        interests: new Object(),

        groups: new Object(),
        
        books: new Object(),
        movies: new Object(),
        television: new Object(),
        
        // @note: there may be veeery many events,
        // consider if the information is relatively helpful
        events: new Object(),
        
        friends: new Object()
        };
     
    // secondary data holders
    this.userSec = {friends:{hometowns: new Array(),
                            locations: new Array()}};

    /*
     * Keep track of which data fields are finished
     * @todo: functions for specifying subsets of permissions
     */
    this.userLoadFinished = new Object();
    this.userSecondaryLoadFinished = new Object();
    this.userDetailsFilled = false;
    
        
    /*
     * @todo: set maximum for user properties
     */
    
    // we only collect up to maxFriends friends
    this.max ={secFriends: 50};

    /*
     * TODO: this may be extended to a progress bar
     */
    this.progress = {
      priAll: 0,
      secFriends: 0  
    };


    this.isUserLoadFinished = function(){
        if (isEmpty(this.userLoadFinished)) {
            return false;
        }
        for(var propt in self.userLoadFinished){
             if (self.userLoadFinished[propt] == false){
                 return false;
             }
        }
        return true;
    }
    /*
     * Keep track of which data fields are finished and execute next stage
     * if so
     * @return boolean
     */
    this.doUserLoadFinished = function(){
        if (!(self.isUserLoadFinished())) return false;
        this.gatherSecondaryUserInfo();
        return true;
    }
    
     /*
     * Keep track of which data fields are finished
     * @return boolean
     */
    
    this.isUserSecondaryLoadFinished = function(){
        if (isEmpty(this.userLoadFinished)) {
            return false;
        }
        for(var propt in self.userSecondaryLoadFinished){
             if (self.userLoadFinished[propt] == false){
                 return false;
             }
        }
        return true;
    }
    
    this.doUserSecondaryLoadFinished = function(){
        if (!(self.isUserSecondaryLoadFinished())) return false;
        self.gatherUserCallback();
        return true;
    }
    

     /*
     * Did we get everything?
     */
    this.allUserLoadFinished = function(){
        if (this.isUserLoadFinished() && this.isUserSecondaryLoadFinished()){
            return true;
        }else return false;  
    }


    /*
     * ENTRY function for collecting User Data
     * starts all the client calls to facebook to gather data
     * @returns 
     * @param finishedCallback handle to function tbc when all
     *          data is collected
     */
    this.gatherUserInfo = function(finishedCallback){

         // initialise userLoadFinished
        for(var propt in this.user){
             this.userLoadFinished[propt] = false;
        }

        // initialise userSecondaryLoadFinished
        this.userSecondaryLoadFinished = new Object();

        // initialise final State
        this.userDetailsFilled = false;

        // assign finished callback
        this.gatherUserCallback = finishedCallback;
        for(var propt in this.user){
             this.user[propt] = this.requestUserInfo(propt,0);
        }
    }
    
    

    /*
     * requests the user information from facebook
     */
    this.requestUserInfo = function(infotype,offset){

       if (!(infotype == 'basic')){
            FB.api('/me' + '/' + infotype +'?access_token='+ config.oauth_token + '&offset=' + offset, function(response) {
                
                if (offset == 0){
                    self.user[infotype] = response.data;
                }else{
                    self.user[infotype] = self.user[infotype].concat(response.data);
                }
                
                /*
                 * @todo: handle paging of information
                 */
                if (!isEmpty(response["data"][0])){
                    self.requestUserInfo(infotype,offset + response.data.length)
                }else{
                    /*
                     * Check wether all data has been given and start
                     * All data given callback
                     */
                     self.userLoadFinished[infotype] = true;
                     self.doUserLoadFinished();
                }
                });
       }else {
            FB.api('/me?access_token='+ config.oauth_token, function(response) {
                if (!isEmpty(response)){
                    self.user.basic = response;
                    /*
                     * Check wether all data has been given and start
                     * All data given callback
                     */
                    self.userLoadFinished[infotype] = true;
                    self.doUserLoadFinished();
                }
            });
        } 
   }
 

  
   
   /*
     * This is the user info which is dependend on the first
     * user info collected. collection is automatically started when
     * the first collection finishes
     * 
     * @todo: PERFORMANCE: continue user details getting during game 
     *          this second step seriously affects the loading time until the 
     *          game starts. thus, only random subsets of friends are loaded
     */
    this.gatherSecondaryUserInfo = function(){
        
        /*
         *  friends: we make a list and then
         *  get the locations of all
         */
        this.userSecondaryLoadFinished["friends"] = false;
        this.requestFriendsLocations();

    }
   

   
    /*
     * Gets the location field of a certain facebook graph node
     */
    this.requestFriendsLocations = function(){

        // reduce stored friends if too many
        // @todo: generalise thsi for all colleted data like events etc
        if (this.user.friends.length > this.max.secFriends) {
            var keepFriends = nRandom(this.user.friends.length-1, this.max.secFriends);
            var newFriends = new Array();
            for (var i=0; i < keepFriends.length; i++){
                newFriends[i] = (this.user.friends[keepFriends[i]]);
            }
            this.user.friends = newFriends;
        }
                
        this.progress.secFriends = 0; 
       
        for(i=0; i < this.user.friends.length; i++ ){
           FB.api(this.user.friends[i].id + '/', function(response) {
               
               // note hometown and location if available
                if (!isEmpty(response.hometown)){
                    self.userSec.friends.hometowns.push(response.hometown);
                } else{
                    self.userSec.friends.hometowns.push(new Object());
                }
                if (!isEmpty(response.location)){
                    self.userSec.friends.locations.push(response.location);
                }else{
                    self.userSec.friends.locations.push(new Object());
                }
                self.progress.secFriends++;
                
                // callback if finished
               if (self.progress.secFriends == self.user.friends.length){
                   self.userSecondaryLoadFinished["friends"] = true;
                   self.doUserSecondaryLoadFinished();
               }
            },{access_token: config.oauth_token,
           fields:"hometown,location"}); 
        }
    }
    /* -------------------------------------------------------------------------
     * Functions for mining and anonymisation 
     */

    /*
     * Converts a particular date into a age
     */
   this.formatDateToAge =function(date){
       
        var today = new Date();
       
        var age=today.getFullYear()-date.getFullYear();
        if(today.getMonth()< date.getMonth() || 
            (today.getMonth()== date.getMonth() && today.getDate()< date.getDate()))
            {age--;}
        return age;
   }
   
    
    /*
     * Gets the education institution classes from the data
     */
    this.formatEducationStatus = function(){
       var category = "";
       var discipline = "";
       
        if(!isEmpty(this.user.basic.education)){
            for(var i = 0; i < this.user.basic.education.length ; i++){

                // education discipline
                if (!isEmpty(this.user.basic.education[i].type)){
                    category = category + this.user.basic.education[i].type;
                    category = category + ", ";
                }
                
                // education discipline
                if (!isEmpty(this.user.basic.education[i].concentration)){
                     for(var j = 0; j < this.user.basic.education[i].concentration.length ; j++){
                        discipline = discipline + this.user.basic.education[i].concentration[j].name;
                        discipline = discipline + ", ";
                     }
                }
                
                // education instituion
            }
        }
    
        return {category: category,
                discipline: discipline};
   }
   
    

    /* -------------------------------------------------------------------------
     * Data transmitted
     * Wrapper and converter for getting Facebook data into UserDetails / db Format
     * fills the global userDetails variable
     */
   this.fillUserDetails = function(){
//       var userDetails = Object();

       // calculate age from birthyear
       userDetails.age = this.formatDateToAge(new Date(this.user.basic.birthday));

       // we don't save any personal data for under 18 year olds'
       if (userDetails.age < 18){
           this.userDetailsFilled = true;
           return;
       }


       // basic user details
       userDetails.name = this.user.basic.first_name;
       userDetails.gender = this.user.basic.gender;
       
       
       /*
        * Locations
        */
       if(!isEmpty(this.user.basic.hometown)){
            userDetails.locationBirth = this.user.basic.hometown.name;
            userDetails.fbHometown = this.user.basic.hometown;
       }
       
       if(!isEmpty(this.user.basic.location)){
            userDetails.locationLiving = this.user.basic.location.name;
            userDetails.fbLocation = this.user.basic.location;
       }
       
       if(!isEmpty(this.user.basic.locale)){
            userDetails.country = this.user.basic.locale.substring(3, 5);
            userDetails.locale = this.user.basic.locale;
       }
       
       if(!isEmpty( this.user.basic.timezone)){
            userDetails.timeZone = this.user.basic.timezone;
       }

       /*
        * Languages
        */
       if(!isEmpty(this.user.basic.languages) && this.user.basic.languages.length > 0){
           userDetails.firstLanguage = this.user.basic.languages[0].name;
       //userDetails.firstLanguageId = this.user.basic.languages[0].id;
       
            if(this.user.basic.languages.length > 1)
                userDetails.secondLanguage = this.user.basic.languages[1].name;
            /*
            * Save further Languages
            */
           userDetails.fbLanguages = this.user.basic.languages;
            //userDetails.secondLanguageId = this.user.basic.languages[1].id;
       }


       /*
        * The following data need special means of mining, particularly to 
        * be involved in the public dataset
        */
       
       
       /*
        * Music. Wouldb e great to get genre tags from API:
        *  Echo nest can use FB Artist Ids
        *  Last.fm can find approximate name matching
        */
       userDetails.fbMusic = this.user.music;
       userDetails.fbAlbums = this.user.albums;
       
        /*
        * Education and Work
        */
       if(!isEmpty(this.user.basic.education)){
            var res = this.formatEducationStatus();
            userDetails.education = res.category;
            userDetails.educationSector = res.discipline;

            userDetails.fbEducation = this.user.basic.education;
            userDetails.fbWork = this.user.basic.work;
       }
       
       
       /*
        * Religion and Politics
        */
       if(!isEmpty(this.user.basic.religion))
            userDetails.fbReligion = this.user.basic.religion;
        
       if(!isEmpty(this.user.basic.politics))
            userDetails.fbPolitics = this.user.basic.politics;
       
       
       /*
        * Friends' locations
        */
//       res = this.friendsLocations();
       userDetails.fbFriendsLocations = this.userSec.friends.locations;
       userDetails.fbFriendsHometowns = this.userSec.friends.hometowns;

       this.userDetailsFilled = true;
       //return userDetails;
   }

   // login and logout functions
   this.login = function(afterFun) {
        FB.login(function(response) {
            if (response.authResponse) {
                
                console.log('User is now logged in');
                
                // complete login               
                afterFun();
                self.loggedIn = true;

            } else {
                self.loggedIn = false;
                // not logged in
            }
        },{scope: config.permissions});
    }

    this.logout = function (afterFun) {
        FB.logout(function(response) {
            self.loggedIn = false;
            console.log('User is now logged out');
            afterFun();
        });
    }

    /*
     * @todo: put this in a javascript helper class
     * Generic rrandom number generator
     * @param int max: maximal value of random number
     * @param int n: number of randoom values to produce
     * @return Array of n random numbers between 0 and max
     */
    function nRandom (max,n) {
        var result = new Array();
        while ( --n >= 0) {
            result[n] = Math.round(Math.random() * max);
        }
        return result;
   }
}
 /**
  * initialises the facebook environment
  */
FaceBookHelper.prototype.init = function(afterFun){
  window.fbAsyncInit = function() {
    // init the FB JS SDK
    FB.init({
      appId      : '427976707257845', // App ID from the App Dashboard
    /*
     * @todo: does the channelURL cause problems on iphone etc?
     */
      channelUrl : config.CLIENT_PATH + 'facebook/channel.php', // Channel File for x-domain communication
      status     : true, // check the login status upon init?
      cookie     : false, // set sessions cookies to allow your server to access the session?
      xfbml      : false  // parse XFBML tags on this page?
    });

   afterFun();
  };

  // Load the SDK's source Asynchronously
  (function(d, debug){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
     ref.parentNode.insertBefore(js, ref);
   }(document, /*debug*/ false));

}

/*
 * Achievement Functions
 */
FaceBookHelper.prototype.publishScore = function(totalPoints){
    FB.api("/me/scores", 'post', {score: totalPoints, access_token: config.oauth_token}, function(response){
       if (!response || response.error) {
          console.log(response);
       } else {
          console.log('Facebook Score: ' + totalPoints);
       }
    });
}


/*
 * Checks in object if user has stated permissions
 * @param permsNeeded array[String] needed permissions
 */
FaceBookHelper.prototype.validPermissions = function(permsNeeded){
        for (var i in permsNeeded) {
          if (this.hasPermissions[permsNeeded[i]] == undefined || this.hasPermissions[permsNeeded[i]] == false) {
              return false;
          }
        }
    return true;
}

/*
 * Pop up facebook dialog with facebook permissions to request
 * @param permsNeeded array[String] needed permissions
 * @param callAfter function to be called after execution
 */
FaceBookHelper.prototype.requestPermissions = function(permsNeeded,callAfter){
    
    // build permissions string
    FB.login(function(response) {
      console.log(response);
      callAfter(response);
    }, {scope: permsNeeded.join(',')});
}

/*
 * Pop up facebook dialog with facebook permissions to request
 * @param permsRemoved array[String]  permissions to be removed
 * @param callAfter function to be called after execution
 */
FaceBookHelper.prototype.removePermissions = function(permsRemoved,callAfter){
    
    // build permissions string
     FB.api(
          {
            method: 'auth.revokeExtendedPermission',
            perm: permsRemoved.join(',')
          },
          function(response) {
            console.log(response);
            callAfter(response);
          }
      ); 
}