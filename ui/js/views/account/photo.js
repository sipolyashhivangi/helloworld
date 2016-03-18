require.config({
    'paths': {
        "jcrop": "libs/jcrop.min",
        "jqueryform": "libs/jquery/jquery.form",
    }
});
define([
    'handlebars',
    'backbone',
    'jcrop',
    'jqueryform',
    'text!../../../html/account/photo.html',
], function(Handlebars, Backbone, jcrop, jqueryform, photoTemplate) {

    var settingView = Backbone.View.extend({
        el: $("#body"),
        render: function() {
            $.ajax({
                url: getNotifySettings,
                type: 'POST',
                data: {
                },
                dataType: "json",
                success: function(data) {
                    var source = $(photoTemplate).html();
                    var template = Handlebars.compile(source);
                    var obj = {};
                    if (userData.user != undefined && userData.advisor == undefined) {
                        data.userdata.urole = 'user';
                    } else if (userData.user == undefined && userData.advisor != undefined) {
                        data.userdata.urole = 'advisor';
                    }
                    $("#settingsDetails").html(template(data));
                    $("#tabPhoto").addClass("selected");
                    $("#tabCredentials").removeClass("selected");
                    $("#tabCommunication").removeClass("selected");
                    $("#tabDelete").removeClass("selected");
                }
            });
        },
        events: {
            "click .cancel": "showProfilePic",
            "change #profile": "uploadPic",
            "click #uploadpic": "uploadPic",
            "click #crop_photo": "cropPhoto",
            "keypress .setting-css input": "removeErrorBubble",
        },
        initialize: function() {
        },
        // use this for close overlay after click close(x) link.

        cropPhoto: function(event) {
            event.preventDefault();
            var height = $('#h').val();
            var width = $('#w').val();
            var x_axis = $('#x').val();
            var y_axis = $('#y').val();
            var src = $('#src').val();
            $.ajax({
                url: cropPhoto,
                type: 'POST',
                data: {
                    height: height,
                    width: width,
                    x_axis: x_axis,
                    y_axis: y_axis,
                    src: src,
                },
                dataType: "json",
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "OK") {
                        $("#headNotifyTags").html(data.notificationCount);
                        $("#menuNotifyTags").html(data.notificationCount);
                        $('#profile-img img').attr('src', '');
                        $('#profile-img img').attr('src', data.profilepic);
                        $(".profileUser").attr('src', data.profilepic);
                        $('.uploadPhoto').removeClass('hdn');
                        $('.cropPhoto').addClass('hdn');
                        $('#msg').html('Profile picture uploaded successfully.');
                        $('.photo-success').removeClass('hdn');
                        if (typeof (userData.user) != 'undefined') {
                            userData.user.image = data.profilepic;
                        }
                    }
                }
            });
        },
        uploadPic: function() {
            //event.preventDefault();

            $('#h').val(0);
            $('#w').val(0);
            $('#x').val(0);
            $('#y').val(0);
            $('.photo-success').addClass('hdn');
            var pics = $('#profile').val();
            if (pics == '') {
                return false;
            }
            $('#profilepic-form').attr('action', uploadPicUrl);
            var options = {
                type: 'POST',
                contentType: "multipart/form-data",
                dataType: 'json',
                beforeSend: function() {
                    $('#uploadpic').html('Please wait...');
                },
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "ERROR")
                    {
                        $('#uploadpic').html('Upload Image');
                        if (data.type == 'profilepic') {
                            $('#filenameError').html(data.message);
                            $('#filenameBubble').removeClass("hdn");
                            $("#filenamediv").addClass('error');
                            PositionErrorMessage("#profile", "#filenameBubble");
                        }
                        $('#profile').click(function() {
                            $('#filenamediv').removeClass('error');
                        });
                    } else if (data.status == 'OK') {

                        $('#uploadpic').html('Upload Image');
                        $('#cropboxContainer').html("<img src='" + data.pic + "' width='" + data.width + "' height ='" + data.height + "'  id=\"cropbox\" alt=\"cropbox\" />");
                        //$('.jcrop-holder img').attr('src', data.pic);

                        $('#src').val(data.pic);
                        $('.uploadPhoto').addClass('hdn');
                        $('.cropPhoto').removeClass('hdn');
                        $('#cropbox').Jcrop({
                            aspectRatio: 1,
                            setSelect: [($('#cropbox').attr('width') / 2) - 100,
                                ($('#cropbox').attr('height') / 2) - 100,
                                ($('#cropbox').attr('width') / 2) + 100,
                                ($('#cropbox').attr('height') / 2) + 100
                            ],
                            onSelect: updateCoords1
                        });

                    }
                },
                error: function(xhr, status, error) {

                }

            };

            // pass options to ajaxForm
            $('#profilepic-form').prop('method', 'POST').ajaxSubmit(options);
            $("#profile").val('');
            return false;
        },
        showProfilePic: function(event) {
            event.preventDefault();
            $('.photo-success').addClass('hdn');
            $('.uploadPhoto').removeClass('hdn');
            $('.cropPhoto').addClass('hdn');
        },
        removeErrorBubble: function(event) {
            event.preventDefault();
            $('.photo-success').addClass('hdn');
            $('.error').removeClass('error');
        },
    });

    return new settingView;
});

function updateCoords1(c) {
    if (c.x == 0)
        $('#x').val('1');
    else
        $('#x').val(c.x);
    if (c.y == 0)
        $('#y').val('1');
    else
        $('#y').val(c.y);
    $('#w').val(c.w);
    $('#h').val(c.h);
}
