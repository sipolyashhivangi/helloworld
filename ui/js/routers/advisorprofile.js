require.config({
    'paths': {
        //"jquerymultiselect": "bootstrap/bootstrap-multiselect",
        "jqueryform": "libs/jquery/jquery.form",
    }
});
define([
    'jquery',
    'underscore',
    'backbone',
    'handlebars',
    'jqueryform',
], function($, _, Backbone, Handlebars, jqueryform) {
    var Router = Backbone.Router.extend({
        initialize: function() {
            init();
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
                        var advisorHash = getQueryVariable('view');
                        $.ajax({
                            url: advisorProfileUrl,
                            type: 'POST',
                            data: {adv_hash: advisorHash,
                            },
                            dataType: "json",
                            success: function(data) {
                                timeoutPeriod = defaultTimeoutPeriod;
                                if (data.status == "OK") {                             
                                    require(['views/base/master', 'views/advisor/profilesimple'],
                                        function(masterV, profileV) {
                                            if(typeof (userData.advisor) != 'undefined') {
    	                                        masterV.render(userData.advisor);
                                                var Stripe = document.createElement('script'); Stripe.type = 'text/javascript'; Stripe.async = true;
                                                Stripe.src = ('https://js.stripe.com/v2/');
                                                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(Stripe, s);
                                            }
                                            else
                                            {
	                                            masterV.render(userData.user);
                                            }
                                            profileV.render(data.userdata);
                                            if (data.connection == null) {
                                                $('#connectbtn').show();
                                                $('#releasebtn').hide();
                                                $('#assignbtn').hide();
                                            }
                                            if (data.connection == 'NO') {
                                                $('#connectbtn').hide();
                                                $('#releasebtn').hide();
                                                $('#assignbtn').hide();
                                                $('span#status').html("<strong>Status : </strong><span class='advisorPaleText'>You have requested a connection to advisor " + data.userdata['firstname'] + ' ' + data.userdata['lastname'] + ".</span>");
                                            }
                                            if (data.connection == 'YES') {
                                                $('#connectbtn').hide();
                                                $('#releasebtn').hide();
                                                $('#assignbtn').hide();
                                                $('span#status').html("<strong>Status : </strong><span class='advisorPaleText'>Connected</span>");
                                            }
                                            if(typeof (userData.advisor) != 'undefined') {
                                                $('#connectbtn').hide();
                                                $('#releasebtn').hide();
                                                $('#assignbtn').hide();
                                                $('span#status').html("");
                                            }
                                            $('#shareP').hide();
                                            
                                            if(typeof (data.advisor) != 'undefined' || data.loggedin_user_created_by=='advisor'){
                                                $('.backList').hide();
                                            }else{
                                                $('.backList').show();
                                            }                                           
                                    });
                                }
                            }
                        });
                        init();
                    } else {                           
                        var redirectUrl = encodeURIComponent(window.location.href);
                        var location = baseUrl + '/signup?redirectUrl='+redirectUrl;
                        window.location = location;
                    }
                },
                error: function(data) {
                    window.location = "./login";
                }
            });
        }
    });
    return Router;
});
