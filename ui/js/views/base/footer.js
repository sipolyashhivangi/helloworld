// Filename: views/base/footer
define([
    'handlebars',
    'text!../../../html/base/footer.html',
     ], function(Handlebars, footerTemplate)
     {
        var footerView = Backbone.View.extend({
            el: $("#mainFooter"),
            render: function(data){
                var source = $(footerTemplate).html();
                var template = Handlebars.compile(source); 
                $("#mainFooter").html(template());

            },
            events: {
                "click .blog": "fnShowBlog", 
            },
            fnShowBlog: function(event) {
                if(lcSummary != "" && window.location.pathname == '/blog')
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
            }            
        });
        return new footerView;
    });
