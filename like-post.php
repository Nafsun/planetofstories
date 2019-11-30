<?php
if(isset($_POST['likepost1'])){
	include("dbconnect.php");
	$mfs->LikePost($_POST['likepost1']);
}
?>