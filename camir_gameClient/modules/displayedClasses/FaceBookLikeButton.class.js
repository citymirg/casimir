goog.provide('FaceBookLikeButton');

goog.require('lime.Layer');

/*
 * Facebook Like Button
 *
 */
FaceBookLikeButton = function(width,height,faces){
    lime.Layer.call(this);
    
    /*
     * @todo: resolve path problems
     */
    var showfaces = "";
    if (faces == undefined || faces == false) {
        showfaces  = "false";
    } else{
        showfaces  = "true";
    }

    var iframe = goog.dom.createDom('iframe', {
        'src': "//www.facebook.com/plugins/like.php?href=" + encodeURI(config.graph_url) + "&amp;send=false&amp;layout=standard&amp;width=" + width + "&amp;show_faces=" + showfaces + "&amp;font=arial&amp;colorscheme=light&amp;action=like&amp;height=" + height + "&amp;appId=" + config.app_id,
                    scrolling: "no", frameborder:"0",
                    style:"border:none; overflow:hidden; width:" + width + "px; height:" + height + "px;",
                    allowTransparency:"true"});


   // var iframe = goog.dom.htmlToDocumentFragment('<div class="fb-like" data-href="http://apps.facebook.com/427976707257845/" data-send="true" data-width='+ width + ' data-show-faces="true"></div>');
//
    this.appendChild(iframe);
}
goog.inherits(FaceBookLikeButton,lime.Layer);

goog.exportSymbol('FaceBookLikeButton', FaceBookLikeButton);