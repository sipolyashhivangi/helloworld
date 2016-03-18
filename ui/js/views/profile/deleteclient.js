define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/deleteclient.html',
], function(Handlebars, Backbone, deleteclientTemplate) {

    var deletecliView = Backbone.View.extend({
        el: $("#body"),
        render: function(deleteId,status,email) {
            $.ajax({
                type: 'GET',
                url: deletec,
                dataType: "JSON",
                data : {
                    id : deleteId,
                    status: status,
                    email: email
                },
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    var source = $(deleteclientTemplate).html();
                    var template = Handlebars.compile(source);
                    $("#deleteclientContents").html(template(data));
                    popUpDeleteclient(data);
                }
            });
        },
        events: {
            "click #delete": "fnDeleteClient",
        },
        fnDeleteClient: function(event) {
            event.preventDefault();
            var clientId = event.target.attributes.getNamedItem('deleteId').nodeValue;
            var advisorid = event.target.attributes.getNamedItem('advisorId').nodeValue;
            var status = event.target.attributes.getNamedItem('status').nodeValue;
            var clientEmail = event.target.attributes.getNamedItem('clientEmail').nodeValue;
            var formValues = {
                id: clientId,
                status: status,
                clientEmail: clientEmail
            };
            $.ajax({
                url: deleteclient,
                type: 'GET',
                dataType: "json",
                data: formValues,
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                timeoutPeriod = defaultTimeoutPeriod;
                    if(data.status == "OK")
                    {
                        $('.user'+clientId).remove();
                        var currentpageNo = parseInt($('#pageNo').val());
                        var sortOrder = $('#sortOrder').val();
                        var sortBy = $('#sortBy').val();
                        getClientList(sortOrder, sortBy, currentpageNo);
                    }
                }
            });
        }
    });
    return new deletecliView;
});