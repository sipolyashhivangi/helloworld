// Filename: views/release/summary
define([
    'handlebars',
    'text!../../../html/release/summary.html',
], function(Handlebars, summaryTemplate) {

    var summaryView = Backbone.View.extend({
        el: $("#body"),
        render: function(jsonData) {
            var source = $(summaryTemplate).html();
            var template = Handlebars.compile(source);
            $("#mainBody").html(template(jsonData));
            lcSummary = jsonData;
        },
        events: {
            "click #presslink": "fnShowPress",
            "click #searchBtnHome": "fnTopicSearch",
            "click .readMoreSummary": "fnLoadPost",
            "keypress #inputIcon": "fnCheckSearch",
        },
        fnCheckSearch: function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                event.preventDefault();
                $("#searchBtnHome").click();
            }
        },
        fnLoadPost: function(event) {
            event.preventDefault();
            var parentid = event.currentTarget.id;
            var elementid = event.target.id;
            if (elementid != "" && elementid != parentid)
                return;

            var str = "RecommendedLink";
            var id = parentid.substring(0, parentid.length - str.length);  //Why we are doing this?
            window.location = "./release?type=post&id=" + id;
        },
        fnLoadGlossary: function(event) {
            event.preventDefault();
            require(
                    ['views/release/gloss'],
                    function(glossView) {
                        var id = event.target.id;
                        var formValues = {
                            postLetter: id,
                            id: id,
                            type: 'glossary'
                        };

                        $.ajax({
                            url: learningCenterGlossaryURL,
                            type: 'POST',
                            dataType: "json",
                            data: formValues,
                            success: function(jsonData) {
								timeoutPeriod = defaultTimeoutPeriod;
                                glossView.render(jsonData);
                                $(".articleNavOn").addClass("hdn");
                                $(".articleNavOff").removeClass("hdn");
                                if (id == "#")
                                    id = "num";
                                $("#" + id + "GlossaryInactive").removeClass("hdn");
                                $("#" + id + "GlossaryActive").addClass("hdn");
                                init();
                                $.scrollTo($('#body'), 200);
                            }
                        });
                    }
            );
        },
        fnLoadCategory: function(event) {
            event.preventDefault();
            require(
                    ['views/release/searchresult', 'views/release/post'],
                    function(searchresultView, postView) {
                        var id = event.target.id;
                        var str = "CategoryLink";
                        id = id.substring(0, id.length - str.length);
                        var formValues = {
                            catid: id,
                            id: id,
                            type: 'category'
                        };

                        $.ajax({
                            url: learningCenterSearchByCatURL,
                            type: 'POST',
                            dataType: "json",
                            data: formValues,
                            success: function(jsonData) {
								timeoutPeriod = defaultTimeoutPeriod;
                                postView.render(jsonData);
                                searchresultView.render(jsonData);
                                $(".articleNavOn").addClass("hdn");
                                $(".articleNavOff").removeClass("hdn");
                                $("#" + id + "CategoryListActive").removeClass("hdn");
                                $("#" + id + "CategoryListInActive").addClass("hdn");
                                init();
                                $.scrollTo($('#body'), 200);
                            }
                        });
                    }
            );
        },
        fnTopicSearch: function(event) {
            event.preventDefault();
            var searchValue = $('#inputIcon').val();
            if (searchValue != "")
            {
                window.location = "./release?type=search&id=" + searchValue;
            }
        },
        fnShowPress: function() {
            $.ajax({
                url: learningCenterPressURL,
                type: 'POST',
                dataType: "json",
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                    learnpressView.render(jsonData);
                }
            });
        }
    });
    return new summaryView;
});