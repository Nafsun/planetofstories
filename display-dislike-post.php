<?php
if(isset($_POST['displaydislikepost'])){
	include("dbconnect.php");
	$mfs->DisplayDisLikePost($_POST['displaydislikepost']);
}
?>