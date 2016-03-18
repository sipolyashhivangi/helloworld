define([
    'jquery',
    'underscore',
    'backbone'
], function($, _, Backbone) {
    var Router = Backbone.Router.extend({
        routes: {
            //"post/:slug": "fnLearningCenterPost"
        },
        initialize: function() {
            userData = null;
            var postUrl = 'blog';
            var categoryUrl = 'blogcategory';
            var searchUrl = 'blogsearch';
            sess = localStorage[serverSess];

            $.ajax({
                url: loginCheckUrl + "?sess=" + sess,
                type: 'GET',
                dataType: "json",
                success: function(data) {
                    userData = data;
                    $('#body').show();
                    if (data.status == "OK") {
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
                    } else {
                        require(
                                ['views/profile/login', 'views/profile/signup', 'views/profile/advisor', 'views/base/headerloggedout', 'views/base/footer'],
                                function(loginV, signupV, advisorV, headerV, footerV) {
                                    headerV.render(data);
                                    footerV.render();
                                    loginV.render();
                                    advisorV.render(data);
                                    signupV.render();
                                    $("signuploginContent").removeClass("hdn");
                                    init();
                                    $("#gnav_blog .bloglink").css("color", "#333");
                                    $("#body").show();
                                }
                        );
                    }
                }
            });

            var prmstr = window.location.pathname;
            if (prmstr.length > 0)
                prmstr = prmstr.substr(1);
            var prmarr = prmstr.split("/");
            var params = {};
            if (prmarr.length == 3) {
                params["type"] = prmarr[1];
                params["id"] = prmarr[2];
            } else {
                params["type"] = prmarr[0];
                params["id"] = prmarr[1];
            }

            if (params["type"] == "blog" && typeof(params["id"]) != 'undefined' && params["id"] != '')
            {
                require(
                        ['views/marketing/post'],
                        function(postView) {
                            var id = params["id"];
                            var formValues = {
                                id: id
                            }
                            $.ajax({
                                url: blogviewURL,
                                type: 'POST',
                                dataType: "json",
                                data: formValues,
                                success: function(jsonData) {
                                    var Month = new Array("Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
                                    if (jsonData.recoposts.length > 0) {
                                        for (var i = 0; i < jsonData.recoposts.length; i++) {
                                            var serverTime = new Date(jsonData.recoposts[i].posted_date); 
                                            var intMonth = serverTime.getMonth();
                                            var browserFullDate = Month[intMonth]+" "+ serverTime.getDate()+", "+ serverTime.getFullYear();
                                            jsonData.recoposts[i].posted_date = browserFullDate;
                                        }
                                    }
                                    $("#body").show();
                                    jsonData.posturl = postUrl;
                                    jsonData.categoryurl = categoryUrl;
                                    jsonData.searchurl = searchUrl;
                                    postView.render(jsonData);
                                    init();
                                }
                            });
                        });
            }
            else if (params["type"] == "blogsearch" && typeof(params["id"]) != 'undefined' && params["id"] != "")
            {
                require(
                        ['views/marketing/searchresult', 'views/marketing/post'],
                        function(searchView, postView) {
                            var searchValue = decodeURIComponent(params["id"]);
                            var formValues = {
                                search: searchValue
                            };
                            $.ajax({
                                url: blogSearchURL,
                                type: 'POST',
                                dataType: "json",
                                data: formValues,
                                success: function(jsonData) {
                                    var Month = new Array("Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
                                    if (jsonData.recoposts.length > 0) {
                                        for (var i = 0; i < jsonData.recoposts.length; i++) {
                                            var serverTime = new Date(jsonData.recoposts[i].posted_date); 
                                            var intMonth = serverTime.getMonth();
                                            var browserFullDate = Month[intMonth]+" "+ serverTime.getDate()+", "+ serverTime.getFullYear();
                                            jsonData.recoposts[i].posted_date = browserFullDate;
                                        }
                                    }
                                    if (jsonData.getLatestArticles.length > 0) {
                                        for (var i = 0; i < jsonData.getLatestArticles.length; i++) {
                                            var serverTime = new Date(jsonData.getLatestArticles[i].posted_date); 
                                            var intMonth = serverTime.getMonth();
                                            var browserFullDate = Month[intMonth]+" "+ serverTime.getDate()+", "+ serverTime.getFullYear();
                                            jsonData.getLatestArticles[i].posted_date = browserFullDate;
                                        }
                                    }
                                    $("#body").show();
                                    jsonData.posturl = postUrl;
                                    jsonData.categoryurl = categoryUrl;
                                    jsonData.searchurl = searchUrl;
                                    postView.render(jsonData);
                                    searchView.render(jsonData);
                                    init();
                                }
                            });
                        });
            }
            else if (params["type"] == "blogcategory" && typeof(params["id"]) != 'undefined' && params["id"] != '')
            {
                require(
                        ['views/marketing/searchresult', 'views/marketing/post'],
                        function(searchresultView, postView) {
                            var id = params["id"];
                            var formValues = {
                                catid: id
                            };
                            $.ajax({
                                url: blogCatURL,
                                type: 'POST',
                                dataType: "json",
                                data: formValues,
                                success: function(jsonData) {
                                    var Month = new Array("Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
                                    if (jsonData.recoposts.length > 0) {
                                        for (var i = 0; i < jsonData.recoposts.length; i++) {
                                            var serverTime = new Date(jsonData.recoposts[i].posted_date); 
                                            var intMonth = serverTime.getMonth();
                                            var browserFullDate = Month[intMonth]+" "+ serverTime.getDate()+", "+ serverTime.getFullYear();
                                            jsonData.recoposts[i].posted_date = browserFullDate;
                                        }
                                    }
                                    if (jsonData.getLatestArticles.length > 0) {
                                        for (var i = 0; i < jsonData.getLatestArticles.length; i++) {
                                            var serverTime = new Date(jsonData.getLatestArticles[i].posted_date); 
                                            var intMonth = serverTime.getMonth();
                                            var browserFullDate = Month[intMonth]+" "+ serverTime.getDate()+", "+ serverTime.getFullYear();
                                            jsonData.getLatestArticles[i].posted_date = browserFullDate;
                                        }
                                    }
                                    $("#body").show();
                                    jsonData.posturl = postUrl;
                                    jsonData.categoryurl = categoryUrl;
                                    jsonData.searchurl = searchUrl;
                                    postView.render(jsonData);
                                    searchresultView.render(jsonData);
                                    $(".articleNavOn").addClass("hdn");
                                    $(".articleNavOff").removeClass("hdn");
                                    $("#" + jsonData.catid + "CategoryListActive").removeClass("hdn");
                                    $("#" + jsonData.catid + "CategoryListInActive").addClass("hdn");
                                    init();
                                }
                            });
                        }
                );
            }
            else
            {
                require(
                        ['views/marketing/summary'],
                        function(summaryV) {
                            $.ajax({
                                url: blogURL,
                                type: 'GET',
                                dataType: "json",
                                success: function(jsonData) {
                                    var Month = new Array("Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
                                    if (jsonData.getLatestArticles.length > 0) {
                                        for (var i = 0; i < jsonData.getLatestArticles.length; i++) {
                                            var serverTime = new Date(jsonData.getLatestArticles[i].posted_date); 
                                            var intMonth = serverTime.getMonth();
                                            var browserFullDate = Month[intMonth]+" "+ serverTime.getDate()+", "+ serverTime.getFullYear();
                                            jsonData.getLatestArticles[i].posted_date = browserFullDate;
                                        }
                                    }
                                    if (jsonData.recoposts.length > 0) {
                                        for (var i = 0; i < jsonData.recoposts.length; i++) {
                                            var serverTime = new Date(jsonData.recoposts[i].posted_date); 
                                            var intMonth = serverTime.getMonth();
                                            var browserFullDate = Month[intMonth]+" "+ serverTime.getDate()+", "+ serverTime.getFullYear();
                                            jsonData.recoposts[i].posted_date = browserFullDate;
                                        }
                                    }
                                    if (jsonData.recopostsmore.length > 0) {
                                        for (var i = 0; i < jsonData.recopostsmore.length; i++) {
                                            var serverTime = new Date(jsonData.recopostsmore[i].posted_date); 
                                            var intMonth = serverTime.getMonth();
                                            var browserFullDate = Month[intMonth]+" "+ serverTime.getDate()+", "+ serverTime.getFullYear();
                                            jsonData.recopostsmore[i].posted_date = browserFullDate;
                                        }
                                    }
                                    $("#body").show();
                                    jsonData.posturl = postUrl;
                                    jsonData.categoryurl = categoryUrl;
                                    jsonData.searchurl = searchUrl;
                                    summaryV.render(jsonData);
                                    init();
                                }
                            });
                        }
                );
            }
        }
    });
    return Router;
});