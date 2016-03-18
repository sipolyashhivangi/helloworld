define([
    'handlebars',
    'backbone',
    'text!../../../html/admin/createnewasproduct.html',
], function(Handlebars, Backbone, createnewASProductTemplate) {

    var createnewView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj) {
            var source = $(createnewASProductTemplate).html();
            var template = Handlebars.compile(source);
            $("#createnewASProductBoxContents").html(template(obj));

        },
        events: {
            "click #saveNewASProduct": "fnsaveNewASProduct",
        },
        fnsaveNewASProduct: function(event)
        {
            //event.preventDefault();
            
            var actionstep = [];//designation
            var actionstepselected = $('#actionOption').find(":selected");
            var checkedactionstepsCount = actionstepselected.length;
            var i = 0;
            var j = 0;
            while (i < checkedactionstepsCount) {
                if(actionstepselected[i].value != 'multiselect-all') {
                    actionstep[j] = actionstepselected[i].value;
                    j++
                }
                i++;
            }
            actionstep = actionstep.join(',');
            var email = $('#userid').val();
            if (!validateEmail(email))
            {
                $('#useriderror').html('Please enter the user\'s valid email address.');
                $('#useridbubble').removeClass("hdn");
                $("#useriddiv").addClass('error');
                PositionErrorMessage("#userid", "#useridbubble");
                return false;
            }else{
                $('#useriderror').html('');
                $('#useridbubble').addClass("hdn");
                $("#useriddiv").removeClass('error');
            }
            var productname = $('#productname').val();
            if (productname == "")
            {
                $('#productnameerror').html('Please enter the product name.');
                $('#productnamebubble').removeClass("hdn");
                $("#productnamediv").addClass('error');
                PositionErrorMessage("#productname", "#productnamebubble");
                return false;
            }else{
                $('#productnameerror').html('');
                $('#productnamebubble').addClass("hdn");
                $("#productnamediv").removeClass('error');
            }
            var productimage = $('#productimage').val();
            if (productimage == "")
            {
                $('#productimageerror').html('Please enter the product image url.');
                $('#productimagebubble').removeClass("hdn");
                $("#productimagediv").addClass('error');
                PositionErrorMessage("#productimage", "#productimagebubble");
                return false;
            }else{
                $('#productimageerror').html('');
                $('#productimagebubble').addClass("hdn");
                $("#productimagediv").removeClass('error');
            }
            var productlink = $('#productlink').val();
            if (productlink == "")
            {
                $('#productlinkerror').html('Please enter the product link url.');
                $('#productlinkbubble').removeClass("hdn");
                $("#productlinkdiv").addClass('error');
                PositionErrorMessage("#productlink", "#productlinkbubble");
                return false;
            }else{
                $('#productlinkerror').html('');
                $('#productlinkbubble').addClass("hdn");
                $("#productlinkdiv").removeClass('error');
            }
            var productdescription = $('#productdescription').val();
            if (productdescription == "")
            {
                $('#productdescriptionerror').html('Please enter the product description.');
                $('#productdescriptionbubble').removeClass("hdn");
                $("#productdescriptiondiv").addClass('error');
                PositionErrorMessage("#productdescription", "#productdescriptionbubble");
                return false;
            }else{
                $('#productdescriptionerror').html('');
                $('#productdescriptionbubble').addClass("hdn");
                $("#productdescriptiondiv").removeClass('error');
            }
            var formValues = {
                actionid: actionstep,
                productname: productname,
                productimage: productimage,
                productlink: productlink,
                productdescription: productdescription,
                useremail: email,
            };
            var url = addproduct;
            var id = $("#currentASProduct").val();
            if(id != "") {
                url = updateproduct;
                formValues["id"] = id;
            }

            $.ajax({
                url: url,
                cache: false,
                type: 'POST',
                dataType: "json",
                data: formValues,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                    getASList();
                    event.preventDefault();
                    removeLayover();
                },
                error: function(data) {
                }
            });
            return false;
        }

    });
    return new createnewView;
});