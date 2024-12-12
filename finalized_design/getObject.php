<?php

$serverName = "sql.cs.oberlin.edu";
$dbName = "mstinson";
$user = "mstinson";
$pw = "Oberlin@123";

try {
    $conn = new PDO("mysql:host=$serverName;dbname=$dbName", $user, $pw);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $name = isset($_GET['name']) ? $_GET['name'] : '';
    $materials = isset($_GET['materials']) ? $_GET['materials'] : '';
    $culture = isset($_GET['culture']) ? $_GET['culture'] : '';
    $period = isset($_GET['period']) ? $_GET['period'] : '';

    $query = "SELECT * FROM object, culture WHERE object.culture_id = culture.culture_id";
    if ($name) {
        $query .= " AND object_name LIKE :name";
    }
    if ($materials) {
        $query .= " AND materials LIKE :materials";
    }
    if ($culture) {
        $query .= " AND culture = :culture";
    }
    if ($period) {
        $query .= " AND period = :period";
    }

    $query .= " LIMIT 200";

    $stmt = $conn->prepare($query);

    if ($name) {
        $stmt->bindValue(':name', "%$name%");
    }
    if ($materials) {
        $stmt->bindValue(':materials', "%$materials%");
    }
    if ($culture) {
        $stmt->bindValue(':culture', $culture);
    }
    if ($period) {
        $stmt->bindValue(':period', $period);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $numRows = count($results);

    echo "<!DOCTYPE html>
    <html>
    <head>
    <title>Object Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4e1f4;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            cursor: pointer;
        }
        th {
            background-color: #6a0dad;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
    </head>
    <body>
    <h1>Object Results</h1><h2>$numRows result(s) returned (maximum 200 rows returned per query). Click on any column header to sort the table by that column.</h2>";

    if ($results) {
        echo "<table id='objectTable'>
        <thead>
        <tr>
            <th onclick='sortTable(0)'>Name</th>
            <th onclick='sortTable(1)'>Materials</th>
            <th onclick='sortTable(2)'>Dimensions</th>
            <th onclick='sortTable(3)'>Culture</th>
            <th onclick='sortTable(4)'>Period</th>
        </tr>
        </thead>
        <tbody>";
        foreach ($results as $row) {
            echo "<tr>
                <td>{$row['object_name']}</td>
                <td>{$row['materials']}</td>
                <td>{$row['dimensions']}</td>
                <td>{$row['culture']}</td>
                <td>{$row['period']}</td>
            </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No results found.</p>";
    }

    echo "<script>
    function sortTable(n) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById('objectTable');
        switching = true;
        dir = 'asc'; 
        while (switching) {
            switching = false;
            rows = table.rows;
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName('TD')[n];
                y = rows[i + 1].getElementsByTagName('TD')[n];
                if (dir == 'asc') {
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == 'desc') {
                    if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                switchcount ++; 
            } else {
                if (switchcount == 0 && dir == 'asc') {
                    dir = 'desc';
                    switching = true;
                }
            }
        }
    }
    </script>";

    echo "</body>
    </html>";

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>