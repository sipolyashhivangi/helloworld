define([
    'handlebars',
    'backbone',
   'text!../../../html/advisor/advisordelete.html',
], function(Handlebars, Backbone, profileTemplate) {

    var profileView = Backbone.View.extend({
        el: $("#body"),
        render: function(advisor_id) {			
			var source = $(profileTemplate).html();			
			var template = Handlebars.compile(source)
			$("#profileContents").html(template(advisor_id));
			$("#advisor_id").val(advisor_id);
			popUpManageCredentials();
		},
        events: {
            "click .cancelProfilePopup": "closeProfileDialog",
			"click .releaseAdvisor": "fnReleaseAdvisor",
        },
        initialize: function() {
//                this.signupButton = $("#signup");
        },
        // use this for close overlay after click close(x) link.
        closeProfileDialog: function(event) {
            event.preventDefault();
            removeLayover();
            
        },
		fnReleaseAdvisor: function(event) {
			advisor_id = $('#advisor_id').val();
            removeLayover();
			var data = {
				advisor_id: advisor_id,
			};
			$.ajax({
				url: releaseAdvisor,
				type: 'POST',
				dataType: "json",
				data: data,
				success: function(data) {
					timeoutPeriod = defaultTimeoutPeriod;
					var list_type = 'assigned';
					var sort_order = 'DESC';
					var sort_by  = 'createdtimestamp';
					var current_page = 1;
					$.ajax({										
						url: getAdvisorList,
						type: 'POST',
						data:{
							list_type : list_type,
							sort_order : sort_order,
							sort_by  : sort_by,
							current_page : current_page
						},
						dataType: "json",							
						success: function( getAll ) {			
							if (getAll.status == "OK") {
								require(['views/advisor/advisorlist'],
									function(listV) {							
										listV.render( getAll.userdata,getAll.total );							
											$('#msg').html('Advisor has been unassigned successfully.');
											$('#msg-box').removeClass('hdn');
											$('#msg').removeClass('hdn');											 	
											if (getAll.total > 0) {
												$('#counter').html('(' + getAll.total + ')');
											} else {
												$('#counter').html('(0)');
												$('#no-record').html('<center>' + getAll.msg + '</center>');
												$('#no-record').show();
											}
										
										
											//$(':input').removeClass('active');
											$('.advisor_designations').show();
											$('.release').show();
											$('.revoke').hide();
											$('.deleteadv').show();
											$('.viewprofile').show();
                                                                                        $('.signupdate').hide();
											
										init();															
									}
								);
							}
						}
					});
					
				}
			});
        },
    });
    return new profileView;
});