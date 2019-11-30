<?php
if(isset($_POST['displaycommentpost'])){
	include("dbconnect.php");
	$mfs->CommentDisplaySecond($_POST['displaycommentpost']);
}
?>