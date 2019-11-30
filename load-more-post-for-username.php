<?php
$count_new_post = $_POST['count_new_post'];
include("dbconnect.php");
$mfs->LoadMorePostForUsername($count_new_post, $_SESSION['search-username']);
?>