<?php
$count_new_post = $_POST['count_new_post'];
include("dbconnect.php");
$mfs->LoadMorePostEdit($count_new_post);
?>