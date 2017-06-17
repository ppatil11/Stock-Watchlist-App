<?php 
require 'init.php';

	$data = $_POST['symbol'];

	$query  = "SELECT * FROM quotes WHERE symbol = '".$data."'";
   	$result = mysqli_query($conn, $query);
   	$rows   = mysqli_num_rows($result);

   	if ($rows == 0) {
         $serverResponse = "NOTEXIST";
   		echo $serverResponse;
   	}else{
   		$row = mysqli_fetch_assoc($result);
   		//insert record in watchlist table
   		$query  = "INSERT INTO `watchlist`
     				VALUES ('".$row['symbol']. " ', ' " 
   													. $row['name'] ."' , ' "
   													. $row['last'] ."' , ' "
   													. $row['change'] ."' , ' "
   													. $row['pctchange'] ."' , ' "
   													. $row['volume'] ."' , ' "
   													. $row['tradetime'] ."')";
	   	$result = mysqli_query($conn, $query);	

	   	$serverResponse = json_encode($row);
	   	echo $serverResponse;
   	}
 ?>