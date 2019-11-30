<?php
$count_new_post = $_POST['count_new_post'];
include("dbconnect.php");
$mfs->LoadMorePost($count_new_post);
?>