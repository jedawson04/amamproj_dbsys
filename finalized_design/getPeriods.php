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

  $stmt = $conn->prepare('SELECT distinct period FROM culture order by period');
  $stmt->execute();

  foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $key =>$val ) { 
    print("<option>" . $val['period'] . "</option>\n");
  }

}

catch(PDOException $e) {
  print("Connection failed: " . $e->getMessage());
}



?>