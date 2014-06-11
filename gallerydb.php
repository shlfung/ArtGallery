<?php
session_start();
error_reporting(E_ALL);
if (!isset($_SESSION['created'])){
	$_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
	session_regenerate_id(true);
}

function executePlainSQL($link, $cmdStr){
	$stmt = mysqli_query($link, $cmdStr);
	if (!$stmt){
		echo "something went wrong";
	}
}
?>

<html>
<head>
	<title>Gallery DB</title>
</head>
<body>
	<h3>GalleryDB</h3>
	<?php

	$link = '';

	if (!isset($_SESSION['uname'])){
		?>	
		<form action="http://localhost/gallerydb.php" method="post">
			Login:<input type="text" name="uname">
			Password:<input type="password" name="password">
			<input type="submit" value="Login">
		</form><?php
	}

	if (isset($_POST['uname'])){
			$_SESSION['uname'] = $_POST['uname'];
			$_SESSION['password'] = $_POST['password'];
			$link = mysqli_connect('localhost:3306', $_SESSION['uname'], $_SESSION['password'], 'gallerydb');
			if ($link){
				die('Connect Error (' . mysqli_connect_errno() . ') '
            	. mysqli_connect_error());
			}
			if (!mysqli_options($link, MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 0')) {
    			die('Setting MYSQLI_INIT_COMMAND failed');
			}
			if (!mysqli_options($link, MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
    			die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
			}
			echo 'Success... ' . mysqli_get_host_info($link) . "<br><br>";
	}

	if (isset($_SESSION['uname'])){
			$link = mysqli_connect('localhost:3306', $_SESSION['uname'], $_SESSION['password'], 'gallerydb');
			if (!$link){
				die('Connect Error (' . mysqli_connect_errno() . ') '
            	. mysqli_connect_error());
			}
			if (!mysqli_options($link, MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 0')) {
    			die('Setting MYSQLI_INIT_COMMAND failed');
			}
			if (!mysqli_options($link, MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
    			die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
			}
	?>
	<form action="http://localhost/gallerydb.php" method="get">
	<button name="aartist" type="submit" value="true">Add Artist</button>
	<input type="submit" value="Find Client">
	<input type="submit" value="Find Artist">
	</form>

	<?php
	if (isset($_GET['aartist'])){?>
		<form action='http://localhost/gallerydb.php' method='post'>
		Artist Name:<input type="text" name="aname"><br>
		Studio Address:<input type="text" name="aaddress"><br>
		Email:<input type="text" name="aemail"><br>
		Phone:<input type="text" name="aphone"><br>
		Status:<input type="text" name="astatus"><br>
		<button name="aartistsql" type="submit" value="true">Add</button>
		</form>
		<?php
	}

	if (isset($_POST['aartistsql'])){
		$query = "insert into artists values('".$_POST['aname']."','"
			.$_POST['aaddress']."','".$_POST['aemail']."','".$_POST['aphone']."','".$_POST['astatus']."');";
		mysqli_query($link, $query);
	}

	// if (isset($_POST['uname'])){
	// 	$success = mysql_connect('localhost:3306', $_POST['uname'], $_POST['password']);
	// 	if (!$success){
	// 		die('You are not authorized:'.mysql_error());
	// 	}
	// 	echo "You did it! You are connected!";
	// 	mysql_close($success);
	// }

	if (isset($_GET['aclient'])){
		if ($_GET['aclient'] == "true"){
			?>
			<form action="http://localhost/gallerydb.php" method="post">
			<input type="text" name="aclient">
			<input type="submit" value="aclient">
			</form>
			<?php
		}
	}
}
?>
</form>
</body>
</html>