define([
    'handlebars',
    'backbone',
    'text!../../../html/admin/usersbystatereport.html'
], function(Handlebars, Backbone, ubsreportTemplate) {
    var loginView = Backbone.View.extend({
        el: $("#mainBody"),
        render: function(obj) {
            var source = $(ubsreportTemplate).html();
            var template = Handlebars.compile(source);
            $(this.el).html(template(obj));
           
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

    });
    return new loginView;
});