// Filename: views/login/advisorsignup
define([
    'jquery',
    'underscore',
    'handlebars',
    'backbone',
    'text!../../../html/advisor/connectadvisor.html',    
], function($, _, Handlebars, Backbone, advisorListTemplate) {
    var connectAdvisorView = Backbone.View.extend({        
        el: $("#body"),
        render: function(obj) {            
            var source = $(advisorListTemplate).html();
            var template = Handlebars.compile(source);                        
            $('#profileContents').html(template(obj));
            popUpManageCredentials();
        },
        events: {
            "click .connectvia": "displayContactVia",
            "click #saveConnnectMode": "fnSaveConnectMode",
            "click .cancelAdvisorPopup": "closeProfileDialog",
        },
        displayContactVia: function(event) {
            $("#advTypediv").removeClass("error");
                $('.connectvia').each(function(){
                    if ( $(this).is(':checked')) {
                        selMode = $(this).val();
                    }
                });
            if (selMode == '2') {
                $('#user-phone').attr('disabled', false);
                $('#user-email').val('');
                $('#user-email').attr('disabled', 'disabled');              
            } else if(selMode == '1') {
                $('#user-email').val($('#user-email-hdn').val());
                $('#user-email').attr('disabled', false);
                $('#user-phone').attr('disabled', 'disabled');
                $('#user-phone').val('');
            } else {
                $('#user-email').val($('#user-email-hdn').val());
                $('#user-email').attr('disabled', false);
                $('#user-phone').attr('disabled', false);
                $('#user-phone').val('');
            }
        },
        fnSaveConnectMode: function(event) {
            var advisorId = event.currentTarget.id;
            advId = $('#' + advisorId).attr('adv-id');
            var selPermission;
            var mode;
            var email;
            var phone;
            topic = $('#topic').val();
            email = $('#user-email').val();
            phone = $('#user-phone').val();
            message = $('#message').val();
            $('input[name=permission]').each(function(){
                if ( $(this).is(':checked')) {
                    selPermission = $(this).val();
                }
            });
            
            var isEmailChecked = $('#ChkEmail').is(':checked');//for email radio button
            var isPhoneChecked = $('#ChkPhone').is(':checked');//for phone radio button
            var isBothChecked = $('#ChkBoth').is(':checked');//for both radio button
            $("#user-email").focus(function() {
                $('#emailConnbubble').addClass("hdn");
                $("#emailADDress").removeClass('error');
            });
             $("#user-phone").focus(function() {
                $('#phonebubble').addClass("hdn");
                $("#phone").removeClass('error');
            });
            if(isEmailChecked){
                if($('#user-email').val() == ""){
                    $('#emailConnerror').html('Enter your email address.');
                    $('#emailConnbubble').removeClass("hdn");
                    $("#emailADDress").addClass('error');
                    PositionErrorMessage("#user-email", "#emailConnbubble");
                    return false;
                }
            }
            else if(isPhoneChecked){
                if($('#user-phone').val() == ""){
                    $('#phoneerror').html('Enter phone number.');
                    $('#phonebubble').removeClass("hdn");
                    $("#phone").addClass('error');
                    PositionErrorMessage("#user-phone", "#phonebubble");
                    return false;
                }
            }
            else if(isBothChecked){
                if($('#user-email').val() == ""){
                   $('#emailConnerror').html('Enter your email address.');
                    $('#emailConnbubble').removeClass("hdn");
                    $("#emailADDress").addClass('error');
                    PositionErrorMessage("#user-email", "#emailConnbubble");
                    return false;
                }else if($('#user-phone').val() == "") {
                    $('#phoneerror').html('Enter phone number.');
                    $('#phonebubble').removeClass("hdn");
                    $("#phone").addClass('error');
                    PositionErrorMessage("#user-phone", "#phonebubble");
                    return false;
                }
            }
            
            $('#message').focus(function(){
                $("#descriptiontextdiv").removeClass("error");
            });
            if ($.trim(message).length <= 0) {          
                $('#descriptionError').html('Description can not be empty.');
                $('#descriptionBubble').removeClass("hdn");
                $("#descriptiontextdiv").addClass('error');
                PositionErrorMessage("#message", "#descriptionBubble");
                return false;
            }
            
            $('#terms').focus(function(){
                $("#termtextdiv").removeClass("error");
            });
            
            if (!$('#terms').is(':checked') ) {
                $('#termError').html('Please accept the terms and conditions.');
                $('#termBubble').removeClass("hdn");
                $("#termtextdiv").addClass('error');
                return false;
            }
            var formValues = {
                    id: advId,
                    permission: selPermission,
            };
            $.ajax({
                url: getUpdatedAdvisorPermission,
                type: 'GET',
                dataType: "json",
                data: formValues,
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                //check if already user has lead advisor
                success: function(data) {
                timeoutPeriod = defaultTimeoutPeriod;
                    if(data.status == "Warning"){
                        $('#warning').show();
                        $('.padd-all').html(data.message);//show the warning message
                        $("#indemnification").hide();
                        $('#actionBtn').hide();
                    }
                    else if(data.status == "OK"){//update the permission
                        $.ajax({
                            url: saveConnectModeUrl,
                            type: 'POST',
                            data: {
                                adv_id: advId,
                                topic: topic,
                                message : message,
                                permission: selPermission,
                                mode: mode,
                                email: email,
                                phone: phone,
                            },
                            dataType: "json",
                            success: function(data) {
                            timeoutPeriod = defaultTimeoutPeriod;
                                if (data.status == 'OK') {
                                    $('#connectbtn').hide();
                                    $('span#status').html("<strong>Status : </strong><span class='advisorPaleText'>You have requested a connection to advisor " + data.adv_name + ".</span>");
                                    var body = $("html, body");
                                    body.animate({scrollTop:0}, '500', 'swing');
                                    $('#form-contents').html(data.message);
                                    $(".profileBottomRow").hide();
                                    
                                } else {
                                    $('#msg1').html(data.message);
                                    $('#success-msg').hide();
                                    $('#error-msg').show(); 
                                }                   
                            }
                        });
                    }
                    
                }
            });
            
            //update the lead advisor by the user
            $('.changeLeadAdvisor'+advId).bind('click', function(){
                var formValues = {
                        id: advId,
                        permission: selPermission,
                };
                $.ajax({
                    url: getUpdateleadadvisor,
                    type: 'GET',
                    dataType: "json",
                    data: formValues,
                    cache: false,
                    beforeSend: function(request) {
                        request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                    },
                    success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                        if(data.status == "OK"){//update the permission
                            $.ajax({
                                url: saveConnectModeUrl,
                                type: 'POST',
                                data: {
                                    adv_id: advId,
                                    topic: topic,
                                    message : message,
                                    permission: selPermission,
                                    mode: mode,
                                    email: email,
                                    phone: phone,
                                },
                                
                                dataType: "json",
                                success: function(data) {
                                timeoutPeriod = defaultTimeoutPeriod;
                                    if (data.status == 'OK') {
                                        var body = $("html, body");
                                        body.animate({scrollTop:0}, '500', 'swing');
                                        $('#form-contents').scrollTop();
                                        $('#form-contents').html(data.message);
                                        $('#connectbtn').hide();
                                    $('span#status').html("<strong>Status : </strong><span class='advisorPaleText'>You have requested a connection to advisor" + data.adv_name+ ".</span>");
                                    } else {
                                        $('#msg1').html(data.message);
                                        $('#success-msg').hide();
                                        $('#error-msg').show(); 
                                    }                   
                                }
                            });
                        }
                    }
                });
            return false;
            });
            
            //cancel the warning message
            $("#cancelleadupdation").bind("click", function() {
                $("#indemnification").show();
                $('#actionBtn').show();
                $("#warning").hide();
                return false;
            });
        },
        closeProfileDialog: function(event) {
            event.preventDefault();
            removeLayover();           
        }
    });
    
    return new connectAdvisorView;
});
$(document).ready(function() {
    $("#ChkPhone, #ChkBoth").live("click",function(){
            $("#user-phone").mask("(999) 999-9999");
        });
    });