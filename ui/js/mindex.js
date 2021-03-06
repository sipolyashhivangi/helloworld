require.config({
    'paths': {
        "underscore": "libs/underscore-min",
        "backbone": "libs/backbone-min",
        "handlebars": "libs/handlebars",
        "modernizr": "libs/modernizr-latest",
        "modernizr-dev": "libs/modernizr-dev",
        "jquerytouchSwipe": "libs/jquery/jquery.touchSwipe.min",
        "jquerycarou": "libs/jquery/jquery.carouFredSel-6.2.0-packed",
        "jqueryui": "libs/jquery/jquery-ui-1.9.2.custom.min",
        "jqueryscrollto": "libs/jquery/jquery.scrollTo-1.4.3.1-min",
        "bootstrapflexscore": "libs/bootstrap/flexscore",
        "homeonload": "libs/homeOnload",
        "bootstrap": "libs/bootstrap/bootstrap.min",
        "carousel": "libs/bootstrap/bootstrap-carousel",
        "bxslider": "libs/jquery/jquery.bxslider.min",
        "easypaginate": "libs/easypaginate",
        "ie-styles": "libs/ie-styles",
        "jquerycorner": "libs/jquery/jquery-corner",
        "utilityshared": "utility/shared",
        "financialsnapshotchart": "utility/financialsnapshotchart",
        "myscorechart": "utility/myscorechart",
        "buttons": "libs/buttons",
        "jqueryform":"libs/jquery/jquery.form",
        "multiselect":"libs/bootstrap/bootstrap-multiselect",
    },
    'shim':
            {
                backbone: {
                    'deps': ['jquery', 'underscore'],
                    'exports': 'Backbone'
                },
                underscore: {
                    'exports': '_'
                },
                handlebars: {
                    exports: 'Handlebars'
                },
                bootstrap: {
                    'deps': ['jquery', 'jqueryui', 'jqueryscrollto'],
                    'exports': 'bootstrap'
                },
                bxslider: {
                    'deps': ['jquery', 'jqueryui', 'jqueryscrollto'],
                    'exports': 'Bxslider'
                }
            },
    urlArgs: "refresh=" + version
});
require([
    'underscore',
    'backbone',
    'jquery',
    'jqueryui',
    'jqueryscrollto',
    'bootstrap',
    'carousel',
    'bxslider',
    'jquerytouchSwipe',
    'utilityshared',
    'jqueryform',
    'multiselect',
    'modernizr',
    'app',
    'routers/index'
],
        function(_, Backbone, $, jqueryui, jqueryscrollto, bootstrap, carousel, bxslider, jquerytouchSwipe, utilityshared,jqueryform,multiselect, Modernizr, app, index) {
            app.init(index);
        });
