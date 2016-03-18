define([
    'jquery',
    'underscore',
    'handlebars',
    'backbone',
    'text!../../../html/profile/userverifypass.html',
    ], function($, _,Handlebars, Backbone, userVerifyPassTemplate){
    
        var userVerifyPassView = Backbone.View.extend({
            el: $("#page-home"),
            render: function(){
                var source = $(userVerifyPassTemplate).html();
                var template = Handlebars.compile(source); 
                $(this.el).html(template());
            },
            events: {
               "click #usrSetpassword": "userSetpasswordLogin"
            },
            userSetpasswordLogin: function(event){
                
                var user= $('#').val();
                
                var formValues = {
                    email: user
                };

                $.ajax({
                    url:usersetpasswordUrl,
                    type:'POST',
                    dataType:"json",
                    data: formValues,
                    success:function (data) {
                        
                        if (data.error && data.error.toString() != ""){
                          
                        }else {
                           
                        }
                    }
                });
            }
        });

        return new userVerifyPassView;
    });