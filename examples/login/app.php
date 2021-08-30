<?php
session_start();
if(isset($_GET['logout'])) {
	session_destroy();
	header('Location: index.php');
	exit();
}
?>
<h1>Welcome <?php print $_SESSION['user']['DisplayName'] ?></h1>
<p><a href="app.php?logout=1">Logout</a></p>