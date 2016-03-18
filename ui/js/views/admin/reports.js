define([
    'handlebars',
    'backbone',
    'text!../../../html/admin/reports.html'
], function(Handlebars, Backbone, reportTemplate) {
    var loginView = Backbone.View.extend({
        el: $("#mainBody"),
        render: function() {
            var source = $(reportTemplate).html();
            var template = Handlebars.compile(source);
            $(this.el).html(template());
           
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
        }
    });
    return new loginView;
});