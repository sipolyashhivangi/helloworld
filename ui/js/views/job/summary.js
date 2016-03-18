// Filename: views/learningcenter/summary
define([
    'handlebars',
    'text!../../../html/job/summary.html',
    'text!../../../html/user/share.html'
], function(Handlebars, summaryTemplate,shareTemplate) {

    var summaryView = Backbone.View.extend({
        el: $("#body"),
        render: function(jsonData) {
            var source = $(summaryTemplate).html();
            var template = Handlebars.compile(source);

            if(typeof(jsonData) != 'undefined' && typeof(jsonData.getLatestArticles) != 'undefined') {
				Modernizr.addTest('sandbox', 'sandbox' in document.createElement('iframe'));
                for(var i = 0; i < jsonData.getLatestArticles.length; i++) {
                    jsonData.getLatestArticles[i].baseUrl = baseUrl;
                    if(Modernizr.video && Modernizr.sandbox) {
                        jsonData.getLatestArticles[i].showSandbox = true;
                    }
                    
                }
            }
            
            if(typeof(jsonData) != 'undefined') {
                jsonData.baseUrl = baseUrl;
            }
            $("#mainBody").html(template(jsonData));

            sourcePost = $(shareTemplate).html();
            templatePost = Handlebars.compile(sourcePost);

            if(typeof(jsonData) != 'undefined' && jsonData.checklogin == 'not_loggedin') {
                require(
                    ['views/profile/purplesignup'],
                    function( pSignupV) {
                        pSignupV.render();
                        $("#bottomSignup").css('margin-top','10px');
                        $("#bottomSignup").css('margin-bottom','10px');
                        init();
                        initSignupButton();
						calcResize();
                    }
                );
            }

            if(typeof(jsonData) != 'undefined' && typeof(jsonData.getLatestArticles) != 'undefined') {
                for(var i = 0; i < jsonData.getLatestArticles.length; i++) {
                    var encoded_title = encodeURIComponent(jsonData.getLatestArticles[i].post_title).replace(/'/g, "\\'");
                    var url = jsonData.getLatestArticles[i].baseUrl + "/" + jsonData.getLatestArticles[i].post_url + "/" + jsonData.getLatestArticles[i].post_name;
                    var encodedurl = encodeURIComponent(url);

                    var media = 'https://www.flexscore.com/ui/images/home_laptop.png';
                    if (typeof(jsonData.getLatestArticles[i].images) != 'undefined' && typeof(jsonData.getLatestArticles[i].images.link) != 'undefined')
                    {
                        media = jsonData.getLatestArticles[i].images.link;
                    }
                    else if (typeof(jsonData.getLatestArticles[i].youtube) != 'undefined' && typeof(jsonData.getLatestArticles[i].youtube.link) != 'undefined')
                    {
                        media = "https://img.youtube.com/vi/" + jsonData.getLatestArticles[i].youtube.link + "/0.jpg";
                        ///Update score engine for learning videos
                    }
                    var obj = {
                        'media': media,
                        'url': url,
                        'encodedurl': encodedurl,
                        'pin_desc': encoded_title,
                        'twitter_title': encoded_title
                    }
                    $('#' + jsonData.getLatestArticles[i].post_id + 'shareDiv').html(templatePost(obj));
                }
            }

            lcSummary = jsonData;
            localStorage.updatelearning = true;                    
        },
        events: {
            "click .searchBtnHome": "fnTopicSearch",
            "click .readMoreSummary": "fnLoadPost",
            "keypress #inputIcon": "fnCheckSearch",
            "change .catinput": "fnChangeCatValue"
        },
        
        fnChangeCatValue: function(event){
                event.preventDefault();
                var blogCategory = $('#blogCategory option:selected').val();
                if(blogCategory!=""){
                location.href=blogCategory;
                }
        },
        fnCheckSearch: function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                event.preventDefault();
                $(".searchBtnHome").click();
            }
        },
        fnLoadPost: function(event) {
            if($(event.target).hasClass('shareLink') || $(event.target).hasClass('categoryLink')) {
                return;
            }
            event.preventDefault();
            var parentid = event.currentTarget.id;
            var elementid = event.target.id;
            if (elementid != "" && elementid != parentid)
                return;
            if (parentid.indexOf('RecommendedHead') != -1 || parentid.indexOf('RecommendedLink') != -1) {
                var str = "RecommendedLink";
            } else {
                var str = "FeaturedVidLink";
            }
            var id = parentid.substring(0, parentid.length - str.length);
            window.location = $("#" + id + str).attr('href');
        },
        fnTopicSearch: function(event) {
            event.preventDefault();
            var searchValue = $('#inputIcon').val().trim();
            var searchUrl = event.target.id;
            searchUrl = searchUrl.substr(0, searchUrl.indexOf('-'));
            if (searchValue != "")
            {
                window.location = baseUrl + "/" + searchUrl + "/" + searchValue;
            }
        }
    });
    return new summaryView;
});