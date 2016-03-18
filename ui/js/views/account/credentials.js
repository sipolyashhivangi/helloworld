define([
    'handlebars',
    'backbone',
    'text!../../../html/account/credentials.html',
], function(Handlebars, Backbone, profileTemplate) {

    var settingView = Backbone.View.extend({
        el: $("body"),
        render: function() {
         if(typeof(userData.advisor) != 'undefined' && typeof(userData.user) != 'undefined'                       
           && ($("#currentPage").val() == "myscore" || $("#currentPage").val() == "financialsnapshot")) {
            userData.user.impersonationMode = true;
            if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                userData.permissions = true;
            }
        }
            var source = $(profileTemplate).html();
            var template = Handlebars.compile(source);

            $("#settingsDetails").html(template(userData));
            timeoutPeriod = defaultTimeoutPeriod;
            init();
        },
        events: {
            "click #changePasswordButton": "changePassword",
            "keypress .profileAccSpecial" : "checkPasswordKey"
        },
        initialize: function() {
        },
        // use this for close overlay after click close(x) link.
        checkPasswordKey: function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            $("#passwordMsg").html('&nbsp;');
            if (keycode == '13') {
                event.preventDefault();
                $("#changePasswordButton").click();
            }
        },
        changePassword: function(event) {
            event.preventDefault();
            $('.alert-success').addClass('hdn');
            var oldPassword = $('#oldpassword').val();
            var pword = $('#password1').val();
            var pwordRetype = $('#password2').val();
            var msg = '';

            // START - client password validations
            if (pword != pwordRetype) {
                $('#password1error').html('Passwords must match.');
                $('#password1bubble').removeClass("hdn");
                $("#password1div").addClass('error');
                PositionErrorMessage("#password1", "#password1bubble");
                $('#forgotButton').removeAttr("disabled");
                return false;
            }

            if (pword.length < 8 || pword.match(/\s/g) != null) {
                $('#password1error').html('Passwords must be at least eight characters in length with no spaces and include at least one uppercase letter, lowercase letter, symbol, and number.');
                $('#password1bubble').removeClass("hdn");
                $("#password1div").addClass('error');
                PositionErrorMessage("#password1", "#password1bubble");
                $('#forgotButton').removeAttr("disabled");
                return false;
            }

            if (pword.search(/[a-z]/) == -1) {
                $('#password1error').html('Passwords must be at least eight characters in length with no spaces and include at least one uppercase letter, lowercase letter, symbol, and number.');
                $('#password1bubble').removeClass("hdn");
                $("#password1div").addClass('error');
                PositionErrorMessage("#password1", "#password1bubble");
                $('#forgotButton').removeAttr("disabled");
                return false;
            }
            if (pword.search(/[A-Z]/) == -1) {
                $('#password1error').html('Passwords must be at least eight characters in length with no spaces and include at least one uppercase letter, lowercase letter, symbol, and number.');
                $('#password1bubble').removeClass("hdn");
                $("#password1div").addClass('error');
                PositionErrorMessage("#password1", "#password1bubble");
                $('#forgotButton').removeAttr("disabled");
                return false;
            }
            if (pword.search(/[0-9]/) == -1) {
                $('#password1error').html('Passwords must be at least eight characters in length with no spaces and include at least one uppercase letter, lowercase letter, symbol, and number.');
                $('#password1bubble').removeClass("hdn");
                $("#password1div").addClass('error');
                PositionErrorMessage("#password1", "#password1bubble");
                $('#forgotButton').removeAttr("disabled");
                return false;
            }
            if (pword.search(/[\W]/) == -1) {
                $('#password1error').html('Passwords must be at least eight characters in length with no spaces and include at least one uppercase letter, lowercase letter, symbol, and number.');
                $('#password1bubble').removeClass("hdn");
                $("#password1div").addClass('error');
                PositionErrorMessage("#password1", "#password1bubble");
                $('#forgotButton').removeAttr("disabled");
                return false;
            }
            // END - client password validations


            $.ajax({
                url: changePwdURL,
                type: 'POST',
                data: {
                    oldpassword: oldPassword,
                    password1: pword,
                    password2: pwordRetype
                },
                dataType: "json",
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                     if (data.status == "OK") {
                        $('#oldpassword').val('');
                        $('#password1').val('');
                        $('#password2').val('');
                        $("#oldpassword").focus();
                        $("#passwordMsg").html('Password updated successfully.');
                        $('.user-settings-success').removeClass('hdn');
                        return false;
                    }else if (data.status == "ERROR" && typeof(data.type) != 'undefined' && data.type == 'password1') {
                        $('#password1error').html(data.message);
                        $('#password1bubble').removeClass("hdn");
                        $("#password1div").addClass('error');
                        PositionErrorMessage("#password1", "#password1bubble");
                        return false;
                    } else {
                        $('#oldpassworderror').html(data.message);
                        $('#oldpasswordbubble').removeClass("hdn");
                        $("#oldpassworddiv").addClass('error');
                        PositionErrorMessage("#oldpassword", "#oldpasswordbubble");
                        return false;
                    }
                }
            });
        }

    });
    return new settingView;
});