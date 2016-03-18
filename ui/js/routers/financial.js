define([
    'jquery',
    'underscore',
    'backbone',
    'handlebars',
], function($, _, Backbone, Handlebars) {
    var Router = Backbone.Router.extend({
        initialize: function() {
            init();
            userData = null;
            sess = localStorage[serverSess];

            $.ajax({
                url: loginCheckUrl + "?sess=" + sess,
                type: 'GET',
                dataType: "json",
                success: function(data) {
                    userData = data;
                    if (data.status == "OK" && typeof(data.user) != 'undefined') {
                        forceUserNotifications = true;
                        require(
                                ['views/user/financialsnapshot', 'views/base/master'],
                                function(financialV, masterV) {
                                    if(typeof(userData.advisor) != 'undefined') {
                                        userData.user.impersonationMode = true;
                                    }
                                    masterV.render(userData.user);
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
                                        financialV.render();
                                        $("#gnav_finSnap").addClass("hover reverseShadowBox");
                                        $("#gnav_finSnap").removeClass("gnavButton");
                                    }
                                }
                        );
                    }
                    else if(data.status == "OK") {
                        window.location = "./dashboard";
                    }
                    else
                    {
                        window.location = "./login";
                    }
                }
            });
        }
    });
    return Router;
});