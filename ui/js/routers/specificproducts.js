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
            var list_type = 'assigned';
            var sort_order = 'DESC';
            var sort_by = 'createdtimestamp';
            var current_page = 1;
            $.ajax({
                url: loginCheckUrl + "?sess=" + sess,
                type: 'GET',
                dataType: "json",
                success: function(data) {
                    if (data.status == "OK" && typeof (data.advisor) == 'undefined' && data.user.urole == '777') {
                        userData = data;
                        $('#body').show();
                        require(['views/base/master', 'views/admin/specificproducts'],
                                function(masterV, specificproductsV) {
                                     masterV.render(data.user);

                                     $.ajax({
                                                url: getadminexternallinkas,
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
                                                    specificproductsV.render(getAll);
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