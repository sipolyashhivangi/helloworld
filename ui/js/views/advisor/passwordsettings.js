define([
    'handlebars',
    'backbone',
    'text!../../../html/advisor/passwordsettings.html',
], function(Handlebars, Backbone, profileTemplate) {

    var settingView = Backbone.View.extend({
        el: $("#tab-details"),
        render: function() {
			var source = $(profileTemplate).html();
            var template = Handlebars.compile(source);
            $("#tab-details").html(template());
			$('.mtab-content').removeClass('hdn');
			init();
        },
        events: {            
            "click #checkSetting": "checkSetting",            
        },
        initialize: function() {
//                this.signupButton = $("#signup");
        },
        // use this for close overlay after click close(x) link.

        checkSetting: function(event) {
			$('.alert-success').addClass('hdn');
            var pword = $('#advisorupdatepwd').val();
			var oldPassword = $('#oldpassword').val();
            var pwordRetype = $('#advisorupdatepwd2').val();
            var msg = '';
            if (pword.length < 8 || pword.match(/\s/g) != null) {
                var msg = 'Passwords must be at least 8 characters long and cannot contain spaces.';
            } else if (pword != pwordRetype) {
                msg = 'Passwords must match.'
            }
            if (msg != '') {
                $('#passwordadverror').html(msg);
                $('#passwordadvbubble').removeClass("hdn");
                $("#passwordadvidiv").addClass('error');
                PositionErrorMessage("#advisorupdatepwd", "#passwordadvbubble");
                return false;
            }
            $.ajax({
                url: advisorSettings,
                type: 'POST',
                data: {
					old_password: oldPassword,
                    password: pword,
                },
                dataType: "json",
                success: function(data) {
					timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "OK") {
                        $('#oldpassword').val('');
						$('#advisorupdatepwd').val('');
						$('#advisorupdatepwd2').val('');
						$('#msg').html('Password updated successfully.');
                        $('.alert-success').removeClass('hdn');
                    } else {
						$('#oldpasswordadverror').html(data.msg);
						$('#oldpasswordadvbubble').removeClass("hdn");
						$("#oldpasswordadvidiv").addClass('error');
						PositionErrorMessage("#oldpassword", "#oldpasswordadvbubble");
						return false;
					}
                }
            });
        }

	});
    return new settingView;
});