<script type="text/javascript">
    $(document).ready(function() {
     $('#loading').hide();
	});
	window.onbeforeunload = function () { $('#loading').show(); } 
</script>
<div id="loading">
  <img id="loading-image" src="images/loading.gif" alt="Loading..." />
</div>
<?php
session_start();
?>
<div id="moneyforstory">
	<h1>PLANET OF STORIES</h1>
	<p><a class="links" href="/">HOME</a><a class="links" href="register.php">REGISTER</a><a class="links" href="login.php">LOGIN</a><a class="links" href="search.php">SEARCH</a><a class="links" href="news.php">NEWS</a><?php if(isset($_SESSION['username'])){ echo "<a class='links' href='account.php'>ACCOUNT</a>"; } ?></p>
</div>
<div id="moneyforstory2">		
	<h1>PLANETOFSTORIES</h1>
	<div id="menu-click">
		<p id="menu">MENU</p>
		<div id="menu-down">
			<p><a href="/">HOME</a></p>
			<p><a href="register.php">REGISTER</a></p>
			<p><a href="login.php">LOGIN</a></p>
			<p><a href="search.php">SEARCH</a></p>
			<p><a href="news.php">NEWS</a></p>
			<?php
				if(isset($_SESSION['username'])){
					echo "<p><a href='account.php'>ACCOUNT</a></p>";
				}
			?>
		</div>
	</div>
</div>
<br>
<br>
<br>