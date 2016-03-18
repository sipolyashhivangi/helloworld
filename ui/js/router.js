// Filename: router.js
define([
    'jQuery',
    'Underscore',
    'Handlebars',
    'Backbone',
    'views/home/main',
    'views/home/menu',
    'views/login/login',
    'views/login/signup',
    'views/login/forgotpassword',
    'views/login/accountdeletion',
    ], function($, _, Handlebars, Backbone, mainHomeView,menuHomeView, loginView,signupView ){
        var AppRouter = Backbone.Router.extend({
            routes: {
                // Define some URL routes
                '/login': 'showLogin',
                '/logout':'showLogout',
                '/signup':'signUp',
                // Default
                '*actions': 'defaultAction'
            },
            showLogin: function(){
                // Call render on the module we loaded in via the dependency array
                // 'views/login/login'
                loginView.render();
            },
            signUp: function(){
                // Call render on the module we loaded in via the dependency array
                // 'views/login/signup'
                signupView.render();
            },
            showLogout: function(){
                // Call render on the module we loaded in via the dependency array
                // 'views/login/main'
                $.ajax({
                    url:"../api/userlogout",
                    type:'GET',
                    dataType:"json",
                    success:function () {
                        console.log(["User logged out..."]);
                        menuHomeView.render();
                        mainHomeView.render();

                    }
                });
            },
            defaultAction: function(actions){
                // We have no matching route, lets display the home page
                menuHomeView.render();
                mainHomeView.render();
                $('#login').show();
                $('#logout').hide();
            }
        });

        var initialize = function(){
            //check if user is logged in
            $.ajax({
                url:"../api/authcheck",
                type:'GET',
                dataType:"json",
                success:function (data) {
                    console.log(["User Login Status :", data]);

                    if (data.error && data.error.toString() != ""){
                    //not logged in go to login screeen
                    }else {
                        //display username
                        //sucessfully logged in to the system
                        var variables = {
                            username: ""+data.username,
                            email: ""+data.email,
                            firstname: ""+data.firstname,
                            lastname: ""+data.lastname,
                            address: ""+data.address,
                            city: ""+data.city,
                            zip: ""+data.zip,
                            state: ""+data.state,
                            country: ""+data.country
                        };
                        $('#alert-error').html("Logged in already");
                        $('#alert-error').show();
                        menuHomeView.showLoggedIn(variables);
                        $('#form-login').hide();
                    }
                }
            });
            var app_router = new AppRouter;
            Backbone.history.start();
        }
        return {
            initialize: initialize
        };
    });