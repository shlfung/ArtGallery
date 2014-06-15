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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
body {
	background-color: #5984E8;
}
h1 {
	font-size: 70;
	color: #E3474A;
}
</style>
</head>
<body>
	<h1 align="center"> GalleryDB</h1>
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
	<form align="center" action="http://localhost/cs304/gallerydb.php" method="get">
	<button name="aartist" type="submit" value="true">Add Artist</button>
	<button name="aclient" type="submit" value="true">Add Client</button>
	<button name="fartist" type="submit" value="true">Find Artist</button>
	<button name="fclient" type="submit" value="true">Find Client</button>
	<button name="inventory" type="submit" value="true">Gallery Inventory</button>
	</form>


	<?php

	// adding an artist

	if (isset($_GET['inventory'])){
		$result = executePlainSQL($link, "SELECT s.fname as fname, s.lname as lname, a.title as title, sc.material as medium, a.price as price
		 								FROM supplies s, art a, sculpture sc
		 								WHERE s.serial_number = a.serial_number and s.serial_number = sc.serial_number
		 								UNION
		 								SELECT s.fname as fname, s.lname as lname, a.title as title, p.medium as medium, a.price as price
		 								FROM supplies s, art a, painting p
		 								WHERE s.serial_number = a.serial_number and s.serial_number = p.serial_number
		 								ORDER BY lname");
		echo '<table>';
		while ($row = mysqli_fetch_array($result)){
			echo '<tr>';
			echo '<td>'.$row['lname'].'</td>';
			echo '<td>'.$row['fname'].'</td>';
			echo '<td>'.$row['title'].'</td>';
			if (isset($row['material'])){
				echo '<td>'.$row['material'].'</td>';	
			}else {
				echo '<td>'.$row['medium'].'</td>';
			}
			echo '<td>'.$row['price'].'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}

	if (isset($_GET['aartist']) || isset($_POST['aartistsql'])){ //either the get flag is set or the artist is being posted
		?>
		<form align="center" action='http://localhost/cs304/gallerydb.php' method='post'>
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
		<form align="center" action='http://localhost/cs304/gallerydb.php' method='post'>
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


	
if (isset($_GET['fartist']) || isset($_POST['fartistsql'])){
	?>
	<form align="center" action='http://localhost/cs304/gallerydb.php' method='post'>
		Search by phone number: <input type="text" name="faphone"> <br>
		<button name="fartistsql" type="submit" value="true">Search</button>
	</form> 
		<?php
}

if (isset($_POST['fartistsql'])){
		$result = executePlainSQL($link,"SELECT * FROM artists WHERE phone='".$_POST['faphone']."'");
		echo "<table border='1' align=center>
		<tr>
		<th>Firstname</th>
		<th>Lastname</th>
		</tr>";

	while($row = mysqli_fetch_array($result)) {
  	echo "<tr>";
  	echo "<td>" . $row['fname'] . "</td>";
  	echo "<td>" . $row['lname'] . "</td>";
  	echo "</tr>";
	}

	echo "</table>";
		
}

if (isset($_GET['fclient']) || isset($_POST['fclientsql'])){
	?>
	<form align="center" action='http://localhost/cs304/gallerydb.php' method='post'>
		Search by <br>
		First Name: <input type="text" name="fcfname"> <br> 
		Last Name: <input type="text" name="fclname"> <br> 
		Phone number: <input type="text" name="fcphone"> <br>
		<button name="fclientsql" type="submit" value="true">Search</button>
	</form> 
		<?php
}

if (isset($_POST['fclientsql'])){
		$result = executePlainSQL($link,"SELECT * FROM clients WHERE fname='".$_POST['fcfname']."' AND lname='".$_POST['fclname']."'
		AND  phone='".$_POST['fcphone']."'");
		echo "<table border='1' align=center>
		<tr>
		<th>Firstname</th>
		<th>Lastname</th>
		<th>Phone Number</th>
		</tr>";

	while($row = mysqli_fetch_array($result)) {
  	echo "<tr>";
  	echo "<td>" . $row['fname'] . "</td>";
  	echo "<td>" . $row['lname'] . "</td>";
  	echo "<td>" . $row['phone'] . "</td>";
  	echo "</tr>";
	}

	echo "</table>";
		
}	

?>
</form>

</body>
</html>
