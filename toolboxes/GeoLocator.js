/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function GeoLocator(){
    var gl;

    self = this;
    this.detectedPos;
    // position.coords.latitude;
    // position.coords.longitude;

    try {
      if (typeof navigator.geolocation === 'undefined'){
        gl = google.gears.factory.create('beta.geolocation');
      } else {
        gl = navigator.geolocation;
      }
    } catch(e) {}

    if (gl) {
      gl.getCurrentPosition(displayPosition, displayError);
    } else {
      alert("Geolocation services are not supported by your web browser.");
    }

    function displayPosition(position) {
        this.detectedPos = position;
    }

    function displayError(positionError) {
      console.log("GeoLocation Failed");
    }
}
