<?php
session_start();
include("dbconnect.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Planet of Stories</title>
		<meta name="keywords" content="planet, stories, funny, happy, sad, personal, life, experience">
		<meta name="description" content="This is a platform that allow people to share happy, funny and sad stories and also share stories of what has happen to them in the past, present or currently happening to them for other people to share their thought and feeling.">
		<?php include("head.php"); ?>
	</head>
	<body>
		<?php include("menu.php"); ?>
		<script type="text/javascript">
			$(function(){
				var count_post = 3;
				$(".more-edit-post").click(function(){
					count_post = count_post + 3;
					$(".story-posted").load("load-more-post.php", {count_new_post: count_post});
				});
			});
		</script>
		<div id="post-story">
			<form method="POST" action="register.php">
				<textarea id="poster" name="poststory" placeholder="tell us your story today"></textarea><br><br>
				<input id="submitpost" type="submit" value="POST">
			</form>
		</div>
		<?php include("right-side-bar.php"); ?>
		<div class="clear-float"></div>
		<div class="story-posted">
			<?php
				$mfs->DisplayPost();
			?>
		</div>
		<div id="story-posted-read-more">
			<br>
			<br>
			<input style="margin-left:40%; border-radius:0px 0px 10px 10px; width:160px;" class="more-edit-post" id="submitpost" type="button" value="READ MORE">
			<br>
			<br>
		</div>
		<?php include("footer.php"); ?>
	</body>
</html>