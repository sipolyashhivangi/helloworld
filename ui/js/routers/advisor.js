define([
    'jquery',
    'underscore',
    'backbone'
], function($, _, Backbone) {

    var Router = Backbone.Router.extend({
        initialize: function() {
            userData = null;
            sess = localStorage[serverSess];

            $.ajax({
                url: loginCheckUrl + "?sess=" + sess,
                type: 'GET',
                dataType: "json",
                success: function(data) {
                    userData = data;
                    $('#body').show();
                    if (data.status == "OK" && typeof(data.advisor) != 'undefined') {
                        window.location = "./dashboard";
                    } else if (data.status == "OK") {
                        window.location = "./myscore";
                    } else {
                        require(
                                ['views/profile/login', 'views/profile/signup', 'views/profile/advisor'],
                                function(loginV, signupV, advisorV) {
                                    loginV.render();
                                    signupV.render();
                                    advisorV.render(data);
                                    $(".cancelPopup").toggleClass("hdn");
                                    $(".cancelAdvisorPopup").toggleClass("hdn");
                                    init();
                                    $("#signupadvisortab").click();
                                }
                        );
                    }
                }
            });
        }
    });
    return Router;
});