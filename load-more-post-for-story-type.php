<?php
$count_new_post = $_POST['count_new_post'];
include("dbconnect.php");
$mfs->LoadMorePostForStoryType($count_new_post, $_SESSION['typeofstory']);
?>