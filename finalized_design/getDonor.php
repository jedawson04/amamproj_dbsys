<?php

$serverName = "sql.cs.oberlin.edu";
$dbName = "mstinson";
$user = "mstinson";
$pw = "Oberlin@123";

try {
    $conn = new PDO("mysql:host=$serverName;dbname=$dbName", $user, $pw);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $name = isset($_GET['name']) ? $_GET['name'] : '';
    $after = $_GET['after'];
    $before = $_GET['before'];

    $query = "SELECT credit_line, count(credit_line) as num FROM art_source, artwork_recieved WHERE art_source.donor_id = artwork_recieved.donor_id";
    if ($name) {
        $query .= " AND credit_line LIKE :name";
    }
    if ($after) {
        $query .= " AND year_received >= :after";
    }
    if ($before) {
        $query .= " AND year_received <= :before";
    }

    $query .= " GROUP BY credit_line LIMIT 200";

    $stmt = $conn->prepare($query);

    if ($name) {
        $stmt->bindValue(':name', "%$name%");
    }
    if ($after) {
        $stmt->bindValue(':after', $after);
    }
    if ($before) {
        $stmt->bindValue(':before', $before);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $numRows = count($results);

    echo "<!DOCTYPE html>
    <html>
    <head>
    <title>Donor Results</title>
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
    <h1>Donor Results</h1><h2>$numRows result(s) returned (maximum 200 rows returned per query). Click on any column header to sort the table by that column.</h2>";

    if ($results) {
        echo "<table id='donorTable'>
        <thead>
        <tr>
            <th onclick='sortTable(0)'>Donor</th>
            <th onclick='sortTable(1)'>Number of Donations</th>
        </tr>
        </thead>
        <tbody>";
        foreach ($results as $row) {
            echo "<tr>
                <td>{$row['credit_line']}</td>
                <td>{$row['num']}</td>
            </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No results found.</p>";
    }

    echo "<script>
    function sortTable(n) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById('donorTable');
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