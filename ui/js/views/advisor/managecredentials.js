define([
    'handlebars',
    'backbone',
   'text!../../../html/advisor/managecredentials.html',
], function(Handlebars, Backbone, profileTemplate) {

    var profileView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj,advisor_id) {
			var source = $(profileTemplate).html();
			var template = Handlebars.compile(source);
			$("#profileContents").html(template(obj));
			$("#advisor_id").val(advisor_id);
			popUpManageCredentials();			
		},
        events: {
            "click .cancelProfilePopup": "closeProfileDialog",            
        },
        initialize: function() {

        },
        
        closeProfileDialog: function(event) {
            event.preventDefault();
            removeLayover();           
        },
    });
    return new profileView;
});