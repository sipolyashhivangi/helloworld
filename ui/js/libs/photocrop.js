var photoCropInitialized = false;

function initPhotoCrop() {
  "use strict";
  if ( !photoCropInitialized ) {
    var jcrop_api = $.Jcrop('#cropphoto');
    var bounds = jcrop_api.getBounds();
    jcrop_api.setSelect( [ 0, 0, bounds[0], bounds[1] ] );
    $('#comparisonBox').css( "height", "600" );
  };
	photoCropInitialized = true;
};

 