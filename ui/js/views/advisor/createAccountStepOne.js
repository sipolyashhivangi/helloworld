define([
    'handlebars',
    'text!../../../html/advisor/createAccountStepOne.html',
    'text!../../../html/advisor/limited.html'
], function(Handlebars, stepOneTemplate)
{
    var onchangetriggered = false;
    var stepOneView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj, location) {

            //FB conversion pixel  start
            var fb_param = {};
            fb_param.pixel_id = '6011427784785';
            fb_param.value = '0.00';
            fb_param.currency = 'USD';
            (function() {
                var fpw = document.createElement('script');
                fpw.async = true;
                fpw.src = '//connect.facebook.net/en_US/fp.js';
                var ref = document.getElementsByTagName('script')[0];
                ref.parentNode.insertBefore(fpw, ref);
            })();
            //FB conversion pixel  start

            //get the details from the getuseritem
            var source = $(stepOneTemplate).html();
            var template = Handlebars.compile(source);

            $.ajax({
                url: getAdvisorprofiledetails, //Advisor profile details to update
                cache: false,
                type: 'GET',
                dataType: "json",
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "OK") {
                        fnUpdateUserData(data);
                        $(obj).html(template(profileUserData));
                        //Checked the how do you charge chcekbox.
                        if (profileUserData.typeofcharge != null) {
                            for (i = 0; i < profileUserData.typeofcharge.length; i++) {
                                var val = profileUserData.typeofcharge[i];
                                $('input:checkbox[value=' + val + ']').attr('checked', 'checked');
                            }
                        }
                        init();
                        $('.multiselect').multiselect({
                            buttonClass: 'ddPlainBtn',
                            includeSelectAllOption: true
                        });
                        $("#stateOption").parent('div').find('.adv_dropdown-menu').addClass('state_dropdown-menu');
                        $("#stateOption").parent('div').find('.ddPlainBtn').css('width', '440px');
                        $("#stateOption").parent('div').find('.ddPlainBtn').css('min-width', '440px');
                        $("#stateOption").parent('div').find('.adv_multiselect').css('width', '438px');
                        $("#designationOption").parent('div').find('.adv_dropdown-menu').addClass('credentials_dropdown-menu');
                        $("#designationOption").change(function() { //to show the other designation textbox
                            var found = false;
                            $("#designationOption option:selected").each(function() {
                            	if ($(this).val() == "Other") {
                            	    found = true;
                                }
                            });
							if (found) {
								$("#extradesig").show();
								$("#otherDesignations").show();
							}
							else {
								$("#otherDesignations").hide();
								$("#extradesig").hide();
								$("#extra").val('');
							}
                        })
                        $("#productAndServiceOption").change(function() { //to show the other product service textbox
                            var found = false;
                            $("#productAndServiceOption option:selected").each(function() {
                                if ($(this).val() == "Other") {
                                    found = true;
                                }
                            });
                            if(found) {
                                $("#extraprod").show();
                                $("#otherPNS").show();
                            }
                            else
                            {
                                $("#extraprod").hide();
                                $("#otherPNS").hide();
                                $("#extraprod").val('');
                            }
                        })
                        //to check the max length of the tell me about yourself.
                        maxLength = $("textarea#description").attr("maxlength");
                        $("textarea#description").after("<div id='textarealeng'><span id='remainingLengthTempId'>"
                                + maxLength + "</span> remaining</div>");

                        $("textarea#description").bind("keyup change", function() {
                            checkMaxLength(this.id, maxLength);
                        });
                        //function to check the max length.
                        function checkMaxLength(textareaID, maxLength) {
                            currentLengthInTextarea = $("#" + textareaID).val().length;
                            $(remainingLengthTempId).text(parseInt(maxLength) - parseInt(currentLengthInTextarea));
                            if (currentLengthInTextarea > (maxLength)) {
                                // Trim the field current length over the maxlength.
                                $("textarea#description").val($("textarea#description").val().slice(0, maxLength));
                                $(remainingLengthTempId).text(0);
                            }
                        }

                        if (location == "about")
                        {
                            $("#designationOption").change(function() { //to show the other designation textbox
                                $("#designationOption option:selected").each(function() {
                                    if ($(this).val() == "Other") {
                                        $("#extradesig").show();
                                        $("#otherDesignations").show();
                                    }
                                    else {
                                        $("#otherDesignations").hide();
                                        $("#extradesig").hide();
                                        $("#extra").val('');
                                    }
                                });
                            })
                            $("#productAndServiceOption").change(function() { //to show the other product service textbox
                                $("#productAndServiceOption option:selected").each(function() {
                                    if ($(this).val() == "Other") {
                                        $("#extraprod").show();
                                        $("#otherPNS").show();
                                    } else {
                                        $("#extraprod").hide();
                                        $("#otherPNS").hide();
                                        $("#extraprod").val('');
                                    }
                                });
                            })
                            //Checked the how do you charge chcekbox.
                            if (data.userdata.typeofcharge != null) {
                                for (i = 0; i < data.userdata.typeofcharge.length; i++) {
                                    var val = data.userdata.typeofcharge[i];
                                    $('input:checkbox[value=' + val + ']').attr('checked', 'checked');
                                }
                            }
                            //show the designation other label and textbox if user entered other designation during sign-up
                            var desig = data.userdata.designation[data.userdata.designation.length - 1];
                            if (desig.others == "")
                                $("#extradesig").hide();
                            else {
                                $("#otherDesignations").show();
                                $("#extradesig").show();
                                $("#extradesig").val(desig.others);
                            }
                            //show the Product Service other label and textbox if user entered other Product Service during sign-up
                            var product = data.userdata.productservice[data.userdata.productservice.length - 1];
                            if (product.others == "")
                                $("#extraprod").hide();
                            else {
                                $("#otherPNS").show();
                                $("#extraprod").show();
                                $("#extraprod").val(product.others);
                            }
                            //show the average balance value comma seperate
                            var avgBal = commaSeparateNumber(data.userdata.avgacntbalanceperclnt);
                            $("#avg_bal").val(avgBal);

                            //show the minimum assest value comma seperate
                            var minAssist = commaSeparateNumber(data.userdata.minasstsforpersclient);
                            $("#min_assist").val(minAssist);

                            $(".aboutIconOff").addClass("aboutIconOn");
                            $(".accOverlayTabOn").removeClass('accOverlayTabOn');
                            $(".aboutIconOn").removeClass("aboutIconOff");
                            $(".aboutIconOn").parents("li").addClass('accOverlayTabOn');
                            $(".profileIconOn").addClass("profileIconOff");
                            $(".profileIconOff").removeClass("profileIconOn");
                            $(".stepOneHeader").hide();
                            $("#ProfileTracker").val('about');
                            $("#editProfileButton").hide();
                            $("#viewProfileButton").show();
                            //$(".nextProfilePopupBox").show();
                        }

                    }
                },
                complete: function() {

                }
            });
        },
        events: {
          //  "click #stepOnessCompleteButton": "fnLoadStepTwo",//Advisor Step two pop up//shifted to shared.js
            "click .takedashboard": "fnloadadv",// back to dashboard from consumer module.
        },
        fnloadadv: function(event) {
            event.preventDefault();
            removeLayover();
        }
    });
    return new stepOneView;
});

 function SubmitFirstForm() {
            var avg_bal = parseFloat($('#avg_bal').val().replace(',', '')) || 0;//Average Balance
            var min_assist = parseFloat($('#min_assist').val().replace(',', '')) || 0;//minimum Assest
            var firstname = $('#firstname').val();//Firstname
            var lastname = $('#lastname').val();//Lastname
            var advisortype = $('input[name="advisortype"]:checked').val();//AdvisorType
            var firmname = $('#firmname').val();//Firmname
            var state = [];//States
            var stateselected = $('#stateOption').find(":selected");
            var checkedstateCount = stateselected.length;
            var i = 0;
            while (i < checkedstateCount) {
                state[i] = stateselected[i].value;
                i++;
            }
            state = state.join(',');

            var description = $('#description').val();//description
            var chargetype = [];//chargetype
            $("input[name='chargetype[]']").each(function() {
                if ($(this).is(":checked")) {
                    chargetype.push($(this).val());
                }
            });
            var designation = [];//designation
            var designationselected = $('#designationOption').find(":selected");
            var checkedDesignationsCount = designationselected.length;
            var i = 0;
            while (i < checkedDesignationsCount) {
                designation[i] = designationselected[i].value;
                i++;
            }
            designation = designation.join(',');

            var productservice = [];//productNservice
            var productServiceselected = $('#productAndServiceOption').find(":selected");
            var checkedProductServiceselectedCount = productServiceselected.length;
            var i = 0;
            while (i < checkedProductServiceselectedCount) {
                productservice[i] = productServiceselected[i].value;
                i++;
            }
            productservice = productservice.join(',');

            var individualCrd = $('#crd').val();//IndividualCrd
            var extradesigvalue = $('#extradesig').val();//Other Designation
            var extraprod = $('#extraprod').val();//Other productNservice
            var formValues = {
                firstname: firstname,
                lastname: lastname,
                firmname: firmname,
                advisortype: advisortype,
                state: state,
                designation: designation,
                productservice: productservice,
                description: description,
                chargetype: chargetype,
                min_assist: min_assist,
                avg_bal: avg_bal,
                individualCrd: individualCrd,
                extradesigvalue: extradesigvalue,
                extraprod: extraprod,
            };
            $.ajax({
                    url:advisorStepTwoDetails,
                    type:'POST',
                    dataType:"json",
                    data: formValues,
                    success:function (data) {
                       timeoutPeriod = defaultTimeoutPeriod;
                       if (data.status == "OK")
                        {
                            $("#headNotifyTags").html(data.notificationCount);
                            $("#menuNotifyTags").html(data.notificationCount);
                            removeLayover();
                            if (data.showSubscription == "true") {
                              require(['views/account/subscription'],
                                function(subscriptionV) {
                                    subscriptionV.render();
                                    popUpActionStep();
                                }
                            );
                           }
                        }
                    }                    
                });
        }


