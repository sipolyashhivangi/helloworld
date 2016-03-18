define([
    'handlebars',
    'text!../../../html/marketing/post.html',
    'text!../../../html/user/share.html'
], function(Handlebars, postTemplate, shareTemplate) {

    var postView = Backbone.View.extend({
        el: $("#body"),
        render: function(jsonData) {
            var sourcePost = $(postTemplate).html();
            var templatePost = Handlebars.compile(sourcePost);
                            
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

            if(typeof(jsonData) != 'undefined' && typeof(jsonData.categories) != 'undefined') {
           		for(var i = 0; i < jsonData.categories.length; i++) {
           			jsonData.categories[i].baseUrl = baseUrl;
            	}
   	        }
            if(typeof(jsonData) != 'undefined') {
           		jsonData.baseUrl = baseUrl;
			}

            if (typeof(jsonData.article) != 'undefined')
            {
				Modernizr.addTest('sandbox', 'sandbox' in document.createElement('iframe'));
                if(Modernizr.video && Modernizr.sandbox) {
                    jsonData.article[0].showSandbox = true;
                }
                jsonData.article[0].encoded_title = encodeURIComponent(jsonData.article[0].post_title).replace(/'/g, "\\'");
                var url = window.location.href.substr(0, window.location.href.indexOf('/' + jsonData.posturl)) + "/" + jsonData.posturl + "/" + jsonData.article[0].post_name;
                var encodedurl = encodeURIComponent(url);
                jsonData.article[0].url = encodedurl;
                $('#mainBody').html(templatePost(jsonData));

                var media = 'https://www.flexscore.com/ui/images/home_laptop.png';
                if (typeof(jsonData.article[0].files) != 'undefined' && jsonData.article[0].files.length > 0 && typeof(jsonData.article[0].files[0].images) != 'undefined')
                {
                    media = jsonData.article[0].files[0].images.link;
                }
                else if (typeof(jsonData.article[0].files) != 'undefined' && jsonData.article[0].files.length > 0 && typeof(jsonData.article[0].files[0].youtube) != 'undefined')
                {
                    media = "https://img.youtube.com/vi/" + jsonData.article[0].files[0].youtube.link + "/0.jpg";
                    ///Update score engine for learning videos
                }
                if (typeof(jsonData.article[0].user_id) != 'undefined' && jsonData.posturl == 'learningcenter' && localStorage.updatelearning != "false") {
                    var formFields = {
                        id: jsonData.article[0].post_id
                    }
                    if (typeof(jsonData.article[0].files) != 'undefined' && jsonData.article[0].files.length > 0 && typeof(jsonData.article[0].files[0].youtube) != 'undefined')
                    {
                        formFields.name = jsonData.article[0].key;
                    }

                    $.ajax({
                        url: addeditlearningURL,
                        type: 'POST',
                        dataType: "json",
                        data: formFields
                    });
                }
				localStorage.updatelearning = true;                    
                var obj = {
                    'media': media,
                    'url': url,
                    'encodedurl': encodedurl,
                    'pin_desc': jsonData.article[0].encoded_title,
                    'twitter_title': jsonData.article[0].encoded_title
                }
                sourcePost = $(shareTemplate).html();
                templatePost = Handlebars.compile(sourcePost);
                $('#shareDiv').html(templatePost(obj));
            }
            else
            {
                $('#mainBody').html(templatePost(jsonData)); //added this to fix "show all" link to work
            }
        },
        events: {
            "click .searchBtnPost": "fnTopicSearch",
            "keypress #search": "fnCheckSearch",
        },
        fnCheckSearch: function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                event.preventDefault();
                $(".searchBtnPost").click();
            }
        },
        fnTopicSearch: function(event) {
            event.preventDefault();
            var searchValue = $('#search').val().trim();
            var searchUrl = event.target.id;
            searchUrl = searchUrl.substr(0, searchUrl.indexOf('-'));
            if (searchValue != "")
            {
                window.location = baseUrl + "/" + searchUrl + "/" + searchValue;
            }
        },
    });
    return new postView;
});