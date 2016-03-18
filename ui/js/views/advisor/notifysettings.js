define([
    'handlebars',
    'backbone',
    'text!../../../html/advisor/notifysettings.html',
], function(Handlebars, Backbone, profileTemplate) {

    var settingView = Backbone.View.extend({
        el: $("#tab-details"),
        render: function(obj) {
			var source = $(profileTemplate).html();
            var template = Handlebars.compile(source);
            $("#tab-details").html(template(obj));
			$('.mtab-content').removeClass('hdn');
        },
        events: {            
            "click #communication": "commSetting",            
        },
        initialize: function() {
//                this.signupButton = $("#signup");
        },
        // use this for close overlay after click close(x) link.

        commSetting: function(event) {
			$('.alert-success').addClass('hdn');
            if ($('#notifyContact').is(':checked'))
                notify = 1;
            else
                notify = 0
            $.ajax({
                url: notifySettings,
                type: 'POST',
                data: {
                    notify: notify,
                },
                dataType: "json",
                success: function(data) {
				timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "OK") {
                        $('#msg').html('Notification settings saved successfully');
                        $('.alert-success').removeClass('hdn');
                    }
                }
            });
        },
	});
    return new settingView;
});

function showProfilePic2(imgSrc) {
		$('#profile-img').html('');
		$('#profile-img').html('<img id="pic" src="' + imgSrc + '" alt="upload photo">');
		}