define([
    'handlebars',
    'backbone',
    'text!../../../html/user/echouser.html',
], function(Handlebars, Backbone, echouserTemplate) {

    var createnewView = Backbone.View.extend({
        el: $("#body"),
        render: function() {
            var source = $(echouserTemplate).html();
            var template = Handlebars.compile(source);
            $("#comparisonBox").html(template());
        },
        events: {
            "click #acceptEchoUser": "AgreementAction",
            "click #declineEchoUser": "AgreementAction",
        },
        AgreementAction: function(event) {
            event.preventDefault();
            var action = event.target.id.substr(0, event.target.id.indexOf('EchoUser'));
            var formValues = { action: action };

            $.ajax({
                url: updateEchoUserAgreement,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    removeLayover();
                    if (localStorage["showNewUserDialog"] === "true")    {
                        require(
                            [ 'views/user/howItWorks'],
                            function( accountOneV){
                                accountOneV.render();
                                popUpActionStep();
                                localStorage["showNewUserDialog"] = false;
                            }
                        );
                    }                
                }
            });
            return false;
        },
    });
    
    return new createnewView;
});