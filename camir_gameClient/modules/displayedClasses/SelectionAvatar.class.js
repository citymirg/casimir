goog.provide('SelectionAvatar');

goog.require('GeneralPurposeButton');
goog.require('lime.Sprite');
goog.require('lime.animation.FadeTo');

var selectionAvatarStack = new Array();
/**
 * SelectionAvatar presented to player in avatar selection dialog
 * (BuyAvatar.screen)
 * @constructor
 * @extends lime.RoundedRect
 * @param player player description should be similar to PhP class
 *
 * @todo: make this a child of Timer
 */
SelectionAvatar = function(parent,avatar,onClickFunction,onClickParams){
    lime.RoundedRect.call(this);

    var self = this;

    /*
     * @todo: save avatar details
     */

    this.onClickFunction = onClickFunction;
    this.onClickParams = onClickParams;

    this.avatar = avatar;

    this.selected = false;
    
    // set Fill and shadow
    this.setFill('#FFFFFF')
        .setStroke(1,'#EEEEEE');
    this.getDeepestDomElement().setAttribute("style", "-moz-box-shadow: 2px 2px 3px #888; -webkit-box-shadow:2px 2px 3px #888; box-shadow: 2px 2px 3px #888;");

    // label for price, maybe one for level needed
    this.lblPoints = new lime.Label();
    setSmallFont(this.lblPoints)
        .setAnchorPoint(0, 0)
        .setPosition(-50,55)
        .setSize(100,20)
        .setAlign("left");
        
    if (this.avatar['playerId'] != null){
                this.lblPoints.setText(_('Owned'));
        }else{
                this.lblPoints.setText(this.avatar['costPoints'] + ' ' +_('Pts'));
        }
    this.appendChild(this.lblPoints);

    /*
     * selection logic
     */
    this.switchSelected = function(){

        // switch selection
        if (this.selected){this.deSelect();}
        else{this.select();}

        this.onClickFunction.call(parent,onClickParams);
    }

    this.select = function(){
        SelectionAvatar.prototype.resetAll();
        this.setStroke(2,'#FF6C00');
        this.selected = true;
    }
    this.deSelect = function(){
         this.setStroke(1,'#EEEEEE');
        this.selected = false;
    }
    // add Button and keep reference
    // this button is transclickeable because it also has to scroll
    this.btn = new GeneralPurposeButton(this,this.switchSelected,
                        './img/avatars/' + this.avatar['uiAvatarFileName'],'',true)
                       .setSize(90,90);
    this.appendChild(this.btn);

    // keep track of this avatar instance
    selectionAvatarStack.push(this);
}
goog.inherits(SelectionAvatar, lime.RoundedRect);

// reset all selection flags
SelectionAvatar.prototype.resetAll = function(){
    for(var i=0; i < selectionAvatarStack.length; i++){
        if (selectionAvatarStack[i].selected){
            selectionAvatarStack[i].deSelect();
        }
    }
}

// returns currently selected Avatar
SelectionAvatar.prototype.getSelected = function(){
    for(var i=0; i < selectionAvatarStack.length; i++){
        if (selectionAvatarStack[i].selected){
            return selectionAvatarStack[i];
        }
    }
}


goog.exportSymbol('SelectionAvatar', SelectionAvatar);