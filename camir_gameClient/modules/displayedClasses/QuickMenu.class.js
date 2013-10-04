goog.provide('QuickMenu');

goog.require('GeneralPurposeButton');
goog.require('lime.RoundedRect');
/**
 * small menu to display during match and highscore
 * @constructor
 * @extends lime.RoundedRect
 * @param game: game instance 
 */
QuickMenu = function(game) {
    lime.RoundedRect.call(this);
    
    this.setSize(70,200);
    this.setAnchorPoint(0.5,0);
    
    // @todo: Exit?
//    this.toMainMenu = new GeneralPurposeButton(game,game.showMainMenu,'','')
//                .setPosition(moduleWidth/2,50)
//                .setSize(moduleWidth,40);
//    this.mainLayer.appendChild(this.toMainMenu);
    
    // Home  - Main Menu
    this.toMainMenu = new GeneralPurposeButton(game, function(){
                            if(!(game.match == undefined)){game.match.stopMatch();}
                            game.showMainMenu();
                        },'img/menu_icons/go-home-2.png','')
                .setPosition(0,0)
                .setSize(50,50);
    this.appendChild(this.toMainMenu);
    
     // Show Options
    // @todo: make screen
    this.toOptions = new GeneralPurposeButton(game, function(){
                            if(!(game.match == undefined)){game.match.stopMatch();}
                            game.showOptions();
                        },'img/menu_icons/applications-system-3.png','')
                .setPosition(0,70)
                .setSize(50,50);
    this.appendChild(this.toOptions);
    
    // @todo to be implemented
    this.toOptions.disable();



}
goog.inherits(QuickMenu, lime.RoundedRect);


goog.exportSymbol('QuickMenu', QuickMenu);