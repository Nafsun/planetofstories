<?php
if(!empty($_POST['messagesent1']) && !empty($_POST['guestname1'])){
	include("dbconnect.php");
	$mfs->ChatUser($_POST['messagesent1'], $_POST['guestname1'], $_SESSION['search-username']);
}
?>