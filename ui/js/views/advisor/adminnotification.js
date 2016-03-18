define([
    'handlebars',
    'backbone',
    'text!../../../html/advisor/adminnotification.html',
], function(Handlebars, Backbone, notificationTemplate) {

    var notificationView = Backbone.View.extend({
        el: $("#body"),
        render: function() {

            $.ajax({
                url: unassignedAdvisorCount,
                dataType: "JSON",
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                        timeoutPeriod = defaultTimeoutPeriod;
                        var source = $(notificationTemplate).html();
                        var template = Handlebars.compile(source);
                        $("#notificationContents").html(template(data));
                        popUpNotification();
                }
            });
        },
        
    });
    return new notificationView;
});