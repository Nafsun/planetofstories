<?php
if(isset($_POST['displaydislikepost'])){
	include("dbconnect.php");
	$mfs->DisplayDisLikePostSecond($_POST['displaydislikepost']);
}
?>