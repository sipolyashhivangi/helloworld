define([
    'handlebars',
    'text!../../../html/admin/cesearchresult.html',
    'text!../../../html/admin/ceusertemplate.html',
], function(Handlebars, cesearchTemplate, ceuserTemplate) {

    var searchresultView = Backbone.View.extend({
        el: $("#body"),
        render: function(jsonData) {
            var source = $(cesearchTemplate).html();
            var template = Handlebars.compile(source);
            $("#idArticleContent").html(template(jsonData));
        },
        events: {
            "click .readMoreSearch": "fnLoadPost",
        },
        fnLoadPost: function(event) {
            event.preventDefault();
            var parentid = event.currentTarget.id;
            var elementid = event.target.id;
            if (elementid != "" && elementid != parentid)
                return;

            var str = "RecommendedHead";
            var id = parentid.substring(0, parentid.length - str.length);

            var obj = {};
            obj.items = {};
            if (reportData != '') {
                for (var i = 0; i < reportData.length; i++) {
                    if (reportData[i]["id"] == id) {
                        obj = reportData[i];
                    }
                }
                $('#comparisonBox').show();
                $('#comparisonBox').css("height", 'auto');
                var offset = $("#navWrap").offset();
                $('#comparisonBox').attr('style', 'height:auto;display:block;top: ' + (offset.top + 50) + 'px');
                var source = $(ceuserTemplate).html();
                var template = Handlebars.compile(source);
                $('#comparisonBox').html(template(obj));
            }
        },
    });
    return new searchresultView;
});