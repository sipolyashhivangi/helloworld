define([
    'handlebars',
    'backbone',
    'text!../../../html/admin/liparams.html',
], function(Handlebars, Backbone, loginTemplate) {
    var loginView = Backbone.View.extend({
        el: $("#ReportsBox"),
        render: function(data) {
            var source = $(loginTemplate).html();
            var template = Handlebars.compile(source);
            $(this.el).html(template(data));
        },
        events: {
            "click #printReport": "fnprintReport",
            "click #closeReport": "fncloseReport",
            "click #refreshReport": "fnrefreshReport",
            "click #pdfReport": "fnpdfReport",
        },
        initialize: function() {
            this.username = $("#username");
            this.password = $("#password");
            this.loginButton = $("#login");
        },
        validated: function(valid) {
            if (valid) {
                this.view.loginButton.removeAttr("disabled");
            } else {
                this.view.loginButton.attr("disabled", "true");
            }
        },
        fnprintReport: function(event) {
            event.preventDefault();
            window.print();
        },
        fncloseReport: function(event) {
            event.preventDefault();
            if (confirm('Are you sure to close this page?')) {
                window.close();
            }
        },
        fnrefreshReport: function(event) {
            event.preventDefault();
            location.reload();
        },
        fnpdfReport: function(event) {
            event.preventDefault();
            alert('Coming soon');
        }
    });
    return new loginView;
});