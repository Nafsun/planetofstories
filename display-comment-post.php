<?php
if(isset($_POST['displaycommentpost'])){
	include("dbconnect.php");
	$mfs->CommentDisplay($_POST['displaycommentpost']);
}
?>