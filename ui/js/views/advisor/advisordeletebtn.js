define([
    'handlebars',
    'backbone',
    'text!../../../html/advisor/advisordeletebtn.html',
], function(Handlebars, Backbone, profileTemplate) {
    var del_list_type = 'assigned';
    var selectedpage = 1;
    var profileView = Backbone.View.extend({
        el: $("#body"),
        render: function(advisor_id, list_type, current_page1) {
            var source = $(profileTemplate).html();
            var template = Handlebars.compile(source)
            $("#profileContents").html(template(advisor_id));
            $("#advisor_id").val(advisor_id);
            popUpManageCredentials();
            del_list_type = list_type;
            selectedpage = current_page1;
        },
        events: {
            "click .cancelProfilePopup": "closeProfileDialog",
            "click .deleteAdvisor": "fnDeleteAdvisor",
        },
        initialize: function() {
//                this.signupButton = $("#signup");
        },
        // use this for close overlay after click close(x) link.
        closeProfileDialog: function(event) {
            event.preventDefault();
            removeLayover();

        },
        fnDeleteAdvisor: function(event) {
            advisor_id = $('#advisor_id').val();
            removeLayover();
            var data = {
                advisor_id: advisor_id,
            };
            $.ajax({
                url: deleteadvisor,
                type: 'POST',
                dataType: "json",
                data: data,
                success: function(data) {

                    timeoutPeriod = defaultTimeoutPeriod;
                    var list_type = del_list_type;
                    var sort_order = 'DESC';
                    var sort_by = 'createdtimestamp';
                    var current_page = selectedpage;

                    $.ajax({
                        url: getAdvisorList,
                        type: 'POST',
                        data: {
                            list_type: list_type,
                            sort_order: sort_order,
                            sort_by: sort_by,
                            current_page: current_page
                        },
                        dataType: "json",
                        success: function(getAll) {
                            timeoutPeriod = defaultTimeoutPeriod;
                            if (getAll.status == "OK") {
                                require(['views/advisor/advisorlist'],
                                        function(listV) {
                                            listV.render(getAll.userdata, getAll.total);
                                            $('#msg').html('Advisor deleted successfully.');
                                            $('#msg-box').removeClass('hdn');
                                            $('#msg').removeClass('hdn');
                                            if (getAll.total > 0) {
                                                $('#counter').html('(' + getAll.total + ')');
                                            } else {
                                                $('#counter').html('(0)');
                                                $('#no-record').html(getAll.msg);
                                                $('#no-record').show();
                                                if(current_page > 1){
                                                    showpage = current_page-1;
                                                }else{
                                                    showpage = current_page;
                                                }
                                                getList(list_type, sort_order, sort_by, showpage);
                                            }
                                            $('.pagination').html(getAll.pagination);
                                            $(':input').removeClass('active');
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
                                                $('.viewprofile').show();
                                                $('.signupdate').hide();
                                            } else if (list_type == 'unassigned') {
                                                $('.width25_6').css('width', '180px');
                                                $('.assignme').show();
                                                $('.viewprofile').show();
                                                $('.assignto').hide();
                                                $('.signupdate').show();
                                                $('.deleteadv').show();
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

                }
            });
        },
    });
    return new profileView;
});