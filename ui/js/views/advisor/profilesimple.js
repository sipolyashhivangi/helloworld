define([
    'handlebars',
    'backbone',
    'text!../../../html/advisor/profilesimple.html',
], function(Handlebars, Backbone, profileTemplate) {
    var profileView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj, element) {

            var source = $(profileTemplate).html();
            var template = Handlebars.compile(source);
            if (typeof (element) != 'undefined') {
                $(element).html(template(obj));
            }
            else
            {
                $("#mainBody").html(template(obj));
            }
            $('#search-contents').hide();
            $('#profile-contents').show();
            $('#back-to-search').show();
            $('.filter').prop('disabled', true);
            $('#searchbox').prop('disabled', true);
            $('#reset').prop('disabled', true);
            var minAssist = commaSeparateNumber(obj.minasstsforpersclient, 0);
            if (minAssist == null || minAssist === '') {
                minAssist = 0;
            }
            $('#minimumAssest').text(minAssist);
            if (typeof (element) == 'undefined') {
                $('html,body').animate({scrollTop: 0}, 'fast', function() {
                });
            }
            
            var connect = getQueryVariable('connect');
            if(connect != "" && typeof (userData.advisor) == 'undefined') {
                $(".connectadvbtn").click();
                $('.backList').hide();
                $('#back-link').addClass('hdn');
            }
            if(typeof (userData.advisor) != 'undefined'){
                $('.backList').css('margin-left','0px');
                $('#back-link').addClass('hdn');
            }
        },
        events: {
            "click #editAdvisorPhoto": "editAdvisorPhoto",
            "click .shareProfile": "shareProfile",
            "click .connectadvbtn": "displayAdvisorProfile"
        },
        shareProfile: function(event) {
            //event.preventDefault();
            var advisor_id = $('#advisorClient').val();
            var emails = $('#invite_emails').val();
            if (emails.indexOf(',') > -1) {
                var emails = $('#invite_emails').val().split(",");
                for (var i = 0; i < emails.length; i++) {
                    if (!validateEmail(emails[i].trim())) {
                        var chk = 1;
                    } else {
                        var chk = 0;
                    }
                }
            } else {
                if (!validateEmail(emails)) {
                    var chk = 1;
                } else {
                    var chk = 0;
                }
            }
            if (chk == 1)
            {
                $('#clienterror').html('Enter a valid email address.');
                $('#clientbubble').removeClass("hdn");
                $("#clientdiv").addClass('error');
                PositionErrorMessage("#invite_emails", "#clientbubble");
                return false;
            }
            $('#invite_emails').click(function() {
                $("#clientdiv").removeClass("error");
                $(".shareProfile").removeClass("error");
            });
            //$('#inviteEmails').show();

            var formValues = {
                emails: emails,
                //password: password
            };
            var url = sendinvitation;
            $.ajax({
                url: url,
                cache: false,
                type: 'POST',
                dataType: "json",
                data: formValues,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                    $('#clientdiv').hide();
                    $('#msg').show();
                },
                error: function(data) {
                    $('#clienterror').html('We cannot process your request.');
                    $('#clientbubble').removeClass("hdn");
                    $("#clientdiv").addClass('error');
                    PositionErrorMessage("#invite_emails", "#clientbubble");
                }
            });

        },
        editAdvisorPhoto: function(event) {
            event.preventDefault();
            removeLayover();
            require(
                    ['views/account/account', 'views/account/settings', 'views/account/photo'],
                    function(accountV, settingsV, photo) {
                        accountV.render(userData);
                        settingsV.render(userData);
                        photo.render(userData);
                        init();
                    }
            );

        },
        displayAdvisorProfile: function(event) {
            var advisor = event.currentTarget.id;
            adv_hash = $('#' + advisor).attr('adv_hash');
            $.ajax({
                url: advisorProfileUrl,
                type: 'POST',
                data: {adv_hash: adv_hash},
                dataType: "json",
                success: function(data) {
                timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "OK") {
                        require(['views/advisor/connectadvisor'],
                            function(profileV) {
                                profileV.render(data.userdata);
                                $('#user-email').val(data.userEmail);   
                                $(".advper").text("View Only");                             
                                $('#user-email-hdn').val(data.userEmail);                               
                                $('#user-email-span').html(data.userEmail);                               
                            });
                    }
                }
            });
        }
    });
    return new profileView;
});