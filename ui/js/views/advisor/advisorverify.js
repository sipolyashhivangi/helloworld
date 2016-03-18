define([
    'handlebars',
    'backbone',
    'text!../../../html/advisor/advisorverify.html',
], function(Handlebars, Backbone, loginTemplate) {

    var loginView = Backbone.View.extend({
        el: $("#advisorVerifyContents"),
        render: function() {
            var source = $(loginTemplate).html();
            var template = Handlebars.compile(source);
            $(this.el).html(template());
            // Advisor verification link
            var url = document.URL;
            var queryString = url.substring( url.indexOf('?') + 1 );
            var value = queryString.split('=');
            var formValues = {
                    code: value[1]
                };
            $.ajax({
                url: advisorverify,
                type: 'GET',
                dataType: "json",
                data: formValues,
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                }
            });
        },
        events: {
            "click #forgotButton": "performForgotPassword",
        },
        initialize: function() {
            this.username = $("#username");
            this.loginButton = $("#login");
        },
        validated: function(valid) {
            if (valid) {
                this.view.loginButton.removeAttr("disabled");
            } else {
                this.view.loginButton.attr("disabled", "true");
            }
        },
        switchTabs: function(event) {
            event.preventDefault();
            $("#signuptab").click();
            $("#yesAccessToken").click();
        },
        performForgotPassword: function(event) {

            event.preventDefault();
            $('#forgotButton').attr("disabled", "true");
            $('#alert-error').hide();

            var email = $('#username').val();

            if (!validateEmail(email))
            {
                $('#usernameerror').html('Enter a valid email address.');
                $('#usernamebubble').removeClass("hdn");
                $("#usernamediv").addClass('error');
                PositionErrorMessage("#username", "#usernamebubble");
                $('#forgotButton').removeAttr("disabled");
                return false;
            }

            var formValues = {
                email: email,
            };

            $.ajax({
                url: advisorForgotPasswordUrl,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                timeoutPeriod = defaultTimeoutPeriod;
                    $('#forgotButton').removeAttr("disabled");
/*                    if (data.status == "FAILED" || data.status == "ERROR" || typeof(data.error) != 'undefined')
                    {
                        $('#usernameerror').html('Enter a valid email address.');
                        $('#usernamebubble').removeClass("hdn");
                        $("#usernamediv").addClass('error');
                        PositionErrorMessage("#username", "#usernamebubble");
                    }
                    else if(data.status == "OK")
                    {
*/
                        $("#forgotLinks").hide();
                        $("#emailGroup").hide();
                        $("#forgotInstructions").attr('style','margin:1em 0;padding:0px 10px;text-align:left');
                        $("#forgotInstructions").html("If this is a valid account, an email containing your password recovery link is on its way. If you do not receive an email within 10 minutes, check your spam folder first, then try again. If the issue persists, please <a href='mailto:support@flexscore.com' target='_blank'>contact us</a>.");
//                   }
                },
                error: function(data) {
                    $('#usernameerror').html('Please try again later.');
                    $('#usernamebubble').removeClass("hdn");
                    $("#usernamediv").addClass('error');
                    PositionErrorMessage("#username", "#usernamebubble");
                }
            });
            return false;
        }
    });
    return new loginView;
});