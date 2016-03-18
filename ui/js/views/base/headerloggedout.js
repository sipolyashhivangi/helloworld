// Filename: views/base/header
define([
    'handlebars',
    'text!../../../html/base/headerloggedout.html',
     ], function(Handlebars, headerTemplate){
        var headerView = Backbone.View.extend({
            el: $("#mainHeader"),
            render: function(data){
                var source = $(headerTemplate).html();
                var template = Handlebars.compile(source); 
                if($("#currentPage").val() == "index")
                    data = { "ishomepage" : 1 };
                $("#mainHeader").html(template(data));
                if(window.location.pathname.indexOf("success") != -1)
                {
                	$("#gnav_signin").hide();
                	$("#gnav_join").hide();
		        }
            },
            events: {
                "click .learningcenter": "fnShowLearningCenter", 
            },
            fnShowLearningCenter: function(event) {
                if(lcSummary != "" && window.location.pathname == '/learningcenter')
                {
                    event.preventDefault();
                    if(!$('.btn-navbar').hasClass('collapsed'))
                        $('.btn-navbar').click();
                        
                    require(
                        [ 'views/marketing/summary' ],
                        function( summaryV ){
                            summaryV.render(lcSummary);
                            init();
                        }
                    );
                }
            },
        });
        return new headerView;
    });