// Filename: views/login/advisorsignup
define([
    'jquery',
    'underscore',
    'handlebars',
    'backbone',
    'text!../../../html/advisor/filters.html',    
], function($, _, Handlebars, Backbone, advisorListTemplate) {
    var limit = 1;
    var current_size = 1;
    var filtersView = Backbone.View.extend({		
        el: $("#body"),
        render: function(obj, searchParams) {    
            var source = $(advisorListTemplate).html();
            var template = Handlebars.compile(source);
            $('#filters').html(template(obj));
            $('.advisorBox').slice(limit).hide();
            $("#myCollapsible").collapse({
				toggle: false
			});
			init();
        },
        events: {
            "keypress #searchbox": "search",
        },
        search:function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            keycode = parseInt(keycode);
            var fname = $('#searchbox').val();
            if (keycode == 13) {
                $.ajax({
                    url: searchAdvisor,
                    type: 'POST',
                    data: {
                        fname: fname
                    },
                    dataType: "json",
                    success: function(getAll) {
					timeoutPeriod = defaultTimeoutPeriod;
                        if (getAll.status == "OK") {
                            require(['views/advisor/search'],
                                    function(listV) {
                                        listV.render(getAll.userdata);
                                        if (getAll.total == 0) {
                                            $('#search-result-area').html('<div class="center" style="color: #666">No result found</div>');
                                            
                                        }
                                        $('#total').html(getAll.total);
                                        init();
                                    }
                            );

                        }
                    }
                });
            }
        }
    });

    return new filtersView;

});
	