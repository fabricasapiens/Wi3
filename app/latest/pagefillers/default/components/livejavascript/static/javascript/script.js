$(function() {
	// Create live examples
	window.wi3 = window.wi3 || {};
	window.wi3.livejavascript_examples = {};
	$(".livejavascript_input").each(function() {
		// Base64 decode content
		this.textContent = $.base64Decode($(this).text());
		var example = new BetterExample($(this), $(this).nextAll(".livejavascript_output").first(), { editor: "codemirror" });
		window.wi3.livejavascript_examples[$(this).attr("id")] = example;
		example.on("keyup", this.onkeyup);
		setTimeout("window.wi3.livejavascript_examples['"+$(this).attr("id")+"'].run()", 1); // Don't stop on errors
	});
});