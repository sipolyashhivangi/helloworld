define([
    'handlebars',
    'text!../../../html/user/howItWorks.html'
    ], function(Handlebars,howItWorksTemplate)  
    {
        
        var howItWorksView = Backbone.View.extend({
            el: $("#body"),
            render: function(){
                //get the details from the getuseritem
                var source = $(howItWorksTemplate).html();
                var template = Handlebars.compile(source); 
                $("#comparisonBox").html(template());                          

            },
            events: {
                "click .createProfile": "fnLoadStepOne",
            },
            fnLoadStepOne: function(event){
                event.preventDefault();
                require(
                    [ 'views/user/createAccountStepOne'],
                    function( accountOneV){
                        accountOneV.render("#comparisonBox", "new");
                        popUpActionStep();
                        init();
                    }
                );
            },
        });    
        return new howItWorksView;
    });