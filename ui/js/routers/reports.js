define([
    'jquery',
    'underscore',
    'backbone'
], function($, _, Backbone) {
    var Router = Backbone.Router.extend({
        initialize: function() {
            userData = null;
            sess = localStorage[serverSess];
            var currentURL = window.location.pathname; // getting current URL
            var currentPageName = currentURL.substring(currentURL.lastIndexOf("/") + 1); // fetching current page name

            $.ajax({
                url: loginCheckUrl + "?sess=" + sess,
                type: 'GET',
                dataType: "json",
                success: function(data) {
                    userData = data;
                    $('#body').show();

                    if (data.status == "OK" && typeof(data.advisor) == 'undefined' && data.user.urole == 777) {
                        $('head').append('<link rel="stylesheet" href="./ui/css/ui-lightness/jquery-ui-1.9.2.custom.css?refresh=' + version + '" type="text/css" />');
                        $('head').append('<link rel="stylesheet" href="./ui/css/normalize.css?refresh=' + version + '" type="text/css" />');
                        $('head').append('<link rel="stylesheet" href="./ui/css/main.css?refresh=' + version + '" type="text/css" />');
                        $('head').append('<link rel="stylesheet" href="./ui/css/css.css?refresh=' + version + '" type="text/css" />');
                        $('head').append('<link rel="stylesheet" href="./ui/css/tabCss.css?refresh=' + version + '" type="text/css" />');
                        $("#navWrap").attr('style', 'font-size:16px');
                        $("#body").attr('style', 'font-size:14px;line-height:20px');
                        
                        var ajaxCallUrl = '';
                        if( currentPageName == 'usersbystatereport') {
                            ajaxCallUrl =  getUsersByStateReportURL;
                        }
                        if( currentPageName == 'basicreport') {
                            ajaxCallUrl = getReportsURL;
                        }
                        if( currentPageName == 'financesreport') {
                            ajaxCallUrl =  getUserFinancesReportURL;
                        }
                        if( currentPageName == 'liparams') {
                            ajaxCallUrl =  getLifeInsuranceParamsURL;
                        }
                        if( currentPageName == 'mcparams') {
                            ajaxCallUrl =  getMonteCarloParamsURL;
                        }
                        if(ajaxCallUrl == '') {
                            require(
                                ['views/base/header', 'views/base/footer', 'views/admin/' + currentPageName],
                                function(headerV, footerV, reportV) {
                                    headerV.render(data.user);
                                    footerV.render();
                                    reportV.render();
                                    init();
                                }
                            );
                        } else {
                            $.ajax({
                                url: ajaxCallUrl,
                                type: 'GET',
                                dataType: "json",
                                success: function(reportData) {
                                    timeoutPeriod = defaultTimeoutPeriod;
                                    require(
                                        ['views/base/header', 'views/base/footer', 'views/admin/' + currentPageName],
                                        function(headerV, footerV, reportV) {
                                            headerV.render(data.user);
                                            footerV.render();
                                            reportV.render(reportData);
                                            init();
                                        }
                                    );
                                }
                            });
                        }
                    } else if (data.status == "OK" && typeof(data.advisor) != 'undefined') {
                        window.location = "./dashboard";
                    } else if (data.status == "OK") {
                        window.location = "./myscore";
                    } else {
                        window.location = "./login";
                    }
                }
            });
        }
    });
    return Router;
});