<?php
if(!empty($_POST['messagesent1'])){
	include("dbconnect.php");
	$mfs->Chat($_POST['messagesent1']);
}
?>