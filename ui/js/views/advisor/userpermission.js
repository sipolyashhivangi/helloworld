define([
    'handlebars',
    'text!../../../html/advisor/userpermission.html'
], function(Handlebars, stepOneTemplate)
{

    var userPermissionView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj, location, data) {
            var source = $(stepOneTemplate).html();
            var template = Handlebars.compile(source);
            $(obj).html(template(data));
            init();
        },
        events: {
            "click .changePermission": "fnPermission",
            "click .savepermissionforadv": "fnSavePermission",
            "click .deleteadvisorconnection": "fnDeclineConnection",
            "click #closeUserPermissions": "fncloseUserPermissionPopup"
        },
        fnPermission: function(event) {
            var advisorId = event.target.attributes.getNamedItem('advisorId').nodeValue;
            var updatepermission = $('#advpermission' + advisorId + ' input[type=radio]:checked').val();
            if (updatepermission == "RO") {
                $(".usrtoadvper" + advisorId).text("View Only");
            } else if (updatepermission == "RW") {
                $(".usrtoadvper" + advisorId).text("View + Edit");
            } else {
                $(".usrtoadvper" + advisorId).text("None");
            }
        },
        fnSavePermission: function(event) {
            event.preventDefault();

            if (!$("#termscond").is(':checked')) {
                $('#termBubbless').removeClass("hdn");
                $("#termtextdivs").addClass('error');
                return false;
            }

            var advisorId = event.target.attributes.getNamedItem('advisorId').nodeValue;
            var updatepermission = $('#advpermission' + advisorId + ' input[type=radio]:checked').val();
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
                    removeLayover();
                    $("#myAdvisorBox").hide();
                },
                error: function(data) {

                },
                complete: function(data) {

                }
            });
        },
        fnDeclineConnection: function(event) {
            var advisorId = event.target.attributes.getNamedItem('deleteAdv').nodeValue;
            var userid = event.target.attributes.getNamedItem('userId').nodeValue;
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
                    removeLayover();
                    $("#myAdvisorBox").hide();
                }
            });
        },
        fncloseUserPermissionPopup: function(event) {
            event.preventDefault();
            timeoutPeriod = defaultTimeoutPeriod;
            removeLayover();
            $('#myAdvisorBox').hide();
        }

    });
    return new userPermissionView;
});