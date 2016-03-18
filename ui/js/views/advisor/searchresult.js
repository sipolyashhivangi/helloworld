// Filename: views/login/advisorsignup
define([
    'jquery',
    'underscore',
    'handlebars',
    'backbone',
    'masking',
    'text!../../../html/advisor/searchresult.html',
], function($, _, Handlebars, Backbone, masking, advisorListTemplate) {
    var jsonObj = '';
    var current_size = 5;
    var limit = 5;
    var advisorSearchView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj, searchParams, msg) {
            jsonObj = obj;
            current_size = limit;
            var source = $(advisorListTemplate).html();
            var filteredAdvisor = new Array();
            var template = Handlebars.compile(source);
            if (searchParams.length > 0) {
                $.each(obj, function(index, value) {

                    advDesignation = new Array();

                    var desig_arr = value['designationverify'].split(', ');
                    for (var counter = 0; counter < desig_arr.length; counter++) {
                        advDesignation.push(desig_arr[counter]);
                    }

                    $.each(value['pns'], function(key, advDesig) {
                        advDesignation.push(advDesig['name']);
                    });

                    found = false;
                    for (i = 0; i < searchParams.length; i++) {
                        if ($.inArray(searchParams[i], advDesignation) > -1) {
                            found = true;
                        } else {
                            found = false;
                            break;
                        }
                    }
                    if (found == true)
                        filteredAdvisor.push(value);
                });
            } else {
                filteredAdvisor = obj;
            }
            jsonObj = filteredAdvisor;
            $('#search-result').html(template(filteredAdvisor));
            if (filteredAdvisor.length > 0) {
            } else {
                if (obj == "") {
                    //to get other state advisor
                    //$('#search-result').html("<p id='searchFound'>Hey, we couldn't found any advisors registered in your state. To get other state advisors <a id='showAdvisors'>Click Here</a></p>");
                    $('.NOTfound').html("Sorry, we couldn’t find any advisors registered in your state.");
                }
                //to get other state advisor
                //$('#searchAll').html('<p id="searchFound">Hey, we have not found the advisors registered in your state. To get other state advisors <a id="showAdvisors">Click Here</a></p>');
                $('#searchAll').html("Sorry, we couldn’t find any advisors registered in your state.");
                $('#notFound').html('<div class="center" style="color: #666;padding-top:15px">No result found.</div>');
            }
            $('#total').html(filteredAdvisor.length);
            if (msg == "match") {
                $('#valid').show();
            }
            $('.advisorBox').slice(limit).hide();
            if (filteredAdvisor.length <= limit) {
                $('#more-result').hide();
            }
            $('#less-result').hide();
            init();
        },
        events: {
            "click .viewprofile": "viewProfile",
            "click .paginationbtn": "pagination",
            "click #less-result": "ShowLess",
        },
        viewProfile: function(event) {
            var advisorHash = event.currentTarget.id;
            window.location = baseUrl + '/advisorprofile?view=' + advisorHash;
        },
        pagination: function() {
            current_size = current_size + limit;
            $(".advisorBox :lt(" + parseInt(current_size) + ")").slideDown('slow');
            if (jsonObj.length <= current_size) {
                current_size = jsonObj.length;
                $('#more-result').hide();
            }
            $('#less-result').show();
        },
        ShowLess: function() {
            current_size = parseInt(current_size - limit);
            if (current_size < limit)
                current_size = limit;
            $(".advisorBox :gt(" + parseInt(current_size - 1) + ")").slideUp('slow');
            if (limit >= parseInt(current_size)) {
                $('#less-result').hide();
            }

            $('#more-result').show();
        },
    });

    return new advisorSearchView;
});