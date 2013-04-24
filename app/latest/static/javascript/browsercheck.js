$(function() {
	// Warn user if he doesn't use supported browsers
	// TODO: feature detection
	if (!$.browser.webkit) {
		var $message = $("<div>").css("display", "none").addClass("topmessage").html("<h2>Let op</h2><p>de werking van het wi3 beheer wordt alleen gegarandeerd in Google Chrome. U gebruikt op dit moment een andere browser. Google Chrome kunt u downloaden van <a href='http://chrome.google.com'>http://chrome.google.com</a></p>");
		$("body").prepend($message);
		$message.slideDown();
	}
});