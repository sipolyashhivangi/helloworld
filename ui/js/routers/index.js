define([
    'jquery',
    'underscore',
    'backbone'
], function($, _, Backbone) {
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
                        //go to financial snapshot
                        if ($("#currentPage").val() == "index" || $("#canlogin").val() == "false")
                            if(typeof(data.advisor) != 'undefined') {
                            	window.location = "./dashboard";
                            }
                            else
                            {
                            	window.location = "./myscore";
                            }
                        else
                        {
                            require(
                                    ['views/base/header', 'views/base/footer'],
                                    function(headerV, footerV) {
                                    	if(typeof(data.advisor) != 'undefined') {
	                                        headerV.render(data.advisor);
                                                var Stripe = document.createElement('script'); Stripe.type = 'text/javascript'; Stripe.async = true;
                                                Stripe.src = ('https://js.stripe.com/v2/');
                                                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(Stripe, s);
	                                    }
	                                    else
	                                    {
	                                        headerV.render(data.user);
	                                    }
                                        footerV.render();
                                        if ($("#currentPage").val() == "faqs")
                                        {
                                            $("#gnav_help").addClass("hover reverseShadowBox");
                                            $("#gnav_help").removeClass("gnavButton");
                                            if (typeof(sendMixpanel) != 'undefined' && sendMixpanel){
                                                mixpanel.identify(data.uniquehash);
                                                mixpanel.people.set({
                                                    'User Read FAQs': new Date()
                                                });
                                                mixpanel.track("User Read FAQs", {
                                                    "user_read_faqs": data.uniquehash
                                                });
                                            }
                                        }
                                        init();
                                        $('head').append('<link rel="stylesheet" href="./ui/css/ui-lightness/jquery-ui-1.9.2.custom.css?refresh=' + version + '" type="text/css" />');
                                        $('head').append('<link rel="stylesheet" href="./ui/css/normalize.css?refresh=' + version + '" type="text/css" />');
                                        $('head').append('<link rel="stylesheet" href="./ui/css/main.css?refresh=' + version + '" type="text/css" />');
                                        $('head').append('<link rel="stylesheet" href="./ui/css/css.css?refresh=' + version + '" type="text/css" />');
	                                    $('head').append('<link rel="stylesheet" href="./ui/css/tabCss.css?refresh=' + version + '" type="text/css" />');
                                        $("#navWrap").attr('style', 'font-size:16px');
                                        $("#body").attr('style', 'font-size:14px;line-height:20px');
                                        $("#body").show();

                                    });
                        }
                    } else {
                        require(
                                ['views/profile/login', 'views/profile/signup', 'views/profile/purplesignup', 'views/profile/advisor', 'views/base/headerloggedout', 'views/base/footer'],
                                function(loginV, signupV, pSignupV, advisorV, headerV, footerV) {
                                    headerV.render(data);
                                    footerV.render();
                                    loginV.render();
                                    signupV.render();
                                    pSignupV.render();
                                    advisorV.render(data);
                                    $("signuploginContent").removeClass("hdn");
                                    init();
                                    $("#body").show();
                                    if ($("#currentPage").val() == "success")
                                    {
                                        $(".footerShare").hide();
                                    }
                                }
                        );
                    }
                }
            });
        }
    });
    return Router;
});