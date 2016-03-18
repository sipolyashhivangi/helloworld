// This is the main entry point for the App

define(['configration/config'], function(config) {
    var init = function(type) {
	    var protocol = window.location.protocol;
        if (protocol == "http:") {
            var host = window.location.hostname;
            if (host.indexOf("staging") == -1 && host.indexOf("dev") == -1 && host.indexOf("localhost") == -1) {
                if (host.indexOf('www.') == -1) {
                    host = "www." + host;
                }
                window.location = "https://" + host + window.location.pathname + window.location.search + window.location.hash;
            }
        }
           
        this.router = new type();
    };
    return {
        init: init
    };
});
