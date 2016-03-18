define([
    'handlebars',
    'text!../../../html/user/peer.html',
], function(Handlebars, peerTemplate) {
    var peerView = Backbone.View.extend({
        el: $("#body"),
        render: function(scoreData) {
            //get the details from the getuseritem
            var source = $(peerTemplate).html();
            var template = Handlebars.compile(source);
            $('.PeerRankContent').html(template(scoreData));

            var localPeer = scoreData.peerval.localpeer;
            var nationalpeer = scoreData.peerval.nationalpeer; // Need to use in future
            var userScore = scoreData.peerval.totalscore;
            var perCent = 0;
            if(parseInt(localPeer) > 0)
            {
	            perCent = Math.round((100 * (parseInt(userScore) - parseInt(localPeer))) / parseInt(localPeer));
	        }
            if (perCent >= 0) {
                textVal = "You are doing <span style='color: black'>" + perCent + "% </span>better than your peers, based on age and location.";
                $('#PeerPercent').html(textVal);
            } else {
            	perCent = Math.abs(perCent);
                textVal = "Your peers are doing <span style='color: black'>" + perCent + "% </span>better than you, based on age and location.";
                $('#PeerPercent').html(textVal);
            }
        }
    });
    return new peerView;
});