// Filename: views/base/timeout

define([
    'handlebars',
    'text!../../../html/base/timeout.html',
], function(Handlebars, timeoutTemplate) {
    var timeoutView = Backbone.View.extend({
        el: $("#body"),
        render: function(data) {
            var source = $(timeoutTemplate).html();
            var template = Handlebars.compile(source);
            $("#comparisonBox").html(template(data));
            init();
            if (socket != null) {
                socket.disconnect();
            }

        },
        events: {
            "click .keepAliveTimeout": "keepAliveTimeout",
            "click .signOutTimeout": "signOutTimeout",
            "keypress #password1": "checkClick",
            "click #forgotPasswordLink": "forgotPasswordClick",
        },
        forgotPasswordClick: function(event) {
            if(typeof(userData.advisor) != 'undefined') {
                window.location = "./forgotpassword.html?type=advisor";
            }
            else {
                window.location = "./forgotpassword.html";
            }
        },
        checkClick: function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                event.preventDefault();
                $(".keepAliveTimeout").click();
            }
        },
        keepAliveTimeout: function() {

            if(typeof(userData.advisor) != 'undefined' && typeof(userData.user) != 'undefined') {
                userData.user.impersonationMode = true;
                var email = userData.advisor.email;
                var formValues = {
                    email: email,
                    password: $("#password1").val()
                };

                url = loginAdvUrl;//For Advisor Resume Session

                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: "json",
                    data: formValues,
                    success: function(data) {

                        if (data.status == "ERROR")
                        {
                            if (typeof (data.type) != 'undefined' && data.type == "locked") {
                                $('#password1error').html(data.message);
                            }
                            else {
                                $('#password1error').html("Invalid password");
                            }
                            $('#password1bubble').removeClass("hdn");
                            $("#password1div").addClass('error');
                            PositionErrorMessage("#password1", "#password1bubble");
                        }
                        else
                        {
                            localStorage[serverSess] = data.sess;
                            userData.advisor.sess = data.sess;
                            userData.user.sess = data.sess;
                            timeoutPeriod = defaultTimeoutPeriod;

                            var email = userData.user.email;
                            var formValues = {
                                email: email,
                            };
                            $.ajax({
                                url: getviewFinances,
                                type: 'POST',
                                dataType: "json",
                                data: formValues,
                                success: function(data) {
                                    timeoutPeriod = defaultTimeoutPeriod;
                                    removeLayover();
                                    timeoutDialogShown = false;
                                    if (socket != null) {
                                        socket.socket.connect();
                                    }
                                }
                            });
                        }
                    },
                    error: function(data) {
                        $('#password1bubble').removeClass("hdn");
                        $("#password1div").addClass('error');
                        PositionErrorMessage("#password1", "#password1bubble");
                    }
                });
            }
            else if ( typeof(userData.advisor) != 'undefined') {//check session out for user.

                var email = userData.advisor.email;
                var formValues = {
                    email: email,
                    password: $("#password1").val()
                };

                url = loginAdvUrl;//For Advisor Resume Session

                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: "json",
                    data: formValues,
                    success: function(data) {

                        if (data.status == "ERROR")
                        {
                            if (typeof (data.type) != 'undefined' && data.type == "locked") {
                                $('#password1error').html(data.message);
                            }
                            else {
                                $('#password1error').html("Invalid password");
                            }
                            $('#password1bubble').removeClass("hdn");
                            $("#password1div").addClass('error');
                            PositionErrorMessage("#password1", "#password1bubble");
                        }
                        else
                        {
                            localStorage[serverSess] = data.sess;
                            userData.advisor.sess = data.sess;
                            timeoutPeriod = defaultTimeoutPeriod;
                            removeLayover();
                            timeoutDialogShown = false;
                            if (socket != null) {
                                socket.socket.connect();
                            }
                        }
                    },
                    error: function(data) {
                        $('#password1bubble').removeClass("hdn");
                        $("#password1div").addClass('error');
                        PositionErrorMessage("#password1", "#password1bubble");
                    }
                });
            }
            else if (typeof(userData.user) != 'undefined') {//check session out for advisor to allow advisor re-login after session out.
                var email = userData.user.email;
                var formValues = {
                    email: email,
                    password: $("#password1").val()
                };

                $.ajax({
                    url: loginUrl,
                    type: 'POST',
                    dataType: "json",
                    data: formValues,
                    success: function(data) {
                        if (data.status == "ERROR")
                        {
                            if (typeof (data.type) != 'undefined' && data.type == "locked") {
                                $('#password1error').html(data.message);
                            }
                            else {
                                $('#password1error').html("Invalid password");
                            }
                            $('#password1bubble').removeClass("hdn");
                            $("#password1div").addClass('error');
                            PositionErrorMessage("#password1", "#password1bubble");
                        }
                        else
                        {
                            localStorage[serverSess] = data.sess;
                            userData.user.sess = data.sess;
                            timeoutPeriod = defaultTimeoutPeriod;
                            removeLayover();
                            timeoutDialogShown = false;
                            if (socket != null) {
                                socket.socket.connect();
                            }
                        }
                    },
                    error: function(data) {
                        $('#password1bubble').removeClass("hdn");
                        $("#password1div").addClass('error');
                        PositionErrorMessage("#password1", "#password1bubble");
                    }
                });
            }//check session out for advisor.
        },
        signOutTimeout: function() {
            $.ajax({
                url: logoutUrl,
                type: 'GET',
                dataType: "json",
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function() {
                    timeoutDialogShown = false;
                    window.location = "./";
                }
            });
        },
    });
    return new timeoutView;
});