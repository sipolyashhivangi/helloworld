define([
    'handlebars',
    'backbone',
    'text!../../../html/advisor/acceptconnection.html',
], function(Handlebars, Backbone, acceptconnectionTemplate) {

    var deletecliView = Backbone.View.extend({
        el: $("#body"),
        render: function(clientId,clientEmail) {
            $.ajax({
                type: 'GET',
                url: deletec,
                dataType: "JSON",
                data : {
                    id : clientId,
                    email : clientEmail,
                },
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                timeoutPeriod = defaultTimeoutPeriod;
                    var source = $(acceptconnectionTemplate).html();
                    var template = Handlebars.compile(source);
                    $("#deleteclientContents").html(template(data));
                    
                }
            });
        },
        events: {
            "click .accepted": "fnConfirmRequest",
            "click .declined": "fnDeclineRequest",
        },
         //Click Yes on the confirmation box to acceptthe request.
        fnConfirmRequest: function(event){
            event.preventDefault();
            var clientId = event.target.attributes.getNamedItem('clientId').nodeValue;
            var clientEmail = event.target.attributes.getNamedItem('clientEmail').nodeValue;
            var formValues = {
                id: clientId,
                email: clientEmail,
                };
                $.ajax({
                    url: connectionRequest,
                    type: 'GET',
                    dataType: "json",
                    data: formValues,
                    cache: false,
                    beforeSend: function(request) {
                        request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                    },
                    success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                        $('.acceptRequest'+clientId).remove();
                        $('.pendingreq'+clientId).remove();
                        $('#dateConnect'+clientId).text(data.date);
                        if(data.permission != "N"){
                            
                            $('.acceptView'+clientId).html('<button id="viewfinanceSummary" class="btn" type="button" clientId="' + clientId + '">Financial&nbsp;Summary</button>');
                            $('.acceptView'+clientId).css('padding-right','5px');
                        }
                    }
                });
        },//Click decline to delete the request and send mail to client.
        fnDeclineRequest: function(event){
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
                        if(data.status == "OK"){
                            $('.user'+clientId).remove();
                        }
                    }
                });
        }
    });
    return new deletecliView;
});