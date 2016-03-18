define([
    'jquery',
    'underscore',
    'handlebars',
    'backbone',
    'text!../../../html/profile/usercontent.html',
    ], function($, _,Handlebars, Backbone, usercontentTemplate){
    
        var usercontentView = Backbone.View.extend({
            el: $("#user-information"),
            render: function(data){
                var sourceOne   = $(usercontentTemplate).html();
                var template = Handlebars.compile(sourceOne); 
                $("#user-information").html(template(data));
            },
            events: {
               "click #menu-refresh":"callRefresh"
            },
            callRefresh: function(event){
                event.preventDefault(); 
                //call refresh service 
                $.ajax({
                    url:refreshAllUrl,
                    type:'GET',
                    dataType:"json",
                    cache: false,
                    beforeSend: function(request) {
                        request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                    },
                    success:function (data) {
                        timeoutPeriod = defaultTimeoutPeriod;
                        if (data.error && data.error.toString() != ""){
                            $('#alert-error').html(""+data.error);
                            $('#alert-error').show();
                        }else {
                           
                        }
                    }
                });
            }
        });

        return new usercontentView;
    });