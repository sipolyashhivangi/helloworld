define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/createnewclient.html',
], function(Handlebars, Backbone, createnewclientTemplate) {

    var sort_order = 'ASC';
    var sort_by = 'status';
    var current_page = 1;
    var record_per_page = 25;

    var createnewView = Backbone.View.extend({
        el: $("#body"),
        render: function(roleid, sortorder, sortby, currentpage, recordperpage) {
            sort_order = sortorder;
            sort_by = sortby;
            current_page = currentpage;
            record_per_page = recordperpage;
            var source = $(createnewclientTemplate).html();
            var template = Handlebars.compile(source);
            $("#createnewclientContents").html(template(roleid));
        },
        events: {
            "click #saveNewclient": "fnSaveNewClient",
            "click .cancelNewProfilePopupBox": "fnCloseNewClient",
            "click .clientMyscoreNew": "fnviewFinancesNew", // To view client finances
            "focus #advcreateClient, #advcreateClient2": "removeErrorMsgs"
        },                        
        fnviewFinancesNew: function(event) {//Client View Finances
            event.preventDefault();
            var email = $('#advcreateClient').val();
            var formValues = {
                email: email
            };

            $.ajax({
                url: getviewFinances,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    window.location.replace(baseUrl + "/myscore");
                }
            });
        },                
        fnCloseNewClient: function(event) {//Client View Finances
            event.preventDefault();
            if(!$("#hideonSuccess").is(':visible')) {
                getClientList(sort_order, sort_by, current_page, record_per_page);
            }
            removeLayover();
        },       
        fnSaveNewClient: function(event)
        {
            event.preventDefault();
            var advisor_id=$('#advisorClient').val();
            var email = $('#advcreateClient').val();
            var emailRetype = $('#advcreateClient2').val();
            if (!validateEmail(email))
            {
                $('#clienterror').html('Enter a valid email address.');
                $('#clientbubble').removeClass("hdn");
                $("#clientdiv").addClass('error');
                PositionErrorMessage("#advcreateClient", "#clientbubble");
                return false;
            }
            if (!validateEmail(emailRetype))
            {
                $('#clienterror2').html('Enter a valid email address.');
                $('#clientbubble2').removeClass("hdn");
                $("#clientdiv2").addClass('error');
                PositionErrorMessage("#advcreateClient2", "#clientbubble2");
                return false;
            }
            if (email != emailRetype) {
                $('#clienterror').html('Email addresses must match.');
                $('#clientbubble').removeClass("hdn");
                $("#clientdiv").addClass('error');
                PositionErrorMessage("#advcreateClient", "#clientbubble");
                return false;
            }
            var formValues = {
                id: advisor_id,
                email: email
            };
            var url = createNewClientSignup;
                
            $.ajax({
               url: url,
               cache: false,
                type: 'POST',
                dataType: "json",
                data: formValues,
                beforeSend: function (request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                timeoutPeriod = defaultTimeoutPeriod;
                if (data.status == "ERROR") {
                        if (data.type == 'email'){
                            $('#clienterror').html(data.message);
                            $('#clientbubble').removeClass("hdn");
                            $("#clientdiv").addClass('error');
                            PositionErrorMessage("#advcreateClient", "#clientbubble");
                        } 
                    } else {
                        if (typeof(sendMixpanel) != 'undefined' && sendMixpanel){ 
                            mixpanel.identify(data.uniquehash);
                            mixpanel.people.set_once({
                                'First Login Date': new Date(),
                            });
                            mixpanel.track('New User', {
                                'new_user': data.uniquehash,
                                'Created By': 'advisor'
                            }, function() { 
                                localStorage[serverSess] = data.sess;
                                $('#hideonSuccess').addClass('hdn');
                                $('#msgOnSuccess').removeClass('hdn');
                                $('#CreateNewClient').addClass('hdn');
                                $('#successNewClient').removeClass('hdn');
                                $('#newClientEmail').html(data.email);
                            });
                        }
                        else
                        {
                            localStorage[serverSess] = data.sess;
                            $('#hideonSuccess').addClass('hdn');
                            $('#msgOnSuccess').removeClass('hdn');
                            $('#CreateNewClient').addClass('hdn');
                            $( "#successNewClient" ).removeClass("hdn");
                            $( "#newClientEmail" ).html(data.email);
                        }
                    }
                },
                error: function(data) {
                $('#clienterror').html('We cannot process your request.');
                $('#clientbubble').removeClass("hdn");
                $("#clientdiv").addClass('error');
                PositionErrorMessage("#advcreateClient", "#clientbubble");
                }
            });
            return false;
        },
        removeErrorMsgs: function(event) {
            event.preventDefault();
            $("#clientdiv").removeClass("error");
            $("#clientdiv2").removeClass("error");
        }
        
    });
    return new createnewView;
});