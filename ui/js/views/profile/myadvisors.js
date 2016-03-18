define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/myadvisors.html',
], function(Handlebars, Backbone, profileTemplate) {

    var myadvisorsView = Backbone.View.extend({
        el: $("#myAdvisorContents"),
        render: function(obj) {

            var source = $(profileTemplate).html();
            var template = Handlebars.compile(source);
            if (typeof (obj) != 'undefined' && obj.length > 0) {
                for(var i = 0; i< obj.length; i++) {
                    obj[i]["currentindex"] = i+1;
                }
            }
            $("#myAdvisorContents").html(template(obj));
            if (typeof (obj) != 'undefined' && obj.length > 0) {
                $("#advisorBox" + obj[obj.length - 1].advisor_id).addClass("last");
            }
        },
        events: {
            "click .cancelAdvisorPopup": "closeProfileDialog", //Cancel the my advisor pop up
            "click .deleteadvisorbyuser": "fnDeleteAdvisor", //show the advisor delete confirmation box on cross click.
            "click .toshowpermission": "fnShowPermissions", //show all the permission
            "change .rouser": "fnUpdatePermission", //update the permission and lead advisor
        },
        //remove the my advisor pop up
        closeProfileDialog: function(event) {
            event.preventDefault();
            removeLayover();
            $("#myAdvisorBox").hide();
        },
        //delete the advisor
        fnDeleteAdvisor: function(event) {
            event.preventDefault();
            var advisorId = event.target.attributes.getNamedItem('deleteAdv').nodeValue;
            var userid = event.target.attributes.getNamedItem('userId').nodeValue;
            $(".allRound" + advisorId).hide();
            $(".deleteadvisorcross" + advisorId).hide();
            $("#showConfirmationBox" + advisorId).show();
            $('#warning' + advisorId).hide();

            //cancel(no) the advisor delete
            $(".canceladvdelete" + advisorId).bind("click", function() {
                $(".allRound" + advisorId).show();
                $(".deleteadvisorcross" + advisorId).show();
                $("#showConfirmationBox" + advisorId).hide();
                $('#warning' + advisorId).hide();
                return false;
            });
            //yes,delete the advisor
            $(".deleteusradvisor" + advisorId).bind("click", function() {
                var formValues = {
                    id: advisorId
                };
                $.ajax({
                    url: getDeleteuseradvisor,
                    type: 'GET',
                    dataType: "json",
                    data: formValues,
                    cache: false,
                    beforeSend: function(request) {
                        request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                    },
                    success: function(data) {
                        timeoutPeriod = defaultTimeoutPeriod;
                        $("#advisorBox" + data.deletedId.id).hide();
                        require('views/base/header').fnMyadvisors(event);
                        $('.deleteadvmsg').show();
                        $('.deleteadvmsg').html(data.message);
                    }
                });
                return false;
            });
        },
        fnShowPermissions: function(event) { // Toggle advisor permissions
            event.preventDefault();
            $("input[type=radio]").click(function() {
                var $this = $(this);
                $this.siblings("input[type=radio]").prop("checked", false);
            });
            var advisorId = event.target.attributes.getNamedItem('advisor').nodeValue;
            var collapseqObj = $("#collapse" + advisorId);
            if (collapseqObj.is(":visible")) {
                $("#collapse" + advisorId).css("display", "none");
            } else {
                $("#collapse" + advisorId).css("display", "block");
            }
        },
        //update the advisor permission by user
        fnUpdatePermission: function(event) {
            var advisorId = event.target.attributes.getNamedItem('advisorId').nodeValue;
            var updatepermission = $('#advpermission' + advisorId + ' input[type=radio]:checked').val();

            if (updatepermission == "RO") {
                $(".advper" + advisorId).text("View Only");
            } else if (updatepermission == "RW") {
                $(".advper" + advisorId).text("View + Edit");
            } else {
                $(".advper" + advisorId).text("None");
            }
            var formValues = {
                id: advisorId,
                permission: updatepermission,
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
                    if (data.status == "Warning") {
                        $('#warning' + advisorId).show();
                        $("#showConfirmationBox" + advisorId).hide();
                        $('.padd-all' + advisorId).html(data.message);//show the warning message
                        $(".allRound" + advisorId).hide();
                        $('.updationMsg' + advisorId).hide();
                        $('#cancelleadupdation' + advisorId).attr('oldpermission', data.permission);//show the warning message
                    }
                    else if (data.status == "OK") {//update the permission
                        $('.updationMsg' + advisorId).show();
                        $('.updationMsg' + advisorId).html(data.message);//show the success update message.
                    }
                },
                error: function(data) {

                },
                complete: function(data) {

                },
            });

            //update the lead advisor by the user
            $('.leadAdvisor' + advisorId).bind('click', function() {
                var updatepermission = $('#advpermission' + advisorId + ' input[type=radio]:checked').val();
                var formValues = {
                    id: advisorId,
                    permission: updatepermission,
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
                        //to show message if permission updated successfully.
                        $('.updationMsg' + advisorId).show();
                        $('.updationMsg' + advisorId).html(data.message);//message.
                        $(".allRound" + advisorId).show();//show the set advisor permission div
                        $('#warning' + advisorId).hide();//hide the warning message
                        $('#showConfirmationBox' + advisorId).hide();//hide the warning message
                        //to call header.js and MyAdvisor function event here.
                        require('views/base/header').fnMyadvisors(event);
                    }
                });
                return false;
            });
            //cancel the warning message
            $("#cancelleadupdation" + advisorId).bind("click", function() {
                $(".allRound" + advisorId).show();
                $("#warning" + advisorId).hide();
                $("#showConfirmationBox" + advisorId).hide();
                $("input:radio[name=advisorPermission" + advisorId + "]")[0].checked = ($("#cancelleadupdation" + advisorId).attr('oldpermission') == "RO");
                $("input:radio[name=advisorPermission" + advisorId + "]")[1].checked = ($("#cancelleadupdation" + advisorId).attr('oldpermission') == "RW");
                $("input:radio[name=advisorPermission" + advisorId + "]")[2].checked = ($("#cancelleadupdation" + advisorId).attr('oldpermission') == "N");
                return false;
            });
        },
    });
    return new myadvisorsView;
});