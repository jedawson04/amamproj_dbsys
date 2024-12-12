<?php

$serverName 	= "sql.cs.oberlin.edu";
$dbName 	= "mstinson";
$user		= "mstinson";
$pw	 	= "Oberlin@123";

function PrintPage($body, $table) {
  print("<!DOCTYPE html>\n");
  print("<html>\n<head>\n<title>Display Table</title>\n");
  print("</head>\n<body>\n");
  print("<h1>Displaying Table: $table </h1>\n");
  print("<div class='formOutput'>$body\n</div>\n");
  print("</body>\n</html>\n");
}

try {
  // grab Post
  $table_name = $_POST['table'];
  // create PDO Object connection
  $conn = new PDO("mysql:host=$serverName;dbname=$dbName", $user, $pw);
  // Set PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $query = "
  SELECT * 
  FROM $table_name 
  LIMIT 4000
  ";
  $stmt = $conn->prepare($query); // prepare query
  $stmt->execute();
  
  $fields = array_keys($stmt->fetch(PDO::FETCH_ASSOC));
  // add headers 
  $body = "<table border='1'><tr>";
  foreach($fields as $field) { 
    $body .= "<th>$field</th>";
  }
  $body .= "</tr>";
  // add rows 
  $stmt->execute(); // re-execute statement
  foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row)
    {  
      $body .= "<tr>";
      foreach($fields as $field) {  // add fields for every row
        $item = "<option>" . $row[$field] . "</option>";
        $body .= "<td>$item</td>";
      }
      $body .= "</tr>";
    }
  // display
  PrintPage($body, $table_name);


} catch (PDOException $e) {
  PrintPage("Connection failed: " . htmlspecialchars($e->getMessage()), "Unknown", "Unknown");
}
 ?>

