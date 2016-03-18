define([
    'handlebars',
    'backbone',
    'text!../../../html/advisor/advisorsignup.html',
], function(Handlebars, Backbone, advisorTemplate) {
    var advisorView = Backbone.View.extend({
        el: $("#advisorSignup"),
        render: function(obj) {
            var source = $(advisorTemplate).html();
            var template = Handlebars.compile(source);
            $(this.el).html(template({}));
            init();
        },
        events: {
            "click #createadvisor": "performSignup",
            "click #advLoginButton": "performAdvLogin"
        },
        initialize: function() {
            this.signupButton = $("#step1Submit");
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
            var pword = $('#advsignpassword1').val();
            var pwordRetype = $('#advsignpassword2').val();
            var email = $('#advsignemail').val();
            var emailRetype = $('#advsignemail2').val();

            if (!validateEmail(email)) {
                $('#emailadverror').html('Enter a valid email address.');
                $('#emailadvbubble').removeClass("hdn");
                $("#emailadvdiv").addClass('error');
                PositionErrorMessage("#advsignemail", "#emailadvbubble");
                return false;
            }

            if (!validateEmail(emailRetype)) {
                $('#email2adverror').html('Enter a valid email address.');
                $('#email2advbubble').removeClass("hdn");
                $("#email2advdiv").addClass('error');
                PositionErrorMessage("#advsignemail2", "#email2advbubble");
                return false;
            }

            if (email != emailRetype) {
                $('#emailadverror').html('Email addresses must match.');
                $('#emailadvbubble').removeClass("hdn");
                $("#emailadvdiv").addClass('error');
                PositionErrorMessage("#advsignemail", "#emailadvbubble");
                return false;
            }

            var msg = 'Passwords must be at least eight characters in length with no spaces and include at least one uppercase letter, lowercase letter, symbol, and number.';

            // START - client password validations
            if (pword != pwordRetype) {
                $('#passwordadverror').html('Passwords must match.');
                $('#passwordadvbubble').removeClass("hdn");
                $("#passwordadvidiv").addClass('error');
                PositionErrorMessage("#advsignpassword1", "#passwordadvbubble");
                return false;
            }

            if (pword.length < 8 || pword.match(/\s/g) != null) {
                $('#passwordadverror').html(msg);
                $('#passwordadvbubble').removeClass("hdn");
                $("#passwordadvidiv").addClass('error');
                PositionErrorMessage("#advsignpassword1", "#passwordadvbubble");
                return false;
            }
            if (pword.search(/[a-z]/) == -1) {
                $('#passwordadverror').html(msg);
                $('#passwordadvbubble').removeClass("hdn");
                $("#passwordadvidiv").addClass('error');
                PositionErrorMessage("#advsignpassword1", "#passwordadvbubble");
                return false;
            }
            if (pword.search(/[A-Z]/) == -1) {
                $('#passwordadverror').html(msg);
                $('#passwordadvbubble').removeClass("hdn");
                $("#passwordadvidiv").addClass('error');
                PositionErrorMessage("#advsignpassword1", "#passwordadvbubble");
                return false;
            }
            if (pword.search(/[0-9]/) == -1) {
                $('#passwordadverror').html(msg);
                $('#passwordadvbubble').removeClass("hdn");
                $("#passwordadvidiv").addClass('error');
                PositionErrorMessage("#advsignpassword1", "#passwordadvbubble");
                return false;
            }
            if (pword.search(/[\W]/) == -1) {
                $('#passwordadverror').html(msg);
                $('#passwordadvbubble').removeClass("hdn");
                $("#passwordadvidiv").addClass('error');
                PositionErrorMessage("#advsignpassword1", "#passwordadvbubble");
                return false;
            }
            // END - client password validations

            // terms checkbox validation
            if (!$('#termscheckadv').is(':checked')) {
                $('#termscheckadvbubble').removeClass("hdn");
                $("#termscheckadvdiv").addClass('error');
                PositionErrorMessage("#termscheckadv", "#termscheckadvbubble");
                return false;
            }

            var formValues = {
                email: email,
                password: pword,
            };

            $.ajax({
                url: advisorSignupUrl,
                cache: false,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "ERROR") {
                        if (data.type == 'email') {
                            $('#emailadverror').html(data.message);
                            $('#emailadvbubble').removeClass("hdn");
                            $("#emailadvdiv").addClass('error');
                            PositionErrorMessage("#advsignemail", "#emailadvbubble");
                            return false;
                        }
                        else if (data.type == 'password') {
                            $('#password1error').html(data.message);
                            $('#password1bubble').removeClass("hdn");
                            $("#password1div").addClass('error');
                            PositionErrorMessage("#advsignpassword1", "#password1bubble");
                        }
                        else  {
                            $('#emailadverror').html(data.message);
                            $('#emailadvbubble').removeClass("hdn");
                            $("#emailadvdiv").addClass('error');
                            PositionErrorMessage("#advsignemail", "#emailadvbubble");
                            return false;
                        }
                    }
                    else
                    {
                        if (typeof (sendMixpanel) != 'undefined' && sendMixpanel) {
                            mixpanel.identify(data.uniquehash);
                            mixpanel.people.set_once({
                                'First Login': new Date()
                            });
                            mixpanel.track("New Advisor", {
                                'new_advisor': data.uniquehash
                            }, function() {
                                localStorage[serverSess] = data.sess;
                                localStorage["showNewUserDialog"] = false;
                                localStorage["showNewAdvisorDialog"] = true;
                                window.location = baseUrl + "/dashboard";
                            });
                        }
                        else {
                            localStorage[serverSess] = data.sess;
                            localStorage["showNewUserDialog"] = false;
                            localStorage["showNewAdvisorDialog"] = true;
                            window.location = baseUrl + "/dashboard";
                        }
                    }
                },
                error: function(data) {
                    $('#emailadverror').html('Enter a valid email address.');
                    $('#emailadvbubble').removeClass("hdn");
                    $("#emailadvdiv").addClass('error');
                    PositionErrorMessage("#advsignemail", "#emailadvbubble");
                }
            });
            return false;
        },
        performAdvLogin: function(event) {
            event.preventDefault();

            $('#advLoginButton').attr("disabled", "true");
            $('#alert-error').hide();

            var user = $('#advusername').val();
            var pword = $('#advpassword').val();


            var formValues = {
                email: user,
                password: pword
            };

            $.ajax({
                url: loginAdvUrl,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "ERROR")
                    {
                        $('#advLoginButton').removeAttr("disabled");
                        $('#advusernameerror').html(data.message);
                        $('#advusernamebubble').removeClass("hdn");
                        $("#advusernamediv").addClass('error');
                        PositionErrorMessage("#advusername", "#advusernamebubble");
                    }
                    else
                    {
                        if (typeof (sendMixpanel) != 'undefined' && sendMixpanel) {
                            mixpanel.identify(data.uniquehash);
                            var formValues = {};
                            formValues["Last Login"] = new Date();
                            mixpanel.people.set(formValues);
                            mixpanel.track("Advisor Logged In", {
                                'advisor_logged_in': data.uniquehash
                            }, function() {
                                localStorage[serverSess] = data.sess;
                                localStorage["showNewUserDialog"] = false;
                                localStorage["showNewAdvisorDialog"] = false;
                                window.location = baseUrl + '/dashboard';
                            });
                        }
                        else {
                            localStorage[serverSess] = data.sess;
                            localStorage["showNewUserDialog"] = false;
                            localStorage["showNewAdvisorDialog"] = false;
                            window.location = baseUrl + '/dashboard';
                        }
                    }

                },
                error: function(data) {
                    $('#advLoginButton').removeAttr("disabled");
                    $('#advusernameerror').html("Incorrect email/password combination.");
                    $('#advusernamebubble').removeClass("hdn");
                    $("#advusernamediv").addClass('error');
                    PositionErrorMessage("#advusername", "#advusernamebubble");
                }
            });
            return false;
        }
    });

    return new advisorView;
});