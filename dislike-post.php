<?php
if(isset($_POST['dislikepost1'])){
	include("dbconnect.php");
	$mfs->DisLikePost($_POST['dislikepost1']);
}
?>