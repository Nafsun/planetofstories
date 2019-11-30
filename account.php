<?php
session_start();
if(!isset($_SESSION['username'])){
	header("Location: login.php");
}
include("dbconnect.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Planet of Stories</title>
		<meta name="keywords" content="planet, stories, funny, happy, sad, personal, life, experience, account, profile, post">
		<meta name="description" content="Planet of stories individual account where you can post stories, edit profile, view your achievements, edit post, chat and delete your account">
		<?php include("head.php"); ?>
	</head>
	<body>
		<?php include("menu.php"); ?>
		<div id="reg-story-1">
			<p id="welcome-1">WELCOME BACK <?php echo strtoupper($_SESSION['username']); ?></p>
			<div id="account-location">
				<div id="links-in-account">
					<p id="s-a-s">SHARE A STORY</p>
					<p id="e-p">EDIT PROFILE</p>
					<p id="a">ACHIEVEMENTS</p>
					<p id="edit-p">EDIT POST</p>
					<p id="message">MESSAGES</p>
					<p id="settings">SETTINGS</p>
				</div>
				<div class="clear-float"></div>
				<div id="space-of-poster">
					<?php
						if(!empty($_POST['typeofstory']) && !empty($_POST['poststory'])){
							$mfs->POST($_POST['poststory'], $_POST['typeofstory']);
							echo "<p class='new-post' id='form-submission'>You have posted a story</p>";
						}
					?>
					<form method="POST">
						<select class="reg-2" id="type-of-story" name="typeofstory" required>
							<option value="" selected>select story type</option>
							<option value="funny">Funny</option>
							<option value="joke">Joke</option>
							<option value="sad">Sad</option>
							<option value="quote">Quote</option>
							<option value="poem">Poem</option>
							<option value="happy">Happy</option>
							<option value="personal life experience">Personal Life Experience</option>
							<option value="eye witness">Eye Witness</option>
							<option value="health">Health</option>
							<option value="need help from the rich">Need Help from the Rich</option>
							<option value="others">Others</option>
						</select><br>
						<textarea class="new-poster" id="poster-new" name="poststory" placeholder="tell us your story today" required></textarea><br><br>
						<input class="click-on-post" id="submitpost" type="submit" value="POST"><br><br>
					</form>
				</div>
				<div id="space-of-poster-2">
						<?php $mfs->DisplayProfileInfo($_SESSION['username']); ?>
						<input id="submitpost-2" type="button" value="EDIT">
					<div id="profile-edit">
					<?php $mfs->UpdateProfile($_SESSION['username']); ?>
					</div>
					<br>
					<br>
					<input class="change-password" id="submitpost-2" type="button" value="CHANGE PASSWORD">
					<div id="change-p">
					<br>
					<?php
					if(isset($_POST['oldpassword']) && isset($_POST['newpassword']) && isset($_POST['renewpassword'])){
						if($_POST['newpassword'] === $_POST['renewpassword']){
							$mfs->PasswordChange($_POST['newpassword'], $_POST['oldpassword']);
						}else{
							echo "<p id='form-submission'>New Passwords do not match</p>";
						}
					}
					?>
					<form method="POST">
						Old Password:<br><input class="reg-2" id="oldpassword" type="text" name="oldpassword" required><br>
						New Password:<br><input class="reg-2" id="newpassword" type="text" name="newpassword" required><br>
						Re-enter New Password:<br><input class="reg-2" id="renewpassword" type="text" name="renewpassword" required><br>
						<input id="submitpost-2" type="submit" value="CHANGE">
					</form>
					</div>
					<br><br>
				</div>
				<div id="space-of-poster-3">
					<?php $mfs->YearlyAndMonthlyPost(); ?>
				</div>
				<script type="text/javascript">
					$(function(){
						var count_post = 5;
						$(".more-edit-post").click(function(){
							count_post = count_post + 5;
							$("#space-of-poster-4").load("load-more-post-edit.php", {count_new_post: count_post});
						});
					});
				</script>
				<div id="space-of-poster-4">
					<?php $mfs->UpdateOrDeletePost(); ?>
				</div>
				<div id="space-of-poster-4-new">
					<br>
					<br>
					<input class="more-edit-post" id="submitpost" type="button" value="MORE">
					<br>
					<br>
				</div>
				<div id="space-of-poster-5">
					<iframe id="wherechatischat" src="displaymessage.php" frameborder="0" scrolling="auto"></iframe>
					<br>
					<br>
					<script type="text/javascript">
						$(function(){
							$(".sentmess").click(function(){
								var messagesent = $("#chat-id").val();
								if(messagesent == ""){
									
								}else{
									$.post("sentmessage.php", {messagesent1: messagesent});
									$("#form-chat")[0].reset();
								}
							});
							$(".delete-all-messages").click(function(){
								var d = confirm("Are you sure you want to delete all messages, click 'OK' if you are sure and 'cancel' to stop the deletion");
								if(d == true){
									$("#d").load("deletemessages.php");
									alert("All your messages are deleted");
								}
							});
						});
					</script>
					<div id="d"></div>
					<form id="form-chat" method="POST">
						<textarea class="reg-bio" id="chat-id" name="chat" placeholder="sent a message to your fans" required></textarea><br>
						<input class="sentmess" id="submitpost" type="button" value="SEND"><br><br>
					</form>
					<input class="delete-all-messages" id="submitpost" type="button" value="DELETE MESSAGES">
				</div>
				<div id="space-of-poster-6">
					<script type="text/javascript">
						$(function(){
							$(".delete-account").click(function(){
								var d = confirm("Are you sure you want to delete your account, click 'OK' if you are sure and 'cancel' to stop the deletion");
								if(d == true){
									$("#ad").load("deleteaccount.php");
									alert("Your account has being deleted");
								}
							});
						});
					</script>
					<div id="ad"></div>
					<br>
					<p>The moment you click on the delete button below, all your post, comments, messages will be deleted, therefore think wisely before you press the delete account button below.</p>
					<input class="delete-account" id="submitpost" type="button" value="DELETE ACCOUNT">
					<br>
					<br>
					<form method="POST">
					<input class="log-out" id="submitpost" name="logout" type="submit" value="LOG OUT">
					</form>
					<?php
					if(isset($_POST['logout'])){
						session_destroy();
						echo "<script>location.href = 'login.php';</script>";
					}
					?>
					<br>
					<br>
				</div>
			</div>
		</div>
		<?php include("right-side-bar.php"); ?>
		<?php include("footer.php"); ?>
	</body>
</html>