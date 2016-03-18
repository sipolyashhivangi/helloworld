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
            var postUrl = 'learningcenter';
            var categoryUrl = 'category';
            var searchUrl = 'search';
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
                                    $("#gnav_learning").addClass("hover reverseShadowBox");
                                    $("#gnav_learning").removeClass("gnavButton");
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
                                    $("#body").show();
                                    $("#gnav_lc .learningcenter").css("color", "#333");
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

            if (params["type"] == "learningcenter" && typeof(params["id"]) != 'undefined' && params["id"] != '')
            {
                require(
                        ['views/marketing/post'],
                        function(postView) {
                            var id = params["id"];
                            var formValues = {
                                id: id
                            }
                            $.ajax({
                                url: learningCenterPostURL,
                                type: 'POST',
                                dataType: "json",
                                data: formValues,
                                success: function(jsonData) {
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
            else if (params["type"] == "search" && typeof(params["id"]) != 'undefined' && params["id"] != "")
            {
                require(
                        ['views/marketing/searchresult', 'views/marketing/post'],
                        function(searchView, postView) {
                            var searchValue = decodeURIComponent(params["id"]);
                            var formValues = {
                                search: searchValue
                            };
                            $.ajax({
                                url: learningCenterPostSearchURL,
                                type: 'POST',
                                dataType: "json",
                                data: formValues,
                                success: function(jsonData) {
                                    $("#body").show();
                                    $('#showSearchHead').show();
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
            else if (params["type"] == "category" && typeof(params["id"]) != 'undefined' && params["id"] != '')
            {
                require(
                        ['views/marketing/searchresult', 'views/marketing/post'],
                        function(searchresultView, postView) {
                            var id = params["id"];
                            var formValues = {
                                catid: id
                            };

                            $.ajax({
                                url: learningCenterSearchByCatURL,
                                type: 'POST',
                                dataType: "json",
                                data: formValues,
                                success: function(jsonData) {
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
            else if (params["type"] == "glossary" && typeof(params["id"]) != 'undefined' && params["id"] != "")
            {
                require(
                        ['views/marketing/gloss'],
                        function(glossView) {
                            var id = params["id"];
                            if (id == "0")
                                id = "#";
                            var formValues = {
                                postLetter: id
                            };
                            $.ajax({
                                url: learningCenterGlossaryURL,
                                type: 'POST',
                                dataType: "json",
                                data: formValues,
                                success: function(jsonData) {
                                    $("#body").show();
                                    jsonData.posturl = postUrl;
                                    jsonData.categoryurl = categoryUrl;
                                    jsonData.searchurl = searchUrl;
                                    glossView.render(jsonData);
                                    $(".articleNavOn").addClass("hdn");
                                    $(".articleNavOff").removeClass("hdn");
                                    if (id == "#")
                                        id = "num";
                                    $("#" + id + "GlossaryInactive").removeClass("hdn");
                                    $("#" + id + "GlossaryActive").addClass("hdn");
                                    init();
                                }
                            });
                        });
            }
            else
            {
                require(
                        ['views/marketing/summary'],
                        function(summaryV) {
                            $.ajax({
                                url: learningCenterURL,
                                type: 'GET',
                                dataType: "json",
                                success: function(jsonData) {
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