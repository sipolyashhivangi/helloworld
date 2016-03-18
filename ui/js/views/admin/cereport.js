define([
    'handlebars',
    'backbone',
    'text!../../../html/admin/cereport.html'
], function(Handlebars, Backbone, cereportTemplate, cesearchV) {
    var loginView = Backbone.View.extend({
        el: $("#mainBody"),
        render: function() {
            var source = $(cereportTemplate).html();
            var template = Handlebars.compile(source);
            $(this.el).html(template());
            var formValues = {
                reset: true
            };
            $.ajax({
                url: getCeUserReportURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    reportData = jsonData.items;
                    require(
                            ['views/admin/cesearchresult'],
                            function(cesearchV) {
                                // Added to hide the existing div and show a message div
                                cesearchV.render(jsonData);
                        	}
                    );
            	}
            });

        },
        events: {
            "click #printReport": "fnprintReport",
            "click #closeReport": "fncloseReport",
            "click #refreshReport": "fnrefreshReport",
            "click #pdfReport": "fnpdfReport",
            "click #cesearch": "fnSubmitEmail",
            "keypress #searchreport": "fnCheckSearch",
        },
        fnCheckSearch: function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                event.preventDefault();
                $("#cesearch").click();
            }
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
        fnswitchReport: function(event) {
            var value = $("input[name=ReportType]:checked").val();
            $("#errorCodeDiv").addClass("hdn");
            $("#emailDiv").addClass("hdn");
            $("#" + value + "Div").removeClass("hdn");
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
        },
        fnSubmitEmail: function(event) {
            event.preventDefault();
            var email = $('#searchreport').val();
            var action = event.target.value;
            var status = '';
            $("input[name=Status]").each(function() {
                if ($(this).is(':checked')) {
                    if (status != "") {
                        status += ",";
                    }
                    status += $(this).val();
                }
            });
            if (status == '') {
                status = 'All';
            }

            var formValues = {
                email: email,
                status: status
            };
            $.ajax({
                url: getCeUserReportURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    //add to table list
                    if (jsonData.status == 'OK') {
                        reportData = jsonData.items;
                        require(
                                ['views/admin/cesearchresult'],
                                function(cesearchV) {
                                    // Added to hide the existing div and show a message div
                                    cesearchV.render(jsonData);
                                }
                        );
                    }
                }
            });
        },
    });
    return new loginView;
});