define([
    'handlebars',
    'text!../../../html/user/createAccountStepTwo.html'
    ], function(Handlebars,stepTwoTemplate)
    {

        var stepTwoView = Backbone.View.extend({
            el: $("#body"),
            render: function(obj){
                //get the details from the getuseritem
                var source = $(stepTwoTemplate).html();
                var template = Handlebars.compile(source);
                $(obj).html(template(profileUserData));
            },
            events: {
                "click #stepTwoBackButton": "fnLoadStepOne",
                "click #stepTwoSave": "fnSaveStepTwo"
            },
            fnLoadStepOne: function(event){
                event.preventDefault();
                require(
                    [ 'views/user/createAccountStepOne'],
                    function( accountOneV){
                        accountOneV.render("#comparisonBox");
                        init();
                    });
            },
            fnSaveStepTwo: function(event){
                event.preventDefault();
                //insert values to database
                var householdIncome = $('#slider1Value').val().replace(/,/g, '');
                var hIfreq = $('#hifreq').val();
                var householdExpense = $('#slider2Value').val().replace(/,/g, '');
                var hEfreq = $('#hefreq').val();
                var householdAsset= $('#slider3Value').val().replace(/,/g, '');
                var householdDebts = $('#slider4Value').val().replace(/,/g, '');
                var householdSav = $('#slider5Value').val().replace(/,/g, '');
                var hSfreq = $('#hsfreq').val();

                var whichyouhave = "";
                $('button.whichyouhave.active').each(function(i){
                    whichyouhave += $(this).text()+",";
                });

                var formValues = {
                    houseincome: householdIncome,
                    hIfreq:hIfreq,
                    houseexpense:householdExpense,
                    hEfreq:hEfreq,
                    houseassets:householdAsset,
                    housedebts:householdDebts,
                    housesavings:householdSav,
                    hSfreq:hSfreq,
                    whichyouhave:whichyouhave.toString()
                };
                $.ajax({
                    url:addUserInfo2URL,
                    type:'POST',
                    dataType:"json",
                    data: formValues,
                    success:function (data) {
						timeoutPeriod = defaultTimeoutPeriod;
                        if (data.status == "OK"){
                            //goto financial section
                            //window.location = "./financialsnapshot";
                            require(
                                [ 'views/user/createAccountStepThree'],
                                function( accountThreeV){
                                    accountThreeV.render("#comparisonBox");
                                    popUpActionStep();
                                    init();
                           });
                        }
                    }

                });
            }
        });
        return new stepTwoView;
    });