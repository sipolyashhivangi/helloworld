define([
    'handlebars',
    'text!../../../html/user/createAccountStepThree.html',
    ], function(Handlebars, stepThreeTemplate)
    {

        var stepTwoView = Backbone.View.extend({
            el: $("#body"),
            render: function(obj){
                //get the details from the getuseritem
                var source = $(stepThreeTemplate).html();
                var template = Handlebars.compile(source);
                $(obj).html(template(profileUserData));
                               
                 $.getJSON(baseUrl + "/service/api/getscore?refresh="+new Date().valueOf(), function(estscoreData) {
                    if (estscoreData.status == "OK") {
                        $(".floatedScoreEstImage").attr("src", "./ui/images/horseshoes/variations/myscore/" + estscoreData.score.image + ".png","style","margin-left: 33px; margin-top: 3px");
                        $(".floatedScoreEstScore").html(estscoreData.score.totalscore);
                        var value = estscoreData.score.point38;
                        value = Math.round(value * 2);
                        var imageId = Math.round(value / 5);
                        imageId = (imageId > 0) ? imageId : 0;
                        imageId = (imageId < 20) ? imageId : 20;
                        $(".floatedScoreProfileComplete").html(value + '<span style="font-size: .6em;display:inline;">%');
                        $(".floatedProfileCompleteImage").attr("src", "./ui/images/horseshoes/variations/profile/ProfileHorseShoe" + imageId + ".png","style","margin-left: 25px; margin-top: 8px");
                    }
                 });
            },
            events: {
                "click #stepTwoBackButton": "fnLoadStepTwo"
            },
            fnLoadStepTwo: function(event){
                event.preventDefault();
                require(
                    [ 'views/user/createAccountStepTwo'],
                    function( accountOneV){
                        accountOneV.render("#comparisonBox");
                        init();
                    });
            },
        });
        return new stepTwoView;
    });