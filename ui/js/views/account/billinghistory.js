define([
    'handlebars',
    'backbone',
    'text!../../../html/account/billinghistory.html',
], function(Handlebars, Backbone, billingHistoryTemplate) {

    var historyView = Backbone.View.extend({
        el: $("body"),
        render: function() {
            $("#historyContent").removeClass("hdn");
            $.ajax({
                url: retrieveinvoicelistURL,
                type: 'GET',
                dataType: "JSON",
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                    if (data.status == "OK") {
                        var Month = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
                        timeoutPeriod = defaultTimeoutPeriod;
                        for (var i = 0; i < data.count; i++) {
                            var invoice_date = new Date(data.invoices[i].invoice_date);
                            var intMonth = invoice_date.getMonth();
                            var browserCreatedDate = Month[intMonth] + " " + invoice_date.getDate() + ", " + invoice_date.getFullYear();
                            data.invoices[i].invoice_date = browserCreatedDate;

                            var period_start = new Date(data.invoices[i].period_start);
                            var intMonth = period_start.getMonth();
                            var browserStartDate = Month[intMonth] + " " + period_start.getDate() + ", " + period_start.getFullYear();
                            data.invoices[i].period_start = browserStartDate;

                            var period_end = new Date(data.invoices[i].period_end);
                            var intMonth = period_end.getMonth();
                            var browserEndDate = Month[intMonth] + " " + period_end.getDate() + ", " + period_end.getFullYear();
                            data.invoices[i].period_end = browserEndDate;
                        }
                        var source = $(billingHistoryTemplate).html();
                        var template = Handlebars.compile(source);
                        $("#subscriptionDetails").html(template(data));
                        $("#historyContent").removeClass("hdn");
                    }
                    else if (data.status == "ERROR") {
                        $("#historyMessage").html(data.message);
                        $("#historyMessage").show();
                        var source = $(billingHistoryTemplate).html();
                        var template = Handlebars.compile(source);
                        $("#subscriptionDetails").html(template(data));
                        $("#historyContent").addClass('hdn');
                        $("#historyMessage").removeClass('hdn');
                    }
                }
            });
            init();
        },
        events: {
            "click #printInvoice": "fnPrintInvoice",
            //"click #downloadInvoice": "fnDownloadInvoice",
        },
        initialize: function() {

        },
        fnPrintInvoice: function(event) {
            event.preventDefault();
            $("#historyContent").printElement();
        },
    });
    return new historyView;
});