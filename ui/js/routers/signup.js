define([
    'jquery',
    'underscore',
    'backbone'
], function($, _, Backbone) {
    var Router = Backbone.Router.extend({
        initialize: function() {
            userData = null;
            sess = localStorage[serverSess];

            $.ajax({
                url: loginCheckUrl + "?sess=" + sess,
                type: 'GET',
                dataType: "json",
                success: function(data) {
                    
                    userData = data;
                    $('#body').show();
                    if (data.status == "OK" && typeof(data.advisor) != 'undefined') {
                        //go to dashboard for advisor
                        window.location = "./dashboard";
                    } else if(data.status == "OK") {
                        //go to myscore for user
                        window.location = "./myscore";
                    } else {
                        require(
                                ['views/profile/signup','views/profile/login','views/profile/advisor'],
                                function(signupV, loginV, advisorV) {
                                    signupV.render();
                                    loginV.render();
                                    advisorV.render(data);
                                    $(".cancelPopup").toggleClass("hdn");
                                    $(".cancelAdvisorPopup").toggleClass("hdn");
                                    init();
                                    if (getQueryVariable('passwordrestflag').length > 0) {
                                        var msgDesc = 'Your password has been successfully changed. Sign in to continue.';
                                        $("#signintab").click();
                                        $('#signininstructions').text(msgDesc);
                                    }
                                    else if (getQueryVariable('deleteflag').length > 0) {
                                        var msgDesc = 'Your account has been successfully deleted. We\'re sorry to see you go.';
                                        $("#signintab").click();
                                        $('#signininstructions').text(msgDesc);
                                    }
                                    else if (window.location.search != "")
                                    {
                                        var error = getQueryVariable('error');
                                        var msg = getQueryVariable('msg');
                                        $('#' + error + 'error').html(msg);
                                        $('#' + error + 'bubble').removeClass("hdn");
                                        $('#' + error + 'div').addClass('error');
                                        $("#showSignUp").click();
                                        PositionErrorMessage("#" + error, "#" + error + "bubble");
                                    }
                                }
                        );
                    }
                }
            });
        }
    });
    return Router;
});