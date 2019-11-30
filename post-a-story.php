<?php
$newpost = $_POST['post1'];  
$newtype = $_POST['typeofstory1'];  
if(!empty($newpost) && !empty($newtype)){
	include("dbconnect.php");
	$mfs->POST($newpost, $newtype);
}
echo "<p class='new-post' id='form-submission'>You have posted a story</p>";
?>