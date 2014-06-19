<?php
    if (isset($_GET['trans']) ){
        ?>
<form align="center" action='http://localhost/cs304/gallerydb.php' method='post'>

<! ----------------------- CLIENT SELECTION ----------------------->
Client:
<select name="cid">
<?php
    
    $result = executePlainSQL($link,"SELECT * FROM clients");
    while($row = mysqli_fetch_array($result)) {
        $fname =  $row['fname'] ;
        $lname =  $row['lname'] ;
        $phone = $row['phone'];
        echo "<option value='".$fname.",".$lname.",".$phone."'>".$fname. ' '.$lname."</option>";
        //".$fname.",".$lname.",".$phone."
    }
    
    ?>
</select>

<! ----------------------- ART SELECTION -----------------------><br>
Art piece:
<select name="aid">
<?php

    $result = executePlainSQL($link,"SELECT * FROM art where (art.sold = 0)");
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
Selling Price (in $):<input type="text" name="sellp">
<br>Card type: <select name="ptype">
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
     ============================== PERSISTING TRANSACTION INFO ==============================
     */
    if(isset($_POST['transsql'])){
        //use $newId for tid
        $date = date('Y-m-d H:i:s');
        $snum = $_POST['aid'];
        $ptype = $_POST['ptype'];
        $price = $_POST['sellp'];
        if(empty($_POST['sellp']) || empty($price) ||  (validate_number1($price) != 1)){
            die("<div align='center' class='error'>\nInvalid price input, transaction aborted</div>");
        }
    
        //-------------------- adding to issue transaction ---------------------------
        $result = executePlainSQL($link,
          "SELECT it1.transaction_id
          FROM Issue_Transaction it1
          WHERE it1.transaction_id >= ALL
          (SELECT it.transaction_id FROM Issue_Transaction it)");



      // Grab and Generate the new SQL Integer from the SQL Statement
      while($row = mysqli_fetch_array($result)) {
      //        echo $row['transaction_id'];
            $newId = $row['transaction_id'];
      }
      $newId++;

      $cid = $_POST['cid'];
//      echo "cid: $cid";
      $option = explode(",",$_POST['cid']);
      $first = $option[0];
      $last = $option[1];
      $phone = $option[2];

//      echo "$first, $last, $phone";
      $query = "INSERT INTO issue_transaction VALUES($newId,'$first','$last','$phone')";
      $success = executePlainSQL($link, $query);
//      echo "What came back from the db from inserting transaction: \n";
//      echo $success;
      if (is_string($success)) {
//      echo $success;
      }else{
      echo "<p align=center >Transaction added successfully.</p>";
      }

      //------------------------------ adding to purchase table


      $query="INSERT INTO purchase VALUES($newId, '$date','$ptype', $price, $snum)";
      $success = executePlainSQL($link, $query);
//      echo "What came back from the db\n";
//      echo $success;
      if (is_string($success)) {
//         echo $success;
      }else{
            echo "<p align=center >Purchase added successfully.</p>";
      }
      //------------------------------ changing value of art variable

        $query = "UPDATE Art SET art.sold = 1 where art.serial_number=$snum";
      $success = executePlainSQL($link, $query);
//      echo "What came back from the db\n";
//      echo $success;
      if (is_string($success)) {
//      echo $success;
      }else{
      echo "<p align=center >Art Sold.</p>";

      }
}

  if (isset($_GET['return'])){
    
    ?>

    <form align="center" action='http://localhost/cs304/gallerydb.php' method='post'>

    <! ----------------------- purchase SELECTION ----------------------->
    Return Item:
    <select name="item">

    <?php //----------------------------PHP

    $result = executePlainSQL($link,"SELECT * FROM purchase");
    while($row = mysqli_fetch_array($result)) {
        $snum =  $row['serial_number'] ;
        $art = executePlainSQL($link, "Select * from Art a where (a.serial_number=$snum)");
        
        $title = "";
        while($r = mysqli_fetch_array($art)){
            $title = $r['title'];
            
        }
        $tid =  $row['transaction_id'] ;
        echo "<option value=".$tid.'-'.$snum.">Title: " .$title." - Serial number: ".$snum." </option>";
//        echo "<option value='0' />.$art.<option>"
    }//------------------------------- PHP
    ?>

    </select> <!-- from the end of the return item select button -->

    <! ----------------------- client SELECTION ----------------------->

    <br/>Client Name:
    <select name="cid">
    <?php
        $result = executePlainSQL($link,"SELECT * FROM clients");
        while($row = mysqli_fetch_array($result)) {
            $fname =  $row['fname'] ;
            $lname =  $row['lname'] ;
            $phone = $row['phone'];
            echo "<option value='".$fname.",".$lname.",".$phone."'>".$fname. ' '.$lname."</option>";
        }
        
        
    echo "</select><br/>";
    echo "<button name='returnsql' type='submit' value='true'>Complete Return</button>";
    echo "</br></form>";
 }
            
//modify the art
    if (isset($_POST['returnsql'])) {

        //get the item
        $tidnsnum = explode("-",$_POST['item']);
        $tid = $tidnsnum[0];
        $snum = $tidnsnum[1];

        $cid = $_POST['cid'];
        $option = explode(",",$_POST['cid']);
        $first = $option[0];
        $last = $option[1];
        $phone = $option[2];
        
        $art = executePlainSQL($link, "Select * from Art a where (a.serial_number=$snum)");
        
        $title = "";
        while($r = mysqli_fetch_array($art)){
            $title = $r['title'];
            
        }
        //echo "$first, $last, $phone";
        //$query = "INSERT INTO issue_transaction VALUES($newId,'$first','$last','$phone')";

        //get the issue transation (to check client matches)!
        $query = "select * from issue_transaction it where(it.transaction_id = $tid)";
        $result = executePlainSQL($link, $query);
        if (is_string($result)) {
            echo $success;
        }else{
//            echo "<p align='center' >Querried success!.</p>";
        }
        
        //get the transaction (to get the user) - double check the user matches!
        while($row = mysqli_fetch_array($result)) {
            $fname1 =  $row['fname'] ;
            $lname1 =  $row['lname'] ;
            $phone1 = $row['phone'];
//            echo "NAMES CORRESPONDING TO THIS TRANSACTION ----
//            $fname1, $lname1, $phone1 ---- NAMES CORRESPONDNG TO INPUT STR $first, $last, $phone\n";
        }
        
        if(strcmp($fname1,$first) != 0 || strcmp($phone1,$phone) != 0 || strcmp($lname1,$last) != 0 ){
            die("<div align='center' class='error'>\nInvalid selection of client and return. Client and purchase do not match!</div>");

        }else{
            echo "<div align='center'>Processing return for client $first $last...</div>";
        }

        //get the purchase so you can copy over the info
        $query = "select * from purchase p where(p.serial_number = $snum)";
        $result = executePlainSQL($link, $query);
        while($row = mysqli_fetch_array($result)) {
            $ptype = $row['pur_type'];
            $amnt = $row['amount'];
        }
       
        $payment = '';
        //amount to be refunded to CUSTOMER X for ART y
        switch ($ptype) {
            case "v":
                $payment = 'Visa';
                break;
            case "mc":
                $payment = 'Master Card';
                break;
            case "d":
                $payment = 'Debit';
                break;
            default;
                $payment = 'Cash';
        }
        echo "<table align='center'><tr><td>Please administer a refund to client " .$first. " " .$last. " in the amount of $" .$amnt. " for the return purchase of " .$title. " (serial number): " .$snum."</td></td></table>";
        echo "<div align='center'>Refund Processesed</div>";


        //now delete purchase
        $query = "delete from purchase where (purchase.serial_number = $snum) AND (purchase.transaction_id = $tid)";
        $result = executePlainSQL($link, $query);
        if (is_string($result)) {
            echo $result;
        }else{
//            echo "<p align='center' >Refund transaction details:</p>";
        }

        $date = date('Y-m-d H:i:s');
        $query = "insert into purchase_return values ($tid, '$date', '$ptype', $amnt, $snum)";
        $result = executePlainSQL($link, $query);
        if (is_string($result)) {
            echo $result;
        }
        
        $query = "UPDATE Art SET art.sold = 0 where art.serial_number=$snum";
        $success = executePlainSQL($link, $query);
        //      echo "What came back from the db\n";
        //      echo $success;
        if (is_string($success)) {
            //      echo $success;
        }else{
            echo "<p align=center >Art returned to purchaseable inventory.</p>";
            
        }

    }
    
    function validate_number1($data){
        if(empty($data)){
            return 0;
        }elseif(!is_numeric($data)) {
            return -1;
            
        }else{
            return 1;
        }
    }
        


?>
