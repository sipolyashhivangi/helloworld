define([
    'handlebars',
    'backbone',
    'text!../../../html/account/delete.html',
], function(Handlebars, Backbone, loginTemplate) {

    var loginView = Backbone.View.extend({
        el: $("#body"),
        render: function() {
            var source = $(loginTemplate).html();
            var template = Handlebars.compile(source);
            $("#settingsDetails").html(template());
			timeoutPeriod = defaultTimeoutPeriod;
			
        },
        events: {
            "click #deleteAccountButton": "performDeleteAccount",
            "change #termscheck": "checkTerms",            
            "click #settingsDetails": "checkTerms"
        },
        
        checkTerms: function(event) {
			$('#deleteMessage').html("&nbsp;");
    	    $('#termscheckbubble').addClass("hdn");
   	        $("#termscheckdiv").removeClass('error');	
        },
        performDeleteAccount: function(event) {

            event.preventDefault();
            $('#deleteAccount').attr("disabled", true);

            if (!$('#termscheck').is(':checked'))
            {
                $('#termscheckbubble').removeClass("hdn");
                $("#termscheckdiv").addClass('error');
                PositionErrorMessage("#termscheck", "#termscheckbubble");
                $("#deleteAccount").removeAttr("disabled");
                return false;
            }

            var formValues = '';

            $.ajax({
                url: userDeleteAcctURL,
                type: 'GET',
                dataType: "json",
                data: formValues,
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
				timeoutPeriod = defaultTimeoutPeriod;
                    $('#deleteAccount').removeAttr("disabled");

                    if (data.status == "ERROR")
                    {
                        $('#deleteMessage').html("We could not delete your account at this time. Please try again later.");
                    }
                    else if(data.status == "OK") 
                    {
                    	if(typeof(userData.advisor) != 'undefined') {
	                        window.location.href = "./login?deleteflag=1&type=advisor";
                        }
                        else
                        {
	                        window.location.href = "./login?deleteflag=1";
                        }
                    }
                },
                error: function(data) {
                    $('#deleteAccount').removeAttr("disabled");
                    $('#deleteMessage').html("We could not delete your account at this time. Please try again later.");
                }
            });
            return false;
        }
    });
    return new loginView;
});