


define([
    'handlebars',
    'text!../../../html/user/actionstep.html',
    'text!../../../html/user/actionOverlay.html',
    'text!../../../html/user/actionReport.html'
], function(Handlebars, actionstepTemplate, actionoverlayTemplate, congratsTemplate) {

    var actionstepView = Backbone.View.extend({
        el: $("#body"),
        render: function(scoreData) {
            //get the details from the getuseritem
            if(typeof(scoreData) != 'undefined' && $("#asLine2").is(":visible"))
            {
                scoreData.showSecondRow = true;
            }
            var source = $(actionstepTemplate).html();
            var template = Handlebars.compile(source);
           
            $('#ActionStepContent').html(template(scoreData));
            $("#sortable").sortable({
                revert: true,
                stop: function(event, ui) {
                    var sorttype = 'general';
                    if($('#categorySearch').val() != '') {
                        sorttype = 'category';
                    }  
                    var list = $(this).sortable("toArray").join("|");
                    var fFlds = {values: list, sorttype: sorttype}
                    $.ajax({
                        url: updateactionstepsortURL,
                        type: 'POST',
                        dataType: "json",
                        data: fFlds
                    });
                }
            });
            init(false);
        },
        events: {
            "click .overlayLink": "fnOverlayRender",
            "click #fakeActionStep" : "fnOverlayRender"
        },
        fnOverlayRender: function(event) {
            //get the score from database
            var elementid = event.target.id;
            if(elementid == "fakeActionStep")
            {
                elementid = $("#fakeActionStep").val();
            }
            event.preventDefault();
            var formValues = {
                id: elementid
            }

            $.ajax({
                url: actionOverlayURL,
                type: 'GET',
                dataType: "json",
                data: formValues,
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(responseData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if(responseData.status == 'OK' && (responseData.actionstepdetail[0].actionstatus == '0' || responseData.actionstepdetail[0].actionstatus == '2' || responseData.actionstepdetail[0].actionstatus == '3')) {
                        Modernizr.addTest('sandbox', 'sandbox' in document.createElement('iframe'));
                        if(Modernizr.video && Modernizr.sandbox) {
                            responseData.actionstepdetail[0].showSandbox = true;
                        }
                        if (responseData.videokey == 'ok') {   //change the RHS cond
                            videokey = '';
                        } else {
                            videokey = responseData.videokey;
                        }
                        currentactionstepid = elementid;
                        var source = $(actionoverlayTemplate).html();
                        var template = Handlebars.compile(source);
                        $.scrollTo($('#body'), 200);
                        $('#comparisonBox').show();
                        $('#darkBackground').show();
                        $('#darkBackground').fadeTo("fast", 0.6);
                        $('#comparisonBox').css("height", 'auto');
                        if(typeof(userData.advisor) != 'undefined') {
                            userData.user.impersonationMode = true;
                            if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                            var total = responseData.actionstepdetail.length;
                                for(var i = 0; i< total; i++){
                                    responseData.actionstepdetail[i].permission = true;
                                }
                            }
                        }

                        $('#comparisonBox').html(template(responseData));

                        // Tooltip text for external link
                        $('.masterTooltip').hover(function(){
                                // Hover over code
                                var title = $(this).attr('title');
                               // alert(title);
                                $(this).data('tipText', title).removeAttr('title');
                                $('<p class="externallink_tooltip" style="font-size:12px;font-weight:bold;"></p>')
                                .text(title)
                                .appendTo('body')
                                .fadeIn('slow');
                        }, function() {
                                // Hover out code
                                $(this).attr('title', $(this).data('tipText'));
                                $('.externallink_tooltip').remove();
                        }).mousemove(function(e) {
                                var mousex = e.pageX + 20; //Get X coordinates
                                var mousey = e.pageY + 10; //Get Y coordinates
                                $('.externallink_tooltip')
                                .css({ top: mousey, left: mousex })
                        });

                        init();
                    }
                    else if(responseData.status == 'OK')
                    {
                        removeLayover();
                        $.ajax({
                            url: finalscoreURL,
                            type: 'POST',
                            dataType: "json",
                            success: function(getAll) {
                                if (getAll.status == "OK") {
                                    $.ajax({
                                        url: userGetScoreURL,
                                        type: 'GET',
                                        dataType: "json",
                                        cache: false,
                                        beforeSend: function(request) {
                                            request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                                        },
                                        success: function(scoreData) {
                                            if (scoreData.status == "OK") {
                                                window.parent.removeLayover();
                                                var source = $(congratsTemplate).html();
                                                var template = Handlebars.compile(source);
                                                $.scrollTo($('#body'), 200);
                                                $('#comparisonBox').show();
                                                $('#darkBackground').show();
                                                $('#darkBackground').fadeTo("fast", 0.6);
                                                $('#comparisonBox').css("height", 'auto');
                                                $('#comparisonBox').html(template(getAll));
                                                var simScore = parseInt(scoreData.score.totalscore);
                                                var imageId = Math.round((simScore * 20) / 1000);
                                                imageId = (imageId > 0) ? imageId : 0;
                                                imageId = (imageId < 20) ? imageId : 20;
                                                alignCongratsScore('reportScore', 'reportHorseshoe', simScore, imageId);
                                            }
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
            });
        }
    });
    return new actionstepView;
});
