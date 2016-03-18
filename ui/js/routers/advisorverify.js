define([
    'jquery',
    'underscore',
    'backbone'
], function($, _, Backbone) {

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
                    if (data.status == "OK" && typeof(data.advisor) == 'undefined' && data.user.urole == '777') {
                        userData = data;

                        require(
                                ['views/advisor/advisorverify'],
                                function(fadvV) {
                                    fadvV.render();
                                    init();
                                    //$(".cancelPopup").toggleClass("hdn");
                                }
                        );
                    } else if(data.status == "OK" && typeof(data.advisor) != 'undefined') {
                        window.location = "./dashboard";
                    } else if(data.status == "OK") {
                        window.location = "./myscore";
                    }
                    else
                    {
                        window.location = "./login";
                    }
                },
                error: function(data) {
                    window.location = "./login";
                }
            });
        }
    });
    return Router;
});