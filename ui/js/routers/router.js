// Filename: router.js
define([
    'jquery',
    'underscore',
    'backbone'
], function($, _, Backbone) {
    var Router = Backbone.Router.extend({
        initialize: function() {

            Backbone.history.start(
                    {
                        pushState: true,
                        root: "/flexscorebb/ui/html/marketing/"
                    }
            );
        },
        routes: {
            // Define some URL routes
            '': "fnLoadHomeScreen",
            'login': 'fnShowLogin',
            'signup': 'fnSignUp',
            'financialsnapshot': 'fnFinancialSnapshot',
            'learningcenter': 'fnLearningCenter'
        },
        fnLoadHomeScreen: function() {
            init();

            //check if user is logged in 
            userData = null;
            sess = localStorage[serverSess];

            $.ajax({
                url: loginCheckUrl + "?sess=" + sess,
                type: 'GET',
                dataType: "json",
                success: function(data) {
                    userData = data;
                    $('#body').show();
                    if (data.status == "OK") {
                        //go to financial snapshot
                        window.location = "./myscore";
                    }
                }
            });
        },
        fnShowLogin: function() {
            window.location = "./login";
        },
        fnSignUp: function() {
            // Call render on the module we loaded in via the dependency array
            //                loginView.render();
            //                signupView.render();
        },
        fnFinancialSnapshot: function() {
            window.location = "./financialsnapshot";
        },
        fnLearningCenter: function() {
            window.location = "./learningcenter";
        }
    });
    return Router;
});