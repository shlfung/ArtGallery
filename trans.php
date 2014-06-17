<?php
    if (isset($_GET['trans'])){
        ?>
        <form align="center" action='http://localhost/cs304/gallerydb.php' method='post'>

        <! ----------------------- CLIENT SELECTION ----------------------->Client:
        <select name="select_client">
        <?php
            
            $result = executePlainSQL($link,"SELECT * FROM clients");
            while($row = mysqli_fetch_array($result)) {
                $fname =  $row['fname'] ;
                $lname =  $row['lname'] ;
                echo "<option value=".$phone.">" .$fname. ' '
                .$lname.
                "</option>";
                
            }
            
            ?>
        </select>

        <! ----------------------- ART SELECTION -----------------------><br>
        Art piece:
        <select name="select_art">
        <?php
            
            $result = executePlainSQL($link,"SELECT * FROM art");
            while($row = mysqli_fetch_array($result)) {
                $snum =  $row['serial_number'] ;
                $title =  $row['title'] ;
                $price = $row['price'];
                echo "<option value=".$snum.">" .$title. ' '
                .$snum. ' ' .$price.
                "</option>";
                
            }
            ?>
        </select>


        <! ----------------------- PRICE SELECTION ----------------------->

        <br>
        Selling Price:<input type="text" name="sellp"> <!-- span class="error"><php echo "$priceErr";??></span> -->
        Card type: <select name="ptype">
        <option value="v">Visa</option>
        <option value="mc">Master Card</option>
        <option value="d">Debit</option>
        <option value="c">Cash</option>
        </select>
        </br>

        <br>
        <button name='transsql' type='submit' value='true'>Submit Transaction</button>
        </br></form>

        <! ----------------------- method for submitting next----------------------->

<?php
    }
    
    if(isset($_POST['transsql'])){
        echo "<p>HIIIIIII<p/>";
        
        //DEALING WITH IMPROPER VALS
//        $priceErr= "";
//        if ($_SERVER["REQUEST_METHOD"] == "POST") {
//            $priceErr =validate_number($_POST["sellp"]);
//            
//        }
       
//todo: error handling
//        if (!empty($fnameErr) or !empty($lnameErr) or !empty($phoneErr) or !empty($emailErr)) {
//    		echo "<p align=center size:large>Artist was not added!</p>";
//  		}
        
//        issue_transaction
//        (transaction_id int not null PRIMARY KEY,
//         fname varchar(30) not null,
//         lname varchar(30) not null,
//         phone int not null);

            $query = "insert into issue_transaction values('"
            .test_input($_POST['phone'])."','"
			.test_input($_POST['fname'])."','"
			.test_input($_POST['lname'])."','"
			.test_input($_POST['phone'])."');";
            $success = executePlainSQL($link, $query);
            if (is_string($success)) {
                echo $success;
            }else{
                echo "<p align=center >Transaction added successfully.</p>";
            }
		
    }
    
    //trying out get method. scrap later
//    if(isset($_GET['transsql'])){
//       echo "<table  align=center>
//       <tr>
//       <th>Lastname</th>
//       <th>Firstname</th>
//       </tr>";
//       
//       $result = executePlainSQL($link, "SELECT * FROM artists");
//       while ($row = mysqli_fetch_array($result)){
//       echo '<tr>';
//       echo '<td>'.$row['lname'].',</td>';
//       echo '<td>'.$row['fname'].'</td>';
//       echo '<tr>';
//       }
//       
//       echo '</table>';
//       }
    


    
       

?>