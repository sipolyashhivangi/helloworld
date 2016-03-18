define([
    'handlebars',
    'text!../../../html/marketing/searchresult.html',
    'text!../../../html/user/share.html'
], function(Handlebars, searchresultTemplate, shareTemplate) {

    var searchresultView = Backbone.View.extend({
        el: $("#body"),
        render: function(jsonData) {
            var sourcePost = $(searchresultTemplate).html();
            var templatePost = Handlebars.compile(sourcePost);
            if(typeof(jsonData) != 'undefined' && typeof(jsonData.getLatestArticles) != 'undefined') {
				Modernizr.addTest('sandbox', 'sandbox' in document.createElement('iframe'));
            	for(var i = 0; i < jsonData.getLatestArticles.length; i++) {
            		jsonData.getLatestArticles[i].baseUrl = baseUrl;
                    if(Modernizr.video && Modernizr.sandbox) {
                        jsonData.getLatestArticles[i].showSandbox = true;
                    }
					for(var j = 0; j < jsonData.getLatestArticles[i].categories.length; j++) {
						jsonData.getLatestArticles[i].categories[j].baseUrl = baseUrl;
						jsonData.getLatestArticles[i].categories[j].post_id = jsonData.getLatestArticles[i].post_id;
					}
            	}
            }
            if(typeof(jsonData) != 'undefined' && typeof(jsonData.articles) != 'undefined') {
            	for(var i = 0; i < jsonData.articles.length; i++) {
            		jsonData.articles[i].baseUrl = baseUrl;
            	}
            }
            if(typeof(jsonData) != 'undefined' && typeof(jsonData.articlesmore) != 'undefined') {
            	for(var i = 0; i < jsonData.articlesmore.length; i++) {
            		jsonData.articlesmore[i].baseUrl = baseUrl;
            	}
            }
            $("#idArticleContent").html(templatePost(jsonData));
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
            	$(".span9").removeClass("floatR");
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
            $("#idArticleContent").attr('style', 'padding:0px');
        },
        events: {
           "click .readMoreSearch": "fnLoadPost",
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

            var str = "RecommendedLink";
            var id = parentid.substring(0, parentid.length - str.length);
            window.location = $("#" + id + str).attr('href');
        },
    });
    return new searchresultView;
});