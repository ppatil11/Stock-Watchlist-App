<?php 
require 'init.php';

	$data = $_POST['symbol'];

   		//delete record from watchlist table
   		$query  = "DELETE FROM `watchlist` WHERE symbol ='" .$data. "'"; 

	   	$result = mysqli_query($conn, $query);	
         if (!$result) {
            die('Invalid query: ' . mysql_error());
         }else{
           $serverResponse = $data; 
         }
	   	
	   	echo $serverResponse;
 ?>