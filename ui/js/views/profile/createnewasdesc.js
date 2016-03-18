define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/createnewasdesc.html',
], function(Handlebars, Backbone, createnewASDescTemplate) {

    var createnewView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj) {
            var source = $(createnewASDescTemplate).html();
            var template = Handlebars.compile(source);
            obj.flexasdesc = obj.flexasdesc.split('<br>');
            $("#createnewASDescContents").html(template(obj));

        },
        events: {
            "click #saveASRecommendation": "fnSaveASRecommendation",
            "click #closeRecommendationDialog": "fnCloseRecommendationDialog"
        },
        fnSaveASRecommendation: function(event) {
            //event.preventDefault();
            var action_id = $('#advisorAS').val();
            var productDescription = $('#advisorASDesc').val();
            var productName = $('#advisorProductName').val();
            var productImageUrl = $('#advisorProductImageUrl').val();
            var productLinkUrl = $('#advisorProductLinkUrl').val();

            if (productDescription == "")
            {
                $('#clienterror').html('Please enter your recommendation.');
                $('#clientbubble').removeClass("hdn");
                $("#clientdiv").addClass('error');
                $("#clientbubble").css('display','inline');
                PositionErrorMessage("#advisorASDesc", "#clientbubble");
                return false;
            }
            $('#advisorASDesc').focus(function() {
                $("#clientdiv").removeClass("error");
                $("#clientbubble").css('display','none');
            });
            var formValues = {
                actionid: action_id,
                description: productDescription,
                name: productName,
                image: productImageUrl,
                link: productLinkUrl,
            };
            var url = updateexternallinkasDesc;

            $.ajax({
                url: url,
                cache: false,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                        getASList();
                        event.preventDefault();
                        removeLayover();
                },
                error: function(data) {
                    $('#clienterror').html('We cannot process your request.');
                    $('#clientbubble').removeClass("hdn");
                    $("#clientdiv").addClass('error');
                    PositionErrorMessage("#advcreateClient", "#clientbubble");
                }
            });
            return false;
        },
        fnCloseRecommendationDialog: function(event) {
            getASList();
            event.preventDefault();
            removeLayover();
        },

    });
    return new createnewView;
});