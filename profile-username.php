<?php
session_start();
include("dbconnect.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Planet of Stories</title>
		<meta name="keywords" content="planet, stories, funny, happy, sad, personal, life, experience, account, profile, post">
		<meta name="description" content="Get latest quotes, funny, jokes and sad stories from different kind of people around the world.  You can also be able to view all their post, send message to them, subscribe to their story channel and see their profile information">
		<?php include("head.php"); ?>
	</head>
	<body>
		<?php include("menu.php"); ?>
		<?php
		if(isset($_GET['username']) || isset($_SESSION['search-username'])){
			if(isset($_GET['username'])){
				$_SESSION['search-username'] = $_GET['username'];
			}
			if(!isset($_SESSION['search-username'])){
				header("Location: search.php");
			}
		?>
		<div id="reg-story-1">
			<p id="welcome-1"><?php echo strtoupper($_SESSION['search-username']); ?> PROFILE</p>
			<div id="account-location">
				<div id="links-in-account">
					<p id="s-a-s">ALL POST</p>
					<p id="e-p">SENT MESSAGE</p>
					<p id="a">PROFILE</p>
					<p id="edit-p">SUBSCRIBE</p>
				</div>
				<div class="clear-float"></div>
				<script type="text/javascript">
					$(function(){
						var count_post = 3;
						$(".more-edit-post").click(function(){
							count_post = count_post + 3;
							$("#space-of-poster").load("load-more-post-for-username.php", {count_new_post: count_post});
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
						if(isset($_SESSION['search-username'])){
							$mfs->DisplayPostForUsername($_SESSION['search-username']);
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
				<script type="text/javascript">
					$(function(){
						$(".sentusermess").click(function(){
							var messagesent = $("#chat-id").val();
							var guestname = $("#nameofguest").val();
							if(messagesent == "" || guestname == ""){
					
							}else{
								$.post("sentmessageuser.php", {messagesent1: messagesent, guestname1: guestname});
								$("#form-chat")[0].reset();
							}
						});
					});
				</script>
				<div id="space-of-poster-2">
					<br>
					<iframe id="wherechatischat" src="displaymessageuser.php" frameborder="0" scrolling="auto"></iframe>
					<br>
					<br>
					<form id="form-chat" method="POST">
						<input class="reg-2" id="nameofguest" type="text" name="nameofguest" placeholder="your name" required><br>
						<textarea class="reg-bio" id="chat-id" name="chat" placeholder="sent a message to <?php echo $_SESSION['search-username']; ?>" required></textarea><br>
						<input class="sentusermess" id="submitpost" type="button" value="SEND"><br><br>
					</form>
				</div>
				<div id="space-of-poster-3">
					<?php
						$mfs->DisplayProfileInfoForUsername($_SESSION['search-username']);
					?>
				</div>
				<div id="space-of-poster-4">
					<p id="comment-display"><?php $mfs->CheckSubscribers(); ?></p>
					<p id="comment-display">Subscribe to receive an email notification each time <?php echo $_SESSION['search-username'] ?> post a story</p>
					<?php
						if(isset($_POST['nameofsubscriber']) && isset($_POST['emailofsubscriber'])){
							$mfs->Subscribe($_POST['nameofsubscriber'], $_POST['emailofsubscriber']);
						}
					?>
					<form id="form-chat" method="POST">
						<input class="reg-2" id="nameofsubscriber" type="text" name="nameofsubscriber" placeholder="your name" required><br>
						<input class="reg-2" id="emailofsubscriber" type="email" name="emailofsubscriber" placeholder="your email" required><br>
						<input class="sentusermess" id="submitpost" type="submit" value="SUBSCRIBE"><br><br>
					</form>
					<?php
						if(isset($_POST['emailofunsubscriber'])){
							$mfs->Unsubscribe($_POST['emailofunsubscriber']);
						}
					?>
					<p id="comment-display">If you have already subscribe, enter your email below to stop receiving stories posted by <?php echo $_SESSION['search-username'] ?></p>
					<form id="form-chat" method="POST">
						<input class="reg-2" id="emailofunsubscriber" type="email" name="emailofunsubscriber" placeholder="your email" required><br>
						<input class="sentusermess" id="submitpost" type="submit" value="UNSUBSCRIBE"><br><br>
					</form>
				</div>
			</div>
		</div>
		<?php } ?>
		<?php include("right-side-bar.php"); ?>
		<?php include("footer.php"); ?>
	</body>
</html>