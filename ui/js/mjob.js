require.config({
    'paths': {
        "underscore": "libs/underscore-min",
        "backbone": "libs/backbone-min",
        "handlebars": "libs/handlebars",
        "modernizr": "libs/modernizr-latest",
        "modernizr-dev": "libs/modernizr-dev",
        "bootstraptrans": "libs/bootstrap/bootstrap-transition",
        "bootstrapalert": "libs/bootstrap/bootstrap-alert",
        "bootstrapmodal": "libs/bootstrap/bootstrap-modal",
        "bootstrapdropdown": "libs/bootstrap/bootstrap-dropdown",
        "bootstrapspy": "libs/bootstrap/bootstrap-scrollspy",
        "bootstraptab": "libs/bootstrap/bootstrap-tab",
        "bootstraptooltip": "libs/bootstrap/bootstrap-tooltip",
        "bootstrappopover": "libs/bootstrap/bootstrap-popover",
        "bootstrapbutton": "libs/bootstrap/bootstrap-button",
        "bootstrapcollapse": "libs/bootstrap/bootstrap-collapse",
        "bootstrapcarousel": "libs/bootstrap/bootstrap-carousel",
        "bootstraptypeahead": "libs/bootstrap/bootstrap-typeahead",
        "jqueryscrollto": "libs/jquery/jquery.scrollTo-1.4.3.1-min",
        "jquerycaroufredsel": "libs/jquery/jquery.carouFredSel-6.2.0-packed",
        "bootstrapflexscore": "libs/bootstrap/flexscore",
        "homeonload": "libs/homeOnload",
        "jquerytouchSwipe": "libs/jquery/jquery.touchSwipe.min",
        "jquerycarou": "libs/jquery/jquery.carouFredSel-6.2.0-packed",
        "jqueryui": "libs/jquery/jquery-ui-1.9.2.custom.min",
        "bootstrap": "libs/bootstrap/bootstrap.min",
        "bxslider": "libs/jquery/jquery.bxslider.min",
        "easypaginate": "libs/easypaginate",
        "ie-styles": "libs/ie-styles",
        "jquerycorner": "libs/jquery/jquery-corner",
        "utilityshared": "utility/shared",
        "buttons": "libs/buttons",
        "jqueryform": "libs/jquery/jquery.form",
        "multiselect": "libs/bootstrap/bootstrap-multiselect",
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
    'bxslider',
    'jquerytouchSwipe',
    'utilityshared',
    'multiselect',
    'jqueryform',
    'modernizr',
    'jquerycaroufredsel',
    'app',
    'routers/job'
],
        function(_, Backbone, $, jqueryui, jqueryscrollto, bootstrap, bxslider, jquerytouchSwipe, utilityshared, multiselect, jqueryform, Modernizr, jquerycaroufredsel, app, job) {
            app.init(job);
        });