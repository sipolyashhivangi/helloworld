


define([
    'handlebars',
    'text!../../../html/user/finacialSummaryActionsteps.html',
    
], function(Handlebars, actionstepTemplate) {

    var actionstepView = Backbone.View.extend({
        el: $("#body"),
        render: function(scoreData) {
            //get the details from the getuseritem
            
            var source = $(actionstepTemplate).html();
            var template = Handlebars.compile(source);
            $('#ActionStepContent').html(template(scoreData));
            init(false);
        },
        events: {
            "change #ActionStepContent": "fnActionStepRender"
        },
        
        fnActionStepRender: function(event) {
            //get the score from database
            init(false);
            event.preventDefault();
            var formValues = {};
            var catName = $('#categorySearch').val();
            var user_id = $('#consumer_id').val();
            if (typeof (stepscount) != 'undefined') {
                formValues['stepscount'] = stepscount;
            } else {
                formValues['stepscount'] = 4;
            }

            if(catName!="") {
                 formValues['catName'] = catName;
            }
            formValues['user_id'] = user_id;
            
            $.ajax({
                url: userGetActionStepURL,
                type: 'GET',
                dataType: "json",
                data: formValues,
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(scoreData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/user/finacialSummaryActionsteps'],
                            function(actionstepV) {
                                actionstepV.render(scoreData);
                                if (loadfakeactionstep) {
                                    $('#fakeActionStep').val(currentactionstepid);
                                    $("#fakeActionStep").click();
                                    loadfakeactionstep = false;
                                }
                            }
                    );
                }
            });
        }
    });
    return new actionstepView;
});
