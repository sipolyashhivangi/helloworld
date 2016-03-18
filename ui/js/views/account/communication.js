define([
    'handlebars',
    'backbone',
    'text!../../../html/account/communication.html',
], function(Handlebars, Backbone, profileTemplate) {

    var settingView = Backbone.View.extend({
        el: $("body"),
        render: function() {
            var source = $(profileTemplate).html();
            var template = Handlebars.compile(source);
            $.ajax({
                url: getSubscriptionStatusUrl,
                type: 'GET',
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                timeoutPeriod = defaultTimeoutPeriod;
                    var notify = 0;
                    if(data.message != "unsubscribed") {
                        notify = 1;
                    }
                    var obj = { notify: notify };
                    if(userData.user != undefined){
                         obj["email"] = data.email_address;
                         if(typeof(userData.advisor) != 'undefined' && typeof(userData.user) != 'undefined'
                            && ($("#currentPage").val() == "myscore" || $("#currentPage").val() == "financialsnapshot")) {
                            userData.user.impersonationMode = true;
                            if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                                obj.permissions = true;
                            }
                        }
                    }else{
                        obj["email"] = data.email_address;
                    }

                    $("#settingsDetails").html(template(obj));
                }
            });
        },
        events: {
            "click #communicationButton": "commSetting",
            "change #notifyContact": "notifyContact",
            "change #notificationsEmail": "notifyContact",
            "keydown #notificationsEmail": "notifyContact",
        },
        initialize: function() {
        },
        // use this for close overlay after click close(x) link.
        notifyContact: function(event) {
            $('.user-settings-success').addClass('hdn');
            $("#communicationButton").removeClass("hdn");
            $("#communicationMsg").html('&nbsp;');
        },
        commSetting: function(event) {
            var formValues = {
                action: ($('#notifyContact').is(':checked')?'Subscribe':'Unsubscribe'),
                email_address: $("#notificationsEmail").val()
            };
            $.ajax({
                url: setSubscriptionStatusUrl,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    $('.user-settings-success').removeClass('hdn');
                    $("#communicationMsg").html('Communication settings saved successfully.');
                    timeoutPeriod = defaultTimeoutPeriod;
                }
            });
            $("#communicationButton").addClass("hdn");
        },
    });
    return new settingView;
});
