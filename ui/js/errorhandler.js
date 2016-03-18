/* 
 * Error handling for Ajax calls.
 */

// Varible for storing the Error messages.
var error_msg = "";           

// Handles all the Java Script errors
window.onerror = function(msg, url, linenumber) {
    error_msg = error_msg + "\n" + "/********** Javascript Exception Occured ***************/"  + "\n" ;    
    error_msg = error_msg + "\n" + msg + "\n  at line number " + linenumber + " (URL: " + url + ")";
    error_msg = error_msg + "\n" + "/********** Javascript Exception Occured ***************/"  + "\n" ;    
};

$(document).ajaxError(function myErrorHandler(event, xhr, ajaxOptions, thrownError) {
    error_msg = error_msg + "\n" + "/********** Exception Occured ***************/"  + "\n" ;
    error_msg = error_msg + "\n" + "/***** Error In URL *******/"  + "\n" ;
    error_msg = error_msg + ajaxOptions.url + "\n" + thrownError;    
    error_msg = error_msg + "\n" + "/***** Error In URL *******/"  + "\n" ;    
    error_msg = error_msg + xhr.responseText;
    error_msg = error_msg + "\n" + "/********** Exception Occured ***************/";
}); 


