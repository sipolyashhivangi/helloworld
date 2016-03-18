define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/signup.html',
], function(Handlebars, Backbone, signupTemplate) {

    var signupView = Backbone.View.extend({
        el: $("#signupUser"),
        render: function() {
            var source = $(signupTemplate).html();
            var template = Handlebars.compile(source);
            $(this.el).html(template());

            if ($.browser.msie && $.browser.version < 9) {
                $('#signupbrowsercheck').show();
            }
            init();
        },
        events: {
            "click #signup": "performSignup"
        },
        initialize: function() {
            this.signupButton = $("#signup");
        },
        validated: function(valid) {
            if (valid) {
                this.view.signupButton.removeAttr("disabled");
            } else {
                this.view.signupButton.attr("disabled", "true");
            }
        },
        performSignup: function(event) {
            event.preventDefault();

            $("#signup").attr("disabled", "true");
            var pword = $('#password1').val();
            var pwordRetype = $('#password2').val();
            var email = $('#email').val();
            var emailRetype = $('#email2').val();

            // START - client email validations
            if (!validateEmail(email))
            {
                $('#emailerror').html('Enter a valid email address.');
                $('#emailbubble').removeClass("hdn");
                $("#emaildiv").addClass('error');
                PositionErrorMessage("#email", "#emailbubble");
                $("#signup").removeAttr("disabled");
                return false;
            }

            if (!validateEmail(emailRetype))
            {
                $('#email2error').html('Enter a valid email address.');
                $('#email2bubble').removeClass("hdn");
                $("#email2div").addClass('error');
                PositionErrorMessage("#email2", "#email2bubble");
                $("#signup").removeAttr("disabled");
                return false;
            }

            if (email != emailRetype)
            {
                $('#emailerror').html('Email addresses must match.');
                $('#emailbubble').removeClass("hdn");
                $("#emaildiv").addClass('error');
                PositionErrorMessage("#email", "#emailbubble");
                $("#signup").removeAttr("disabled");
                return false;
            }
            // END - client email validations

            // START - client password validations
            var msg = 'Passwords must be at least eight characters in length with no spaces and include at least one uppercase letter, lowercase letter, symbol, and number.';

            if (pword != pwordRetype) {
                $('#password1error').html('Passwords must match.');
                $('#password1bubble').removeClass("hdn");
                $("#password1div").addClass('error');
                PositionErrorMessage("#password1", "#password1bubble");
                $("#signup").removeAttr("disabled");
                return false;
            }

            if (pword.length < 8 || pword.match(/\s/g) != null) {
                $('#password1error').html(msg);
                $('#password1bubble').removeClass("hdn");
                $("#password1div").addClass('error');
                PositionErrorMessage("#password1", "#password1bubble");
                $("#signup").removeAttr("disabled");
                return false;
            }
            if (pword.search(/[a-z]/) == -1) {
                $('#password1error').html(msg);
                $('#password1bubble').removeClass("hdn");
                $("#password1div").addClass('error');
                PositionErrorMessage("#password1", "#password1bubble");
                $("#signup").removeAttr("disabled");
                return false;
            }
            if (pword.search(/[A-Z]/) == -1) {
                $('#password1error').html(msg);
                $('#password1bubble').removeClass("hdn");
                $("#password1div").addClass('error');
                PositionErrorMessage("#password1", "#password1bubble");
                $("#signup").removeAttr("disabled");
                return false;
            }
            if (pword.search(/[0-9]/) == -1) {
                $('#password1error').html(msg);
                $('#password1bubble').removeClass("hdn");
                $("#password1div").addClass('error');
                PositionErrorMessage("#password1", "#password1bubble");
                $("#signup").removeAttr("disabled");
                return false;
            }
            if (pword.search(/[\W]/) == -1) {
                $('#password1error').html(msg);
                $('#password1bubble').removeClass("hdn");
                $("#password1div").addClass('error');
                PositionErrorMessage("#password1", "#password1bubble");
                $("#signup").removeAttr("disabled");
                return false;
            }
            // END - client password validations

            if (!$('#termscheck').is(':checked'))
            {
                $('#termscheckbubble').removeClass("hdn");
                $("#termscheckdiv").addClass('error');
                PositionErrorMessage("#termscheck", "#termscheckbubble");
                $("#signup").removeAttr("disabled");
                return false;
            }

            var redirectUrl = getQueryVariable('redirectUrl');
            if (redirectUrl != "") {
                var location = redirectUrl;
            } else {
                var location = baseUrl + '/myscore';
            }

            var formValues = {
                password: pword,
                email: email,
                jsMixpanelCall: true
            };

            $.ajax({
                url: signupUrl,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    if (data.status == "ERROR")
                    {
                        $("#signup").removeAttr("disabled");
                        if (data.type == 'email')
                        {
                            $('#emailerror').html(data.message);
                            $('#emailbubble').removeClass("hdn");
                            $("#emaildiv").addClass('error');
                            PositionErrorMessage("#email", "#emailbubble");
                        }
                        else if (data.type == 'password')
                        {
                            $('#password1error').html(data.message);
                            $('#password1bubble').removeClass("hdn");
                            $("#password1div").addClass('error');
                            PositionErrorMessage("#password1", "#password1bubble");
                        }
                        else
                        {
                            $('#emailerror').html(data.message);
                            $('#emailbubble').removeClass("hdn");
                            $("#emaildiv").addClass('error');
                            PositionErrorMessage("#email", "#emailbubble");
                        }
                    }
                    else
                    {
                        if (typeof (sendMixpanel) != 'undefined' && sendMixpanel) {
                            mixpanel.identify(data.uniquehash);
                            mixpanel.people.set_once({
                                'First Login Date': new Date()
                            });
                            mixpanel.track('New User', {
                                'new_user': data.uniquehash,
                                'Created By': 'user'
                            }, function() {
                                localStorage[serverSess] = data.sess;
                                localStorage["showNewUserDialog"] = true;
                                localStorage["showNewAdvisorDialog"] = false;
                                window.location = location;
                            });
                        }
                        else
                        {
                            localStorage[serverSess] = data.sess;
                            localStorage["showNewUserDialog"] = true;
                            localStorage["showNewAdvisorDialog"] = false;
                            window.location = location;
                        }
                    }
                },
                error: function(data) {
                    $('#emailerror').html('We are unable to add this user.');
                    $('#emailbubble').removeClass("hdn");
                    $("#emaildiv").addClass('error');
                    $("#signup").removeAttr("disabled");
                    PositionErrorMessage("#email", "#emailbubble");
                }
            });
            return false;
        }
    });
    return new signupView;
});
