$(function(){
	var u = "http://127.0.0.1/pulse/codes/lite/Pulse"; // NO trailing slash
	$("a.pulse_vote_button").live('click', function(){
		var item_id = $(this).attr("data-itemId"); // id of the item to vote
		var format = $(this).attr("data-format"); // result format
		var dir = $(this).attr("data-dir"); // voting direction; up or down
		el = $(this);
		el.parent().append(" <img class='busy' src='"+u+"/assets/images/ajaxloader.gif'/> "); // show loading animation
		el.parent().children(".pulse_vote_button").hide(); // hide buttons
		$.ajax({
			type: "POST",
			data: "action="+dir+"&item_id="+item_id+"&format="+format,
			url: u+"/votes.php",
			dataType: 'json',
			error: function(a,b){
				alert("Cannot connect to database. Please try again.");
				el.parent().children(".pulse_vote_button").show();
				el.siblings(".busy").remove();
			},
			success: function(obj)
			{
				if('error' in obj){
					switch(obj.error){
						case "already_voted":
							alert("You have already voted");
							el.parent().text(""); // clear the buttons and loader
							break;
						case "database_error":
							alert("Cannot connect to database. Please try again.");
							el.show();
							el.siblings(".busy").remove();
							break;
					}
					return false;
				} if('msg' in obj) {
					var re = /\\/g; 
					obj.msg = obj.msg.replace(re, "");
					el.parent().next(".pulse_result_format").text(obj.msg); // insert the formatted result
					el.siblings(".busy").remove(); // remove loader
					el.parent().children(".pulse_vote_button").remove(); // remove buttons
				}
			}
		});
	});
});