<?php 
include("head.php"); 
?>
<div id="display-extracted-chat"></div>
<script type="text/javascript">
$(function(){
	setInterval(function(){
		$("#display-extracted-chat").load("extractchat.php");
	}, 5000);
});
</script>