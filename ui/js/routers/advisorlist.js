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
                    if (data.status == "OK" && typeof(data.advisor) == 'undefined' && data.user.urole == '777') {
                        userData = data;
                        $('#body').show();
                        $.ajax({
                            url: getAdvisorList,
                            type: 'POST',
                            data: {list_type: list_type,
                                sort_order: sort_order,
                                sort_by: sort_by,
                                current_page: current_page,
                            },
                            dataType: "json",
                            success: function(getAll) {
                                if (getAll.status == "OK") {
                                    require(['views/base/master', 'views/advisor/advisorlist'],
                                            function(masterV, advisorV) {
                                                masterV.render(data.user);
                                                advisorV.render(getAll.userdata, getAll.total);
                                                init();
                                                if (getAll.total > 0) {
                                                    $('#counter').html('( ' + getAll.total + ' )');
                                                } else {
                                                    $('#counter').html('( 0 )');
                                                    $('#no-record').html('<center>' + getAll.msg + '</center>');
                                                    $('#no-record').show();
                                                }
                                                $('.pagination').html(getAll.pagination);
                                                $('#' + list_type).addClass('active');
                                                $('.assignto').css('visibility', 'hidden');
                                                $('.assignme').hide();
                                                $('.revoke').hide();
                                                $('.deleteadv').hide();
                                                $('.viewprofile').hide();
                                                $('.advisor_designations').hide();
                                                $('.release').hide();

                                                if (list_type == 'all') {
                                                    $('.assignto').css('visibility', 'visible');
                                                    $('.assignme').show();
                                                    $('.deleteadv').show();
                                                    $('.viewprofile').show();
                                                    $('.signupdate').hide();
                                                } else if (list_type == 'unassigned') {
                                                    $('.assignme').show();
                                                    $('.viewprofile').show();
                                                    $('.signupdate').show();
                                                } else if (list_type == 'deleted') {
                                                    $('.assignto').css('visibility', 'visible');
                                                    $('.revoke').show();
                                                    $('.viewprofile').show();
                                                    $('.signupdate').hide();
                                                } else {
                                                    $('.advisor_designations').show();
                                                    $('.release').show();
                                                    $('.deleteadv').show();
                                                    $('.viewprofile').show();
                                                    $('.signupdate').hide();
                                                }

                                                init();

                                            }
                                    );
                                }
                            }
                        });
                    } else if(data.status == "OK" && typeof(data.advisor) != 'undefined') {
                        window.location = "./dashboard";
                    }
                    else if(data.status == "OK") {
                        window.location = "./myscore";
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