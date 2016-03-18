define([
    'jquery',
    'underscore',
    'backbone',
    'handlebars',
], function($, _, Backbone, Handlebars) {
    var Router = Backbone.Router.extend({
        initialize: function() {
            userData = null;
            sess = localStorage[serverSess];
            $.ajax({
                url: loginCheckUrl + "?sess=" + sess,
                cache: false,
                type: 'GET',
                dataType: "json",
                asnyc: false,
                success: function(data) {
                    userData = data;
                    if (data.status == "OK" && typeof (data.user) != 'undefined') {
                        forceUserNotifications = true;
                        require(
                                ['views/user/myscore', 'views/base/master', 'views/user/howItWorks',
                                    'text!../html/user/actionReport.html'],
                                function(myscoreV, masterV, accountOneV, congratsTemplate) {
                                    if (typeof (userData.advisor) != 'undefined') {
                                        userData.user.impersonationMode = true;
                                    }
                                    masterV.render(userData.user);
                                    $("#gnav_myScore").addClass("hover reverseShadowBox");
                                    $("#gnav_myScore").removeClass("gnavButton");
                                    if (userData.user.verified == 0) {
                                        require(
                                                ['views/profile/consumeremailverify', 'text!../../ui/html/profile/limited.html'],
                                                function(createnewV,limitedT) {
                                                    createnewV.render(userData.user);
                                                    popUpConsumerEmailverify();
                                                    var source = $(limitedT).html();
                                                    var template = Handlebars.compile(source);
                                                    //div id under which we want to show the content of current html file.
                                                    $('#mainBody').html(template());
                                                    if (localStorage["showNewUserDialog"] === "true") {
                                                        $("#verificationContentNew").show();
                                                    }
                                                }
                                        );
                                    } else {
                                        myscoreV.render(userData.user);
                                        if (localStorage["showNewUserDialog"] === "true") {
                                            accountOneV.render();
                                            popUpActionStep();
                                            localStorage["showNewUserDialog"] = false;
                                        } else {
                                            //check if new user has to verify his connection with advisor
                                            if (typeof (data.advisor) == 'undefined') {
                                                $.ajax({
                                                    url: loginCheckIdemnification,
                                                    type: 'GET',
                                                    dataType: "json",
                                                    success: function(data) {
                                                        if (data.status == "OK") {
                                                            require(
                                                                    ['views/advisor/userpermission'],
                                                                    function(userPermissionV) {
                                                                        if (data.data.indemnification_check == "0") {
                                                                            userPermissionV.render("#myAdvisorBox", "new", data);
                                                                            $(".usrtoadvper" + data.data.advisor_id).text("View Only");
                                                                            popUpMyadvisor();
                                                                        }
                                                                    });
                                                        }
                                                    }
                                                });
                                            }
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
                                                                    $('#ActionStepContent').trigger('change');
                                                                }
                                                            }
                                                        });
                                                    }
                                                }
                                            });
                                        }
                                    }
                                    init();
                                }
                        );
                    }
                    else if (data.status == 'OK') {
                        window.location = "./dashboard";
                    }
                    else
                    {
                        window.location = "./login";
                    }
                },
                error: function(error) {
                    window.location = "./login";
                }
            });
        }
    });
    return Router;
});
