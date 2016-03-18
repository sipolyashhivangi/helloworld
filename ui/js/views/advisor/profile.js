define([
    'handlebars',
    'backbone',
    'text!../../../html/advisor/profile.html',
], function(Handlebars, Backbone, profileTemplate) {
    var profileView = Backbone.View.extend({
        el: $("#profileContents"),
        render: function(obj, assign) {
            var source = $(profileTemplate).html();
            var template = Handlebars.compile(source);
            $("#profileContents").html(template(obj, assign));
			if (assign == 1) {
				$('#releasebtn').show();
				$('#assignbtn').hide();
			} else {
				$('#releasebtn').hide();
				$('#assignbtn').show();
			}	
            var minAssist = commaSeparateNumber(obj.minasstsforpersclient,0);
            if (minAssist == null || minAssist === '') { minAssist = 0; }
            $('#minimumAssest').text(minAssist);
			popUpManageCredentials();
        },
        events: {
            "click .cancelProfilePopup": "closeProfileDialog"            
        },
		closeProfileDialog: function(event) {
			event.preventDefault();
            removeLayover();
        }
    });
    return new profileView;
});