// Filename: views/login/advisorsignup
define([
    'handlebars',
    'text!../../../html/admin/specificproducts.html',
], function(Handlebars, specificproductsTemplate) {

    var sort_order = 'ASC';
    var sort_by = 'createdtimestamp';
    var current_page = 1;

    var specificproductsView = Backbone.View.extend({
        //body div id .
        el: $("#body"),
        render: function(obj) {
            timeoutPeriod = defaultTimeoutPeriod;
            var source = $(specificproductsTemplate).html();
            var template = Handlebars.compile(source);
            //div id under which we want to show the content of current html file.
            $('#mainBody').html(template(obj));
            if (typeof (userData) == 'undefined') {
                userData = {};
            }
        },
        events: {
            "click .pagelink ": "performPagination",
            "click .sorting": "performSorting",
            "click .editASProduct": "editASProduct",
            "click .deleteASProduct": "deleteASProduct",
            "change .pagelink_drop ": "performDropDownPagination",
        },
        editASProduct: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var id = name.substring(0, name.indexOf("editASProduct"));
            addadminProductForAS(id);
        },
        deleteASProduct: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var id = name.substring(0, name.indexOf("deleteASProduct"));
            var url = deleteproduct;
            formValues = { id : id };

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
        },
        performPagination: function(event) {
            current_page = event.target.attributes.getNamedItem('pageno').nodeValue;
            timeoutPeriod = defaultTimeoutPeriod;
            getASList(sort_order, sort_by, current_page);

        },
        performDropDownPagination: function(event) {
            current_page = event.target.value;
            getASList(sort_order, sort_by, current_page);
        },
        performSorting: function(event) {
            sort_by = event.target.attributes.getNamedItem('sorttype').nodeValue;
            timeoutPeriod = defaultTimeoutPeriod;
            if (sort_order == 'ASC')
                sort_order = 'DESC';
            else
                sort_order = 'ASC';


            getASList(sort_order, sort_by, current_page);

        }


    });

    return new specificproductsView;
});

function getASList(sort_order, sort_by, current_page) {
    $.ajax({
        url: getadminexternallinkas,
        cache: false,
        type: 'POST',
        dataType: "json",
        data: {
            sort_order: sort_order,
            sort_by: 'actionname',
            current_page: '1',
        },
        success: function(data) {
            timeoutPeriod = defaultTimeoutPeriod;
            if (data.status == "OK") {
                require(['views/admin/specificproducts'],
                        function(specificproductsV) {
                            specificproductsV.render(data);
                            //$("#gnav_finadv").addClass("hover reverseShadowBox");
                            //$("#gnav_finadv").removeClass("gnavButton");
                            //$('.pagination').html(data.pagination);
                            $('#allUsers').html(data.userSortdata);
                        }
                );
            } else if (data.status == "ERROR") {
                require(['views/advisor/specificproducts'],
                        function(specificproductsV) {
                            specificproductsV.render(data);
                            //$("#gnav_finadv").addClass("hover reverseShadowBox");
                            //$("#gnav_finadv").removeClass("gnavButton");
                            // $('.norecorderror').show();
                            // $('.norecorderror').html(data.msg);
                            $('.sorting').removeClass('sorting');
                        }
                );
            }
        }
    });

}

function addadminProductForAS(id) {


    require(
            ['views/admin/createnewasproduct'],
            function(createnewV) {
                $.ajax({
                    url: getadminexternallinkas,
                    cache: false,
                    type: 'POST',
                    dataType: "json",
                    data: {
                        sort_by: 'actionname',
                        current_page: '1',
                    },
                    success: function(data) {
                        timeoutPeriod = defaultTimeoutPeriod;
                        if (data.status == "OK") {
                           
                            createnewV.render(data);
                            $('.multiselect').multiselect({
                                buttonClass: 'ddPlainBtn',
                                includeSelectAllOption: true,
                                numberDisplayed: 0
                            });
                            $("#actionOption").parent('div').find('.adv_dropdown-menu').addClass('state_dropdown-menu');
                            $("#actionOption").parent('div').find('.ddPlainBtn').css('width', '440px');
                            $("#actionOption").parent('div').find('.ddPlainBtn').css('min-width', '440px');
                            $("#actionOption").parent('div').find('.adv_multiselect').css('width', '438px');
                            $( ".adv_dropdown-menu > li" ).css( "float", "none" );
                            $( ".adv_dropdown-menu > li" ).css( "width", "" );
                            popUpCreatenewASProduct();
                            if(typeof(id) != 'undefined') {
                                $("#productimage").val($("#" + id + "productimage").attr('src'));
                                $("#productlink").val($("#" + id + "productlink").attr('href'));
                                $("#productdescription").val($("#" + id + "productdescription").html());
                                $("#productname").val($("#" + id + "productname").val());
                                $("#userid").val($("#" + id + "useremail").html());
                                $("#currentASProduct").val(id);
                                $("#useridDiv").hide();
                                $("#actioidDiv").hide();
                                $("#productTitle").html("Update Product");
                                $("#saveNewASProduct").html("Update Product");
                            }
                        }
                    }
                });

            }
    );
}