<?php
session_start();
include("dbconnect.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Planet of Stories</title>
		<meta name="keywords" content="planet, stories, funny, happy, sad, personal, life, experience, account, profile, post, forgot, password">
		<meta name="description" content="Planet of stories page where you can reset your password">
		<?php include("head.php"); ?>
	</head>
	<body>
		<?php include("menu.php"); ?>
		<div id="reg-story-2">
			<p id="welcome">FORGOT PASSWORD</p>
			<?php
				if(isset($_POST['username']) && isset($_POST['email'])){
					$username = strip_tags(strtolower($_POST['username']));
					$email = strip_tags(strtolower($_POST['email']));
					$mfs->CheckUsernameMatchEmail($username, $email);
				}
			?>
			<?php
			if(!isset($_SESSION['username']) && !isset($_SESSION['email']) && !isset($_SESSION['randomnumber'])){
			?>	
				<form method="POST">
					Email:<br><input class="reg" id="email" type="text" name="email" required><br>
					Username:<br><input class="reg" id="username" type="text" name="username" required><br><br>
					<input id="submitpost" type="submit" value="SUBMIT"><br><br>
				</form>
			<?php
			}
			?>
			<?php
			if(isset($_POST['code'])){
				$mfs->CodeChecker($_POST['code']);
			}
			?>
			<?php
				if(isset($_POST['resendcode'])){
					$mfs->ResendCode();
				}
			?>
			<?php
				if(isset($_POST['cancelrequest'])){
					session_destroy();
					echo "<script>location.href = 'login.php';</script>";
				}
			?>
			<?php
			if(isset($_SESSION['username']) && isset($_SESSION['email']) && isset($_SESSION['randomnumber'])){
			?>
				<p>Enter the code sent to your email address down below:</p>
				<form method="POST">
					Code:<br><input class="reg" id="code" type="tel" name="code" required><br>
					<input id="submitpost" type="submit" value="VERIFY">
				</form>
				<br>
				<form method="POST">
					<input class="reg" id="resendcode" type="hidden" name="resendcode"><br>
					<input id="submitpost" type="submit" value="RESEND CODE">
				</form>
				<br>
				<form method="POST">
					<input class="reg" id="cancelrequest" type="hidden" name="cancelrequest"><br>
					<input id="submitpost" type="submit" value="CANCEL REQUEST">
				</form>
			<?php
			}
			?>
			<?php
				if(isset($_POST['newpassword']) && isset($_POST['repassword'])){
					if($_POST['newpassword'] == $_POST['repassword']){
						$mfs->PasswordForgotChange($_POST['newpassword']);
					}else{
						echo "<p id='form-submission'>Passwords do not match</p>";
					}
				}
			?>
			<?php
			if(isset($_SESSION['codematch'])){
			?>
				<form method="POST">
					New Password:<br><input class="reg" id="newpassword" type="text" name="newpassword" required><br>
					Re-Enter Password:<br><input class="reg" id="repassword" type="text" name="repassword" required><br>
					<input id="submitpost" type="submit" value="VERIFY">
				</form>
			<?php
			}
			?>
		</div>
		<?php include("right-side-bar.php"); ?>
		<?php include("footer.php"); ?>
	</body>
</html>