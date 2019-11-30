<?php
if(isset($_POST['comment_name1']) && isset($_POST['comment_text1']) && isset($_POST['comment_holder1'])){
	include("dbconnect.php");
	$mfs->Comment($_POST['comment_name1'], $_POST['comment_text1'], $_POST['comment_holder1']);
}
?>