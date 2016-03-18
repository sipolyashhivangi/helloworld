define([
    'handlebars',
    'text!../../../html/marketing/gloss.html'
], function(Handlebars, glossTemplate) {
    var postView = Backbone.View.extend({
        el: $("#mainBody"),
        render: function(jsonData) {
            var sourceGloss = $(glossTemplate).html();
            var templatePost = Handlebars.compile(sourceGloss);
            if(typeof(jsonData) != 'undefined' && typeof(jsonData.categories) != 'undefined') {
        		for(var i = 0; i < jsonData.categories.length; i++) {
        			jsonData.categories[i].baseUrl = baseUrl;
            	}
 	        }
            if(typeof(jsonData) != 'undefined') {
           		jsonData.baseUrl = baseUrl;
			}
            $("#mainBody").html(templatePost(jsonData));
        },
        events: {
            "click .searchBtnGloss": "fnTopicSearch",
            "keypress #search": "fnCheckSearch",
        },
        fnCheckSearch: function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                event.preventDefault();
                $(".searchBtnGloss").click();
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