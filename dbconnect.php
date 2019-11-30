<?php
session_start();
require 'PHPMailer/PHPMailerAutoload.php';
?>
<?php
class PDOConnection{
	private $hostdb = "mysql:host=localhost;dbname=moneyforstory";
	private $username = "root";
	private $password = "12345678";
	
	public function dbconnection(){
		try{
			$connection = new PDO($this->hostdb, $this->username, $this->password);
			return $connection;
		}catch (PDOException $e){
			echo "Connection Error:" . $e->getMessage() . "";
		}
	}
}

class MoneyForStory extends PDOConnection{
	public function Register($fullname, $email, $username, $password, $yearofregister, $dateandtimeofregistration){
		$checkforusername = $this->dbconnection()->query("SELECT username FROM usersinfo");
		if($checkforusername->fetchColumn() === $username){
			echo "<p id='form-submission'>There is already someone with that username, choose another username</p>";
		}else{
			$hash = md5(rand(10, 100));
			if(!empty($_SERVER["HTTP_CLIENT_IP"])){
				//check for ip from share internet
				$ip = $_SERVER["HTTP_CLIENT_IP"];
			}elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
				// Check for the Proxy User
				$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			}else{
				$ip = $_SERVER["REMOTE_ADDR"];
			}
			$register = $this->dbconnection()->prepare("INSERT INTO usersinfo (fullname, email, username, yearofregistration, dateandtimeofregistration, yearlypostcount, monthlypostcount, ipaddress, hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$register->execute([$fullname, $email, $username, $yearofregister, $dateandtimeofregistration, 0, 0, $ip, $hash]);
			$verify = $this->dbconnection()->prepare("INSERT INTO verification (username, password) VALUES (?, ?)");
			$verify->execute([$username, $password]);
			$this->dbconnection()->query("CREATE TABLE {$username}_post (id int(11) AUTO_INCREMENT, post TEXT, rvforpost varchar(100), dtofpost varchar(100), storytype varchar(100), PRIMARY KEY(id));");
			$this->dbconnection()->query("CREATE TABLE {$username}_chat (id int(11) AUTO_INCREMENT, name varchar(100), message TEXT, dtofchat varchar(100), ip varchar(100), PRIMARY KEY(id));");
			$this->dbconnection()->query("CREATE TABLE {$username}_subscribe (id int(11) AUTO_INCREMENT, name varchar(100), email varchar(100), PRIMARY KEY(id));");
			$_SESSION['verify-send'] = "email verification";
			$this->SendEmailVerification($email, $hash);
		}
	}
	public function Login($username, $password){
		$login = $this->dbconnection()->prepare("SELECT password FROM verification WHERE username = ?");
		$login->execute([$username]);
		if($login->fetchColumn() === $password){
			$check_acti = $this->dbconnection()->query("SELECT activation FROM usersinfo WHERE username = {$this->dbconnection()->quote($username)}");
			if($check_acti->fetchColumn() == 1){
				$_SESSION['username'] = $username;
				echo "<script>location.href = 'account.php';</script>";
			}else{
				echo "<p id='form-submission'>You have not activated your account, check your email to verify</p>";
			}
		}else{
			echo "<p id='form-submission'>Username or Password incorrect</p>";
		}
	}
	public function Post($user_post, $storytype){
		$rv = "{$_SESSION['username']}_" . rand(1, 1000000000);
		$like = "{$_SESSION['username']}_like_" . rand(1, 1000000000);
		$dislike = "{$_SESSION['username']}_dislike_" . rand(1, 1000000000);
		date_default_timezone_set("Africa/Lagos");
		$dateandtime = date('d/m/y h:i:s a', time());
		if(!empty($_SERVER["HTTP_CLIENT_IP"])){
			//check for ip from share internet
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
			// Check for the Proxy User
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}else{
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		$post = $this->dbconnection()->prepare("INSERT INTO {$_SESSION['username']}_post (post, rvforpost, dtofpost, storytype) VALUES (?, ?, ?, ?)");
		$post->execute([$user_post, $rv, $dateandtime, $storytype]);
		$allpost = $this->dbconnection()->prepare("INSERT INTO post (username, post, rvforpost, likepost, dislikepost, dtofpost, storytype, ipaddress) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
		$allpost->execute([$_SESSION['username'], $user_post, $rv, $like, $dislike, $dateandtime, $storytype, $ip]);
		$this->dbconnection()->query("CREATE TABLE {$rv} (id int(11) AUTO_INCREMENT, name varchar(100), comment TEXT, ip varchar(100), PRIMARY KEY(id))");
		$this->dbconnection()->query("CREATE TABLE {$like} (id int(11) AUTO_INCREMENT, name varchar(100), PRIMARY KEY(id))");
		$this->dbconnection()->query("CREATE TABLE {$dislike} (id int(11) AUTO_INCREMENT, name varchar(100), PRIMARY KEY(id))");
		$this->dbconnection()->query("UPDATE usersinfo SET yearlypostcount = yearlypostcount + 1 WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
		$this->dbconnection()->query("UPDATE usersinfo SET monthlypostcount = monthlypostcount + 1 WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
		$sub_sender = $this->dbconnection()->query("SELECT email FROM {$_SESSION['username']}_subscribe");
		while($row = $sub_sender->fetch()){
			$mail = new PHPMailer;
			$mail->setFrom('no-reply@planetofstories.com', 'Planet of Stories');
			$mail->addAddress($row['email']);
			$mail->isHTML(true);
			$mail->Subject  = 'Story Post';
			$mail->Body     = "<h1>Stories</h1><p>{$_SESSION['username']} have posted a new story, visit https://planetofstories.com/profile-username.php?username={$_SESSION['username']} and check it out</p>";
			if(!$mail->send()){
				echo '<p id="formsubmission">Message was not sent.</p>';
				echo 'Mailer error: ' . $mail->ErrorInfo;
			}else{
				passthru;
			}
		}
	}
	public function DisplayPost(){
		$display = $this->dbconnection()->query("SELECT username, post, rvforpost, likepost, dislikepost, dtofpost, storytype FROM post ORDER BY id DESC LIMIT 3");
		while($row = $display->fetch()){
			echo "<script type='text/javascript'>
			$(function(){
				$('.{$row['likepost']}_post').click(function(){
					var likepost = $('#{$row['likepost']}_like').val();
					$.post('like-post.php', {likepost1: likepost});
					$('#{$row['likepost']}_display_like').load('display-like-post.php', {displaylikepost: likepost});
					$('#{$row['likepost']}_display_like').load('display-like-post-second.php', {displaylikepost: likepost});
				});
				$('.{$row['dislikepost']}_post').click(function(){
					var dislikepost = $('#{$row['dislikepost']}_dislike').val();
					$.post('dislike-post.php', {dislikepost1: dislikepost});
					$('#{$row['dislikepost']}_display_dislike').load('display-dislike-post.php', {displaydislikepost: dislikepost});
					$('#{$row['dislikepost']}_display_dislike').load('display-dislike-post-second.php', {displaydislikepost: dislikepost});
				});
				$('.{$row['rvforpost']}_click').click(function(){
					var comment_name = $('.{$row['rvforpost']}_name').val();
					var comment_text = $('.{$row['rvforpost']}_text').val();
					var comment_holder = $('#{$row['rvforpost']}_holder').val();
					if(comment_name == '' || comment_text == ''){
						
					}else{
						$.post('comment-post.php', {comment_name1: comment_name, comment_text1: comment_text, comment_holder1: comment_holder});
						$('.{$row['rvforpost']}_comment').load('display-comment-post.php', {displaycommentpost: comment_holder});
						$('.{$row['rvforpost']}_comment').load('display-comment-post-second.php', {displaycommentpost: comment_holder});
						$('#{$row['rvforpost']}_comment-former')[0].reset();
					}
				});
			});
		</script>";
			echo "<div id='stories'>";
			echo "<p id='name'><a href='profile-username.php?username={$row['username']}'>{$row['username']}</a></p><p id='date'>{$row['dtofpost']}</p>";
			echo "<p id='story-teller'>" . ucwords($row['storytype']) . "<br><br>{$row['post']}</p>";
			echo "<div id='like'>
					<div id='like-form'>
						<form method='POST'>
						<input id='{$row['likepost']}_like' type='hidden' value='{$row['likepost']}' name='like'>
						<input class='{$row['likepost']}_post' id='comment-submission-like' type='button' value='LIKE'><br>
						";
				$liker = $this->dbconnection()->query("SELECT count(*) FROM {$row['likepost']}");
				while($rowli = $liker->fetch()){
					echo "<span id='{$row['likepost']}_display_like'>{$rowli[0]} <br>likes</span>";
				}
			echo "</form>
					</div>
					<div id='dislike-form'>
						<form method='POST'>
						<input id='{$row['dislikepost']}_dislike' type='hidden' value='{$row['dislikepost']}' name='dislike'>
						<input class='{$row['dislikepost']}_post' id='comment-submission-like' type='button' value='DISLIKE'><br>
					";
				$disliker = $this->dbconnection()->query("SELECT count(*) FROM {$row['dislikepost']}");
				while($rowdis = $disliker->fetch()){
					echo "<span id='{$row['dislikepost']}_display_dislike'>{$rowdis[0]} <br>dislikes</span>";
				}
			echo "</form>
					</div>
				</div>
				<div style='clear:both;'></div><br>
				<p id='comment-display'>Share this post on:
				<a href='https://www.facebook.com/sharer/sharer.php?u=https://planetofstories.com/profile-username.php?username={$row['username']}&quote={$row['post']}' target='__blank'><img class='social' src='images/facebook-share.png' alt='facebook'></a>
				<a href='https://www.twitter.com/intent/tweet?text={$row['post']} For more visit https://planetofstories.com/profile-username.php?username={$row['username']}' target='__blank'><img class='social' src='images/twitter-share.png' alt='twitter'></a>
				<a href='whatsapp://send?text={$row['post']} For more visit  https://planetofstories.com/profile-username.php?username={$row['username']}' target='__blank'><img class='social' src='images/whatsapp-share.png' alt='whatsapp'></a>
				</p>
			";
			echo "<div class='{$row['rvforpost']}_comment'>";
			$comment = $this->dbconnection()->query("SELECT name, comment FROM {$row['rvforpost']} ORDER BY id DESC LIMIT 5");
			while($rowtwo = $comment->fetch()){
				echo "<p id='comment-display'><span>{$rowtwo['name']}</span><br><br>{$rowtwo['comment']}</p>";
			}
			echo "</div>";
			echo "<form id='{$row['rvforpost']}_comment-former' method='POST'>
						<input class='{$row['rvforpost']}_name' id='names' type='text' placeholder='name' name='names' required><br><br>
						<textarea class='{$row['rvforpost']}_text' id='comments' placeholder='comment here' name='comments' required></textarea><br>
						<input id='{$row['rvforpost']}_holder' type='hidden' value='{$row['rvforpost']}' name='holder'>
						<input class='{$row['rvforpost']}_click' id='comment-submission' type='button' value='POST'>
					</form>";
			echo "</div>";
		}
	}
	public function DisplayPostForUsername($username_search){
		$check_u = $this->dbconnection()->query("SELECT username FROM post WHERE username = {$this->dbconnection()->quote($username_search)}");
		if($check_u->fetchColumn() === $username_search){
			$display = $this->dbconnection()->query("SELECT username, post, rvforpost, likepost, dislikepost, dtofpost, storytype FROM post WHERE username = {$this->dbconnection()->quote($username_search)} ORDER BY id DESC LIMIT 3");
			while($row = $display->fetch()){
				echo "<script type='text/javascript'>
					$(function(){
						$('.{$row['likepost']}_post').click(function(){
							var likepost = $('#{$row['likepost']}_like').val();
							$.post('like-post.php', {likepost1: likepost});
							$('#{$row['likepost']}_display_like').load('display-like-post.php', {displaylikepost: likepost});
							$('#{$row['likepost']}_display_like').load('display-like-post-second.php', {displaylikepost: likepost});
						});
						$('.{$row['dislikepost']}_post').click(function(){
							var dislikepost = $('#{$row['dislikepost']}_dislike').val();
							$.post('dislike-post.php', {dislikepost1: dislikepost});
							$('#{$row['dislikepost']}_display_dislike').load('display-dislike-post.php', {displaydislikepost: dislikepost});
							$('#{$row['dislikepost']}_display_dislike').load('display-dislike-post-second.php', {displaydislikepost: dislikepost});
						});
						$('.{$row['rvforpost']}_click').click(function(){
							var comment_name = $('.{$row['rvforpost']}_name').val();
							var comment_text = $('.{$row['rvforpost']}_text').val();
							var comment_holder = $('#{$row['rvforpost']}_holder').val();
							if(comment_name == '' || comment_text == ''){
								
							}else{
								$.post('comment-post.php', {comment_name1: comment_name, comment_text1: comment_text, comment_holder1: comment_holder});
								$('.{$row['rvforpost']}_comment').load('display-comment-post.php', {displaycommentpost: comment_holder});
								$('.{$row['rvforpost']}_comment').load('display-comment-post-second.php', {displaycommentpost: comment_holder});
								$('#comment-former')[0].reset();
							}
						});
					});
				</script>";
				echo "<div style='margin-top:-10%; margin-left:-10%; margin-bottom:10%;' id='stories'>";
				echo "<p id='name'>{$row['username']}</p><p id='date'>{$row['dtofpost']}</p>";
				echo "<p id='story-teller'>" . ucwords($row['storytype']) . "<br><br>{$row['post']}</p>";
				echo "<div id='like'>
						<div id='like-form'>
							<form method='POST'>
							<input id='{$row['likepost']}_like' type='hidden' value='{$row['likepost']}' name='like'>
							<input class='{$row['likepost']}_post' id='comment-submission-like' type='button' value='LIKE'><br>
							";
					$liker = $this->dbconnection()->query("SELECT count(*) FROM {$row['likepost']}");
					while($rowli = $liker->fetch()){
						echo "<span id='{$row['likepost']}_display_like'>{$rowli[0]} <br>likes</span>";
					}
				echo "</form>
						</div>
						<div id='dislike-form'>
							<form method='POST'>
							<input id='{$row['dislikepost']}_dislike' type='hidden' value='{$row['dislikepost']}' name='dislike'>
							<input class='{$row['dislikepost']}_post' id='comment-submission-like' type='button' value='DISLIKE'><br>
						";
					$disliker = $this->dbconnection()->query("SELECT count(*) FROM {$row['dislikepost']}");
					while($rowdis = $disliker->fetch()){
						echo "<span id='{$row['dislikepost']}_display_dislike'>{$rowdis[0]} <br>dislikes</span>";
					}
				echo "</form>
						</div>
					</div>
					<div style='clear:both;'></div><br>
					<p id='comment-display'>Share this post on:
					<a href='https://www.facebook.com/sharer/sharer.php?u=https://planetofstories.com/profile-username.php?username={$row['username']}&quote={$row['post']}' target='__blank'><img class='social' src='images/facebook-share.png' alt='facebook'></a>
					<a href='https://www.twitter.com/intent/tweet?text={$row['post']} For more visit https://planetofstories.com/profile-username.php?username={$row['username']}' target='__blank'><img class='social' src='images/twitter-share.png' alt='twitter'></a>
					<a href='whatsapp://send?text={$row['post']} For more visit  https://planetofstories.com/profile-username.php?username={$row['username']}' target='__blank'><img class='social' src='images/whatsapp-share.png' alt='whatsapp'></a>
					</p>
				";
				echo "<div class='{$row['rvforpost']}_comment'>";
				$comment = $this->dbconnection()->query("SELECT name, comment FROM {$row['rvforpost']} ORDER BY id DESC LIMIT 5");
				while($rowtwo = $comment->fetch()){
					echo "<p id='comment-display'><span>{$rowtwo['name']}</span><br><br>{$rowtwo['comment']}</p>";
				}
				echo "</div>";
				echo "<form id='{$row['rvforpost']}_comment-former' method='POST'>
						<input class='{$row['rvforpost']}_name' id='names' type='text' placeholder='name' name='names' required><br><br>
						<textarea class='{$row['rvforpost']}_text' id='comments' placeholder='comment here' name='comments' required></textarea><br>
						<input id='{$row['rvforpost']}_holder' type='hidden' value='{$row['rvforpost']}' name='holder'>
						<input class='{$row['rvforpost']}_click' id='comment-submission' type='button' value='POST'>
					</form>";
				echo "</div>";
			}
		}else{
			$_SESSION['notfoundusername'] = "not found username";
			echo "<script>location.href = 'search.php';</script>";
		}
	}
	public function DisplayPostForStoryType($story_search){
		$check_s = $this->dbconnection()->query("SELECT storytype FROM post WHERE storytype = {$this->dbconnection()->quote($story_search)}");
		if($check_s->fetchColumn() === $story_search){
			$display = $this->dbconnection()->query("SELECT username, post, rvforpost, likepost, dislikepost, dtofpost, storytype FROM post WHERE storytype = {$this->dbconnection()->quote($story_search)} ORDER BY id DESC LIMIT 3");
			while($row = $display->fetch()){
				echo "<script type='text/javascript'>
					$(function(){
						$('.{$row['likepost']}_post').click(function(){
							var likepost = $('#{$row['likepost']}_like').val();
							$.post('like-post.php', {likepost1: likepost});
							$('#{$row['likepost']}_display_like').load('display-like-post.php', {displaylikepost: likepost});
							$('#{$row['likepost']}_display_like').load('display-like-post-second.php', {displaylikepost: likepost});
						});
						$('.{$row['dislikepost']}_post').click(function(){
							var dislikepost = $('#{$row['dislikepost']}_dislike').val();
							$.post('dislike-post.php', {dislikepost1: dislikepost});
							$('#{$row['dislikepost']}_display_dislike').load('display-dislike-post.php', {displaydislikepost: dislikepost});
							$('#{$row['dislikepost']}_display_dislike').load('display-dislike-post-second.php', {displaydislikepost: dislikepost});
						});
						$('.{$row['rvforpost']}_click').click(function(){
							var comment_name = $('.{$row['rvforpost']}_name').val();
							var comment_text = $('.{$row['rvforpost']}_text').val();
							var comment_holder = $('#{$row['rvforpost']}_holder').val();
							if(comment_name == '' || comment_text == ''){
								
							}else{
								$.post('comment-post.php', {comment_name1: comment_name, comment_text1: comment_text, comment_holder1: comment_holder});
								$('.{$row['rvforpost']}_comment').load('display-comment-post.php', {displaycommentpost: comment_holder});
								$('.{$row['rvforpost']}_comment').load('display-comment-post-second.php', {displaycommentpost: comment_holder});
								$('#comment-former')[0].reset();
							}
						});
					});
				</script>";
				echo "<div style='margin-top:-10%; margin-left:-10%; margin-bottom:10%;' id='stories'>";
				echo "<p id='name'><a href='profile-username.php?username={$row['username']}'>{$row['username']}</a></p><p id='date'>{$row['dtofpost']}</p>";
				echo "<p id='story-teller'>{$row['post']}</p>";
				echo "<div id='like'>
						<div id='like-form'>
							<form method='POST'>
							<input id='{$row['likepost']}_like' type='hidden' value='{$row['likepost']}' name='like'>
							<input class='{$row['likepost']}_post' id='comment-submission-like' type='button' value='LIKE'><br>
							";
					$liker = $this->dbconnection()->query("SELECT count(*) FROM {$row['likepost']}");
					while($rowli = $liker->fetch()){
						echo "<span id='{$row['likepost']}_display_like'>{$rowli[0]} <br>likes</span>";
					}
				echo "</form>
						</div>
						<div id='dislike-form'>
							<form method='POST'>
							<input id='{$row['dislikepost']}_dislike' type='hidden' value='{$row['dislikepost']}' name='dislike'>
							<input class='{$row['dislikepost']}_post' id='comment-submission-like' type='button' value='DISLIKE'><br>
						";
					$disliker = $this->dbconnection()->query("SELECT count(*) FROM {$row['dislikepost']}");
					while($rowdis = $disliker->fetch()){
						echo "<span id='{$row['dislikepost']}_display_dislike'>{$rowdis[0]} <br>dislikes</span>";
					}
				echo "</form>
						</div>
					</div>
					<div style='clear:both;'></div><br>
					<p id='comment-display'>Share this post on:
					<a href='https://www.facebook.com/sharer/sharer.php?u=https://planetofstories.com/profile-username.php?username={$row['username']}&quote={$row['post']}' target='__blank'><img class='social' src='images/facebook-share.png' alt='facebook'></a>
					<a href='https://www.twitter.com/intent/tweet?text={$row['post']} For more visit https://planetofstories.com/profile-username.php?username={$row['username']}' target='__blank'><img class='social' src='images/twitter-share.png' alt='twitter'></a>
					<a href='whatsapp://send?text={$row['post']} For more visit  https://planetofstories.com/profile-username.php?username={$row['username']}' target='__blank'><img class='social' src='images/whatsapp-share.png' alt='whatsapp'></a>
					</p>
				";
				echo "<div class='{$row['rvforpost']}_comment'>";
				$comment = $this->dbconnection()->query("SELECT name, comment FROM {$row['rvforpost']} ORDER BY id DESC LIMIT 5");
				while($rowtwo = $comment->fetch()){
					echo "<p id='comment-display'><span>{$rowtwo['name']}</span><br><br>{$rowtwo['comment']}</p>";
				}
				echo "</div>";
				echo "<form id='{$row['rvforpost']}_comment-former' method='POST'>
						<input class='{$row['rvforpost']}_name' id='names' type='text' placeholder='name' name='names' required><br><br>
						<textarea class='{$row['rvforpost']}_text' id='comments' placeholder='comment here' name='comments' required></textarea><br>
						<input id='{$row['rvforpost']}_holder' type='hidden' value='{$row['rvforpost']}' name='holder'>
						<input class='{$row['rvforpost']}_click' id='comment-submission' type='button' value='POST'>
					</form>";
				echo "</div>";
			}
		}else{
			$_SESSION['notfoundstory'] = "not found story";
			echo "<script>location.href = 'search.php';</script>";
		}
	}
	public function Comment($name, $comment, $holder){
		if(!empty($_SERVER["HTTP_CLIENT_IP"])){
			//check for ip from share internet
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
			// Check for the Proxy User
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}else{
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		$com = $this->dbconnection()->prepare("INSERT INTO {$holder} (name, comment, ip) VALUES (?, ?, ?)");
		$com->execute([$name, $comment, $ip]);
	}
	public function CommentDisplay($holder){
		$comments = $this->dbconnection()->query("SELECT name, comment FROM {$holder} ORDER BY id DESC LIMIT 5");
		while($rowt = $comments->fetch()){
			echo "<p id='comment-display'><span>{$rowt['name']}</span><br><br>{$rowt['comment']}</p>";
		}
		echo "<script>alert('your comment was submitted');</script>";
	}
	public function CommentDisplaySecond($holder){
		$comments = $this->dbconnection()->query("SELECT name, comment FROM {$holder} ORDER BY id DESC LIMIT 5");
		while($rowt = $comments->fetch()){
			echo "<p id='comment-display'><span>{$rowt['name']}</span><br><br>{$rowt['comment']}</p>";
		}
	}
	public function LoadMorePost($load){
		$load_post = $this->dbconnection()->query("SELECT username, post, rvforpost, likepost, dislikepost, dtofpost FROM post ORDER BY id DESC LIMIT {$load}");
		while($row = $load_post->fetch()){
			echo "<div id='stories'>";
			echo "<p id='name'>{$row['username']}</p><p id='date'>{$row['dtofpost']}</p>";
			echo "<p id='story-teller'>" . ucwords($row['storytype']) . "<br><br>{$row['post']}</p>";
			echo "<div id='like'>
						<div id='like-form'>
							<form method='POST'>
							<input id='holder-like' type='hidden' value='{$row['likepost']}' name='like'>
							<input class='like-post' id='comment-submission-like' type='submit' value='LIKE'><br>
							";
					$liker = $this->dbconnection()->query("SELECT count(*) FROM {$row['likepost']}");
					while($rowli = $liker->fetch()){
						echo "<span>{$rowli[0]} <br>likes</span>";
					}
				echo "</form>
						</div>
						<div id='dislike-form'>
							<form method='POST'>
							<input id='holder-dislike' type='hidden' value='{$row['dislikepost']}' name='dislike'>
							<input class='dislike-post' id='comment-submission-like' type='submit' value='DISLIKE'><br>
						";
					$disliker = $this->dbconnection()->query("SELECT count(*) FROM {$row['dislikepost']}");
					while($rowdis = $disliker->fetch()){
						echo "<span>{$rowdis[0]} <br>dislikes</span>";
					}
				echo "</form>
						</div>
					</div>
					<div style='clear:both;'></div><br>
				";
			$comment = $this->dbconnection()->query("SELECT name, comment FROM {$row['rvforpost']} ORDER BY id DESC LIMIT 5");
			while($rowtwo = $comment->fetch()){
				echo "<p id='comment-display'><span>{$rowtwo['name']}</span><br><br>{$rowtwo['comment']}</p>";
			}
			echo "<form method='POST'>
						<input id='names' type='text' placeholder='name' name='names' required><br><br>
						<textarea id='comments' placeholder='comment here' name='comments' required></textarea><br>
						<input id='holder-for-id' type='hidden' value='{$row['rvforpost']}' name='holder'>
						<input id='comment-submission' type='submit' value='POST'>
					</form>";
			echo "</div>";
		}
	}
	public function LoadMorePostForUsername($load, $u_s){
		$load_post = $this->dbconnection()->query("SELECT username, post, rvforpost, likepost, dislikepost, dtofpost FROM post WHERE username = {$this->dbconnection()->quote($u_s)} ORDER BY id DESC LIMIT {$load}");
		while($row = $load_post->fetch()){
			echo "<div style='margin-top:-10%; margin-left:-10%; margin-bottom:10%;' id='stories'>";
			echo "<p id='name'>{$row['username']}</p><p id='date'>{$row['dtofpost']}</p>";
			echo "<p id='story-teller'>" . ucwords($row['storytype']) . "<br><br>{$row['post']}</p>";
			echo "<div id='like'>
						<div id='like-form'>
							<form method='POST'>
							<input id='holder-like' type='hidden' value='{$row['likepost']}' name='like'>
							<input class='like-post' id='comment-submission-like' type='submit' value='LIKE'><br>
							";
					$liker = $this->dbconnection()->query("SELECT count(*) FROM {$row['likepost']}");
					while($rowli = $liker->fetch()){
						echo "<span>{$rowli[0]} <br>likes</span>";
					}
				echo "</form>
						</div>
						<div id='dislike-form'>
							<form method='POST'>
							<input id='holder-dislike' type='hidden' value='{$row['dislikepost']}' name='dislike'>
							<input class='dislike-post' id='comment-submission-like' type='submit' value='DISLIKE'><br>
						";
					$disliker = $this->dbconnection()->query("SELECT count(*) FROM {$row['dislikepost']}");
					while($rowdis = $disliker->fetch()){
						echo "<span>{$rowdis[0]} <br>dislikes</span>";
					}
				echo "</form>
						</div>
					</div>
					<div style='clear:both;'></div><br>
				";
			$comment = $this->dbconnection()->query("SELECT name, comment FROM {$row['rvforpost']} ORDER BY id DESC LIMIT 5");
			while($rowtwo = $comment->fetch()){
				echo "<p id='comment-display'><span>{$rowtwo['name']}</span><br><br>{$rowtwo['comment']}</p>";
			}
			echo "<form method='POST'>
						<input id='names' type='text' placeholder='name' name='names' required><br><br>
						<textarea id='comments' placeholder='comment here' name='comments' required></textarea><br>
						<input id='holder-for-id' type='hidden' value='{$row['rvforpost']}' name='holder'>
						<input id='comment-submission' type='submit' value='POST'>
					</form>";
			echo "</div>";
		}
	}
	public function LoadMorePostForStoryType($load, $u_s){
		$load_post = $this->dbconnection()->query("SELECT username, post, rvforpost, likepost, dislikepost, dtofpost, storytype FROM post WHERE storytype = {$this->dbconnection()->quote($u_s)} ORDER BY id DESC LIMIT {$load}");
		while($row = $load_post->fetch()){
			echo "<div style='margin-top:-10%; margin-left:-10%; margin-bottom:10%;' id='stories'>";
			echo "<p id='name'>{$row['username']}</p><p id='date'>{$row['dtofpost']}</p>";
			echo "<p id='story-teller'>{$row['post']}</p>";
			echo "<div id='like'>
						<div id='like-form'>
							<form method='POST'>
							<input id='holder-like' type='hidden' value='{$row['likepost']}' name='like'>
							<input class='like-post' id='comment-submission-like' type='submit' value='LIKE'><br>
							";
					$liker = $this->dbconnection()->query("SELECT count(*) FROM {$row['likepost']}");
					while($rowli = $liker->fetch()){
						echo "<span>{$rowli[0]} <br>likes</span>";
					}
				echo "</form>
						</div>
						<div id='dislike-form'>
							<form method='POST'>
							<input id='holder-dislike' type='hidden' value='{$row['dislikepost']}' name='dislike'>
							<input class='dislike-post' id='comment-submission-like' type='submit' value='DISLIKE'><br>
						";
					$disliker = $this->dbconnection()->query("SELECT count(*) FROM {$row['dislikepost']}");
					while($rowdis = $disliker->fetch()){
						echo "<span>{$rowdis[0]} <br>dislikes</span>";
					}
				echo "</form>
						</div>
					</div>
					<div style='clear:both;'></div><br>
				";
			$comment = $this->dbconnection()->query("SELECT name, comment FROM {$row['rvforpost']} ORDER BY id DESC LIMIT 5");
			while($rowtwo = $comment->fetch()){
				echo "<p id='comment-display'><span>{$rowtwo['name']}</span><br><br>{$rowtwo['comment']}</p>";
			}
			echo "<form method='POST'>
						<input id='names' type='text' placeholder='name' name='names' required><br><br>
						<textarea id='comments' placeholder='comment here' name='comments' required></textarea><br>
						<input id='holder-for-id' type='hidden' value='{$row['rvforpost']}' name='holder'>
						<input id='comment-submission' type='submit' value='POST'>
					</form>";
			echo "</div>";
		}
	}
	public function DisplayProfileInfo($user){
		$display_profile = $this->dbconnection()->prepare("SELECT * FROM usersinfo WHERE username = ?");
		$display_profile->execute([$user]);
		while($row = $display_profile->fetch()){
			echo "<p>Full Name: {$row['fullname']}</p>
						<p>Email: {$row['email']}</p>
						<p>Username: {$row['username']}</p>
						<p>Phone number: {$row['phonenumber']}</p>
						<p>Gender: {$row['gender']}</p>
						<p>Age: {$row['age']}</p>
						<p>Hobby: {$row['hobby']}</p>
						<p>Country: {$row['country']}</p>
						<p>State: {$row['state']}</p>
						<p>Local Government: {$row['localgovt']}</p>
						<p>City: {$row['city']}</p>
						<p>Bio: {$row['bio']}</p>";
		}
	}
	public function DisplayProfileInfoForUsername($user){
		$display_profile = $this->dbconnection()->prepare("SELECT * FROM usersinfo WHERE username = ?");
		$display_profile->execute([$user]);
		while($row = $display_profile->fetch()){
			echo "<p>Full Name: {$row['fullname']}</p>
						<p>Gender: {$row['gender']}</p>
						<p>Age: {$row['age']}</p>
						<p>Hobby: {$row['hobby']}</p>
						<p>Country: {$row['country']}</p>
						<p>State: {$row['state']}</p>
						<p>Local Government: {$row['localgovt']}</p>
						<p>City: {$row['city']}</p>
						<p>Bio: {$row['bio']}</p>";
		}
	}
	public function UpdateProfile($user){
		$display_profile = $this->dbconnection()->prepare("SELECT * FROM usersinfo WHERE username = ?");
		$display_profile->execute([$user]);
		while($row = $display_profile->fetch()){
			echo "<form method='POST'>
							Full Name:<br><input value='{$row['fullname']}' class='reg-2' id='fullname' type='text' name='fullname' required><br>
							Email:<br><input value='{$row['email']}' class='reg-2' id='email' type='email' name='email' required><br>
							Username:<br><input value='{$row['username']}' class='reg-2' id='username' type='text' name='username' required><br>
							Phone number:<br><input value='{$row['phonenumber']}' class='reg-2' id='phonenumber' type='text' name='phonenumber' required><br>
							Gender:<br><select class='reg-2' id='gender' name='gender' required><option value=''>select your gender</option><option value='male'>Male</option><option value='female'>Female</option></select><br>
							Age:<br><input value='{$row['age']}' class='reg-2' id='age' type='text' name='age' required><br>
							Hobby:<br><input value='{$row['hobby']}' class='reg-2' id='hobby' type='text' name='hobby' required><br>
							Country:<br><input value='{$row['country']}' class='reg-2' id='country' type='text' name='country' required><br>
							State:<br><input value='{$row['state']}' class='reg-2' id='state' type='text' name='state' required><br>
							Local Government:<br><input value='{$row['localgovt']}' class='reg-2' id='localgovt' type='text' name='localgovt' required><br>
							City:<br><input value='{$row['city']}' class='reg-2' id='city' type='text' name='city' required><br>
							Bio:<br><textarea class='reg-bio' id='bio' name='poststory' placeholder='tell us something about you' required>{$row['bio']}</textarea><br>
							<input class='update-profile' name='submission' id='submitpost' type='submit' value='UPDATE'>
						</form>";
		}
		if(isset($_POST['submission'])){
			$com = $this->dbconnection()->prepare("UPDATE usersinfo SET fullname = ?, email = ?, username = ?, phonenumber = ?,
			gender = ?, age = ?, hobby = ?, country = ?, state = ?, localgovt = ?, city = ?, bio = ? WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
			$com->execute([$_POST['fullname'], $_POST['email'], $_SESSION['username'], $_POST['phonenumber'], $_POST['gender'], $_POST['age'], 
			$_POST['hobby'], $_POST['country'], $_POST['state'], $_POST['localgovt'], $_POST['city'], $_POST['poststory']]);
			$com = $this->dbconnection()->prepare("UPDATE verification SET username = ? WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
			$com->execute([$_SESSION['username']]);
			echo "<script>location.href = 'account.php';</script>";
		}
	}
	public function PasswordChange($newp, $oldp){
		$op = $this->dbconnection()->query("SELECT password FROM verification WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
		if($op->fetchColumn() === $oldp){
			$pc = $this->dbconnection()->prepare("UPDATE verification SET password = ? WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
			$pc->execute([$newp]);
			session_destroy();
			echo "<script>location.href = 'login.php';</script>";
		}else{
			echo "<p id='form-submission'>Wrong Old Password</p>";
		}
	}
	public function YearlyAndMonthlyPost(){
		$ymp = $this->dbconnection()->query("SELECT yearofregistration, yearlypostcount, monthlypostcount FROM usersinfo WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
		while($row = $ymp->fetch()){
			if($row['yearlypostcount'] == 0){
				echo "<p>You have posted {$row['yearlypostcount']} stories since 20{$row['yearofregistration']}.</p>";
			}elseif($row['yearlypostcount'] == 1){
				echo "<p>You have posted {$row['yearlypostcount']} story since 20{$row['yearofregistration']}.</p>";
			}else{
				echo "<p>You have posted {$row['yearlypostcount']} stories since 20{$row['yearofregistration']}.</p>";
			}
			if($row['monthlypostcount'] == 0){
				echo "<p>You have posted {$row['monthlypostcount']} stories this month.</p>";
			}elseif($row['monthlypostcount'] == 1){
				echo "<p>You have posted {$row['monthlypostcount']} story this month.</p>";
			}else{
				echo "<p>You have posted {$row['monthlypostcount']} stories this month.</p>";
			}
		}
		echo "<p>" . $this->CheckSubscribersAccount() . "</p>";
	}
	public function UpdateOrDeletePost(){
		$uodp = $this->dbconnection()->query("SELECT * FROM post WHERE username = {$this->dbconnection()->quote($_SESSION['username'])} ORDER BY id DESC LIMIT 5");
		while($row = $uodp->fetch()){
			echo "<form method='POST'>
						<textarea id='poster-new' name='poststory' required>{$row['post']}</textarea><br><br>
						<input id='submitpost' type='hidden' name='update' value='{$row['id']}'>
						<input id='submitpost' name='submitupdate' type='submit' value='UPDATE'>
					</form>
					<form method='POST'>
						<input id='submitpost' type='hidden' name='delete' value='{$row['id']}'>
						<input class='delete-post' name='submitdelete' id='submitpost' type='submit' value='DELETE'>
					</form><br>";
		}
		if(isset($_POST['submitupdate'])){
			$uodp = $this->dbconnection()->prepare("UPDATE post SET post = ? WHERE id = {$this->dbconnection()->quote($_POST['update'])} AND username = {$this->dbconnection()->quote($_SESSION['username'])}");
			$uodp->execute([$_POST['poststory']]);
			echo "<script>location.href = 'account.php';</script>";
		}
		if(isset($_POST['submitdelete'])){
			$remove_comment = $this->dbconnection()->query("SELECT rvforpost FROM post WHERE id = {$this->dbconnection()->quote($_POST['delete'])} AND username = {$this->dbconnection()->quote($_SESSION['username'])} LIMIT 1");
			while($row = $remove_comment->fetch()){
				$this->dbconnection()->query("DROP TABLE {$row['rvforpost']}");
			}
			$remove_like = $this->dbconnection()->query("SELECT likepost FROM post WHERE id = {$this->dbconnection()->quote($_POST['delete'])} AND username = {$this->dbconnection()->quote($_SESSION['username'])} LIMIT 1");
			while($row = $remove_like->fetch()){
				$this->dbconnection()->query("DROP TABLE {$row['likepost']}");
			}
			$remove_dislike = $this->dbconnection()->query("SELECT dislikepost FROM post WHERE id = {$this->dbconnection()->quote($_POST['delete'])} AND username = {$this->dbconnection()->quote($_SESSION['username'])} LIMIT 1");
			while($row = $remove_dislike->fetch()){
				$this->dbconnection()->query("DROP TABLE {$row['dislikepost']}");
			}
			$uodp = $this->dbconnection()->query("DELETE FROM post WHERE id = {$this->dbconnection()->quote($_POST['delete'])} AND username = {$this->dbconnection()->quote($_SESSION['username'])}");
			$this->dbconnection()->query("UPDATE usersinfo SET yearlypostcount = yearlypostcount - 1 WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
			$this->dbconnection()->query("UPDATE usersinfo SET monthlypostcount = monthlypostcount - 1 WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
			echo "<script>location.href = 'account.php';</script>";
		}
	}
	public function LoadMorePostEdit($load){
		$load_post_edit = $this->dbconnection()->query("SELECT * FROM post WHERE username = {$this->dbconnection()->quote($_SESSION['username'])} ORDER BY id DESC LIMIT {$load}");
		while($row = $load_post_edit->fetch()){
			echo "<form method='POST'>
						<textarea id='poster-new' name='poststory' required>{$row['post']}</textarea><br><br>
						<input id='submitpost' type='hidden' name='update' value='{$row['id']}'>
						<input id='submitpost' name='submitupdate' type='submit' value='UPDATE'>
					</form>
					<form method='POST'>
						<input id='submitpost' type='hidden' name='delete' value='{$row['id']}'>
						<input class='delete-post' name='submitdelete' id='submitpost' type='submit' value='DELETE'>
					</form><br>";
		}
	}
	public function Chat($message){
		if(!empty($_SERVER["HTTP_CLIENT_IP"])){
			//check for ip from share internet
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
			// Check for the Proxy User
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}else{
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		date_default_timezone_set("Africa/Lagos");
		$dateandtime = date('d/m/y h:i:s a', time());
		$chat_insert = $this->dbconnection()->prepare("INSERT INTO {$_SESSION['username']}_chat (name, message, dtofchat, ip) VALUES (?, ?, ?, ?)");
		$chat_insert->execute([$_SESSION['username'], $message, $dateandtime, $ip]);
	}
	public function ChatUser($message, $nameofuser, $user_search_name){
		if(!empty($_SERVER["HTTP_CLIENT_IP"])){
			//check for ip from share internet
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
			// Check for the Proxy User
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}else{
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		date_default_timezone_set("Africa/Lagos");
		$dateandtime = date('d/m/y h:i:s a', time());
		$chat_insert = $this->dbconnection()->prepare("INSERT INTO {$user_search_name}_chat (name, message, dtofchat, ip) VALUES (?, ?, ?, ?)");
		$chat_insert->execute([$nameofuser, $message, $dateandtime, $ip]);
		$chat_sender = $this->dbconnection()->query("SELECT email FROM usersinfo WHERE username = {$this->dbconnection()->quote($_SESSION['search-username'])}");
		while($row = $chat_sender->fetch()){
			$mail = new PHPMailer;
			$mail->setFrom('no-reply@planetofstories.com', 'Planet of Stories');
			$mail->addAddress($row['email']);
			$mail->isHTML(true);
			$mail->Subject  = 'Messages';
			$mail->Body     = "<h1>Chat</h1><p>You have receive a message from your fans, login to check it out - https://planetofstories.com/login.php</p>";
			if(!$mail->send()) {
				echo '<p id="formsubmission">Message was not sent.</p>';
				echo 'Mailer error: ' . $mail->ErrorInfo;
			}else{
				passthru;
			}
		}
	}
	public function DisplayChat(){
		$chat_display = $this->dbconnection()->query("SELECT * FROM {$_SESSION['username']}_chat ORDER BY id DESC LIMIT 20");
		while($row = $chat_display->fetch()){
			echo "<p id='realchat'>
				<span id='name-user'>{$row['name']}</span>
				<span id='date-2'>{$row['dtofchat']}</span>
				<br>
				<br>
				<span id='mess'>{$row['message']}</span>
			</p>";
		}
	}
	public function DisplayChatUsername($user_search_name){
		$chat_display = $this->dbconnection()->query("SELECT * FROM {$user_search_name}_chat ORDER BY id DESC LIMIT 20");
		while($row = $chat_display->fetch()){
			echo "<p id='realchat'>
				<span id='name-user'>{$row['name']}</span>
				<span id='date-2'>{$row['dtofchat']}</span>
				<br>
				<br>
				<span id='mess'>{$row['message']}</span>
			</p>";
		}
	}
	public function DeleteMessages(){
		$this->dbconnection()->query("TRUNCATE {$_SESSION['username']}_chat");
	}
	public function DeleteAccount(){
		$deleted_comment = $this->dbconnection()->query("SELECT rvforpost FROM post WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
		while($row = $deleted_comment->fetch()){
			$this->dbconnection()->query("DROP TABLE {$row['rvforpost']}");
		}
		$deleted_like = $this->dbconnection()->query("SELECT likepost FROM post WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
		while($row = $deleted_like->fetch()){
			$this->dbconnection()->query("DROP TABLE {$row['likepost']}");
		}
		$deleted_dislike = $this->dbconnection()->query("SELECT dislikepost FROM post WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
		while($row = $deleted_dislike->fetch()){
			$this->dbconnection()->query("DROP TABLE {$row['dislikepost']}");
		}
		$this->dbconnection()->query("DROP TABLE {$_SESSION['username']}_chat");
		$this->dbconnection()->query("DROP TABLE {$_SESSION['username']}_post");
		$this->dbconnection()->query("DROP TABLE {$_SESSION['username']}_subscribe");
		$this->dbconnection()->query("DELETE FROM post WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
		$this->dbconnection()->query("DELETE FROM usersinfo WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
		$this->dbconnection()->query("DELETE FROM verification WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
		session_destroy();
		echo "<script>location.href = '/';</script>";
	}
	public function CheckUsernameMatchEmail($username, $email){
		$checkume = $this->dbconnection()->prepare("SELECT username FROM usersinfo WHERE email = ?");
		$checkume->execute([$email]);
		if($checkume->fetchColumn() === $username){
			$_SESSION['username'] = $username;
			$_SESSION['email'] = $email;
			$mail = new PHPMailer;
			$mail->setFrom('no-reply@planetofstories.com', 'Planet of Stories');
			$mail->addAddress($_SESSION['email']);
			$mail->isHTML(true);
			$randomvalue = rand(1000, 5000);
			$mail->Subject  = 'Forgot Password';
			$mail->Body     = "<h1>Forgot Password</h1><p>Copy the numbers down below to confirm<br> your new password</p><h2>{$randomvalue}</h2>";
			$_SESSION['randomnumber'] = $randomvalue;
			if(!$mail->send()) {
			  echo '<p id="formsubmission">Message was not sent.</p>';
			  echo 'Mailer error: ' . $mail->ErrorInfo;
			}else{
				passthru;
			}
		}else{
			echo "<p id='form-submission'>Username do not match the email you entered</p>";
		}
	}
	public function ResendCode(){
		$mail = new PHPMailer;
		$mail->setFrom('no-reply@planetofstories.com', 'Planet of Stories');
		$mail->addAddress($_SESSION['email']);
		$mail->isHTML(true);
		$randomvalue = rand(1000, 5000);
		$mail->Subject  = 'Forgot Password';
		$mail->Body     = "<h1>Forgot Password</h1><p>Copy the numbers down below to confirm<br> your new password</p><h2>{$randomvalue}</h2>";
		$_SESSION['randomnumber'] = $randomvalue;
		if(!$mail->send()) {
			echo '<p id="formsubmission">Message was not sent.</p>';
			echo 'Mailer error: ' . $mail->ErrorInfo;
		}else{
			passthru;
		}
	}
	public function CodeChecker($code){
		if($_SESSION['randomnumber'] == $code){
			$_SESSION['codematch'] = "codematch";
			unset($_SESSION['randomnumber']);
		}else{
			echo "<p id='form-submission'>Invalid code</p>";
		}
	}
	public function PasswordForgotChange($pass){
		$psc = $this->dbconnection()->prepare("UPDATE verification SET password = ? WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
		$psc->execute([$pass]);
		session_destroy();
		echo "<script>location.href = 'login.php';</script>";
	}
	public function Subscribe($name, $email){
		$check_subscriber = $this->dbconnection()->query("SELECT email FROM {$_SESSION['search-username']}_subscribe WHERE email = {$this->dbconnection()->quote($email)}");
		if($check_subscriber->fetchColumn() === $email){
			echo "<script>alert('you have already subscribed to {$_SESSION['search-username']} stories')</script>";
		}else{
			$subscriber = $this->dbconnection()->prepare("INSERT INTO {$_SESSION['search-username']}_subscribe (name, email) VALUES (?, ?)");
			$subscriber->execute([$name, $email]);
			$subscribe_sender = $this->dbconnection()->query("SELECT email FROM usersinfo WHERE username = {$this->dbconnection()->quote($_SESSION['search-username'])}");
			while($row = $subscribe_sender->fetch()){
				$mail = new PHPMailer;
				$mail->setFrom('no-reply@planetofstories.com', 'Planet of Stories');
				$mail->addAddress($row['email']);
				$mail->isHTML(true);
				$mail->Subject  = 'Subscribers';
				$mail->Body     = "<h1>Story Subscriber</h1><p>A person have just subscribe to your story channel, login to check it out - https://planetofstories.com/login.php</p>";
				if(!$mail->send()) {
					echo '<p id="formsubmission">Message was not sent.</p>';
					echo 'Mailer error: ' . $mail->ErrorInfo;
				}else{
					passthru;
				}
			}
			echo "<script>alert('you have subscribe to {$_SESSION['search-username']} stories')</script>";
			echo "<script>location.href = 'profile-username.php';</script>";
		}
	}
	public function Unsubscribe($email){
		$check_unsubscriber = $this->dbconnection()->query("SELECT email FROM {$_SESSION['search-username']}_subscribe WHERE email = {$this->dbconnection()->quote($email)}");
		if($check_unsubscriber->fetchColumn() === $email){
			$this->dbconnection()->query("DELETE FROM {$_SESSION['search-username']}_subscribe WHERE email = {$this->dbconnection()->quote($email)}");
			echo "<script>alert('you have unsubscribe to {$_SESSION['search-username']} stories')</script>";
			echo "<script>location.href = 'profile-username.php';</script>";
		}else{
			echo "<script>alert('No one with that email address have subscribe before')</script>";
		}
	}
	public function CheckSubscribers(){
		$check_subscribers = $this->dbconnection()->query("SELECT count(*) FROM {$_SESSION['search-username']}_subscribe");
		while($row = $check_subscribers->fetch()){
			echo ucfirst($_SESSION['search-username']) . " Subscribers: " . $row[0];
		}
	}
	public function CheckSubscribersAccount(){
		$check_subscribers = $this->dbconnection()->query("SELECT count(*) FROM {$_SESSION['username']}_subscribe");
		while($row = $check_subscribers->fetch()){
			echo "Subscribers: " . $row[0];
		}
	}
	public function SendEmailVerification($e, $hash){
		$mail = new PHPMailer;
		$mail->setFrom('no-reply@planetofstories.com', 'Planet of Stories');
		$mail->addAddress($e);
		$mail->isHTML(true);
		$mail->Subject  = 'Account Verification';
		$mail->Body     = "<h2>Verification Link</h2><p>Click the link here to activate your account - <br> https://www.planetofstories.com/register.php?hash={$hash}</p>";
		if(!$mail->send()){
			echo '<p id="formsubmission">Message was not sent.</p>';
			echo 'Mailer error: ' . $mail->ErrorInfo;
		}else{
			passthru;
		}
	}
	public function VerifySuccess($check_hash){
		$c_hash = $this->dbconnection()->query("SELECT hash FROM usersinfo WHERE hash = {$this->dbconnection()->quote($check_hash)}");
		if($c_hash->fetchColumn() === $check_hash){
			$c_activate = $this->dbconnection()->query("SELECT activation FROM usersinfo WHERE hash = {$this->dbconnection()->quote($check_hash)}");
			if($c_activate->fetchColumn() == 1){
				echo "<p>You have already activated your account</p>";
			}else{
				$this->dbconnection()->query("UPDATE usersinfo SET activation = 1 WHERE hash = {$this->dbconnection()->quote($check_hash)}");
				$_SESSION['ac'] = "activated account";
				echo "<script>location.href = 'login.php';</script>";
			}
		}else{
			echo "<p>Hash key not Found</p>";
		}
	}
	public function LikePost($like_loc){
		if(isset($_SESSION['username'])){
			$check_if_like = $this->dbconnection()->query("SELECT name FROM {$like_loc} WHERE name = {$this->dbconnection()->quote($_SESSION['username'])}");
			if($check_if_like->fetchColumn() == $_SESSION['username']){
				passthru;
			}else{
				$like = $this->dbconnection()->prepare("INSERT INTO {$like_loc}(name) VALUES(?)");
				$like->execute([$_SESSION['username']]);
				$like_sender = $this->dbconnection()->query("SELECT email FROM usersinfo WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
				while($row = $like_sender->fetch()){
					$mail = new PHPMailer;
					$mail->setFrom('no-reply@planetofstories.com', 'Planet of Stories');
					$mail->addAddress($row['email']);
					$mail->isHTML(true);
					$mail->Subject  = 'Like Post';
					$mail->Body     = "<h1>Post</h1><p>Someone have just liked one of your stories, visit https://planetofstories.com/profile-username.php?username={$_SESSION['username']} and check it out</p>";
					if(!$mail->send()){
						echo '<p id="formsubmission">Message was not sent.</p>';
						echo 'Mailer error: ' . $mail->ErrorInfo;
					}else{
						passthru;
					}
				}
			}
		}
	}
	public function DisplayLikePost($dlp){
		if(isset($_SESSION['username'])){
			$display_like = $this->dbconnection()->query("SELECT count(*) FROM {$dlp}");
			while($rowl = $display_like->fetch()){
				echo $rowl[0] . "<br>likes";
			}
		}else{
			echo "<script>alert('you have to login to be able to like a story');</script>";
		}
	}
	public function DisplayLikePostSecond($dlp){
		$display_like = $this->dbconnection()->query("SELECT count(*) FROM {$dlp}");
		while($rowl = $display_like->fetch()){
			echo $rowl[0] . "<br>likes";
		}
	}
	public function DisLikePost($dislike_loc){
		if(isset($_SESSION['username'])){
			$check_if_dislike = $this->dbconnection()->query("SELECT name FROM {$dislike_loc} WHERE name = {$this->dbconnection()->quote($_SESSION['username'])}");
			if($check_if_dislike->fetchColumn() == $_SESSION['username']){
				passthru;
			}else{
				$dislike = $this->dbconnection()->prepare("INSERT INTO {$dislike_loc}(name) VALUES(?)");
				$dislike->execute([$_SESSION['username']]);
				$dislike_sender = $this->dbconnection()->query("SELECT email FROM usersinfo WHERE username = {$this->dbconnection()->quote($_SESSION['username'])}");
				while($row = $dislike_sender->fetch()){
					$mail = new PHPMailer;
					$mail->setFrom('no-reply@planetofstories.com', 'Planet of Stories');
					$mail->addAddress($row['email']);
					$mail->isHTML(true);
					$mail->Subject  = 'DisLike Post';
					$mail->Body     = "<h1>Post</h1><p>Someone have just disliked one of your stories, visit https://planetofstories.com/profile-username.php?username={$_SESSION['username']} and check it out</p>";
					if(!$mail->send()){
						echo '<p id="formsubmission">Message was not sent.</p>';
						echo 'Mailer error: ' . $mail->ErrorInfo;
					}else{
						passthru;
					}
				}
			}
		}
	}
	public function DisplayDisLikePost($ddlp){
		if(isset($_SESSION['username'])){
			$display_dislike = $this->dbconnection()->query("SELECT count(*) FROM {$ddlp}");
			while($rowd = $display_dislike->fetch()){
				echo $rowd[0] . "<br>dislikes";
			}
		}else{
			echo "<script>alert('you have to login to be able to dislike a story');</script>";
		}
	}
	public function DisplayDisLikePostSecond($ddlp){
		$display_dislike = $this->dbconnection()->query("SELECT count(*) FROM {$ddlp}");
		while($rowd = $display_dislike->fetch()){
			echo $rowd[0] . "<br>dislikes";
		}
	}
}
$mfs = new MoneyForStory();
?>