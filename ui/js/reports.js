require.config({
    'paths': {
        "underscore": "libs/underscore-min",
        "backbone": "libs/backbone-min",
        "handlebars": "libs/handlebars",
        "modernizr":"libs/modernizr-latest",
        "modernizr-dev":"libs/modernizr-dev",
        "jquerytouchSwipe":"libs/jquery/jquery.touchSwipe.min",
        "jquerycarou":"libs/jquery/jquery.carouFredSel-6.2.0-packed",
        "jqueryui":"libs/jquery/jquery-ui-1.9.2.custom.min",
        "jqueryscrollto":"libs/jquery/jquery.scrollTo-1.4.3.1-min",
        "bootstrapflexscore":"libs/bootstrap/flexscore",
        "homeonload":"libs/homeOnload",
        "bootstrap":"libs/bootstrap/bootstrap.min",
        "bxslider":"libs/jquery/jquery.bxslider.min",
        "easypaginate":"libs/easypaginate",
        "jquerycorner":"libs/jquery/jquery-corner",
        "utilityshared":"utility/shared",
        "buttons": "libs/buttons"
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
            'deps': ['jquery','jqueryui','jqueryscrollto'],
            'exports': 'bootstrap'
        },
        bxslider: {
            'deps': ['jquery','jqueryui','jqueryscrollto'],
            'exports': 'Bxslider'
        }
    },
    urlArgs: "refresh="+version
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
    'modernizr',
    'app',
    'routers/reports'
    ],
    function(_, Backbone,$, jqueryui,jqueryscrollto,bootstrap,bxslider,jquerytouchSwipe,utilityshared,Modernizr,app, reports){
        app.init(reports);
    });
