<?php
session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Planet of Stories</title>
		<meta name="keywords" content="planet, stories, funny, happy, sad, personal, life, experience, account, profile, post, search, username, storytype">
		<meta name="description" content="Planet of stories page where you can search for a person base on his username or search a story base on the category it falls in">
		<?php include("head.php"); ?>
	</head>
	<body>
		<?php include("menu.php"); ?>
		<div id="reg-story-2">
			<p id="welcome">SEARCH</p>
			<?php
			if(isset($_SESSION['notfoundusername'])){
				echo "<p id='notfoundmessage'>No one with the username: {$_SESSION['search-username']} was found in our database.</p>";
				unset($_SESSION['notfoundusername']);
			}
			?>
			<?php
			if(isset($_SESSION['notfoundstory'])){
				echo "<p id='notfoundmessage'>No one with the story type: {$_SESSION['typeofstory']} was found in our database.</p>";
				unset($_SESSION['notfoundstory']);
			}
			?>
			<p>Search a specific person by his username to see all his stories, profile and even send a message to him.</p>
			<form method="POST">
				<input class="reg" id="search-username" type="text" name="search-username" placeholder="username" required><br><br>
				<input id="submitpost" type="submit" value="SEARCH">
			</form>
			<?php
				if(isset($_POST['search-username'])){
					$_SESSION['search-username'] = strip_tags(strtolower($_POST['search-username']));
					echo "<script>location.href = 'profile-username.php';</script>";
				}
			?>
			<br>
			<?php
				if(isset($_POST['typeofstory'])){
					$_SESSION['typeofstory'] = $_POST['typeofstory'];
					echo "<script>location.href = 'profile-story-type.php';</script>";
				}
			?>
			<p>Search a specific category of story.</p>
			<form method="POST">
				<select class="reg" id="type-of-story" name="typeofstory" required>
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
				</select><br><br>
				<input id="submitpost" type="submit" value="SEARCH">
			</form>
			<br>
			<br>
		</div>
		<?php include("right-side-bar.php"); ?>
		<?php include("footer.php"); ?>
	</body>
</html>