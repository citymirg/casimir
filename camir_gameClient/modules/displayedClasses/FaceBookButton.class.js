goog.provide('FaceBookButton');

goog.require('GeneralPurposeButton');
/*
 * Facebook Button
 *
 * @todo: structure: make checkbutton inherit from this
 * @param parent:
 * @param onLogin:
 * @param onLogout:
 */
FaceBookButton = function(game, onLogin, onLogout){
    GeneralPurposeButton.call(this,this,null,'','');
    var self = this;

   
    // set Button Text and Image
    this.setText = function(){
        if (fbUserDetails.loggedIn){
            this.setSize(100,29);
            this.setFill('img/fb_logout_medium.png');
        }else{
            this.setSize(100,31);
            this.setFill('img/fb_login_medium.png');
        }
    }
    this.setText();

    // login log out functionality
    this.onClickFunction = function(){
        if (fbUserDetails.loggedIn){

                // logout facebook
                fbUserDetails.logout(function(){
                    // reload browser
                    self.setText();
                    window.top.location.href = config.CLIENT_PATH;
                });
                
               
        }else{
            // reset client registration status
            game.client.reset();


            /*
             *  trying redirect login
             */
//            self.setText();
//            window.top.location.href = config.auth_url;
//            return;
           
            if( window.fbAsyncInit == undefined ){
                FaceBookHelper.prototype.init(function(){
                    
                    // @todo: BUGFIX connected() seems to reset the game, but not controlled 
                    //game.showMainMenu();
                    
                    connected();
                    // reload browser
                    self.setText();
                });
            }else{
                 // login facebook
                fbUserDetails.login(function(){
                    
                    connected();
                    // reload browser
                    self.setText();
                });
            }
        }
        // update button appearance
        this.setText();
    }
}
goog.inherits(FaceBookButton,GeneralPurposeButton);

goog.exportSymbol('FaceBookButton', FaceBookButton);