<?php
if(isset($_POST['displaylikepost'])){
	include("dbconnect.php");
	$mfs->DisplayLikePost($_POST['displaylikepost']);
}
?>