<?php
session_start();
include("dbconnect.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Planet of Stories</title>
		<meta name="keywords" content="planet, stories, funny, happy, sad, personal, life, experience, account, profile, post, registration, register">
		<meta name="description" content="Planet of stories page where you can register and be able to post a story">
		<?php include("head.php"); ?>
	</head>
	<body>
		<?php include("menu.php"); ?>
		<div id="reg-story-2">
			<p id="welcome">Register as a story teller</p>
			<?php
				if(isset($_POST['fullname']) && isset($_POST['email']) && 
				isset($_POST['username']) && isset($_POST['password']) && isset($_POST['repassword'])){
					if($_POST['password'] === $_POST['repassword']){
			?>
			<?php
						$fullname = strip_tags($_POST['fullname']);
						$email = strip_tags($_POST['email']);
						$username = strip_tags(strtolower($_POST['username']));
						date_default_timezone_set("Africa/Lagos");
						$year = date('y', time());
						$dateandtime = date('d/m/y h:i:s a', time());
						$mfs->Register($fullname, $email, $username, $_POST['password'], $year, $dateandtime);
			?>
			<?php
					}else{
						echo "<p id='form-submission'>Passwords do not match</p>";
					}
				}
			?>
			<?php
				if(!isset($_SESSION['verify-send']) && !isset($_GET['hash'])){
			?>
			<form method="POST">
				Full Name:<br><input class="reg" id="fullname" type="text" name="fullname" required><br>
				Email:<br><input class="reg" id="email" type="email" name="email" required><br>
				Username:<br><input class="reg" id="username" type="text" name="username" required><br>
				Password:<br><input class="reg" id="password" type="password" name="password" required><br>
				Re-enter Password:<br><input class="reg" id="repassword" type="password" name="repassword" required><br><br>
				<input id="submitpost" type="submit" value="SUBMIT">
			</form>
			<?php
				}elseif(isset($_SESSION['verify-send']) && !isset($_GET['hash'])){
					unset($_SESSION['verify-send']);
			?>
			<p>Check your email address and click on the link sent to it.... if it takes longer than 3 minute to appear, check your Email Spam</p>
			<?php
			}elseif(isset($_GET['hash'])){
				$mfs->VerifySuccess($_GET['hash']);
			}
			?>
		</div>
		<?php include("right-side-bar.php"); ?>
		<?php include("footer.php"); ?>
	</body>
</html>