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
		return "Error: ".mysqli_error($link);
	}else{
		return $stmt;
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

	// $link = '';

	if (!isset($_SESSION['uname'])){
		?>	
		<form action="http://localhost/cs304/gallerydb.php" method="post">
			Login:<input type="text" name="uname">
			Password:<input type="password" name="password">
			<input type="submit" value="Login">
		</form><?php
	}

	if (isset($_POST['uname'])){
			$_SESSION['uname'] = $_POST['uname'];
			$_SESSION['password'] = $_POST['password'];
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
	<form action="http://localhost/cs304/gallerydb.php" method="get">
	<button name="aartist" type="submit" value="true">Add Artist</button>
	<button name="aclient" type="submit" value="true">Add Client</button>
	<button name="fartist" type="submit" value="true">Find Artist</button>
	<button name="client" type="submit" value="true">Find Client</button>
	</form>


	<?php

	// adding an artist

	if (isset($_GET['aartist']) || isset($_POST['aartistsql'])){ //either the get flag is set or the artist is being posted
		?>
		<form action='http://localhost/cs304/gallerydb.php' method='post'>
		First Name: <input type="text" name="afname"> 
		Last Name: <input type="text" name="alname"> <br>
		Street: <input type="text" name="astreet"> City:<input type="text" name="acity"><br>
		Province: <select name="aprovince">
					<option value="AB">Alberta</option>
					<option value="BC">British Columbia</option>
					<option value="MB">Manitoba</option>
					<option value="NB">New Brunswick</option>
					<option value="NL">Newfoundland and Labrador</option>
					<option value="NS">Nova Scotia</option>
					<option value="ON">Ontario</option>
					<option value="PE">Prince Edward Island</option>
					<option value="QC">Quebec</option>
					<option value="SK">Saskatchewan</option>
					<option value="NT">Northwest Territories</option>
					<option value="NU">Nunavut</option>
					<option value="YT">Yukon</option>
				</select> 
		Postal Code: <input type="text" name="apcode"><br>
		Email: <input type="text" name="aemail"><br>
		Phone: <input type="text" name="aphone"><br>
		Status: <select name="astatus">
					<option value="inactive">Inactive</option>
					<option value="active">Active</option>
				</select><br>
		<button name="aartistsql" type="submit" value="true">Add</button>
		</form>
		<?php
	}

	if (isset($_POST['aartistsql'])){
		$query = "insert into artists values('"
			.$_POST['afname']."','"
			.$_POST['alname']."','"
			.$_POST['astreet']."','"
			.$_POST['acity']."','"
			.$_POST['aprovince']."','"
			.$_POST['apcode']."','"
			.$_POST['aemail']."','"
			.$_POST['aphone']."','"
			.$_POST['astatus']."');";
		$success = executePlainSQL($link, $query);
		if ($success) {
			echo "Statement: <br>".$query."<br>executed successfully.";
		}
	}

	// adding a client

	if (isset($_GET['aclient']) || isset($_POST['aclientsql'])){ //either the get flag is set or the client is being posted
		?>
		<form action='http://localhost/cs304/gallerydb.php' method='post'>
		First Name: <input type="text" name="cfname"> 
		Last Name: <input type="text" name="clname"> <br>
		Street: <input type="text" name="cstreet"> City:<input type="text" name="ccity"><br>
		Province: <select name="cprovince">
					<option value="AB">Alberta</option>
					<option value="BC">British Columbia</option>
					<option value="MB">Manitoba</option>
					<option value="NB">New Brunswick</option>
					<option value="NL">Newfoundland and Labrador</option>
					<option value="NS">Nova Scotia</option>
					<option value="ON">Ontario</option>
					<option value="PE">Prince Edward Island</option>
					<option value="QC">Quebec</option>
					<option value="SK">Saskatchewan</option>
					<option value="NT">Northwest Territories</option>
					<option value="NU">Nunavut</option>
					<option value="YT">Yukon</option>
				</select>  Postal Code: <input type="text" name="cpcode"><br>
		Email: <input type="text" name="cemail"><br>
		Phone: <input type="text" name="cphone"><br>
		<button name="aclientsql" type="submit" value="true">Add</button>
		</form>
			<?php
	}

	if (isset($_POST['aclientsql'])){
		$query = "insert into clients values('"
			.$_POST['cfname']."','"
			.$_POST['clname']."','"
			.$_POST['cstreet']."','"
			.$_POST['ccity']."','"
			.$_POST['cprovince']."','"
			.$_POST['cpcode']."','"
			.$_POST['cemail']."','"
			.$_POST['cphone']."');";
		$success = executePlainSQL($link, $query);
		if ($success) {
			echo "Statement: <br>".$query."<br>executed successfully.";
		}
	}

	// finding an artist
	// finding a client


}
?>
</form>
</body>
</html>