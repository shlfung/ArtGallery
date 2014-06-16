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
<style>
.error {color: #FF0000;}
</style>
</head>
<body>
	<h1 align="center"> <a href="http://localhost/cs304/gallerydb.php"> GalleryDB </a></h1>
	<?php

	// $link = '';

	if (!isset($_SESSION['uname'])){
		?>	
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
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
	<form align="center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="get">
	<button name="aartist" type="submit" value="true">Add Artist</button>
	<button name="aclient" type="submit" value="true">Add Client</button>
	<button name="fartist" type="submit" value="true">Find Artist</button>
	<button name="fclient" type="submit" value="true">Find Client</button> <br>
	<button name="dartist" type="submit" value="true">Delete Artist</button>
	<button name="dclient" type="submit" value="true">Delete Client</button>
	<button name="inventory" type="submit" value="true">Gallery Inventory</button>
	</form>


	<?php

	//inventory

	if (isset($_GET['inventory'])){
		$filter = executePlainSQL($link, "SELECT *
		 								FROM artists");
		$result = executePlainSQL($link, "SELECT ar.phone as phone, s.fname as fname, s.lname as lname, a.title as title, sc.material as medium, a.price as price
		 								FROM supplies s, art a, sculpture sc, artists ar
		 								WHERE s.serial_number = a.serial_number and s.serial_number = sc.serial_number
		 								UNION
		 								SELECT ar.phone as phone, s.fname as fname, s.lname as lname, a.title as title, p.medium as medium, a.price as price
		 								FROM supplies s, art a, painting p, artists ar
		 								WHERE s.serial_number = a.serial_number and s.serial_number = p.serial_number
		 								ORDER BY lname");
		echo "<div align=center>";
		echo 'Filter by Artist:';
		echo '<select name ="artist">';
		while ($row = mysqli_fetch_array($filter)){
			echo '<option value="'.$row['phone'].'">';
			echo $row['lname'].', '.$row['fname'];
			echo '</option>';
			}
		echo '</select>';
		echo '<button name="invfbartist" value="true">Go</button><br>';

		echo 'Filter by price:';
		echo '<select name ="gthan">';
		echo '<option value="true">greater than</option>';
		echo '<option value="false">less than</option>';
		echo '</select>';
		echo '$<input type="text" name="value">';
		echo '<button name="invfbartist" value="true">Go</button><br>';

		echo "</div>";

		echo "<hr width=60% color=red>";
		

		echo "<table border='1' align=center>
		<tr>
		<th>Lastname</th>
		<th>Firstname</th>
		<th>Title</th>
		<th>Material</th>
		<th>Price</th>
		</tr>";

		while ($row = mysqli_fetch_array($result)){
			echo '<tr>';
			echo '<td>'.$row['lname'].',</td>';
			echo '<td>'.$row['fname'].'</td>';
			echo '<td>'.$row['title'].'</td>';
			if (isset($row['material'])){
				echo '<td>'.$row['material'].'</td>';	
			}else {
				echo '<td>'.$row['medium'].'</td>';
			}
			echo '<td>$'.$row['price'].'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}


	// adding an artist


	if (isset($_GET['aartist']) || isset($_POST['aartistsql'])){ //either the get flag is set or the artist is being posted
		// define variables and set to empty values
	$nameErr="";
		?>
		<form align="center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method='post'>
		<h2 align=center>Add Artist</h2>
		<p><span class="error">* required field.</span></p>
		First Name: <input type="text" name="afname"> <span class="error">*</span> <br>
		Last Name: <input type="text" name="alname"> <span class="error">*</span><br>
		Street: <input type="text" name="astreet"> <br>
		City:<input type="text" name="acity"><br>
		State/Province: <input type="text" name="aprovince"><br>
		Postal Code: <input type="text" name="apcode"><br>
		Country: <input type="text" name="acountry"><br>
		Email: <input type="text" name="aemail"><br>
		Phone: <input type="text" name="aphone"><span class="error">*</span><br>
		Status: <select name="astatus">
					<option value="inactive">Inactive</option>
					<option value="active">Active</option>
				</select><br>
		<button name="aartistsql" type="submit" value="true">Add</button>
		</form>
		<?php
		
   		
	}

	

	if (isset($_POST['aartistsql'])){
		validate_name($_POST["afname"]);
		if (empty($_POST["afname"]) or empty($_POST["alname"]) or empty($_POST["aphone"]) ) {
    		echo "<p style='color:yellow' align=center>  One or more of the required fields is/are empty!  </p> <br><br>" ;
  		}else{
		$query = "insert into artists values('"
			.test_input($_POST['afname'])."','"
			.test_input($_POST['alname'])."','"
			.test_input($_POST['astreet'])."','"
			.test_input($_POST['acity'])."','"
			.test_input($_POST['aprovince'])."','"
			.test_input($_POST['apcode'])."','"
			.test_input($_POST['acountry'])."','"
			.test_input($_POST['aemail'])."','"
			.test_input($_POST['aphone'])."','"
			.test_input($_POST['astatus'])."');";
		$success = executePlainSQL($link, $query);
		if (is_string($success)) {
			echo $success;
		}else{
			echo "<p align=center >Artist Added successfully.</p>";
		}
		}	
	}

	// adding a client

	if (isset($_GET['aclient']) || isset($_POST['aclientsql'])){ //either the get flag is set or the client is being posted
		?>
		<form align="center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method='post'>
		<h2 align=center>Add Client</h2>
		<p><span class="error">* required field.</span></p>
		First Name: <input type="text" name="cfname"> <span class="error">*</span> <br>
		Last Name: <input type="text" name="clname"> <span class="error">*</span><br>
		Street: <input type="text" name="cstreet"><br>
		City:<input type="text" name="ccity"><br>
		State/Province: <input type="text" name="cprovince"><br>
		Country: <input type="text" name="ccountry"><br>
		Postal Code: <input type="text" name="cpcode"><br>
		Email: <input type="text" name="cemail"><br>
		Phone: <input type="text" name="cphone"><span class="error">*</span><br>
		<button name="aclientsql" type="submit" value="true">Add</button>
		</form>
			<?php
	}

	if (isset($_POST['aclientsql'])){
		if (empty($_POST["cfname"]) or empty($_POST["clname"]) or empty($_POST["cphone"]) ) {
    		echo "<p style='color:yellow' align=center>  One or more of the required fields is/are empty!  </p> <br><br>" ;
  		}else{
		$query = "insert into clients values('"
			.test_input($_POST['cfname'])."','"
			.test_input($_POST['clname'])."','"
			.test_input($_POST['cstreet'])."','"
			.test_input($_POST['ccity'])."','"
			.test_input($_POST['cprovince'])."','"
			.test_input($_POST['ccountry'])."','"
			.test_input($_POST['cpcode'])."','"
			.test_input($_POST['cemail'])."','"
			.test_input($_POST['cphone'])."');";
		$success = executePlainSQL($link, $query);
		if (is_string($success)) {
			echo $success;
		}else{
			echo "<p align=center size=24 >Client Added successfully.</p>";
		}
		}
	}
}

	




// finding an artist
	
if (isset($_GET['fartist']) || isset($_POST['fartistsql'])){
	?>
	<form align="center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method='post'>
		<h2 align=center>Find Artist </h2>
		Search by <br>
		First Name: <input type="text" name="fafname"> <br> 
		Last Name: <input type="text" name="falname"> <br> 
		Phone number: <input type="text" name="faphone"> <br>
		<button name="fartistsql" type="submit" value="true">Search</button>
		<hr width=50%>
		Search All Artists by City: <br>
		City: <input type="text" name="facity"> <br> 
		<button name="fallartistsql" type="submit" value="true">Find All Artists</button>
	</form> 
		<?php
}

if (isset($_POST['fartistsql'])){
	if (empty($_POST["fafname"]) or empty($_POST["falname"]) or empty($_POST["faphone"]) ) {
    		echo "<p style='color:yellow' align=center>  All required fields must be filled!  </p> <br><br>" ;
  	}else{
	    $Artist_fname = test_input($_POST['fafname']);
	    $Artist_lname = test_input($_POST['falname']);
	    $Artist_phone = test_input($_POST['faphone']);
		$result = executePlainSQL($link,"SELECT * FROM artists WHERE fname='$Artist_fname' 
		AND lname='$Artist_lname'
		AND  phone='$Artist_phone'");
		if (!$result) {
   		 die('Invalid query: ' . mysql_error());
		}

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
}

if (isset($_POST['fallartistsql'])){
	if (empty($_POST["facity"])) {
    		echo "<p style='color:yellow' align=center>  Please fill in the city field!  </p> <br><br>" ;
  	}else{
		$artist_city =test_input($_POST['facity']);
		$result = executePlainSQL($link,"SELECT * FROM artists WHERE city='$artist_city'");

		if (!$result) {
   		 die('Invalid query: ' . mysql_error());
		}

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
}

//finding a client

if (isset($_GET['fclient']) || isset($_POST['fclientsql'])){
	?>
	<h2 align=center>Find Client </h2> 
	<form align="center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method='post'>
		Search by <br>
		First Name: <input type="text" name="fcfname"> <br> 
		Last Name: <input type="text" name="fclname"> <br> 
		Phone number: <input type="text" name="fcphone"> <br>
		<button name="fclientsql" type="submit" value="true">Search</button>
		<hr width=50%>
		Search All Clients by City: <br>
		City: <input type="text" name="fccity"> <br> 
		<button name="fallclientsql" type="submit" value="true">Find All Clients</button>
	</form> 
		<?php
}

if (isset($_POST['fclientsql'])){
	if (empty($_POST["fcfname"]) or empty($_POST["fclname"]) or empty($_POST["fcphone"]) ) {
    		echo "<p style='color:yellow' align=center>  All required fields must be filled!  </p> <br><br>" ;
  	}else{
		$Client_fname = test_input($_POST['fcfname']);
	    $Client_lname = test_input($_POST['fclname']);
	    $Client_phone = test_input($_POST['fcphone']);
		$result = executePlainSQL($link,"SELECT * FROM clients WHERE fname='$Client_fname' 
		AND lname='$Client_lname'
		AND  phone='$Client_phone'");
		if (!$result) {
   		 die('Invalid query: ' . mysql_error());
		}

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
}

if (isset($_POST['fallclientsql'])){
	if (empty($_POST["fccity"])) {
    		echo "<p style='color:yellow' align=center>  Please fill in the city field!  </p> <br><br>" ;
  	}else{
		$client_city =test_input($_POST['fccity']);
		$result = executePlainSQL($link,"SELECT * FROM clients WHERE city='$client_city'");
		if (!$result) {
   		 die('Invalid query: ' . mysql_error());
		}

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
}	

// delete an artist

if (isset($_GET['dartist']) || isset($_POST['dartistsql'])){
	?>
	<form align="center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method='post'>
	<h2 align=center>Delete Artist</h2>
		Choose Artist:
		<select name="select_artist">
	<?php
	if (isset($_POST['dartistsql'])){
		$delete = executePlainSQL($link,"DELETE  FROM artists WHERE  phone = '".$_POST['select_artist']."'");
	}	
	$result = executePlainSQL($link,"SELECT * FROM artists");
		while($row = mysqli_fetch_array($result)) {
			$fname =  $row['fname'] ;
			$lname =  $row['lname'] ;
			$phone =  $row['phone'] ;
			echo "<option value=".$phone.">" .$fname. ','
									 .$lname. ','
									 .$phone. 
							"</option>";

		}
	echo "</select> ";
	echo "<button name='dartistsql' type='submit' value='true'>Delete</button>";
	echo "</form>" 	;

}

//delete a client

if (isset($_GET['dclient']) || isset($_POST['dclientsql'])){
	?>
	<form align="center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method='post'>
	<h2 align=center>Delete Client</h2>
		Choose client:
		<select name="select_client">
	<?php
	if (isset($_POST['dclientsql'])){
		$delete = executePlainSQL($link,"DELETE  FROM clients WHERE  phone = '".$_POST['select_client']."'");
	}	
	$result = executePlainSQL($link,"SELECT * FROM clients");
		while($row = mysqli_fetch_array($result)) {
			$fname =  $row['fname'] ;
			$lname =  $row['lname'] ;
			$phone =  $row['phone'] ;
			echo "<option value=".$phone.">" .$fname. ','
									 .$lname. ','
									 .$phone. 
							"</option>";

		}
	echo "</select> ";
	echo "<button name='dclientsql' type='submit' value='true'>Delete</button>";
	echo "</form>" 	;

}
?>

</form>
<?php
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function validate_name($data){
	if (!preg_match("/^[a-zA-Z ]*$/",$data)) {
  echo "<script type='text/javascript'>alert('Only letters and white space allowed!'');</script>"; 
	}
}

function validate_number($data){
	if (!preg_match("/^[a-zA-Z ]*$/",$data)) {
  echo "<script type='text/javascript'>alert('Only letters and white space allowed!'');</script>"; 
	}
}
?>

</body>
</html>
