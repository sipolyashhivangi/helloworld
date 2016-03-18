define([
    'handlebars',
    'text!../../../html/user/createAccountStepOne.html'
    ], function(Handlebars,stepOneTemplate)
    {

        var stepOneView = Backbone.View.extend({
            el: $("#body"),
            render: function(obj, location){

                //FB conversion pixel  start
                /*
                var fb_param = {};
                fb_param.pixel_id = '6011427784785';
                fb_param.value = '0.00';
                fb_param.currency = 'USD';
                (function(){
                    var fpw = document.createElement('script');
                    fpw.async = true;
                    fpw.src = '//connect.facebook.net/en_US/fp.js';
                    var ref = document.getElementsByTagName('script')[0];
                    ref.parentNode.insertBefore(fpw, ref);
                })();
                //FB conversion pixel  start
*/
                //get the details from the getuseritem
                var source = $(stepOneTemplate).html();
                var template = Handlebars.compile(source);

                if(profileUserData.needsUpdate)
                {
                    $.ajax({
                        url:getUserDetails,
                        type:'GET',
                        dataType:"json",
                        cache: false,
                        beforeSend: function(request) {
                            request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                        },
                        success:function (data) {
                            timeoutPeriod = defaultTimeoutPeriod;
                            if (data.status == "OK"){
                                fnUpdateUserData(data);
                                if(typeof(userData.advisor) != 'undefined') {
                                    userData.user.impersonationMode = true;
                                    profileUserData.impersonationMode = true;
                                    if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                                        profileUserData.permission = true;
                                    }
                                }
                                var count = 0;
                                var questionIds = [];
                                for(var i = 0; i< profileUserData.securityresponse.length; i++) {
                                    if(questionIds.indexOf(profileUserData.securityresponse[i].question_id) == -1 && $.trim(profileUserData.securityresponse[i].response_text) != "") {
                                        count++
                                        questionIds[questionIds.length] = profileUserData.securityresponse[i].question_id;
                                        profileUserData.securityresponse[i].active = true;
                                    }
                                    else {
                                        profileUserData.securityresponse[i].active = false;
                                    }
                                }
                                profileUserData.configuredQuestions = count;
                                $(obj).html(template(profileUserData));
                                init();
                                if(location == "about")
                                {
                                    $(".aboutIconOff").addClass("aboutIconOn");
                                    $(".accOverlayTabOn").removeClass('accOverlayTabOn');
                                    $(".aboutIconOn").removeClass("aboutIconOff");
                                    $(".aboutIconOn").parents("li").addClass('accOverlayTabOn');
                                    $(".stepOneHeader").hide();
                                    $("#ProfileTracker").val('about');
                                    $(".nextProfilePopupBox").show();
                                }
                            }
                        }
                    });
                }
                else
                {
                    if(typeof(userData.advisor) != 'undefined') {
                        userData.user.impersonationMode = true;
                        profileUserData.impersonationMode = true;
                        if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                            profileUserData.permission = true;
                        }
                    }
                    var count = 0;
                    var questionIds = [];
                    for(var i = 0; i< profileUserData.securityresponse.length; i++) {
                        if(questionIds.indexOf(profileUserData.securityresponse[i].question_id) == -1 && $.trim(profileUserData.securityresponse[i].response_text) != "") {
                            count++
                            questionIds[questionIds.length] = profileUserData.securityresponse[i].question_id;
                            profileUserData.securityresponse[i].active = true;
                        }
                        else {
                            profileUserData.securityresponse[i].active = false;
                        }
                    }
                    profileUserData.configuredQuestions = count;
                    $(obj).html(template(profileUserData));
                    if(location == "about")
                    {
                        $(".aboutIconOff").addClass("aboutIconOn");
                        $(".accOverlayTabOn").removeClass('accOverlayTabOn');
                        $(".aboutIconOn").removeClass("aboutIconOff");
                        $(".aboutIconOn").parents("li").addClass('accOverlayTabOn');
                        $(".stepOneHeader").hide();
                        $("#ProfileTracker").val('about');
                        $(".nextProfilePopupBox").show();
                    }
                }
            },
            events: {
                "click #stepOneCompleteButton": "fnLoadStepTwo",
                "change .stepsinput": "fnChangeStepsForm",
                "click .maritalStatus": "fnChangeMaritalStatus",
                "focus .securityResponseSecure": "fnTextField",
                "blur .securityResponse": "fnPasswordField",
                "change .securityQuestion": "fnQuestionChanged"
            },
            fnTextField: function(event) {
                $(event.target).hide();
                $("#" + event.target.id.substr(0, event.target.id.indexOf("_secure"))).val('');
                $("#" + event.target.id.substr(0, event.target.id.indexOf("_secure")) + "_span").html('Not Active');
                $("#" + event.target.id.substr(0, event.target.id.indexOf("_secure")) + "_span").css('color','#ca2c36');
                $("#" + event.target.id.substr(0, event.target.id.indexOf("_secure"))).show();
                $("#" + event.target.id.substr(0, event.target.id.indexOf("_secure"))).focus();
                
            },
            fnQuestionChanged: function(event) {
                var index = event.target.id.substr(event.target.id.indexOf("question_id_") + 12,event.target.id.length);
                $("#response_text_" + index).val('');
                $("#response_text_" + index + "_secure").val('');               
            },
            fnPasswordField: function(event) {
                if($.trim($(event.target).val()) == "") {
                    $(event.target).val($("#" + event.target.id + "_secure").val());
                }
                var count = 0;
                var questionIds = [];
                var i = 0;
                $('[name^="response_text"]').each(function () {
                    if(questionIds.indexOf($("#question_id_" + i + " option:selected").val()) == -1 && $.trim(this.value) != "") {
                        $("#" + this.id + "_span").html('Active');
                        $("#" + this.id + "_span").css('color','#5fa439');
                        questionIds[questionIds.length] = $("#question_id_" + i).val();
                        count++;
                    }
                    else
                    {
                        $("#" + this.id + "_span").html('Not Active');
                        $("#" + this.id + "_span").css('color','#ca2c36');
                    }
                    i++;
                });
                $("#configuredQuestions").html(count);

                $("#" + event.target.id + "_secure").show();
                $("#" + event.target.id + "_secure").val($(event.target).val());
                $("#" + event.target.id + "_secure").blur();
                $(event.target).hide();
            },
            fnLoadStepTwo: function(event){
                event.preventDefault();
                //insert values to database
                var firstName = $('#FirstName').val();
                var lastName = $('#LastName').val();
                var ageMonth = $('#DOBMonth option:selected').val();
                var ageDay = $('#DOBDay option:selected').val();
                var ageYear = $('#DOBYear option:selected').val();
                var zipCode = $('#ZipCode').val();

                var maritalStatus = $('button.maritalStatus.active').text();
                if(maritalStatus == "DomesticPartner")
                    maritalStatus = "Domestic Union";
                var sAgeMonth = "";
                var sAgeDay = "";
                var sAgeYear = "";
                if(maritalStatus == "Domestic Union" || maritalStatus == "Married") {
                    var sAgeMonth = $('#SpouseDOBMonth option:selected').val();
                    var sAgeDay = $('#SpouseDOBDay option:selected').val();
                    var sAgeYear = $('#SpouseDOBYear option:selected').val();
                }
                var numChildren = $('#NumChildren option:selected').val();
                var retiredVal = $('input:radio[name=retired]:checked').val();
                var goalStatus = 1;
                if(retiredVal == 1) {
                    goalStatus = 0;
                }
                for(var i = 0; i < financialData.goals.length; i++) {
                    if(financialData.goals[i].goaltype == 'RETIREMENT') {
                        financialData.goals[i].goalstatus = goalStatus;
                    }
                }

                //add clhild dobs
                var childDOBs = "";
                for (i=1;i<= numChildren;i++){
                    if(i > 1) { childDOBs += ","; }
                    var cAgeMonth = $('#Child'+i+'DOBMonth option:selected').val();
                    var cAgeDay = $('#Child'+i+'DOBDay option:selected').val();
                    var cAgeYear = $('#Child'+i+'DOBYear option:selected').val();
                    childDOBs += cAgeYear+"-"+cAgeMonth+"-"+cAgeDay;
                }
                userData.firstname = firstName;
                userData.lastname = lastName;
                userData.retirementstatus = retiredVal;

                var loadStepTwo = true;
                if($(".aboutIconOn").length > 0)
                    loadStepTwo = false;

/*
                var question_id     = $('[name^="question_id"]').serializeArray();
                var response_id     = $('[name^="response_id"]').serializeArray();
                var response_text   = $('[name^="response_text"]').serializeArray();
*/
                var question_id     = $('[name^="question_id"]').map(function () { return $(this).val(); }).get();
                var response_id     = $('[name^="response_id"]').map(function () { return $(this).val(); }).get();
                var response_text   = $('[name^="response_text"]').map(function () { return $(this).val(); }).get();

                for(var i = 0; i< response_text.length; i++) {
                    if($.trim(response_text[i]) == "") {
                       response_text[i] = $("#response_text_" + i + "_secure").val();
                    }
                }
                var count = 0;
                var questionIds = [];
                var i = 0;
                $('[name^="response_text"]').each(function () {
                    if(questionIds.indexOf($("#question_id_" + i + " option:selected").val()) == -1 && $.trim(this.value) != "") {
                        $("#" + this.id + "_span").html('Active');
                        $("#" + this.id + "_span").css('color','#5fa439');
                        questionIds[questionIds.length] = $("#question_id_" + i).val();
                        count++;
                    }
                    else
                    {
                        $("#" + this.id + "_span").html('Not Active');
                        $("#" + this.id + "_span").css('color','#ca2c36');
                    }
                    i++;
                });
                $("#configuredQuestions").html(count);

                var formValues = {
                    firstname:      firstName,
                    lastname:       lastName,
                    age:            ageYear+"-"+ageMonth+"-"+ageDay,
                    zip:        zipCode,
                    spouseage:      sAgeYear+"-"+sAgeMonth+"-"+sAgeDay,
                    maritalstatus:  maritalStatus,
                    noofchildren:      numChildren,
                    retirementstatus:        retiredVal,
                    question_id:    question_id,
                    answer:  response_text,
                    childrensage:      childDOBs
                };

                $.ajax({
                    url:addUserInfo1URL,
                    type:'POST',
                    dataType:"json",
                    data: formValues,
                    success:function (data) {

                        timeoutPeriod = defaultTimeoutPeriod;
                        if (data.status == "OK"){
                            if(profileUserData.age != ageYear+"-"+ageMonth+"-"+ageDay)
                            {
                                financialData.accountsdownloading = true;
                            }
                            fnUpdateUserData(data);

                            profileUserData.firstname = data.userdata.firstname;
                            profileUserData.lastname = data.userdata.lastname;
                            $(".gnavName").html(CalculateHeaderText());
                            if(loadStepTwo)
                            {
                                require(
                                    [ 'views/user/createAccountStepThree'],
                                    function( accountThreeV){
                                        accountThreeV.render("#comparisonBox");
                                        popUpActionStep();
                                        init();
                                    }
                                );
                            }
                            else
                            {
                                for(var i = 0; i< profileUserData.securityresponse.length; i++) {
                                    $("#response_text_" + i + "_secure").val(profileUserData.securityresponse[i].response_text);
                                }
                            
                            }
                        }
                    }
                });
            },
            fnChangeMaritalStatus: function(event){
                event.preventDefault();
                $(".maritalStatus").removeClass("active");
                $(event.target).addClass("active");
                if($(event.target).hasClass("spouse"))
                    $("#spouseInfo").removeClass("hdn");
                else
                    $("#spouseInfo").addClass("hdn");
                if($("#profileBox").is(":visible"))
                    $("#stepOneCompleteButton").click();
            },
            fnChangeStepsForm: function(event){

                event.preventDefault();
                if($("#profileBox").is(":visible"))
                    $("#stepOneCompleteButton").click();
            }

        });
        return new stepOneView;
    });
