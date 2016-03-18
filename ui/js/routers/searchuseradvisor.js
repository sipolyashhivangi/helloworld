require.config({
    'paths': {
        //"jquerymultiselect": "bootstrap/bootstrap-multiselect",
        "jqueryform": "libs/jquery/jquery.form",
    }
});
define([
    'jquery',
    'underscore',
    'backbone',
    'handlebars',
    'jqueryform',
], function($, _, Backbone, Handlebars, jqueryform) {
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
                    $('#body').show();
                    if (data.status == "OK" && typeof (data.advisor) == 'undefined') {
                        $.ajax({
                            url: searchAdvisor,
                            type: 'POST',
                            dataType: "json",
                            success: function(getAll) {
                                if (getAll.status == "OK" && (typeof(getAll.loggedin_user_created_by) == 'undefined' || getAll.loggedin_user_created_by == "")) {
                                    require(['views/base/master', 'views/advisor/search', 'views/advisor/searchresult'],
                                            function(masterV, searchV, searchResultV) {
                                                masterV.render(data.user);
                                                searchV.render(getAll.userdata);
                                                searchParams = new Array();
                                                searchResultV.render(getAll.userdata, searchParams, getAll.msg);
                                                init();

                                            }
                                    );

                                } else {
                                    window.location = "./myscore";
                                }
                            }
                        });
                        init();
                    } else {
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
