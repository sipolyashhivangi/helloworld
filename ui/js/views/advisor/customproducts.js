// Filename: views/login/advisorsignup
define([
    'handlebars',
    'text!../../../html/advisor/customproducts.html',
    'text!../../../html/advisor/limited.html',
], function(Handlebars, advisorTemplate) {

    var sort_order = 'ASC';
    var sort_by = 'createdtimestamp';
    var current_page = 1;

    var advisorHomeView = Backbone.View.extend({
        //body div id .
        el: $("#body"),
        render: function(obj) {
            timeoutPeriod = defaultTimeoutPeriod;
            var source = $(advisorTemplate).html();
            var template = Handlebars.compile(source);
            //div id under which we want to show the content of current html file.
            $('#mainBody').html(template(obj));
            if (typeof (userData) == 'undefined') {
                userData = {};
            }
        },
        events: {
            "click .pagelink ": "performPagination",
            "click .sorting": "performSorting",
            "change .pagelink_drop ": "performDropDownPagination",
            "click .editASProduct": "editASProduct",
            "click .restoreDefaultASDesc": "restoreDefault"
        },
        restoreDefault: function(event) {
            var id = event.target.id;            
            var action_id = id.substring(0, id.indexOf("restoreDefaultASDesc"));

            var formValues = {
                actionid: action_id,
                restore: true
            };
            var url = updateexternallinkasDesc;

            $.ajax({
                url: url,
                cache: false,
                type: 'POST',
                dataType: "json",
                data: formValues,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                    $('#desc' + action_id).html('No recommendation available.');
                    $('#productname' + action_id).val('');
                    $('#productimage' + action_id).val('');
                    $('#productlink' + action_id).val('');
                    $('#asdesc' + action_id).val('');
                    $("#" + action_id + "restoreDefaultASDesc").hide();
                    $("#" + action_id + "productImageTableCell").hide();
                    $('#advproducttype' + action_id).hide();
                    $('#advproductname' + action_id).show();
                    event.preventDefault();
                    removeLayover();
                },
                error: function(data) {
                }
            });
            return false;

        },
        editASProduct: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var id = name.substring(0, name.indexOf("editASProduct"));
            addASDescription(id);
        },


        performPagination: function(event) {
            current_page = event.target.attributes.getNamedItem('pageno').nodeValue;
            timeoutPeriod = defaultTimeoutPeriod;
            getASList(sort_order, sort_by, current_page);
        },
        performDropDownPagination: function(event) {
            current_page = event.target.value;
            getASList(sort_order, sort_by, current_page);
        },
        performSorting: function(event) {
            sort_by = event.target.attributes.getNamedItem('sorttype').nodeValue;
            timeoutPeriod = defaultTimeoutPeriod;
            if (sort_order == 'ASC')
                sort_order = 'DESC';
            else
                sort_order = 'ASC';
            getASList(sort_order, sort_by, current_page);
        }
    });

    return new advisorHomeView;
});

function getClientList(sort_order, sort_by, current_page) {
    $.ajax({
        url: advisorDetails,
        type: 'POST',
        dataType: "json",
        data: {sort_order: sort_order,
            sort_by: sort_by,
            current_page: current_page,
            tabname: 'clientlist',
        },
        success: function(data) {
            timeoutPeriod = defaultTimeoutPeriod;
            if (data.status == "OK") {
                require(['views/advisor/advisorhome', 'views/base/master'],
                        function(advisorhomeV, masterV) {
                            masterV.render(userData.advisor);
                            advisorhomeV.render(data);
                            $("#gnav_finadv").addClass("hover reverseShadowBox");
                            $("#gnav_finadv").removeClass("gnavButton");
                            $('.pagination').html(data.pagination);
                            $('#allAdvisors').html(data.userSortdata);
                            $('#total_clients').html('(' + data.totalClient + ')');
                        }
                );
            } else if (data.status == "ERROR") {
                require(['views/advisor/advisorhome', 'views/base/master'],
                        function(advisorhomeV, masterV) {
                            masterV.render(userData.advisor);
                            advisorhomeV.render(data);
                            $("#gnav_finadv").addClass("hover reverseShadowBox");
                            $("#gnav_finadv").removeClass("gnavButton");
                            $('.norecorderror').show();
                            $('.norecorderror').html(data.msg);
                            $('.sorting').removeClass('sorting');
                        }
                );
            }
        }
    });
}

function getASList(sort_order, sort_by, current_page) {
    $.ajax({
        url: getexternallinkas,
        cache: false,
        type: 'POST',
        dataType: "json",
        data: {
            sort_order: sort_order,
            sort_by: 'actionname',
            current_page: '1',
        },
        success: function(data) {
            timeoutPeriod = defaultTimeoutPeriod;
            if (data.status == "OK") {
                require(['views/advisor/customproducts', 'views/base/master'],
                        function(advisorhomeV, masterV) {
                            masterV.render(userData.advisor);
                            advisorhomeV.render(data);
                            //$("#gnav_finadv").addClass("hover reverseShadowBox");
                            //$("#gnav_finadv").removeClass("gnavButton");
                            //$('.pagination').html(data.pagination);
                            $('#allAdvisors').html(data.userSortdata);
                            $('#total_clients').html('(' + data.totalAS + ')');
                        }
                );
            } else if (data.status == "ERROR") {
                require(['views/advisor/customproducts', 'views/base/master'],
                        function(advisorhomeV, masterV) {
                            masterV.render(userData.advisor);
                            advisorhomeV.render(data);
                            //$("#gnav_finadv").addClass("hover reverseShadowBox");
                            //$("#gnav_finadv").removeClass("gnavButton");
                            // $('.norecorderror').show();
                            // $('.norecorderror').html(data.msg);
                            $('.sorting').removeClass('sorting');
                        }
                );
            }
        }
    });
}

function addASDescription(actionid, actionname) {

    var desc = $('#asdesc' + actionid).val();
    var flexdesc = $('#flexasdesc' + actionid).val();
    var productname = $('#productname' + actionid).val();
    var productimage = $('#productimage' + actionid).val();
    var productlink = $('#productlink' + actionid).val();

    var data = {
        actionId: actionid,
        actionname: actionname,
        actiondesc: desc,
        flexasdesc: flexdesc,
        product_name: productname,
        product_image: productimage,
        product_link: productlink,
    };
    require(
            ['views/profile/createnewasdesc'],
            function(createnewV) {
                createnewV.render(data);
                popUpCreateASDescPopup();
            }
    );
}
