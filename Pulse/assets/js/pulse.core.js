/**
Pulse Lite Voting Script
http://s.technabled.com/PulseVote
**/
$(function(){
	var u = "ENTER_ABS_PATH_OF_Pulse_DIR_HERE"; // NO trailing slash; Same as PULSE_DIR in pulse.config.php
	$("input.pulse_vote_button").live('click', function(){
		var item_id = $(this).attr("data-itemId"); // id of the item to vote
		var format = $(this).attr("data-format"); // result format
		var dir = $(this).attr("data-dir"); // voting direction; up or down
		el = $(this);
		result = el.parent().next(".pulse_result_format").text();
		el.parent().next(".pulse_result_format").html(" <img class='busy' src='"+u+"/assets/images/ajaxloader.gif'/> "); // show loading animation
		el.parent().children(".pulse_vote_button").each(function(){
			$(this).attr("disabled","disabled"); // disable buttons
			$(this).addClass('disabled');
		});
		$.ajax({
			type: "POST",
			data: "action="+dir+"&item_id="+item_id+"&format="+format,
			url: u+"/votes.php",
			dataType: 'json',
			error: function(a,b){
				alert("Cannot connect to database. Please try again.");
				el.parent().children(".pulse_vote_button").each(function(){
					$(this).removeAttr("disabled"); // disable buttons
					$(this).removeClass('disabled');
				});				
				el.parent().next(".pulse_result_format").text(result);
			},
			success: function(obj)
			{
				if('error' in obj){
					switch(obj.error){
						case "already_voted":
							alert("You have already voted");
							el.parent().next(".pulse_result_format").text(result); // clear the loader and replace with result
							break;
						case "database_error":
							alert("Cannot connect to database. Please try again.");
							el.parent().children(".pulse_vote_button").each(function(){
								$(this).removeAttr("disabled"); // disable buttons
								$(this).removeClass('disabled');
							});	
							el.parent().next(".pulse_result_format").text(result); // clear the loader and replace with result
							break;
					}
					return false;
				} if('msg' in obj) {
					var re = /\\/g; 
					obj.msg = obj.msg.replace(re, "");
					el.siblings(".busy").remove(); // remove loader
					el.parent().next(".pulse_result_format").text(obj.msg); // insert the formatted result
					el.parent().next(".pulse_result_format").show();
				}
			}
		});
	});
});