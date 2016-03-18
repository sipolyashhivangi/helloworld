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
                        require(['text!../../ui/html/advisor/limited.html', 'views/advisor/advisorhome', 'views/base/master'],
                                function(limitedT, advisorhomeV, masterV) {
                                    masterV.render(userData.advisor);
                                    $.getJSON(createinvoicelistURL, function(data) {});
                                    if (localStorage["showNewAdvisorDialog"] === "true") {
                                        require(['views/advisor/createAccountStepOne'],
                                                function(accountTwoV) {
                                                    accountTwoV.render("#comparisonBox", "new");
                                                    popUpActionStep();
                                                    localStorage["showNewAdvisorDialog"] = false;
                                                }
                                        );
                                    }
                                    var Stripe = document.createElement('script'); Stripe.type = 'text/javascript'; Stripe.async = true;
                                    Stripe.src = ('https://js.stripe.com/v2/');
                                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(Stripe, s);
                                    $.ajax({
                                        url: checkAdvisorSubscriptionURL,
                                        type: 'GET',
                                        dataType: "json",
                                        success: function(subData) {
                                            timeoutPeriod = defaultTimeoutPeriod;
                                            $("#gnav_finadv").addClass("hover reverseShadowBox");
                                            $("#gnav_finadv").removeClass("gnavButton");
                                            if (subData.status == "ERROR") {
                                                var source = $(limitedT).html();
                                                var template = Handlebars.compile(source);
                                                //div id under which we want to show the content of current html file.
                                                $('#mainBody').html(template());
                                                $("#limited").show();
                                                $("#past_due").hide();
                                                $("#canceled").hide();
                                                if (subData.subscription_status == "past_due") {
                                                    $("#limited").hide();
                                                    $("#canceled").hide();
                                                    $("#past_due").show();
                                                }
                                                if (subData.subscription_status == "canceled") {
                                                    $("#limited").hide();
                                                    $("#past_due").hide();
                                                    $("#canceled").show();
                                                }
                                            }
                                            else if (subData.status == "OK") {
                                                $.ajax({
                                                    url: advisorDetails,
                                                    cache: false,
                                                    type: 'POST',
                                                    dataType: "json",
                                                    data: {sort_order: 'ASC',
                                                        sort_by: 'status',
                                                        current_page: '1',
                                                        tabname: 'clientlist',
                                                    },
                                                    success: function(getAll) {
                                                        timeoutPeriod = defaultTimeoutPeriod;
                                                        advisorhomeV.render(getAll);
                                                        if (getAll.status == "OK") {
                                                            $('.pagination').html(getAll.pagination);
                                                            $('#allAdvisors').html(getAll.userSortdata);
                                                            $('#total_clients').html('(' + getAll.totalClient + ')');
                                                            $(".rppDD1").show();

                                                        } else if (getAll.status == "ERROR") {
                                                            $('.norecorderror').show();
                                                            $('.norecorderror').html(getAll.msg);
                                                            $('.sorting').removeClass('sorting');
                                                        }
                                                    }
                                                });
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
