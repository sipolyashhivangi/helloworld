define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/purplesignup.html',
], function(Handlebars, Backbone, signupTemplate) {

    var signupView = Backbone.View.extend({
        el: $("#bottomSignUpDiv"),
        render: function() {
            var source = $(signupTemplate).html();
            var template = Handlebars.compile(source);
            $(this.el).html(template());
            init();
            $.fn.placeholder();
        },
        events: {
            "click #bottomSignup": "performBottomSignup"
        },
        initialize: function() {
            this.signupButton = $("#bottomSignUpButton");
        },
        validated: function(valid) {
            if (valid) {
                this.view.signupButton.removeAttr("disabled");
            } else {
                this.view.signupButton.attr("disabled", "true");
            }
        },
        performBottomSignup: function(event) {
            event.preventDefault();
            $("#bottomSignup").attr("disabled", "true");
            $('#alert-error-signup').hide();
            var pword = '';
            var pwordRetype = '';
            var email = '';
            var emailRetype = '';
            if ($('#bottomPassword1').attr('type') == 'password') {
                pword = $('#bottomPassword1').val();
            }
            if ($('#bottomPassword2').attr('type') == 'password') {
                pwordRetype = $('#bottomPassword2').val();
            }
            if ($('#bottomEmail').attr('placeholder') != $('#bottomEmail').val()) {
                email = $('#bottomEmail').val();
            }
            if ($('#bottomEmail2').attr('placeholder') != $('#bottomEmail2').val()) {
                emailRetype = $('#bottomEmail2').val();
            }
            var termsChecked = $('#bottomTermsCheck').is(':checked');

            if (!validateEmail(email))
            {
                currentErrorMsg = "Enter a valid email address.";
                currentErrorType = "email";
                $("#signupPopupButton").click();
                $("#email").val(email);
                $("#email2").val(emailRetype);
                $("#password1").val(pword);
                $("#password2").val(pwordRetype);
                $("#termscheck").prop('checked', termsChecked);
                $(".controls").removeClass('error');
                $('#emailerror').html('Enter a valid email address.');
                $('#emailbubble').removeClass("hdn");
                $("#emaildiv").addClass('error');
                $("#showSignUp").click();
                $("#bottomSignup").removeAttr("disabled");
                PositionErrorMessage("#email", "#emailbubble");
                $("#bottomEmail").val("");
                $("#bottomEmail2").val("");
                $("#bottomPassword1").val("");
                $("#bottomPassword2").val("");
                $("#bottomEmail").blur();
                $("#bottomEmail2").blur();
                $("#bottomPassword1").blur();
                $("#bottomPassword2").blur();
                $("#bottomTermsCheck").prop('checked', false);
                return false;
            }

            if (!validateEmail(emailRetype))
            {
                currentErrorMsg = "Enter a valid email address.";
                currentErrorType = "email";
                $("#signupPopupButton").click();
                $("#email").val(email);
                $("#email2").val(emailRetype);
                $("#password1").val(pword);
                $("#password2").val(pwordRetype);
                $("#termscheck").prop('checked', termsChecked);
                $(".controls").removeClass('error');
                $('#email2error').html('Enter a valid email address.');
                $('#email2bubble').removeClass("hdn");
                $("#email2div").addClass('error');
                $("#showSignUp").click();
                $("#bottomSignup").removeAttr("disabled");
                PositionErrorMessage("#email2", "#email2bubble");
                $("#bottomEmail").val("");
                $("#bottomEmail2").val("");
                $("#bottomPassword1").val("");
                $("#bottomPassword2").val("");
                $("#bottomEmail").blur();
                $("#bottomEmail2").blur();
                $("#bottomPassword1").blur();
                $("#bottomPassword2").blur();
                $("#bottomTermsCheck").prop('checked', false);
                return false;
            }

            if(email != emailRetype) {
                currentErrorMsg = "Email addresses must match.";
                currentErrorType = "email";
                $("#signupPopupButton").click();
                $("#email").val(email);
                $("#email2").val(emailRetype);
                $("#password1").val(pword);
                $("#password2").val(pwordRetype);
                $("#termscheck").prop('checked', termsChecked);
                $(".controls").removeClass('error');
                $('#emailerror').html('Email addresses must match.');
                $('#emailbubble').removeClass("hdn");
                $("#emaildiv").addClass('error');
                $("#showSignUp").click();
                $("#bottomSignup").removeAttr("disabled");
                PositionErrorMessage("#email", "#emailbubble");
                $("#bottomEmail").val("");
                $("#bottomEmail2").val("");
                $("#bottomPassword1").val("");
                $("#bottomPassword2").val("");
                $("#bottomEmail").blur();
                $("#bottomEmail2").blur();
                $("#bottomPassword1").blur();
                $("#bottomPassword2").blur();
                $("#bottomTermsCheck").prop('checked', false);
                return false;
            }

            //client validations
            if (pword != pwordRetype) {
                currentErrorMsg = "Passwords must match.";
                currentErrorType = "password1";
                $("#signupPopupButton").click();
                $("#email").val(email);
                $("#email2").val(emailRetype);
                $("#password1").val(pword);
                $("#password2").val(pwordRetype);
                $("#termscheck").prop('checked', termsChecked);
                $(".controls").removeClass('error');
                $('#password1error').html('Passwords must match.');
                $('#password1bubble').removeClass("hdn");
                $("#password1div").addClass('error');
                $("#showSignUp").click();
                $("#bottomSignup").removeAttr("disabled");
                PositionErrorMessage("#password1", "#password1bubble");
                $("#bottomEmail").val("");
                $("#bottomEmail2").val("");
                $("#bottomPassword1").val("");
                $("#bottomPassword2").val("");
                $("#bottomEmail").blur();
                $("#bottomEmail2").blur();
                $("#bottomPassword1").blur();
                $("#bottomPassword2").blur();
                $("#bottomTermsCheck").prop('checked', false);
                return false;
            }

            if (pword.length < 8 || pwordRetype < 8 || pword.match(/\s/g) != null) {
                currentErrorMsg = "Passwords must be at least eight characters in length with no spaces and include at least one uppercase letter, lowercase letter, symbol, and number.";
                currentErrorType = "password1";
                $("#signupPopupButton").click();
                $("#email").val(email);
                $("#email2").val(emailRetype);
                $("#password1").val(pword);
                $("#password2").val(pwordRetype);
                $("#termscheck").prop('checked', termsChecked);
                $(".controls").removeClass('error');
                $('#password1error').html('Passwords must be at least eight characters in length with no spaces and include at least one uppercase letter, lowercase letter, symbol, and number.');
                $('#password1bubble').removeClass("hdn");
                $("#password1div").addClass('error');
                $("#showSignUp").click();
                $("#bottomSignup").removeAttr("disabled");
                PositionErrorMessage("#password1", "#password1bubble");
                $("#bottomEmail").val("");
                $("#bottomEmail2").val("");
                $("#bottomPassword1").val("");
                $("#bottomPassword2").val("");
                $("#bottomEmail").blur();
                $("#bottomEmail2").blur();
                $("#bottomPassword1").blur();
                $("#bottomPassword2").blur();
                $("#bottomTermsCheck").prop('checked', false);
                return false;
            }

            if (!termsChecked)
            {
                currentErrorType = "terms";
                currentErrorMsg = "You must accept the Terms of Use.";
                $("#signupPopupButton").click();
                $("#email").val(email);
                $("#email2").val(emailRetype);
                $("#password1").val(pword);
                $("#password2").val(pwordRetype);
                $("#termscheck").prop('checked', termsChecked);
                $(".controls").removeClass('error');
                $('#termscheckbubble').removeClass("hdn");
                $("#termscheckdiv").addClass('error');
                $("#showSignUp").click();
                $("#bottomSignup").removeAttr("disabled");
                PositionErrorMessage("#termscheck", "#termscheckbubble");
                $("#bottomEmail").val("");
                $("#bottomEmail2").val("");
                $("#bottomPassword1").val("");
                $("#bottomPassword2").val("");
                $("#bottomEmail").blur();
                $("#bottomEmail2").blur();
                $("#bottomPassword1").blur();
                $("#bottomPassword2").blur();
                $("#bottomTermsCheck").prop('checked', false);
                return false;
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
                        $("#bottomSignup").removeAttr("disabled");
                        if (data.type == 'email')
                        {
                            currentErrorMsg = data.message;
                            currentErrorType = "email";
                            $("#signupPopupButton").click();
                            $("#email").val(email);
                            $("#email2").val(emailRetype);
                            $("#password1").val(pword);
                            $("#password2").val(pwordRetype);
                            $("#termscheck").prop('checked', termsChecked);
                            $(".controls").removeClass('error');
                            $('#emailerror').html(data.message);
                            $('#emailbubble').removeClass("hdn");
                            $("#emaildiv").addClass('error');
                            $("#showSignUp").click();
                            PositionErrorMessage("#email", "#emailbubble");
                            $("#bottomEmail").val("");
                            $("#bottomEmail2").val("");
                            $("#bottomPassword1").val("");
                            $("#bottomPassword2").val("");
                            $("#bottomEmail").blur();
                            $("#bottomEmail2").blur();
                            $("#bottomPassword1").blur();
                            $("#bottomPassword2").blur();
                            $("#bottomTermsCheck").prop('checked', false);
                        }
                    }
                    else
                    {
                        if (typeof(sendMixpanel) != 'undefined' && sendMixpanel){
                            mixpanel.identify(data.uniquehash);
                            mixpanel.people.set_once({
                                'First Login Date': new Date(),
                            });
                            mixpanel.track("New User", {
                               "new_user": data.uniquehash
                            }, function() {
                                localStorage[serverSess] = data.sess;
                                localStorage["showNewUserDialog"] = true;
                                localStorage["showNewAdvisorDialog"] = false;
                                window.location = baseUrl + "/myscore";
                            });
                        }
                        else
                        {
                            localStorage[serverSess] = data.sess;
                            localStorage["showNewUserDialog"] = true;
                            localStorage["showNewAdvisorDialog"] = false;
                            window.location = baseUrl + "/myscore";
                        }
                    }
                },
                error: function(data) {
                    currentErrorMsg = "We are unable to add this user.";
                    currentErrorType = "email";
                    $("#signupPopupButton").click();
                    $("#email").val(email);
                    $("#email2").val(emailRetype);
                    $("#password1").val(pword);
                    $("#password2").val(pwordRetype);
                    $("#termscheck").prop('checked', termsChecked);
                    $(".controls").removeClass('error');
                    $('#emailerror').html('We are unable to add this user.');
                    $('#emailbubble').removeClass("hdn");
                    $("#emaildiv").addClass('error');
                    $("#showSignUp").click();
                    $("#bottomSignup").removeAttr("disabled");
                    PositionErrorMessage("#email", "#emailbubble");
                    $("#bottomEmail").val("");
                    $("#bottomEmail2").val("");
                    $("#bottomPassword1").val("");
                    $("#bottomPassword2").val("");
                    $("#bottomEmail").blur();
                    $("#bottomEmail2").blur();
                    $("#bottomPassword1").blur();
                    $("#bottomPassword2").blur();
                    $("#bottomTermsCheck").prop('checked', false);
                }
            });
            return false;
        }
    });
    return new signupView;
});