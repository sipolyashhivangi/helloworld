// Filename: views/login/login
define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/connectsearch.html',
    ], function(Handlebars, Backbone, searchTemplate) {

        var searchView = Backbone.View.extend({
            el: $("#body"),
            render: function(obj) {
                var source   = $(searchTemplate).html();
                var template = Handlebars.compile(source);

                if (typeof (comingsoon) != 'undefined' && (typeof (cewhitelist) == 'undefined' || cewhitelist.indexOf(userData.email) == -1))
                {
                    obj.comingsoon = true;
                }
                $("#connectSearchDiv").prepend(template(obj));
            },
            events: {
                "keypress #search": "fnCheckSearch"
            },
            fnCheckSearch: function(event) {
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if (keycode == '13') {
                    event.preventDefault();
                    $("#searchButton").click();
                }
            }
        });
        return new searchView;
    });