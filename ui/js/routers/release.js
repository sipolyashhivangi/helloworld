define([
    'jquery',
    'underscore',
    'backbone'
    ], function($, _, Backbone){
        var Router  = Backbone.Router.extend({
            routes: {
            //"post/:slug": "fnLearningCenterPost"
            },
            initialize: function(){    
                userData = null;
                sess = localStorage[serverSess];
                
                $.ajax({
                    url:loginCheckUrl+"?sess="+sess,
                    type:'GET',
                    dataType:"json",
                    success:function (data) {
                        userData = data;
                        $('#body').show();
                        if (data.status == "OK"){
                            require(
                                [ 'views/base/header', 'views/base/footer'],
                                function(headerV, footerV ){
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
                                    init();
                                    $("#body").show();
                                    $('head').append('<link rel="stylesheet" href="./ui/css/ui-lightness/jquery-ui-1.9.2.custom.css?refresh=' + version + '" type="text/css" />');                                        
                                    $('head').append('<link rel="stylesheet" href="./ui/css/normalize.css?refresh=' + version + '" type="text/css" />');
                                    $('head').append('<link rel="stylesheet" href="./ui/css/main.css?refresh=' + version + '" type="text/css" />');
                                    $('head').append('<link rel="stylesheet" href="./ui/css/css.css?refresh=' + version + '" type="text/css" />');
                                    $('head').append('<link rel="stylesheet" href="./ui/css/tabCss.css?refresh=' + version + '" type="text/css" />');
                                    $("#navWrap").attr('style', 'font-size:16px');
                                    $("#body").attr('style', 'font-size:14px;line-height:20px');
                                });
                        }else{
                            require(
                                [ 'views/profile/login', 'views/profile/signup', 'views/profile/advisor', 'views/base/headerloggedout','views/base/footer'  ],
                                function( loginV, signupV, advisorV, headerV, footerV ){
                                    headerV.render(data);
                                    footerV.render();
                                    advisorV.render(data);
                                    loginV.render();
                                    signupV.render();
                                    //pSignupV.render();
                                    $("signuploginContent").removeClass("hdn");
                                    init();
                                    $("#body").show();
                                }
                                );
                        }
                    }
                });

                require(
                        [ 'views/release/summary' ],
                        function( summaryV ){
                            $.ajax({
                                url:learningCenterPressRelease,
                                type:'GET',
                                dataType:"json",
                                success:function (jsonData) {
                                    $("#body").show();                            
                                    summaryV.render(jsonData);
                                    init();
                                }
                            });
                        }
                        );
            }
        });
        return Router;
    });