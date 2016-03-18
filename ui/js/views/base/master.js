// Filename: views/base/master
define([
    'handlebars',
    'text!../../../html/base/master.html',
    'views/base/header', 
    'views/base/footer', 
    ], function(Handlebars, masterTemplate,header,footer){
        var masterView = Backbone.View.extend({
            el: $("#body"),
            render: function(data){
                var source = $(masterTemplate).html();
                var template = Handlebars.compile(source); 
                var obj = {};

                var text = "Improve your score with $500 from FlexScore.";
                if(typeof(giveawayText) != 'undefined') {  
                   text = giveawayText; 
                }
                obj.giveawayText = text;

                text = "";
                if(typeof(giveawayText2) != 'undefined') { 
                   text = giveawayText2; 
                }
                obj.giveawayText2 = text;

                $(this.el).html(template(obj));
                header.render(data);
                footer.render();
                init();
                initializeAfter();
                
                function showgiveaway() {
                    $("#giveawayBox").slideDown( "slow", function() {
                    // Animation complete.
                    });
                    clearInterval(giveawayId);
                    localStorage.showgiveaway = false;
                }
                if(typeof(showGiveaway) != 'undefined' && showGiveaway && typeof(localStorage.showgiveaway) == 'undefined') {
                    giveawayId = setInterval(showgiveaway, 5000);

                    var s = document.createElement('script');
                    var code = "(function(t,e,i,d){var o=t.getElementById(i),n=t.createElement(e);o.style.height=150;o.style.width=180;o.style.display='inline-block';n.id='ibb-widget',n.setAttribute('src',('https:'===t.location.protocol?'https://':'http://')+d),n.setAttribute('width','180'),n.setAttribute('height','150'),n.setAttribute('frameborder','0'),n.setAttribute('scrolling','no'),o.appendChild(n)})(document,'iframe','ibb-widget-root-916633885',\"banners.itunes.apple.com/banner.html?partnerId=&aId=&bt=catalog&t=catalog_black&id=916633885&c=us&l=en-US&w=180&h=150\");";
                
                    try {
                        s.appendChild(document.createTextNode(code));
                        document.body.appendChild(s);
                    } catch (e) {
                        s.text = code;
                        document.body.appendChild(s);
                    }
                }
            },
            events: {
            "click .giveawayLink": "ShowGiveaway",
            "click #closeGiveaway": "CloseGiveaway"
            },
            ShowGiveaway: function(event) {
                var showurl = "http://giveaway.flexscore.com";
                if(typeof(giveawayUrl) != 'undefined') 
                {
                    showurl = giveawayUrl   
                }
                window.open(showurl, '_blank'); 
            },
            CloseGiveaway: function(event) {
                $("#giveawayBox").slideUp( "slow", function() {
                    // Animation complete.
                });
            }
        });
        return new masterView;
    });