// Filename: views/login/advisorsignup
define([
    'jquery',
    'underscore',
    'handlebars',
    'backbone',
    'text!../../../html/advisor/advisorlist.html',
    'text!../../../html/advisor/adminnotification.html',
], function($, _, Handlebars, Backbone, advisorListTemplate, notificationTemplate) {

    var list_type = 'assigned';
    var sort_order = 'DESC';
    var sort_by = 'createdtimestamp';
    var current_page = 1;

    var advisorListView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj, xyz) {
            var current_page = 1;
            var source = $(advisorListTemplate).html();
            var template = Handlebars.compile(source);
            $('#mainBody').html(template(obj, xyz));
            //init();
        },
        events: {
            "click .pagelink": "performPagination",
            "click .savebtn": "actionAdvDesigVerification",
            "click .cancelbtn": "fnCancel",
            "click .sorting": "performNameSorting",
            "click .advisor_list": "actionAdvisorList",
            "click .assignme": "actionAssignAdvisor",
            "click .advisor_designations": "editDesignations",
            "change .pagelink_drop": "dropdownPagination",
            "click .release": "confirmAdvisorDelete",
            "click .viewprofile": "displayProfile",
            "click .deleteadv": "fnadvisorDelete",
            "click #adminnotifications": "fnadminnotifications",
            "click .revoke": "revokeAdvisor",
        },
        confirmAdvisorDelete: function(event) {
            var advisorId = event.target.attributes.getNamedItem('adv_id').nodeValue;
            require(['views/advisor/advisordelete'],
                    function(advisordeleteV) {
                        advisordeleteV.render(advisorId);
                    }
            );
        },
        fnadvisorDelete: function(event) {
            var advisorId = event.target.attributes.getNamedItem('adv_id').nodeValue;
            require(['views/advisor/advisordeletebtn'],
                    function(advisordelV) {
                        advisordelV.render(advisorId,list_type,current_page);
                    }
            );
        },
        fnupdateUnverified: function(event) {

        },
        editDesignations: function(event) {
            var advisorId = event.target.attributes.getNamedItem('adv_id').nodeValue;
            $('.savebtn' + advisorId).show();
            $('.cancelbtn' + advisorId).show();
            $('.editbtn' + advisorId).hide();
            $('#deleteadv' + advisorId).hide();
            $('.releasebtn' + advisorId).hide();
            $('.viewprofilebtn' + advisorId).hide();
            $('#unedit' + advisorId).hide();
            $('.designations_container' + advisorId).show();
        },
        fnCancel: function(event) {
            var advisorId = event.target.attributes.getNamedItem('adv_id').nodeValue;
            $('.savebtn' + advisorId).hide();
            $('.cancelbtn' + advisorId).hide();
            $('.editbtn' + advisorId).show();
            $('#deleteadv' + advisorId).show();
            $('.designations_container' + advisorId).hide();
            $('#unedit' + advisorId).show();
            $('.releasebtn' + advisorId).show();
            $('.viewprofilebtn' + advisorId).show();
        },
        actionAdvisorList: function(event) {
            list_type = event.target.attributes.getNamedItem('list_type').nodeValue;
            sort_order = 'DESC';
            sort_by = 'createdtimestamp';
            current_page = 1;
            getList(list_type, sort_order, sort_by, current_page);
        },
        actionAssignAdvisor: function(event) {
            var advisorId = event.target.attributes.getNamedItem('adv_id').nodeValue;
            removeLayover();
            $('#msg').addClass('hdn');
            $.ajax({
                url: assignAdvisor,
                type: 'POST',
                data: {
                    adv_id: advisorId
                },
                dataType: "json",
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == 'OK') {
                        msg = 'Advisor assigned sucessfully.';
                        getList(list_type, sort_order, sort_by, current_page, msg, true);
                    } else {
                        /*timeoutPeriod = defaultTimeoutPeriod;
                         var source = $(notificationTemplate).html();
                         var template = Handlebars.compile(source);
                         $("#notificationContents").html(template(data));
                         popUpNotification();*/
                    }
                }
            });
        },
        actionAdvDesigVerification: function(event) {
            $('#msg-box').addClass('hdn');
            var adv_id = event.target.attributes.getNamedItem('adv_id').nodeValue;
            var varify_designations = new Array();
            var unvarify_designations = new Array();
            var verified_index = 0;
            var unverified_index = 0;
            designationOptions = $('.designations' + adv_id);
            $.each(designationOptions[0], function(index, designation) {
                if (designation.selected) {
                    varify_designations[verified_index] = designation.value;
                    verified_index++;
                } else {
                    unvarify_designations[unverified_index] = designation.value;
                    unverified_index++;
                }
            });
            $.ajax({
                url: advisorDesignationVerification,
                type: 'POST',
                data: {adv_id: adv_id,
                    varify_designations: varify_designations.join(','),
                    unvarify_designations: unvarify_designations.join(',')
                },
                dataType: "json",
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    getList(list_type, sort_order, sort_by, current_page, data.msg, true);
                    //getList(list_type, sort_order, sort_by, current_page);                            
                }
            });
        },
        performPagination: function(event) {
            current_page = event.target.attributes.getNamedItem('pageno').nodeValue;
            getList(list_type, sort_order, sort_by, current_page, '', false);
        },
        dropdownPagination: function(event) {
            current_page = event.target.value;
            getList(list_type, sort_order, sort_by, current_page, '', false);
        },
        performNameSorting: function(event) {
            var sort = event.target.attributes.getNamedItem('sorttype').nodeValue;
            sort_by = sort;
            if (sort_order == 'ASC')
                sort_order = 'DESC';
            else if (sort_order == 'DESC')
                sort_order = 'ASC';
            getList(list_type, sort_order, sort_by, current_page, '', false);
        },
        displayProfile: function(event) {
            var advisorId = event.target.attributes.getNamedItem('adv_id').nodeValue;
            var advisorHash = event.target.attributes.getNamedItem('adv_hash').nodeValue;
            var assigneeId = $('#assigneeId' + advisorId).val();
            $.ajax({
                url: advisorProfileUrl,
                type: 'POST',
                data: {adv_hash: advisorHash, assignee_id: assigneeId,
                },
                dataType: "json",
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "OK") {
                        require(['views/advisor/profile'],
                                function(profileV) {
                                    profileV.render(data.userdata, data.assign);
                                });
                    }
                }
            });
        },
        fnadminnotifications: function(event) {


        },
        revokeAdvisor: function(event) {
            var advisorId = event.target.attributes.getNamedItem('adv_id').nodeValue;
            $('#msg').addClass('hdn');
            $.ajax({
                url: revokeAdvisor,
                type: 'POST',
                data: {
                    adv_id: advisorId
                },
                dataType: "json",
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == 'OK') {
                        timeoutPeriod = defaultTimeoutPeriod;
                        msg = 'Advisor revoked sucessfully.';
                        getList(list_type, sort_order, sort_by, current_page, msg, true);
                        /*getList(list_type, sort_order, sort_by, current_page);
                         $('#msg').html('Advisor revoked sucessfully.');
                         $('#msg-box').removeClass('hdn');*/
                    } else {
                        /*timeoutPeriod = defaultTimeoutPeriod;
                         var source = $(notificationTemplate).html();
                         var template = Handlebars.compile(source);
                         $("#notificationContents").html(template(data));
                         popUpNotification();*/
                    }
                }
            });
        },
    });

    return new advisorListView;
});

