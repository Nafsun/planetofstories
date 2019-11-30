<?php
session_start();
if(!isset($_SESSION['typeofstory'])){
	header("Location: search.php");
}
include("dbconnect.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Planet of Stories</title>
		<meta name="keywords" content="planet, stories, funny, happy, sad, personal, life, experience, account, profile, story, type, post">
		<meta name="description" content="Get latest quotes, funny, jokes and sad stories from different kind of people around the world.">
		<?php include("head.php"); ?>
	</head>
	<body>
		<?php include("menu.php"); ?>
		<div id="reg-story-1">
			<p id="welcome-1"><?php echo strtoupper($_SESSION['typeofstory']); ?> STORIES</p>
			<div id="account-location">
				<div class="clear-float"></div>
				<script type="text/javascript">
					$(function(){
						var count_post = 3;
						$(".more-edit-post").click(function(){
							count_post = count_post + 3;
							$("#space-of-poster").load("load-more-post-for-story-type.php", {count_new_post: count_post});
						});
					});
				</script>
				<div id="space-of-poster">
					<?php
						if(isset($_POST['names']) && isset($_POST['comments']) && isset($_POST['holder'])){
							$mfs->Comment($_POST['names'], $_POST['comments'], $_POST['holder']);
						}
					?>
					<?php
						if(isset($_POST['like'])){
							$mfs->LikePost($_POST['like']);
						}
						if(isset($_POST['dislike'])){
							$mfs->DisLikePost($_POST['dislike']);
						}
					?>
					<?php
						if(isset($_SESSION['typeofstory'])){
							$mfs->DisplayPostForStoryType($_SESSION['typeofstory']);
						}
					?>
				</div>
				<div id="story-posted-read-user">
					<br>
					<br>
					<input style="margin-left:40%; border-radius:10px;" class="more-edit-post" id="submitpost" type="button" value="READ MORE">
					<br>
					<br>
				</div>
			</div>
		</div>
		<?php include("right-side-bar.php"); ?>
		<?php include("footer.php"); ?>
	</body>
</html>