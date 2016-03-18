// Filename: views/login/advisorsignup
define([
    'handlebars',
    'text!../../../html/advisor/dashboard.html'
], function(Handlebars, advisorTemplate) {

    var sort_order = 'ASC';
    var sort_by = 'status';
    var current_page = 1;
    var record_per_page = 25;

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
            $('#sortBy').val(sort_by);
            $('#pageNo').val(current_page);
            $('#sortOrder').val(sort_order);
            $('#recordPerPage').val(record_per_page);
        },
        events: {
            "click .pagelink ": "performPagination",
            "click .recordlink ": "performPaginationByPageRecord",
            "click .sorting": "performSorting",
            "change .pagelink_drop": "performDropDownPagination",
            "click #btnCreateNewClient": "createNewClient", //function to create new client by advisor
            "click #btnUploadNewClient": "performCSVUploadNewClients",
            "click #btnUploadMoreClient": "performCSVUploadNewClients",
            "click #viewfinanceSummary": "performClientfinanceSummary"
        },
        performPaginationByPageRecord: function(event) {
            event.preventDefault();
            record_per_page = event.target.attributes.getNamedItem('recordscount').nodeValue;
            timeoutPeriod = defaultTimeoutPeriod;
            getClientList(sort_order, sort_by, current_page, record_per_page);
        },
        performClientfinanceSummary: function(event) {
            //alert("hi");
            event.preventDefault();
            var user_id = event.currentTarget.attributes.getNamedItem('clientId').nodeValue;
            require(
                    ['views/profile/clientfinancialsummary'],
                    function(createnewV) {
                        $.ajax({
                            url: getAllItem,
                            type: 'GET',
                            dataType: "json",
                            data: {
                                user_id: user_id,
                            },
                            cache: false,
                            beforeSend: function(request) {
                                request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                            },
                            success: function(data) {
                                timeoutPeriod = defaultTimeoutPeriod;
                                if (data.status == "OK") {
                                    fnUpdateAllData(data);
                                    fnUpdateFinancialData();
                                    createnewV.render(financialData, user_id);
                                    popUpclientFinancialSummary();
                                    init();


                                    $.ajax({
                                        url: userGetActionStepURL,
                                        type: 'GET',
                                        dataType: "json",
                                        data: {
                                            user_id: user_id,
                                            stepscount: 4
                                        },
                                        cache: false,
                                        beforeSend: function(request) {
                                            request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                                        },
                                        success: function(scoreData) {
                                            timeoutPeriod = defaultTimeoutPeriod;
                                            require(
                                                    ['views/user/finacialSummaryActionsteps'],
                                                    function(actionstepV) {
                                                        actionstepV.render(scoreData);
                                                        if (loadfakeactionstep) {
                                                            $('#fakeActionStep').val(currentactionstepid);
                                                            $("#fakeActionStep").click();
                                                            loadfakeactionstep = false;
                                                        }
                                                    }
                                            );
                                        }
                                    });
                                }
                            }
                        });
                    }
            );

        },
        createNewClient: function(event) {
            event.preventDefault();
            var advRoleID = event.currentTarget.attributes.getNamedItem('roleId').nodeValue;
            require(
                    ['views/profile/createnewclient'],
                    function(createnewV) {
                        createnewV.render(advRoleID, sort_order, sort_by, current_page, record_per_page);
                        popUpCreatenewclient();
                    }
            );
        },
        performCSVUploadNewClients: function(event) {
            event.preventDefault();
            var advRoleID = event.currentTarget.attributes.getNamedItem('roleId').nodeValue;
            require(
                    ['views/profile/uploadnewclientlist'],
                    function(createnewV) {
                        createnewV.render(advRoleID, sort_order, sort_by, current_page, record_per_page);
                        popUpUploadnewclientList();
                    }
            );

        },
        performPagination: function(event) {
            current_page = event.target.attributes.getNamedItem('pageno').nodeValue;
            timeoutPeriod = defaultTimeoutPeriod;
            getClientList(sort_order, sort_by, current_page, record_per_page);
        },
        performDropDownPagination: function(event) {
            current_page = event.target.value;
            getClientList(sort_order, sort_by, current_page, record_per_page);
        },
        performSorting: function(event) {
            sort_by = event.target.attributes.getNamedItem('sorttype').nodeValue;
            timeoutPeriod = defaultTimeoutPeriod;
            if (sort_order == 'ASC')
                sort_order = 'DESC';
            else
                sort_order = 'ASC';

            getClientList(sort_order, sort_by, current_page, record_per_page);

        },
    });

    return new advisorHomeView;
});

function getClientList(sort_order, sort_by, current_page, record_per_page) {
    $.ajax({
        url: advisorDetails,
        type: 'POST',
        dataType: "json",
        data: {
            sort_order: sort_order,
            sort_by: sort_by,
            current_page: current_page,
            record_per_page: record_per_page,
            tabname: 'clientlist'
        },
        success: function(data) {
            timeoutPeriod = defaultTimeoutPeriod;
            if (data.status == "OK") {
                require(['views/advisor/advisorhome'],
                        function(advisorhomeV) {
                            advisorhomeV.render(data);
                            $('.pagination').html(data.pagination);
                            $('#allAdvisors').html(data.userSortdata);
                            $('#total_clients').html('(' + data.totalClient + ')');
                            $(".recordsButtonSpan").html(record_per_page);
                            $(".rppDD1").show();
                        }
                );
            } else if (data.status == "ERROR") {
                require(['views/advisor/advisorhome'],
                        function(advisorhomeV) {
                            advisorhomeV.render(data);
                            $('.norecorderror').show();
                            $('.norecorderror').html(data.msg);
                            $('.sorting').removeClass('sorting');
                            $("#recordsButtonSpan").html(record_per_page);
                        }
                );
            }
        }
    });
}

function getASList(sort_order, sort_by, current_page) {

    $("#btnCreateCustomProduct").addClass('active');
    document.getElementById("connectedConsumers").style.display = "none";
    document.getElementById("customProducts").style.display = "";
    $.ajax({
        url: advisorDetails,
        cache: false,
        type: 'POST',
        dataType: "json",
        data: {
            sort_order: 'ASC',
            sort_by: 'actionid',
            current_page: '1',
            tabname: 'actionsteps',
        },
        success: function(getAll) {
            timeoutPeriod = defaultTimeoutPeriod;
            $("#gnav_finadv").addClass("hover reverseShadowBox");
            $("#gnav_finadv").removeClass("gnavButton");
            //advisorhomeV.render(getAll);
            if (getAll.status == "OK") {
                $('.pagination').html(getAll.pagination);
                $('#allAdvisors').html(getAll.userSortdata);
                $('#total_clients').html('(' + getAll.totalClient + ')');
            } else if (getAll.status == "ERROR") {
                $('.norecorderror').show();
                $('.norecorderror').html(getAll.msg);
                $('.sorting').removeClass('sorting');
            }
        }
    });

}

function addASDescription(advId, actionid, actionname) {

    var desc = $('#asdesc' + actionid).val();
    var flexdesc = $('#flexasdesc' + actionid).val();
    var data = {
        advId: advId,
        actionId: actionid,
        actionname: actionname,
        actiondesc: decodeURIComponent(desc.replace(/\+/g, ' ')),
        flexasdesc: flexdesc,
        advRoleID: '999'
    };
    require(
            ['views/profile/createnewasdesc'],
            function(createnewV) {
                createnewV.render(data);
                popUpCreateASDescPopup();
            }
    );
}