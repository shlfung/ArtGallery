<?php
    if (isset($_GET['trans']) || isset($_POST['transsql'])){
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

<?php }
    
    

    /*
     ============================== ATTEMPT 1 ==============================
     */
    if(isset($_POST['transsql'])){
    
    $result = executePlainSQL($link,
                              "SELECT A.serial_number FROM issue_transaction A WHERE A.transaction_id >= ALL (SELECT Issue_transaction.transaction_id FROM Issue_Transaction)");
    
    // Grab and Generate the new SQL Integer from the SQL Statement
    while($row = mysqli_fetch_array($result)) {
        echo $row['transaction_id'];
        $newId = $row['transaction_id'];
        echo "<br>";
    }
    
    $newId++;
    
    $query = "insert into issue_transaction values($newId,'"
    .test_input($_POST['fname'])."','"
    .test_input($_POST['lname'])."','"
    .test_input($_POST['phone'])."');";
    $success = executePlainSQL($link, $query);
    if (is_string($success)) {
        echo $success;
    }else{
        echo "<p align=center >Transaction added successfully.</p>";
    }
    
    
    //$query = "insert into issue_transaction values (123456, 'first', 'last', '1234')";
    
    }
    
    /*
     ============================== ATTEMPT 2 ==============================
     */

    if (isset($_POST['transsql'])) {
            echo "<br>HELLO</br>";
        }
    
    /*
     ============================== ATTEMPT 3 ==============================
     */

    if (isset($_POST['transsql'])) {
        // Find the largest serial number and add 1 to be the new serial number
        $result = executePlainSQL($link,
                                  "SELECT A.serial_number FROM issue_transaction A WHERE A.transaction_id >= ALL (SELECT Issue_transaction.transaction_id FROM Issue_Transaction)");
                                  
          // Grab and Generate the new SQL Integer from the SQL Statement
          while($row = mysqli_fetch_array($result)) {
              echo $row['transaction_id'];
              $newId = $row['transaction_id'];
              echo "<br>";
          }
        
        $newId++;
        echo $newId;
        echo "<br>";
        
//          $query="INSERT INTO art VALUES ($newSerial,'"
//          .$_POST['ptitle']."','"
//          .$_POST['pprice']."','"
//          .$_POST['purl']."');";
//          $query2="INSERT INTO painting VALUES($newSerial,'"
//          .$_POST['pmedium']."','"
//          .$_POST['pstyle']."');";
//          
//          $success =  executePlainSQL($link, $query);
//          $success2 = executePlainSQL($link, $query2);
//          if ($success and $success2) {
//          echo "Statement: <br>".$query."<br>Executed successfully.";
//          echo "Statement: <br>".$query2."<br>Executed successfully.";        
//                                  }
        }
    


    if (isset($_GET['return']) || isset($_POST['returnsql'])){
            ?>
    <form align="center" action='http://localhost/cs304/gallerydb.php' method='post'>

        <! ----------------------- CLIENT SELECTION ----------------------->
        Purchases:
        <select name="select_work">
        <?php
            
            $result = executePlainSQL($link,"SELECT * FROM purchase");
            while($row = mysqli_fetch_array($result)) {
                $snum =  $row['serial_number'] ;
                
                $art = executePlainSQL($link, "Select a.title from Art a where a.serial_number = '.$snum '");
                $tid =  $row['transaction_id'] ;
                echo "<option value=".$tid.">transacton id: ".$tid." serial number: ".$snum."</option>";
                
            }
            
            ?>
        </select>

        <button name='returnsql' type='submit' value='true'>Complete Return</button>
        </br></form>

        <! ----------------------- method for submitting next----------------------->

    <?php }
   
?>