<?php

$serverName 	= "sql.cs.oberlin.edu";
$dbName 	= "mstinson";
$user		= "mstinson";
$pw	 	= "Oberlin@123";


try {

  $conn = new PDO("mysql:host=$serverName;dbname=$dbName", 
		  $user, $pw);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $conn->prepare('SELECT distinct classification FROM art_piece order by classification');
  $stmt->execute();

  foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $key =>$val ) { 
    print("<option>" . $val['classification'] . "</option>\n");
  }

}

catch(PDOException $e) {
  print("Connection failed: " . $e->getMessage());
}



?>