$(document).ready(function(){
	$("#menu-down").hide();
	$("#space-of-poster-2").hide();
	$("#space-of-poster-3").hide();
	$("#space-of-poster-4").hide();
	$("#space-of-poster-4-new").hide();
	$("#space-of-poster-5").hide();
	$("#space-of-poster-6").hide();
	$("#profile-edit").hide();
	$("#change-p").hide();
	$("#menu").click(function(){
		$("#menu-down").slideToggle(1000);
	});
	$("#s-a-s").click(function(){
		$("#space-of-poster").show();
		$("#space-of-poster-2").hide();
		$("#space-of-poster-3").hide();
		$("#space-of-poster-4").hide();
		$("#space-of-poster-4-new").hide();
		$("#space-of-poster-5").hide();
		$("#space-of-poster-6").hide();
		$("#story-posted-read-user").show();
	});
	$("#e-p").click(function(){
		$("#space-of-poster").hide();
		$("#space-of-poster-2").show();
		$("#space-of-poster-3").hide();
		$("#space-of-poster-4").hide();
		$("#space-of-poster-4-new").hide();
		$("#space-of-poster-5").hide();
		$("#space-of-poster-6").hide();
		$("#story-posted-read-user").hide();
	});
	$("#a").click(function(){
		$("#space-of-poster").hide();
		$("#space-of-poster-2").hide();
		$("#space-of-poster-3").show();
		$("#space-of-poster-4").hide();
		$("#space-of-poster-4-new").hide();
		$("#space-of-poster-5").hide();
		$("#space-of-poster-6").hide();
		$("#story-posted-read-user").hide();
	});
	$("#edit-p").click(function(){
		$("#space-of-poster").hide();
		$("#space-of-poster-2").hide();
		$("#space-of-poster-3").hide();
		$("#space-of-poster-4").show();
		$("#space-of-poster-4-new").show();
		$("#space-of-poster-5").hide();
		$("#space-of-poster-6").hide();
		$("#story-posted-read-user").hide();
	});
	$("#message").click(function(){
		$("#space-of-poster").hide();
		$("#space-of-poster-2").hide();
		$("#space-of-poster-3").hide();
		$("#space-of-poster-4").hide();
		$("#space-of-poster-4-new").hide();
		$("#space-of-poster-5").show();
		$("#space-of-poster-6").hide();
	});
	$("#settings").click(function(){
		$("#space-of-poster").hide();
		$("#space-of-poster-2").hide();
		$("#space-of-poster-3").hide();
		$("#space-of-poster-4").hide();
		$("#space-of-poster-4-new").hide();
		$("#space-of-poster-5").hide();
		$("#space-of-poster-6").show();
	});
	$("#submitpost-2").click(function(){
		$("#profile-edit").slideToggle(3000);
	});
	$(".change-password").click(function(){
		$("#change-p").slideToggle(3000);
	});
});