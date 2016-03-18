if (!Modernizr.borderradius) {
	$.getScript("jquery-corner.js", function () {
		$(".box").corner();
	});
}