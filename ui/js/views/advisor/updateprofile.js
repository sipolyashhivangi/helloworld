define([
    'handlebars',
    'backbone',
    'text!../../../html/advisor/updateprofile.html',
], function(Handlebars, Backbone, profileTemplate) {

    var profileView = Backbone.View.extend({
        el: $("#profileContents"),
        render: function(obj) {

            var source = $(profileTemplate).html();
            var template = Handlebars.compile(source);
            timeoutPeriod = defaultTimeoutPeriod;
            $("#profileContents").html(template(obj));
        },
        events: {
            "click .cancelAdvisorProfilePopUp": "closeProfileDialog",
            "click #updateProfile": "saveAdvisorProfile",
            "change .stepsinput": "fnSaveChangedData",
            "click .tabProfile": "loadProfileData",
            "click .tabAbout": "loadEditProfileData",
            "click .nextProfilePopupBox": "loadProfileData",
            "click .prevProfilePopupBox": "loadEditProfileData"
        },
        loadEditProfileData: function(event) {
            event.preventDefault();
            require(
                    ['views/advisor/createAccountStepOne'],
                    function(stepOneV) {
                        stepOneV.render("#profileDetails", "about");
                        popUpProfile();
                        init();
                    }
            );
        },
        loadProfileData: function(event) {
            event.preventDefault();
            $.ajax({
                url: advisorViewProfileUrl,
                type: 'GET',
                dataType: "json",
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "OK") {
                        require(['views/advisor/profilesimple'],
                                function(profileV) {
                                    data.userdata.embeddablelink = baseUrl + "/advisorprofile?connect=true&view=" + data.userdata.advhash;
                                    profileV.render(data.userdata, "#profileDetails");
                                    $('#connectbtn').hide();
                                    $('#releasebtn').hide();
                                    $('#assignbtn').hide();
                                    $('span#status').hide();
                                    $("#editProfilePhoto").show();
                                    $("#profileDetails .line2").hide();
                                    $("#profileDetails .lsPillBtnGreen").hide();
                                    $(".aboutIconOn").addClass("aboutIconOff");
                                    $(".aboutIconOff").removeClass("aboutIconOn");
                                    $(".profileIconOff").addClass("profileIconOn");
                                    $(".profileIconOn").removeClass("profileIconOff");
                                    $(".accOverlayTabOn").removeClass('accOverlayTabOn');
                                    $(".profileIconOn").parents("li").addClass('accOverlayTabOn');                                  
                                    $("#editProfileButton").show();
                                    $("#viewProfileButton").hide();
                                });
                    }
                }
            });
        },
        saveAdvisorProfile: function(event) {
            event.preventDefault();
            var avg_bal = parseFloat($('#avg_bal').val().replace(/,/g, '')) || 0;//Average Balance
                var min_assist = parseFloat($('#min_assist').val().replace(/,/g, '')) || 0;//minimum Assest
                var firstname = $('#firstname').val();//Firstname
                var lastname = $('#lastname').val();//Lastname
                var advisortype = $('input[name="advisortype"]:checked').val();//AdvisorType
                var firmname = $('#firmname').val();//Firmname
                var state = [];//States
                var stateselected = $('#stateOption').find(":selected");
                var checkedstateCount = stateselected.length;
                var i = 0;
                while(i < checkedstateCount) {
                    state[i] = stateselected[i].value;
                    i++;
                }
                state = state.join(',');
          
                var description = $('#description').val();//description
                var chargetype = [];//chargetype
                $("input[name='chargetype[]']").each( function () {
                    if($(this).is(":checked")){
                       chargetype.push($(this).val());
                    }
                });
                var designation = [];//designation
                var designationselected = $('#designationOption').find(":selected");
                var checkedDesignationsCount = designationselected.length;
                var i = 0;
                while(i < checkedDesignationsCount) {
                    designation[i] = designationselected[i].value;
                    i++;
                }
                designation = designation.join(',');

                var productservice = [];//productNservice
                var productServiceselected = $('#productAndServiceOption').find(":selected");
                var checkedProductServiceselectedCount = productServiceselected.length;
                var i = 0;
                while(i < checkedProductServiceselectedCount) {
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
                    advisortype:advisortype,
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
                url: updateAdvisorProfile,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == 'OK') {
                        profileUserData.firstname = firstname;
                        profileUserData.lastname = lastname;
                        $(".gnavName").html(CalculateHeaderText());
                        $("#headNotifyTags").html(data.notificationCount);
                        $("#menuNotifyTags").html(data.notificationCount);
                    }
                    //removeLayover();
                }
            });
            return false;
        },
        fnSaveChangedData: function(event){
            event.preventDefault();
            if($("#profileBox").is(":visible"))
                $("#updateProfile").click();
            },
        initialize: function() {
        },
        // use this for close overlay after click close(x) link.
        closeProfileDialog: function(event) {
            event.preventDefault();
            removeLayover();
            $("#profileBox").hide();
        },
    });
    return new profileView;
});