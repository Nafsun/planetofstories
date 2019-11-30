<?php
if(isset($_POST['displaylikepost'])){
	include("dbconnect.php");
	$mfs->DisplayLikePostSecond($_POST['displaylikepost']);
}
?>