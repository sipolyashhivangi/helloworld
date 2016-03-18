define([
    'jquery',
    'underscore',
    'handlebars',
    'backbone',
    'text!../../../html/profile/profilebuilderpopup.html',
    'text!../../../html/profile/profilebuildertwo.html',
    ], function($, _,Handlebars, Backbone, profileBuilderTemplate,profileBuilderTwoTemplate){
    
        var profileBuilderView = Backbone.View.extend({
            el: $("#body"),
            render: function(){
                var source = $(profileBuilderTemplate).html();
                var template = Handlebars.compile(source); 
                //for dialog box
                popUpActionStep('#comparisonContents',template(), 780);
            },
            events: {
                "click #saveAnd2ndStep": "profileContentAddOne",
                "click #save2ndStep": "profileContentAddTwo"
            },
            profileContentAddOne: function(event){
                event.preventDefault(); 
                
                var firstname = $('#firstname').val();
                var lastname = $('#lastname').val();
               
                var age = $('#age').val();
                var zipcode = $('#zipcode').val();
                var noofchildren = $('#noofchildren').val();
                
                var retired  = $('input:radio[name=retired]:checked').val()
                var formValues = {
                    firstname: firstname,
                    lastname:lastname,
                    age:age,
                    zipcode:zipcode,
                    noofchildren:noofchildren,
                    retired:retired
                };

                $.ajax({
                    url:addUserInfo1URL,
                    type:'POST',
                    dataType:"json",
                    data:formValues,
                    success:function () {
						timeoutPeriod = defaultTimeoutPeriod;
                        var sourceTwo = $(profileBuilderTwoTemplate).html();
                        var templateTwo = Handlebars.compile(sourceTwo); 
                        //for dialog box
                        $('#comparisonContents').html(templateTwo());
                    }
                });
                    
            },
            profileContentAddTwo: function(event){
                event.preventDefault(); 
                
                alert("second step");
            }
        });
        return new profileBuilderView;
    });