function getList(list_type, sort_order, sort_by, current_page, msg, displayMsg) {
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
                require(['views/advisor/advisorlist'],
                        function(listV) {
                            listV.render(getAll.userdata, getAll.total);
                            if (displayMsg == true) {
                                $('#msg').html(msg);
                                $('#msg-box').removeClass('hdn');
                                $('#msg').removeClass('hdn');
                            } else {
                                $('#msg').html('');
                                $('#msg-box').addClass('hdn');
                                $('#msg').addClass('hdn');
                            }
                            if (getAll.total > 0) {
                                $('#counter').html('(' + getAll.total + ')');
                            } else {
                                $('#counter').html('(0)');
                                $('#no-record').html('<center>' + getAll.msg + '</center>');
                                $('#no-record').show();
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
                                $('.width25_6').css('width', '182px');
                                $('.assignme').show();
                                $('.viewprofile').show();
                                $('.assignto').hide();
                                $('.signupdate').show();
                                $('.deleteadv').show();
                            } else if (list_type == 'deleted') {
                                $('.revoke').show();
                                $('.viewprofile').hide();
                                $('.assignto').css('visibility', 'hidden');
                                $('.signupdate').hide();
                            } else if (list_type == 'others') {
                                $('.assignto').css('visibility', 'visible');
                                $('.viewprofile').show();
                                $('.signupdate').hide();
                            } else {
                                $('.advisor_designations').show();
                                $('.release').show();
                                $('.deleteadv').show();
                                $('.viewprofile').show();
                                $('.signupdate').hide();
                            }
                        }
                );
            }
        }
    });
}

$(document).ready(function() {
    $('.multiselect').live('change', function() {
        var ids = $(this).attr('id');
        rowId = $(this).attr('replace-id');
        var str = '';
        var unverifiedStr = $('#unverfied' + rowId).text();
        var unverifiedArr = new Array();
        var verifiedArr = new Array();
        $('#' + ids + ' option').each(function() {
            var $this = $(this);
            if ($(this).is(':selected')) {
                verifiedArr.push($this.text());
            } else {
                if (!$.inArray($.trim($this.text()), unverifiedArr) >= 0) {
                    unverifiedArr.push($this.text());
                }
            }
        });
        str = array_diff(unverifiedArr, verifiedArr);
        if (str == '')
            str = Array('N/A');
        $('#unverfied' + rowId).html(str.join(', '));
    })

    function array_diff(unverifiedArr, verifiedArr) {
        var difference = [];
        cleanUnverifiedArr = [];
        cleanVerifiedArr = [];
        $.each(unverifiedArr, function() {
            cleanUnverifiedArr.push($.trim(this));
        });

        $.each(verifiedArr, function() {
            cleanVerifiedArr.push($.trim(this));
        });
        jQuery.grep(cleanUnverifiedArr, function(el) {
            if (jQuery.inArray(el, cleanVerifiedArr) == -1)
                difference.push(el);
        });
        return difference;
    }

});