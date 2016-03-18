define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/login.html',
], function(Handlebars, Backbone, loginTemplate) {
    var loginView = Backbone.View.extend({
        el: $("#loginUser"),
        render: function() {
            var source = $(loginTemplate).html();
            var template = Handlebars.compile(source);

            $(this.el).html(template());
            if ($.browser.msie && $.browser.version == 10) {
                $(".fade").removeClass("fade");
            }
            if ($.browser.msie && $.browser.version < 9) {
                $('#browsercheck').show();
            }
        },
        events: {
            "click #loginButton": "performLogin",
        },
        initialize: function() {
            this.username = $("#username");
            this.password = $("#password");
            this.loginButton = $("#login");
        },
        validated: function(valid) {
            if (valid) {
                this.view.loginButton.removeAttr("disabled");
            } else {
                this.view.loginButton.attr("disabled", "true");
            }
        },
        performLogin: function(event) {
            event.preventDefault();
            $('#loginButton').attr("disabled", "true");
            $('#alert-error').hide();

            var user = $('#username').val();
            var pword = $('#password').val();

            var redirectUrl = getQueryVariable('redirectUrl');
            if (redirectUrl != "") {
                var location = redirectUrl;
            } else {
                var location = baseUrl + '/myscore';
            }

            var formValues = {
                email: user,
                password: pword,
                jsMixpanelCall: true
            };

            $.ajax({
                url: loginUrl,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    if (data.status == "ERROR")
                    {
                        $('#loginButton').removeAttr("disabled");
                        $('#usernameerror').html(data.message);
                        $('#usernamebubble').removeClass("hdn");
                        $("#usernamediv").addClass('error');
                        PositionErrorMessage("#username", "#usernamebubble");
                    }
                    else
                    {
                        localStorage['dialogShown'] = false;
                        /// Call file for actionstep to create new while user login for first time.
                        if (typeof (sendMixpanel) != 'undefined' && sendMixpanel) {
                            var trackValues = {};
                            trackValues["user_logged_in"] = data.uniquehash;
                            var formValues = {};
                            formValues["Last Login"] = new Date();
                            if (typeof (data.mixPanelData.currentScore) != 'undefined' && data.mixPanelData.currentScore) {
                                formValues["Score"] = data.mixPanelData.currentScore;
                                trackValues["score"] = data.mixPanelData.currentScore;
                            }
                            if (typeof (data.mixPanelData.age) != 'undefined' && data.mixPanelData.age) {
                                formValues["Age"] = data.mixPanelData.age;
                            }
                            if (typeof (data.mixPanelData.connectedAccounts) != 'undefined' && data.mixPanelData.connectedAccounts) {
                                formValues["Has Connected Accounts"] = data.mixPanelData.connectedAccounts;
                            }
                            if (typeof (data.mixPanelData.autoLoanRate) != 'undefined' && data.mixPanelData.autoLoanRate) {
                                formValues["Auto Loan Rate"] = data.mixPanelData.autoLoanRate;
                            }

                            if (typeof (data.mixPanelData.IRAcontribution) != 'undefined' && data.mixPanelData.IRAcontribution) {
                                formValues["IRA Contribution"] = data.mixPanelData.IRAcontribution;
                            }

                            if (typeof (data.mixPanelData.CRcontribution) != 'undefined' && data.mixPanelData.CRcontribution) {
                                formValues["CR User Contribution"] = data.mixPanelData.CRcontribution;
                            }

                            if (typeof (data.mixPanelData.CREmpContribution) != 'undefined' && data.mixPanelData.CREmpContribution) {
                                formValues["CR Employer Contribution"] = data.mixPanelData.CREmpContribution;
                            }

                            if (typeof (data.mixPanelData.noofchildren) != 'undefined' && data.mixPanelData.noofchildren) {
                                formValues["No. of children"] = data.mixPanelData.noofchildren;
                            }

                            if (typeof (data.mixPanelData.wmortrate) != 'undefined' && data.mixPanelData.wmortrate) {
                                formValues["Weighted Mortgage Rate"] = data.mixPanelData.wmortrate;
                            }

                            if (typeof (data.mixPanelData.wccrate) != 'undefined' && data.mixPanelData.wccrate) {
                                formValues["Weighted CC Rate"] = data.mixPanelData.wccrate;
                            }

                            if (typeof (data.mixPanelData.wloanrate) != 'undefined' && data.mixPanelData.wloanrate) {
                                formValues["Weighted Loan Rate"] = data.mixPanelData.wloanrate;
                            }

                            if (typeof (data.mixPanelData.monthlyIncome) != 'undefined' && data.mixPanelData.monthlyIncome) {
                                formValues["Income"] = data.mixPanelData.monthlyIncome;
                            }

                            if (typeof (data.mixPanelData.creditScore) != 'undefined' && data.mixPanelData.creditScore) {
                                formValues["Credit Score"] = data.mixPanelData.creditScore;
                            }

                            if (typeof (data.mixPanelData.totalAssets) != 'undefined' && data.mixPanelData.totalAssets) {
                                formValues["Total Assets"] = data.mixPanelData.totalAssets;
                            }

                            if (typeof (data.mixPanelData.totalDebts) != 'undefined' && data.mixPanelData.totalDebts) {
                                formValues["Total Debts"] = data.mixPanelData.totalDebts;
                            }

                            if (typeof (data.mixPanelData.hasWill) != 'undefined' && data.mixPanelData.hasWill) {
                                formValues["Has Will"] = data.mixPanelData.hasWill;
                            }

                            if (typeof (data.mixPanelData.willReviewed) != 'undefined' && data.mixPanelData.willReviewed) {
                                formValues["Will Reviewed"] = data.mixPanelData.willReviewed;
                            }

                            if (typeof (data.mixPanelData.riskValue) != 'undefined' && data.mixPanelData.riskValue) {
                                formValues["Risk"] = data.mixPanelData.riskValue;
                            }

                            if (typeof (data.mixPanelData.profileCompleteness) != 'undefined' && data.mixPanelData.profileCompleteness) {
                                formValues["Profile Completeness"] = data.mixPanelData.profileCompleteness;
                            }

                            if (typeof (data.mixPanelData.collegeAmount) != 'undefined' && data.mixPanelData.collegeAmount) {
                                formValues["College Balance"] = data.mixPanelData.collegeAmount;
                            }

                            if (typeof (data.mixPanelData.lifeInsuranceNeeded) != 'undefined' && data.mixPanelData.lifeInsuranceNeeded) {
                                formValues["Life Insurance Needed"] = data.mixPanelData.lifeInsuranceNeeded;
                            }

                            if (typeof (data.mixPanelData.disabilityInsuranceNeeded) != 'undefined' && data.mixPanelData.disabilityInsuranceNeeded) {
                                formValues["Disability Insurance Needed"] = data.mixPanelData.disabilityInsuranceNeeded;
                            }

                            mixpanel.identify(data.uniquehash);
                            mixpanel.people.set(formValues);
                            mixpanel.track("User Logged In", trackValues, function() {
                                localStorage[serverSess] = data.sess;
                                localStorage["showNewUserDialog"] = false;
                                localStorage["showNewAdvisorDialog"] = false;
                                window.location = location;
                            });
                        }
                        else {
                            localStorage[serverSess] = data.sess;
                            localStorage["showNewUserDialog"] = false;
                            localStorage["showNewAdvisorDialog"] = false;
                            window.location = location;
                        }
                    }
                },
                error: function(data) {
                    $('#usernameerror').html("Incorrect email/password combination.");
                    $('#usernamebubble').removeClass("hdn");
                    $("#usernamediv").addClass('error');
                    $('#loginButton').removeAttr("disabled");
                    PositionErrorMessage("#username", "#usernamebubble");
                }
            });
            return false;
        }
    });
    return new loginView;
});