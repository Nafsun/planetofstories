<?php
session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Planet of Stories</title>
		<meta name="keywords" content="planet, stories, funny, happy, sad, personal, life, experience, account, profile, post, login">
		<meta name="description" content="Planet of stories page where you can login and access your account">
		<?php include("head.php"); ?>
	</head>
	<body>
		<?php include("menu.php"); ?>
		<div id="reg-story-2">
			<?php
				if(isset($_POST['username']) && isset($_POST['password'])){
					include("dbconnect.php");
					$username = strip_tags(strtolower($_POST['username']));
					$mfs->LOGIN($username, $_POST['password']);
				}
			?>
			<p id="welcome">LOGIN</p>
			<?php
				if(isset($_SESSION['ac'])){
					echo "<p id='form-submission'>Your account has being activated, enter your username and password to update your profile and post a story</p>";
					session_destroy();
				}
			?>
			<form method="POST">
				Username:<br><input class="reg" id="username" type="text" name="username" required><br>
				Password:<br><input class="reg" id="password" type="password" name="password" required><br>
				<a id="fp" href="forgot-password.php">forgot password</a><br><br>
				<input id="submitpost" type="submit" value="SUBMIT">
			</form>
		</div>
		<?php include("right-side-bar.php"); ?>
		<?php include("footer.php"); ?>
	</body>
</html>