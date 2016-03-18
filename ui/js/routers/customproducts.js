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
                    timeoutPeriod = defaultTimeoutPeriod;
                    userData = data;
                    if (data.status == "OK" && typeof (data.advisor) != 'undefined') {
                        require(['views/advisor/customproducts', 'views/base/master'],
                            function(advisorhomeV, masterV) {
                                masterV.render(userData.advisor);
                                $.ajax({
                                    url: getexternallinkas,
                                    cache: false,
                                    type: 'POST',
                                    dataType: "json",
                                    data: {sort_order: 'ASC',
                                        sort_by: 'actionname',
                                        current_page: '1',
                                    },
                                    success: function(getAll) {
                                        timeoutPeriod = defaultTimeoutPeriod;
                                        //$("#gnav_finadv").addClass("hover reverseShadowBox");
                                        //$("#gnav_finadv").removeClass("gnavButton");
                                        advisorhomeV.render(getAll);
                                        if (getAll.status == "OK") {
                                            $('.pagination').html(getAll.pagination);
                                            $('#allAdvisors').html(getAll.userSortdata);
                                            $('#total_clients').html('(' + getAll.totalAS + ')');
                                        } else if (getAll.status == "ERROR") {
                                            $('.norecorderror').show();
                                            $('.norecorderror').html(getAll.msg);
                                            $('.sorting').removeClass('sorting');
                                        }
                                    }
                                });
                            }
                        );
                    }
                    else if (data.status == 'OK') {
                        window.location = "./myscore";
                    } else {
                        window.location = "./advisorlogin";
                    }

                },
                error: function(error) {
                    window.location = "./advisorlogin";
                }
            });
        }
    });
    return Router;
});
