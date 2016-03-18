define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/consumeremailverify.html',
], function(Handlebars, Backbone, consumeremailverifyTemplate) {

    var createnewView = Backbone.View.extend({
        el: $("#body"),
        render: function(data) {
            var source = $(consumeremailverifyTemplate).html();
            var template = Handlebars.compile(source);
            $("#consumeremailverifyContents").html(template(data));

        },
        events: {
            "click #sendEmailVerification": "sendVerificationEmail",
            "click #signoutAndClose": "fnCloseDialog",
            "click #iAmDone": "fnCloseDialog",
        },
        sendVerificationEmail: function(event) {
            event.preventDefault();
            $('#sendEmailVerification').attr("disabled", "true");
            $('#alert-error').hide();

            $.ajax({
                url: sendverificationemailURL,
                type: 'POST',
                dataType: "json",
                success: function(data) {
                    if (data.status == "ERROR")
                    {
                        $('#verificationContent').hide();
                        $('#verificationResults').show();
                        $('#verificationResults').html(data.message);
                        $('#verificationResults').css('color', '#ec1c23');
                        $('#sendEmailVerification').hide();
                        $('#iAmDone').show();
                    }
                    else
                    {
                        $('#verificationContent').hide();
                        $('#verificationResults').show();
                        $('#verificationResults').html(data.message);
                        $('#verificationResults').css('color', '#5fa439');
                        $('#sendEmailVerification').hide();
                        $('#iAmDone').show();
                    }
                    $('#sendEmailVerification').removeAttr("disabled");
                },
                error: function(data) {
                        $('#verificationContent').hide();
                        $('#verificationResults').show();
                        $('#verificationResults').html('Email could not be sent');
                        $('#verificationResults').css('color', '#ec1c23');
                        $('#sendEmailVerification').hide();
                        $('#iAmDone').show();
                        $('#sendEmailVerification').removeAttr("disabled");
                }
            });

            return false;
        },

        fnCloseDialog: function(event) {
            event.preventDefault();
            removeLayover();
        }

    });



    return new createnewView;
});