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
            die("<div align='centre' class='error'>\nInvalid price input, transaction aborted</div>");
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

//      $query = "insert into issue_transaction values ('$newId', '$first', '$last', '1234')";
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
    <select name="select_work">
    <?php

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
    }
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
        
        ?>
    </select><br/>
    <button name='returnsql' type='submit' value='true'>Complete Return</button>
    </br></form>

<! ----------------------- TESTS TO REMOVE ----------------------->

<!--  <div>TESTS</div> -->
        <?php
 }
            
            
//    if(isset($_GET['return'])){
//        $art = executePlainSQL($link, "Select * from Art a where (a.serial_number=$snum)");
//        
//        while($r = mysqli_fetch_array($art)){
//            $title = $r['title'];
//            echo $title;
//            
//        }
//
////        $echo "</div>";
//    }

    //submitting returns

    //add to 'issue_transactions
    //add to 'transaction_returns
    //modify the art
    if (isset($_POST['returnsql'])) {
        echo "<br>HELLO</br>";

        //            $query = "insert into artists values(");";
        $success = executePlainSQL($link, $query);
        if (is_string($success)) {
            echo $success;
        }else{
            echo "<p align=center >Artist Added successfully.</p>";
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
