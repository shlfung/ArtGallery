<?php
session_start();
// error_reporting(E_ALL);
if (!isset($_SESSION['created'])){
	$_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
	session_unset();
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
<link rel="stylesheet" type="text/css" href="gallerydb.css">
</head>
<body>
	<h1 align="center"> <a href="http://localhost/cs304/gallerydb.php"> GalleryDB </a></h1>
	<?php

	// $link = '';
	if (isset($_GET['logout'])){
		session_unset();
		session_destroy();
		echo 'Logout Successful!';
	}


	if (isset($_POST['uname'])){
			$link = mysqli_connect('localhost:3306', $_POST['uname'], $_POST['password'], 'gallerydb');
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
			$_SESSION['uname'] = $_POST['uname'];
			$_SESSION['password'] = $_POST['password'];
			echo 'Success... ' . mysqli_get_host_info($link) . "<br>";
	}
	if (!isset($_SESSION['uname'])){
		?>
		<div id='login'>
		<form align='center' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			Login:<input type="text" name="uname">
			Password:<input type="password" name="password">
			<input type="submit" value="Login">
		</form>
		</div><?php
	}

	if (isset($_SESSION['uname'])){
			$link = mysqli_connect('localhost:3306', $_SESSION['uname'], $_SESSION['password'], 'gallerydb');
			if (!$link){
				die('Connect Error (' . mysqli_connect_errno() . ') '
            	. mysqli_connect_error());
				session_unset();
            }
			if (!mysqli_options($link, MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 0')) {
    			die('Setting MYSQLI_INIT_COMMAND failed');
				session_unset();
    		}
			if (!mysqli_options($link, MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
    			die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
				session_unset();
			}


	if ($_SESSION['uname'] == 'root'){?>
	<form align="center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="get">
	<button name="aartist" type="submit" value="true">Add Artist</button>
	<button name="aclient" type="submit" value="true">Add Client</button>
    <button name="apainting" type="submit" value="true">Add Painting</button>
    <button name="asculpture" type="submit" value="true">Add Sculpture</button>
    <button name="uprice" type="submit" value="true">Update Prices</button>
	<button name="fartist" type="submit" value="true">Find Artist</button>
	<button name="fclient" type="submit" value="true">Find Client</button> <br>
	<button name="dartist" type="submit" value="true">Delete Artist</button>
	<button name="dclient" type="submit" value="true">Delete Client</button>
	<button name="inventory" type="submit" value="true">Gallery Inventory</button>
	<button name="summary" type="submit" value="true">Gallery Summary</button>
	<button name="trans" type="submit" value="true">Administer Transaction</button>
    <button name="return" type="submit" value="true">Administer Return</button>
	<button name="invite_clients" type="submit" value="true">Invite Clients</button><br>
	<button name="popular_artists" type="submit" value="true" style="color:red">Most Popular Artists of The Gallery</button>
	<button name="logout" type="submit" value="true">Logout</button>
	</form>
    <div name="transphp"><br></br><?php include 'trans.php';?></div>
	</form><?php
	}else{?>
	<form align="center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="get">
		<button name="inventory" type="submit" value="true">Gallery Inventory</button>
		<button name="summary" type="submit" value="true">Gallery Summary</button>
		<button name="logout" type="submit" value="true">Logout</button>
	</form>
    <div name="transphp"><br></br><?php include 'trans.php';?></div>

	<?php
	}

	if (isset($_GET['summary']) or isset($_POST['summary'])){?>
		<form align="center" action='http://localhost/cs304/gallerydb.php' method='post'>
			Show artists'
		<select name="maxmin">
			<option value="max">max</option>
			<option value="min">min</option>
		</select>
		sale price
		<button name="summary" type="submit" value="true">Go</button>
		</form>

	<?php
	}

	if (isset($_POST['maxmin'])){
		$result = executePlainSQL($link, 'SELECT ar.fname as fname, ar.lname as lname,'.$_POST['maxmin'].'(a.price) as price
											  FROM artists ar, supplies s, art a
											  WHERE ar.phone = s.phone and s.serial_number = a.serial_number
											  GROUP BY ar.lname');
		echo "<table  align=center>
		<tr>
		<th>Lastname</th>
		<th>Firstname</th>
		<th>".$_POST['maxmin']." price</th>
		</tr>";

		while ($row = mysqli_fetch_array($result)){
			echo '<tr>';
			echo '<td>'.$row['lname'].'</td>';
			echo '<td>'.$row['fname'].'</td>';
			echo '<td>$'.$row['price'].'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}

	//inventory
	if (isset($_GET['inventory']) or isset($_POST['invfbartist']) or isset($_POST['invfbvalue'])){
		$filter = executePlainSQL($link, "SELECT *
		 								FROM artists");
		if (isset($_POST['invfbartist']) && $_POST['artist']){
			$result = executePlainSQL($link, "SELECT DISTINCT ar.phone as phone, s.fname as fname, s.lname as lname, a.title as title, sc.material as medium, a.price as price, a.pic_url as url
			 								FROM supplies s, art a, sculpture sc, artists ar
			 								WHERE ar.phone = s.phone and s.phone = '".$_POST['artist']."' and s.serial_number = a.serial_number and s.serial_number = sc.serial_number
			 								UNION
			 								SELECT DISTINCT ar.phone as phone, s.fname as fname, s.lname as lname, a.title as title, p.medium as medium, a.price as price, a.pic_url as url
			 								FROM supplies s, art a, painting p, artists ar
			 								WHERE ar.phone = s.phone and s.phone = '".$_POST['artist']."' and s.serial_number = a.serial_number and s.serial_number = p.serial_number
			 								ORDER BY lname");
		}
		elseif (isset($_POST['invfbvalue']) && $_POST['gthan'] == 'gthan'){
			$result = executePlainSQL($link, "SELECT DISTINCT ar.phone as phone, s.fname as fname, s.lname as lname, a.title as title, sc.material as medium, a.price as price, a.pic_url as url
			 								FROM supplies s, art a, sculpture sc, artists ar
			 								WHERE ar.phone = s.phone and a.price > ".$_POST['value']." and s.serial_number = a.serial_number and s.serial_number = sc.serial_number
			 								UNION
			 								SELECT DISTINCT ar.phone as phone, s.fname as fname, s.lname as lname, a.title as title, p.medium as medium, a.price as price, a.pic_url as url
			 								FROM supplies s, art a, painting p, artists ar
			 								WHERE ar.phone = s.phone and a.price > ".$_POST['value']." and s.serial_number = a.serial_number and s.serial_number = p.serial_number
			 								ORDER BY lname");
		}
		elseif (isset($_POST['invfbvalue']) && $_POST['gthan'] == 'lthan'){
			$result = executePlainSQL($link, "SELECT DISTINCT ar.phone as phone, s.fname as fname, s.lname as lname, a.title as title, sc.material as medium, a.price as price, a.pic_url as url
			 								FROM supplies s, art a, sculpture sc, artists ar
			 								WHERE ar.phone = s.phone and a.price < ".$_POST['value']." and  s.serial_number = a.serial_number and s.serial_number = sc.serial_number
			 								UNION
			 								SELECT DISTINCT ar.phone as phone, s.fname as fname, s.lname as lname, a.title as title, p.medium as medium, a.price as price, a.pic_url as url
			 								FROM supplies s, art a, painting p, artists ar
			 								WHERE ar.phone = s.phone and a.price < ".$_POST['value']." and s.serial_number = a.serial_number and s.serial_number = p.serial_number
			 								ORDER BY lname");
		}
		else {
			$result = executePlainSQL($link, "SELECT DISTINCT ar.phone as phone, s.fname as fname, s.lname as lname, a.title as title, sc.material as medium, a.price as price, a.pic_url as url
			 								FROM supplies s, art a, sculpture sc, artists ar
			 								WHERE ar.phone = s.phone and s.serial_number = a.serial_number and s.serial_number = sc.serial_number
			 								UNION
			 								SELECT DISTINCT ar.phone as phone, s.fname as fname, s.lname as lname, a.title as title, p.medium as medium, a.price as price, a.pic_url as url
			 								FROM supplies s, art a, painting p, artists ar
			 								WHERE ar.phone = s.phone and s.serial_number = a.serial_number and s.serial_number = p.serial_number
			 								ORDER BY lname");
		}
		echo "<form action='http://localhost/cs304/gallerydb.php' method='post'>";
		echo "<div align=center>";
		echo 'Filter by Artist:';
		echo '<select name ="artist">';
		echo '<option selected value=""></option>';
		while ($row = mysqli_fetch_array($filter)){
			echo '<option value="'.$row['phone'].'">';
			echo $row['lname'].', '.$row['fname'];
			echo '</option>';
			}
		echo '</select>';
		echo '<button name="invfbartist" value="true">Go</button><br>';

		echo 'Filter by price:';
		echo '<select name ="gthan">';
		echo '<option selected value=""></option>';
		echo '<option value="gthan">greater than</option>';
		echo '<option value="lthan">less than</option>';
		echo '</select>';
		echo '$<input type="text" name="value">';
		echo '<button name="invfbvalue" value="true">Go</button><br>';
		echo '</form>';
		echo "</div>";
		echo "<hr width=60% color=red>";


		echo "<table  align=center>
		<tr>
		<th>Lastname</th>
		<th>Firstname</th>
		<th>Title</th>
		<th>Material</th>
		<th>Price</th>
		<th>Image</th>
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
			echo '<td><img src="'.$row['url'].'" style="max-height: 100px; max-width: 100px"></td>';
			echo '</tr>';
		}
		echo '</table>';
	}


	// adding an artist


	if (isset($_GET['aartist']) || isset($_POST['aartistsql'])){ //either the get flag is set or the artist is being posted

// define variables and set to empty values
$fnameErr = $lnameErr =$emailErr = $phoneErr= "";
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
  		$fnameErr=validate_name($_POST["afname"]);
  		$lnameErr=validate_name($_POST["alname"]);
  		$phoneErr=validate_number($_POST["aphone"]);
  		$emailErr=validate_email($_POST["aemail"]);
  }
		?>
		<form align="center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method='post'>
		<h2 align=center>Add Artist</h2>
		<p><span class="error">* required field.</span></p>
		First Name: <input type="text" name="afname"> <span class="error">*<?php echo "$fnameErr";?></span> <br>
		Last Name: <input type="text" name="alname"> <span class="error">*<?php echo "$lnameErr";?></span><br>
		Street: <input type="text" name="astreet"> <br>
		City:<input type="text" name="acity"><br>
		State/Province: <input type="text" name="aprovince"><br>
		Postal Code: <input type="text" name="apcode"><br>
		Country: <input type="text" name="acountry"><br>
		Email: <input type="text" name="aemail"><span class="error"><?php echo "$emailErr";?></span><br>
		Phone: <input type="text" name="aphone"><span class="error">*<?php echo "$phoneErr";?></span><br>
		Status: <select name="astatus">
					<option value="inactive">Inactive</option>
					<option value="active">Active</option>
				</select><br>
		<button name="aartistsql" type="submit" value="true">Add Artist</button>
		</form>
		<?php


	}



	if (isset($_POST['aartistsql'])){
		if (!empty($fnameErr) or !empty($lnameErr) or !empty($phoneErr) or !empty($emailErr)) {
    		echo "<p align=center size:large>Artist was not added!</p>";
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

    // adding a painting

    if (isset($_GET['apainting']) || isset($_POST['apaintingsql'])) {
     ?>
     <form align="center" action='http://localhost/cs304/gallerydb.php' method="post">
         Title: <input type="text" name="ptitle"><br>
         Price: <input type="text" name="pprice"> <br>
         Medium: <input type="text" name="pmedium"><br>
         Style: <input type="text" name="pstyle"><br>
         Image Link: <input type="text" name="purl"> <br>
         Comission Rate: <input type='text' name='pcommission'>
         Choose Artist: <select name="select_artist">
    <?php

    //Generating Artist Selection
	$theArtist = executePlainSQL($link,"SELECT * FROM artists");
		while($row = mysqli_fetch_array($theArtist)) {
			$fname =  $row['fname'] ;
			$lname =  $row['lname'] ;
			$phone =  $row['phone'] ;
			echo "<option value=".$phone.">" .$fname. ','
									 .$lname. ','
									 .$phone.
							"</option>";
		}
	echo "</select> ";
    ?>

     <button name='apaintingsql' type='submit' value='true'>Add</button>
     </form>

    <?php
    }

    if (isset($_POST['apaintingsql'])) {

     $result = executePlainSQL($link, "SELECT A.serial_number FROM Art A WHERE A.serial_number >= ALL (SELECT Art.serial_number FROM Art)");

     // Find the largest serial number and add 1 to be the new serial number
     //$result9 = executePlainSQL($link, "SELECT MAX(A.serial_number) FROM Art A");
     //vardump($result9);

     // Grab and Generate the new SQL Integer from the SQL Statement
     while($row = mysqli_fetch_array($result)) {
         echo $row['serial_number'];
         $newSerial = $row['serial_number'];
         echo "<br>";
     }
     //$newSerial = $result;
     $newSerial++;
     echo $newSerial;
     echo "<br>";

     $query="INSERT INTO art VALUES ($newSerial,'"
     .$_POST['ptitle']."','"
     .$_POST['pprice']."','"
     .$_POST['purl']."', '0');";
     $query2="INSERT INTO painting VALUES($newSerial,'"
     .$_POST['pmedium']."','"
     .$_POST['pstyle']."');";

     $success =  executePlainSQL($link, $query);
     $success2 = executePlainSQL($link, $query2);
     if ($success and $success2) {
         echo "Statement: <br>".$query."<br>Executed successfully.";
         echo "Statement: <br>".$query2."<br>Executed successfully.";
     }

     // Insert the Art and Artist to the Supplies Table
     $artistInfo = executePlainSQL($link,"SELECT * FROM artists WHERE phone = '".$_POST['select_artist']."'");
      while($row=mysqli_fetch_array($artistInfo)) {

            $artistFname = $row['fname'];
            $artistLname = $row['lname'];
            $artistPhone = $row['phone'];
        }

      echo "<br>";
      echo $artistFname;
      echo $artistLname;
      echo $artistPhone;
      echo $newSerial;

      $commission = $_POST['pcommission'];

      $query3="INSERT INTO supplies VALUES('$artistFname', '$artistLname', $artistPhone, $commission, $newSerial)";

      $success3 = executePlainSQL($link, $query3);
      if ($success3) {
          echo "<br>";
          echo "Statement: <br>".$query3."<br>Executed successfully.";
      }
    }

    // adding a sculpture

    if (isset($_GET['asculpture']) || isset($_POST['asculpturesql'])) {
     ?>
     <form align="center" action='http://localhost/cs304/gallerydb.php' method="post">
         Title: <input type="text" name="stitle">
         Price: <input type="text" name="sprice"> <br>
         Material: <input type="text" name="smaterial">
         Style: <input type="text" name="sstyle">
         Image Link: <input type="text" name="surl"> <br>
         Comission Rate: <input type='number' name='scommission'>
         Choose Artist: <select name="select_artist">
    <?php

    //Generating Artist Selection
	$theArtist = executePlainSQL($link,"SELECT * FROM artists");
		while($row = mysqli_fetch_array($theArtist)) {
			$fname =  $row['fname'] ;
			$lname =  $row['lname'] ;
			$phone =  $row['phone'] ;
			echo "<option value=".$phone.">" .$fname. ','
									 .$lname. ','
									 .$phone.
							"</option>";
		}
	echo "</select> ";
    ?>

     <button name='asculpturesql' type='submit' value='true'>Add</button>
     </form>

    <?php
    }

    if (isset($_POST['asculpturesql'])) {

     $result = executePlainSQL($link, "SELECT A.serial_number FROM Art A WHERE A.serial_number >= ALL (SELECT Art.serial_number FROM Art)");

     // Find the largest serial number and add 1 to be the new serial number
     //$result = executePlainSQL($link, "SELECT MAX(A.serial_number) FROM Art A");

     // Grab and Generate the new SQL Integer from the SQL Statement
     while($row = mysqli_fetch_array($result)) {
         echo $row['serial_number'];
         $newSerial = $row['serial_number'];
         echo "<br>";
     }
     $newSerial++;
     echo $newSerial;
     echo "<br>";

     $query="INSERT INTO art VALUES ($newSerial,'"
     .$_POST['stitle']."','"
     .$_POST['sprice']."','"
     .$_POST['surl']."');";
     $query2="INSERT INTO sculpture VALUES($newSerial,'"
     .$_POST['smaterial']."','"
     .$_POST['sstyle']."');";

     $success =  executePlainSQL($link, $query);
     $success2 = executePlainSQL($link, $query2);
     if ($success and $success2) {
         echo "Statement: <br>".$query."<br>Executed successfully.";
         echo "Statement: <br>".$query2."<br>Executed successfully.";
     }

     // Insert the Art and Artist to the Supplies Table
     $artistInfo = executePlainSQL($link,"SELECT * FROM artists WHERE phone = '".$_POST['select_artist']."'");
      while($row=mysqli_fetch_array($artistInfo)) {

            $artistFname = $row['fname'];
            $artistLname = $row['lname'];
            $artistPhone = $row['phone'];
        }

      echo "<br>";
      echo $artistFname;
      echo $artistLname;
      echo $artistPhone;
      echo $newSerial;

      $commission = $_POST['scommission'];

      $query3="INSERT INTO supplies VALUES('$artistFname', '$artistLname', $artistPhone, $commission, $newSerial)";

      $success3 = executePlainSQL($link, $query3);
      if ($success3) {
          echo "<br>";
          echo "Statement: <br>".$query3."<br>Executed successfully.";
      }
    }
    if (isset($_POST['upricesql'])) {

     $newPrice = $_POST['new_price'];
     $selectedArtSerial = $_POST['select_art'];

      // Update the Price of the Arts
      $updateArtPrice= executePlainSQL($link,"UPDATE Art SET Price=$newPrice WHERE serial_number=$selectedArtSerial");

      if ($updateArtPrice) {
          echo "<br>";
          echo "Price Updated";
      }

    }

    // Update Prices in the Inventory
    if (isset($_GET['uprice']) || isset($_POST['upricesql'])) {
     ?>
         <form align="center" action='http://localhost/cs304/gallerydb.php' method="post">
         Updated Price: <input type="text" name="new_price">
         <br>
         Choose Artist: <select name="select_art">
     <?php
         //Generating Artist Selection
	$theArts = executePlainSQL($link,"SELECT serial_number, title, price FROM Art");
		while($row = mysqli_fetch_array($theArts)) {
			$serial_number =  $row['serial_number'] ;
			$title =  $row['title'] ;
			$price =  $row['price'] ;
			echo "<option value=".$serial_number.">" .$title. ','
									 .$price. ','
									 .$serial_number.
							"</option>";
         }
    echo "</select> ";
    ?>

     <button name='upricesql' type='submit' value='true'>Update</button>
     </form>

    <?php
    }




	/////////////////////////////////////////////////// ADD CLIENT ----------------------------------------------------->

	if (isset($_GET['aclient']) || isset($_POST['aclientsql'])){ //either the get flag is set or the client is being posted
		// define variables and set to empty values
	$fnameErr = $lnameErr =$emailErr = $phoneErr= "";
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
  		$fnameErr=validate_name($_POST["cfname"]);
  		$lnameErr=validate_name($_POST["clname"]);
  		$phoneErr=validate_number($_POST["cphone"]);
  		$emailErr=validate_email($_POST["cemail"]);
  }
		?>
		<form align="center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method='post'>
		<h2 align=center>Add Client</h2>
		<p><span class="error">* required field.</span></p>
		First Name: <input type="text" name="cfname"> <span class="error">*<?php echo "$fnameErr";?></span> <br>
		Last Name: <input type="text" name="clname"> <span class="error">*<?php echo "$lnameErr";?></span><br>
		Street: <input type="text" name="cstreet"><br>
		City:<input type="text" name="ccity"><br>
		State/Province: <input type="text" name="cprovince"><br>
		Country: <input type="text" name="ccountry"><br>
		Postal Code: <input type="text" name="cpcode"><br>
		Email: <input type="text" name="cemail"><span class="error">*<?php echo "$emailErr";?></span><br>
		Phone: <input type="text" name="cphone"><span class="error">*<?php echo "$phoneErr";?></span><br>
		<button name="aclientsql" type="submit" value="true">Add Client</button>
		</form>
			<?php
	}

	if (isset($_POST['aclientsql'])){
		echo $fnameErr . $lnameErr . $phoneErr . $emailErr;
		if (!empty($fnameErr) or !empty($lnameErr) or !empty($phoneErr) or !empty($emailErr)) {
    		echo "<p align=center size:large>Client was not added!</p>";
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

if (isset($_GET['fartist']) || isset($_POST['fartistsql']) || isset($_POST['fallartistsql']) || isset($_POST['find_artist_by_wildcard'])){
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

		<hr width=50%>
		Don't know their full name and phone number ? <br>
		No problem! just type in their name or lastname or even just their phone number! <br><br>

		<input type="text" name="search_artist_wildcard"><br>
		<button name="find_artist_by_wildcard" type="submit" value="true">Search</button
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

		if (mysqli_num_rows($result) == 0){
			echo "<p align=center>No results found!</p>";
		}
		else{
		echo "<table align=center>
		<tr>
		<th>Firstname</th>
		<th>Lastname</th>
		<th>Phone Number</th>
		<th>Email</th>
		<th>Status</th>
		</tr>";
		}
	while($row = mysqli_fetch_array($result)) {
  	echo "<tr>";
  	echo "<td>" . $row['fname'] . "</td>";
  	echo "<td>" . $row['lname'] . "</td>";
  	echo "<td>" . $row['phone'] . "</td>";
  	echo "<td>" . $row['email'] . "</td>";
  	echo "<td>" . $row['status'] . "</td>";
  	echo "</tr>";
	}

	echo "</table>";
	}
}

if (isset($_POST['find_artist_by_wildcard'])){
	if (empty($_POST["search_artist_wildcard"])) {
    		echo "<p style='color:yellow' align=center>  Please fill in the name field!  </p> <br><br>" ;
  	}else{
  		$artist_wildcard =test_input($_POST['search_artist_wildcard']);
  		$result = executePlainSQL($link,"SELECT * FROM artists WHERE fname LIKE  '%$artist_wildcard%'
  															   OR    lname LIKE  '%$artist_wildcard%'
  															   OR    phone LIKE  '%$artist_wildcard%'");

		if (!$result) {
   		 die('Invalid query: ' . mysql_error());
		}
		if (mysqli_num_rows($result) == 0){
			echo "<p align=center>No results found!</p>";
		}
		else{
		echo "<table border='1' align=center>
		<tr>
		<th>Firstname</th>
		<th>Lastname</th>
		<th>Phone Number</th>
		<th>Email</th>
		<th>Status</th>
		</tr>";
		}
	while($row = mysqli_fetch_array($result)) {
  	echo "<tr>";
  	echo "<td>" . $row['fname'] . "</td>";
  	echo "<td>" . $row['lname'] . "</td>";
  	echo "<td>" . $row['phone'] . "</td>";
  	echo "<td>" . $row['email'] . "</td>";
  	echo "<td>" . $row['status'] . "</td>";
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
		if (mysqli_num_rows($result) == 0){
			echo "<p align=center>No results found!</p>";
		}
		else{
		echo "<table border='1' align=center>
		<tr>
		<th>Firstname</th>
		<th>Lastname</th>
		<th>Phone Number</th>
		<th>Status</th>
		</tr>";
		}
	while($row = mysqli_fetch_array($result)) {
  	echo "<tr>";
  	echo "<td>" . $row['fname'] . "</td>";
  	echo "<td>" . $row['lname'] . "</td>";
  	echo "<td>" . $row['phone'] . "</td>";
  	echo "<td>" . $row['status'] . "</td>";
  	echo "</tr>";
	}

	echo "</table>";
	}
}

//finding a client

if (isset($_GET['fclient']) || isset($_POST['fclientsql']) || isset($_POST['fallclientsql']) || isset($_POST['find_client_by_wildcard'])){
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


		<hr width=50%>
		Don't know their full name and phone number ? <br>
		No problem! just type in their name or lastname or even just their phone number! <br><br>

		<input type="text" name="search_client_wildcard"><br>
		<button name="find_client_by_wildcard" type="submit" value="true">Search</button>
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

		if (mysqli_num_rows($result) == 0){
			echo "<p align=center>No results found!</p>";
		}
		else{
		echo "<table border='1' align=center>
		<tr>
		<th>Firstname</th>
		<th>Lastname</th>
		<th>Phone Number</th>
		<th>Email</th>
		</tr>";
		}
	while($row = mysqli_fetch_array($result)) {
  	echo "<tr>";
  	echo "<td>" . $row['fname'] . "</td>";
  	echo "<td>" . $row['lname'] . "</td>";
  	echo "<td>" . $row['phone'] . "</td>";
  	echo "<td>" . $row['email'] . "</td>";
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

		if (mysqli_num_rows($result) == 0){
			echo "<p align=center>No results found!</p>";
		}
		else{
		echo "<table border='1' align=center>
		<tr>
		<th>Firstname</th>
		<th>Lastname</th>
		<th>Phone Number</th>
		<th>Email</th>
		</tr>";
		}
	while($row = mysqli_fetch_array($result)) {
  	echo "<tr>";
  	echo "<td>" . $row['fname'] . "</td>";
  	echo "<td>" . $row['lname'] . "</td>";
  	echo "<td>" . $row['phone'] . "</td>";
  	echo "<td>" . $row['email'] . "</td>";
  	echo "</tr>";
	}

	echo "</table>";

	}
}
if (isset($_POST['find_client_by_wildcard'])){
	if (empty($_POST["search_client_wildcard"])) {
    		echo "<p style='color:yellow' align=center>  Please fill in the name field!  </p> <br><br>" ;
  	}else{
  		$client_wildcard =test_input($_POST['search_client_wildcard']);
  		$result = executePlainSQL($link,"SELECT * FROM clients WHERE fname LIKE  '%$client_wildcard%'
  															   OR    lname LIKE  '%$client_wildcard%'
  															   OR    phone LIKE  '%$client_wildcard%'");
  		if (!$result) {
   		 die('Invalid query: ' . mysql_error());
		}

		if (mysqli_num_rows($result) == 0){
			echo "<p align=center>No results found!</p>";
		}
		else{
		echo "<table border='1' align=center>
		<tr>
		<th>Firstname</th>
		<th>Lastname</th>
		<th>Phone Number</th>
		<th>Email</th>
		</tr>";
		}
	while($row = mysqli_fetch_array($result)) {
  	echo "<tr>";
  	echo "<td>" . $row['fname'] . "</td>";
  	echo "<td>" . $row['lname'] . "</td>";
  	echo "<td>" . $row['phone'] . "</td>";
  	echo "<td>" . $row['email'] . "</td>";
  	echo "</tr>";
	}

	echo "</table>";
	}
}



 /////////////////////////////////////////////////// DELETE ARTIST ----------------------------------------------------->

// delete an artist

if (isset($_GET['dartist']) || isset($_POST['dartistsql'])){
	?>
	<form align="center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method='post'>
	<h2 align=center>Delete Artist</h2>
		Choose Artist:
		<select name="select_artist">
	<?php
	if (isset($_POST['dartistsql'])){
		$artist_pieces = explode(" ", $_POST['select_artist']);
		$afname_piece = $artist_pieces[0];
		$alname_piece = $artist_pieces[1];
		$aphone_piece = $artist_pieces[2];
		$delete = executePlainSQL($link,"DELETE  FROM artists WHERE  fname='$afname_piece'
															  AND    lname='$alname_piece'
															  AND    phone='$aphone_piece'");
	}
	$result = executePlainSQL($link,"SELECT * FROM artists");
		while($row = mysqli_fetch_array($result)) {
			$fname =  $row['fname'] ;
			$lname =  $row['lname'] ;
			$phone =  $row['phone'] ;
			echo '<option value="'.$fname. ' '.$lname.' '.$phone.'" >'
									 .$fname. ','
									 .$lname. ','
									 .$phone.
							'</option>';

		}
	echo "</select> ";
	echo "<button name='dartistsql' type='submit' value='true'>Delete</button><br><br>";
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
	echo "<p style='font-size:24'>$afname_piece $alname_piece deleted!</p>";
	}
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

		$client_pieces = explode(" ", $_POST['select_client']);
		$cfname_piece = $client_pieces[0];
		$clname_piece = $client_pieces[1];
		$cphone_piece = $client_pieces[2];
		$delete = executePlainSQL($link,"DELETE  FROM clients WHERE  fname='$cfname_piece'
															  AND    lname='$clname_piece'
															  AND    phone='$cphone_piece'");
	}
	$result = executePlainSQL($link,"SELECT * FROM clients");
		while($row = mysqli_fetch_array($result)) {
			$fname =  $row['fname'] ;
			$lname =  $row['lname'] ;
			$phone =  $row['phone'] ;
			echo '<option value="'.$fname. ' '.$lname.' '.$phone.'" >'
									 .$fname. ','
									 .$lname. ','
									 .$phone.
							"</option>";

		}
	echo "</select> ";
	echo "<button name='dclientsql' type='submit' value='true'>Delete</button><br><br>";
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
	echo "<p style='font-size:24'>$cfname_piece $clname_piece deleted!</p>";
	}
	echo "</form>" 	;

}

if (isset($_GET['popular_artists'])){
	?>
	<form align="center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method='post'>
		<h3 >Artists who have sold all their artwork</h3>
	</form>
	<?php
	$popular_artists = executePlainSQL($link,"SELECT *
											  FROM artists a , supplies s1
											  WHERE a.fname=s1.fname AND a.lname=s1.lname AND a.phone=s1.phone AND s1.serial_number IS NOT NULL
											  AND not exists
											  (SELECT s2.serial_number
											  FROM supplies s2
											  WHERE a.fname=s2.fname AND a.lname=s2.lname AND a.phone=s2.phone
											  AND not exists
											  (SELECT transaction_id
											  FROM purchase p
											  WHERE p.serial_number=s2.serial_number));");
    if (!$popular_artists) {
   		 die('Invalid query: ' . mysql_error());
		}

		if (mysqli_num_rows($popular_artists) == 0){
			echo "<p align=center>No results found!</p>";
		}
		else{
		echo "<table border='1' align=center>
		<tr>
		<th>Firstname</th>
		<th>Lastname</th>
		<th>Phone Number</th>
		</tr>";
		}
	while($row = mysqli_fetch_array($popular_artists)) {
  	echo "<tr>";
  	echo "<td>" . $row['fname'] . "</td>";
  	echo "<td>" . $row['lname'] . "</td>";
  	echo "<td>" . $row['phone'] . "</td>";
  	echo "</tr>";
	}

	echo "</table>";

}

if (isset($_GET['invite_clients'])){
	?>
	<form align="center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method='post'>
		<h2 >Invite Clients For an Exhibition</h2>
		Exhibition Name: <input type="text" name="ename"><br>
		<button name="invitesql" type="submit" value="true">Invite All Clients</button>
	</form>
	<?php
}

if (isset($_POST['invitesql'])){
	$Exhibition=$_POST['invitesql'];
	$client = executePlainSQL($link,"SELECT *
											FROM clients");

	while($row = mysqli_fetch_array($client)){
		//send email
		$to = $row['email'];
		$subject = $Exhibition;
		$message = "Dear ".$row['fname']." ".$row['lname']." \n\n

										We invite you to visit our gallery for our new exhibition called ".$Exhibition.".\n

										Please RSVP asap\n\n

										Thank you! ";
		mail($to,$subject,$message);

	}
	echo "<p align=center style='font-size:30'>Invitations Sent!</p>";
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
	if(empty($data)){
		return "Field is empty!";
	}elseif (!preg_match("/^[a-zA-Z ]*$/",$data)) {
		return "Only letters and white space allowed!";
	}
}

function validate_number($data){
	if(empty($data)){
		return "Field is empty!";
	}elseif(!is_numeric($data)) {
		return "Phone number consists of non-numerical values!";

	}
}

function validate_email($data){
	if(!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$data)) {
		return "Invalid email format!";

	}
}
?>

</body>
</html>
