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
                                ['views/profile/login', 'views/profile/signup', 'views/profile/advisor'],
                                function(loginV, signupV, advisorV) {
                                    loginV.render();
                                    signupV.render();
                                    advisorV.render(data);
                                    $(".cancelPopup").toggleClass("hdn");
                                    $(".cancelAdvisorPopup").toggleClass("hdn");
                                    init();
                                   
                                    var msgDesc = "";
                                    if (getQueryVariable('passwordrestflag').length > 0 && getQueryVariable('passwordrestflag')=='1') {
                                        msgDesc = 'Your password has been successfully changed. Sign in to continue.';
                                    }
                                    else if (getQueryVariable('passwordrestflag').length > 0 && getQueryVariable('passwordrestflag')=='2') {
                                        msgDesc = 'Your password has been successfully created. Sign in to continue.';
                                    }
                                    else if (getQueryVariable('deleteflag').length > 0) {
                                        msgDesc = 'Your account has been successfully deleted. We\'re sorry to see you go.';
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
                                    if (msgDesc != "" && getQueryVariable('type').length > 0 && getQueryVariable('type')=='advisor')
                                    {
                                        $("#signupadvisortab").click();
                                        $('#advisorsignininstructions').text(msgDesc);                                        
                                        $('#advisorsignininstructions').addClass('user-settings-success');
                                    }
                                    else if(msgDesc != "") 
                                    {
                                        $("#signintab").click();
                                        $('#signininstructions').text(msgDesc);
                                        $('#signininstructions').addClass('user-settings-success');
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