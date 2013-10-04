


function isEmptyObject(map) {
   for(var key in map) {
      if (map.hasOwnProperty(key)) {
         return false;
      }
   }
   return true;
}

/*
 * @todo: put this in a javascript helper class
 * Generic isempty function
 * @return boolean
 */
function isEmpty(obj) {
    if (typeof obj == 'undefined' || obj === null || obj === '') return true;
    if (typeof obj == 'number' && isNaN(obj)) return true;
    if (obj instanceof Date && isNaN(Number(obj))) return true;
    if (isEmptyObject(obj)) return true;
    return false;
}

/*
 *  Get unique entries in array
 */
function array_unique(array){
    var o = {}, i, l = array.length, r = [];
    for(i=0; i<l;i+=1) o[array[i]] = array[i];
    for(i in o) r.push(o[i]);
    return r;
}
/*
 * Get partial parameters from a url
 */
function getURLParameter(url,name){
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(url)||[,""])[1].replace(/\+/g, '%20'))||null;
}

/*
 * Loads a js file
 * @return DOM element for the js script
 */
function loadjs(filename){
      var fileref=document.createElement('script');
      fileref.setAttribute("type","text/javascript");
      fileref.setAttribute("src", filename);
      return fileref;
 }

/*
 * Internationalisation Stuff
 */
function sprintf(s,argin) {
	var bits = s.split('%');
	var out = bits[0];
	var re = /^([ds])(.*)$/;
	for (var i=1; i<bits.length; i++) {
		p = re.exec(bits[i]);
		if (!p || isEmpty(argin) || argin[i]==null) continue;
		if (p[1] == 'd') {
			out += parseInt(argin[i], 10);
		} else if (p[1] == 's') {
			out += argin[i];
		}
		out += p[2];
	}
	return out;
}

/*
 * if you put _(" %d String",10), this function will return the
 * string in the approproate internationalisation object
 * or the input string, containing the number in the correct place
 *
 * @maybe try first without sprintf
 */
function _(s) {
	if (typeof(i18n)!='undefined' && i18n[s]) {
		return i18n[s];
	}
	return sprintf(s,arguments);
}