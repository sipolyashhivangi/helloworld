define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/uploadnewclientlist.html',
], function(Handlebars, Backbone, uploadnewclientlistTemplate) {

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
            var source = $(uploadnewclientlistTemplate).html();
            var template = Handlebars.compile(source);
            $("#uploadnewclientlistContents").html(template(roleid));
            $('#uploadHideOnSuccess').removeClass("hdn");
            $('#createUploadClients').removeClass("hdn");
        },
        events: {
            "click #uploadClientCSV": "uploadClientCSV",
            "click .cancelUploadProfilePopupBox": "fnCloseUploadClient",
        },
        fnCloseUploadClient: function(event) {//Client View Finances
            event.preventDefault();
            if(!$("#uploadHideOnSuccess").is(':visible')) {
                getClientList(sort_order, sort_by, current_page, record_per_page);
            }
            removeLayover();
        },
        uploadClientCSV: function() {
            //event.preventDefault();

            var file = $('#file').val();
            var pathLength = file.length;

            var lastDot = file.lastIndexOf(".");
            var fileType = file.substring(lastDot, pathLength);
            if (file == "")
            {
                $('#uploaderror').html('Please upload the client CSV file.');
                $('#uploadbubble').removeClass("hdn");
                $("#uploaddiv").addClass('error');
                PositionErrorMessage("#file", "#uploadbubble");
                return false;
            }
            if (file != "" && fileType != ".csv")
            {
                $('#uploaderror').html('Invalid file format! Please upload only a valid CSV file.');
                $('#uploadbubble').removeClass("hdn");
                $("#uploaddiv").addClass('error');
                PositionErrorMessage("#file", "#uploadbubble");
                return false;
            } else {
                $("#uploaddiv").removeClass("error");
            }
            
            $('#uploadcsv-form').attr('action', uploadClients);
            var options = {
                type: 'POST',
                contentType: "multipart/form-data",
                dataType: 'json',
                data: { timezoneoffset: new Date().getTimezoneOffset() },
                beforeSend: function() {
                     $('#uploadClientCSV').html('Please wait...');
                    $('#uploadClientCSV').css('disabled',true);
                },
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "ERROR") {
                        $('#uploadClientCSV').html('Upload CSV');
                        $('#uploaderror').html(data.message);
                        $('#uploadbubble').removeClass("hdn");
                        $("#uploaddiv").addClass('error');
                        PositionErrorMessage("#file", "#uploadbubble");
                    } else if (data.status == 'OK') {
                        var source = $(uploadnewclientlistTemplate).html();
                        var template = Handlebars.compile(source);
                        $('#comparisonContents').html(template(data));
                        $('#createUploadClients').addClass("hdn");
                        $('#uploadHideOnSuccess').addClass("hdn");
                        $('#successUploadClients').removeClass("hdn");
                        $('#uploadSummary').removeClass("hdn");
                    }
                },
                error: function(xhr, status, error) {
                }
            };
            // pass options to ajaxForm
            $('#uploadcsv-form').prop('method', 'POST').ajaxSubmit(options);
            //$("#file").val('');
            return false;
        },
    });
    return new createnewView;
});