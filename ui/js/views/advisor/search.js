// Filename: views/login/advisorsignup
define([
    'jquery',
    'underscore',
    'handlebars',
    'backbone',
    'text!../../../html/advisor/search.html',
    'text!../../../html/advisor/adminnotification.html',
], function($, _, Handlebars, Backbone, advisorListTemplate, notificationTemplate) {
    var jsonObj = '';
    var resetState = false;
    var advisorListView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj, searchDesignations, collapseDesignations) {
            require(['views/profile/myadvisors']);
            var source = $(advisorListTemplate).html();
            var template = Handlebars.compile(source);
            $('#mainBody').html(template(obj));
            if(typeof(searchDesignations) != 'undefined') {
                $('.filter').each(function() {
                    for(var i = 0; i < searchDesignations.length; i++) {
                        if ($(this).val() == searchDesignations[i]) {
                            $(this).attr('checked',true);
                        }
                    }
                });
            }
            if(typeof(collapseDesignations) != 'undefined') {
	            for(var i = 0; i< collapseDesignations.length; i++) {
	                 $("#"+ collapseDesignations[i]).parents(".accordion-group").find(".accordion-toggle").click();
	            }
	        }
            jsonObj = obj;
            init();
        },
        events: {
            "keypress .searchbox": "search",
            "click #searchMagnify": "search",
            "click .filter": "passiveSearch",
            'click #back-link': 'displaySearchResult',
            'click #reset': 'resetSearchResult',
            //"click .toshowpermission": "fnShowPermissions",//show all the permission
            "click .rouserConnect": "fnShowLegalMessage", //show legal indemnification message box
            "click #showAdvisors": "fnShowAllAdvisor",
        },
        search: function(event) {
            var search_by_click = false;
            var keycode = (event.keyCode ? event.keyCode : event.which);

            if (keycode == 13) {
                keycode = parseInt(keycode);
            } else if (keycode == 1) {
                search_by_click = true;
            }
            var fname = $('#searchbox').val();

            if (keycode == 13 || search_by_click) {
                if (resetState == false)
                    url = searchAdvisor;
                else
                    url = showAllAdvisors;
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        fname: fname
                    },
                    dataType: "json",
                    success: function(getAll) {
                        timeoutPeriod = defaultTimeoutPeriod;
                        var searchDesignations = new Array();
                        var collapseDesignations = new Array();
                        $('.filter').each(function() {
                            if ($(this).is(':checked')) {
                                searchDesignations.push($(this).val());
                            }
                        });
                        var tempArray = [ "One", "Two", "Three", "Four", "Five", "Six"];
                        for(var i = 0; i < tempArray.length; i++) {
                        	if($("#collapse" + tempArray[i]).height() > 0) {
                                collapseDesignations.push("collapse" + tempArray[i]);
                        	}
                        }
                        if (getAll.status == "OK") {
                            require(['views/advisor/search', 'views/advisor/searchresult'],
                                    function(listV, searchResultV) {
                                        listV.render(getAll.userdata, searchDesignations, collapseDesignations);
                                        searchResultV.render(getAll.userdata, searchDesignations);
                                        $('#searchbox').val(fname)
                                        init();
                                    }
                            );

                        }
                    }
                });
            }
        },
        passiveSearch: function() {
            var searchDesignations = new Array();
            $('.filter').each(function() {
                if ($(this).is(':checked')) {
                    searchDesignations.push($(this).val());
                }
            });
            require(['views/advisor/searchresult'],
                    function(listV) {
                        listV.render(jsonObj, searchDesignations);
                    }
            );
        }, fnShowAllAdvisor: function(event) {
            event.preventDefault();
            resetState = true;
            $.ajax({
                url: showAllAdvisors,
                type: 'POST',
                dataType: "json",
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "OK") {

                        jsonObj = data.userdata;
                        require('views/advisor/searchresult').render(jsonObj, searchParams, '');
                    }
                }
            });
        },
        resetSearchResult: function() {
            resetState = false;

            var searchDesignations = new Array();
            $('.filter').each(function() {
                if ($(this).is(':checked')) {
                    $(this).prop('checked', false);
                }
            });
            $('#searchbox').val('');
            $.ajax({
                url: searchAdvisor,
                type: 'POST',
                data: {
                },
                dataType: "json",
                success: function(getAll) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (getAll.status == "OK") {
                        require(['views/advisor/search', 'views/advisor/searchresult'],
                                function(listV, searchResultV) {
                                    listV.render(getAll.userdata, searchDesignations);
                                    searchResultV.render(getAll.userdata, searchDesignations);
                                    if (getAll.total == 0) {
                                        $('#search-result-area').html('<div class="center" style="color: #666">No result found</div>');

                                    }
                                    $('#total').html(getAll.total);
                                    $('#searchbox').val('');
                                    if (getAll.msg == "match") {
                                        $('#valid').show();
                                    }
                                    init();
                                }
                        );

                    }
                }
            });
        },
        fnShowLegalMessage: function(event) {
            var advisorId = event.target.attributes.getNamedItem('advisor').nodeValue;
            var permissionNode = event.currentTarget.id
            var permission = $('#' + permissionNode).val();
            if (permission == "RO") {
                $(".advper").text("View Only");
            }
            else if (permission == "RW") {
                $(".advper").text("View + Edit");//change the text on switch of radio button
            }
            else {
                $(".advper").text("None");
            }
        },
        displaySearchResult: function() {
            $("#profile-contents").html();
            $('#search-contents').show();
            $('#profile-contents').hide();
            $('#back-to-search').hide();
            $('.filter').prop('disabled', false);
            $('#searchbox').prop('disabled', false);
            $('#reset').prop('disabled', false);
        }
    });
    return new advisorListView;
});
    