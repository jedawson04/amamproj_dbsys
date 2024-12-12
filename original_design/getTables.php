<?php

$serverName 	= "sql.cs.oberlin.edu";
$dbName 	= "mstinson";
$user		= "mstinson";
$pw	 	= 'Oberlin@123';


try {
  $conn = new PDO("mysql:host=$serverName;dbname=$dbName", 
		  $user, $pw);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $conn->prepare('SHOW tables'); // grab available tables
  $stmt->execute();
  $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $access_key = 'Tables_in_'. $dbName; // generic access key for tables
  foreach($tables as $table) { 
    print("<option>" . $table[$access_key] . "</option>\n");
  }
}

catch(PDOException $e) {
  print("Connection failed: " . $e->getMessage());
}



?>
