<?php

$serverName = "sql.cs.oberlin.edu";
$dbName = "mstinson";
$user = "mstinson";
$pw = "Oberlin@123";

try {
    $conn = new PDO("mysql:host=$serverName;dbname=$dbName", $user, $pw);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $title = isset($_GET['title']) ? $_GET['title'] : '';
    $artistName = isset($_GET['artistName']) ? $_GET['artistName'] : '';
    $donorName = isset($_GET['donorName']) ? $_GET['donorName'] : '';
    $classification = isset($_GET['classification']) ? $_GET['classification'] : '';
    $department = isset($_GET['department']) ? $_GET['department'] : '';
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

    $query = "SELECT * FROM art_piece, art_source, artwork_recieved, department WHERE art_piece.accession_id = artwork_recieved.accession_id AND artwork_recieved.donor_id = art_source.donor_id AND art_piece.department_id = department.department_id";
    if ($title) {
        $query .= " AND title LIKE :title";
    }
    if ($artistName) {
        $query .= " AND maker LIKE :artistName";
    }
    if ($donorName) {
        $query .= " AND credit_line LIKE :donorName";
    }
    if ($classification) {
        $query .= " AND classification = :classification";
    }
    if ($department) {
        $query .= " AND department_name = :department";
    }
    if ($startDate) {
        $query .= " AND century >= :startDate";
    }
    if ($endDate) {
        $query .= " AND century <= :endDate";
    }

    $query .= " LIMIT 200";

    $stmt = $conn->prepare($query);

    if ($title) {
        $stmt->bindValue(':title', "%$title%");
    }
    if ($artistName) {
        $stmt->bindValue(':artistName', "%$artistName%");
    }
    if ($donorName) {
        $stmt->bindValue(':donorName', "%$donorName%");
    }
    if ($classification) {
        $stmt->bindValue(':classification', $classification);
    }
    if ($department) {
        $stmt->bindValue(':department', $department);
    }
    if ($startDate) {
        $stmt->bindValue(':startDate', (int)$startDate);
    }
    if ($endDate) {
        $stmt->bindValue(':endDate', (int)$endDate);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $numRows = count($results);

    echo "<!DOCTYPE html>
    <html>
    <head>
    <title>Art Pieces Results</title>
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
    <h1>Art Pieces Results</h1><h2>$numRows result(s) returned (maximum 200 rows returned per query). Click on any column header to sort the table by that column.</h2>";

    if ($results) {
        echo "<table id='artPiecesTable'>
        <thead>
        <tr>
            <th onclick='sortTable(0)'>Title</th>
            <th onclick='sortTable(1)'>Artist Name</th>
            <th onclick='sortTable(2)'>Donor Name</th>
            <th onclick='sortTable(3)'>Classification</th>
            <th onclick='sortTable(4)'>Department</th>
            <th onclick='sortTable(5)'>Century</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody>";
        foreach ($results as $row) {
            echo "<tr>
                <td>{$row['title']}</td>
                <td>{$row['maker']}</td>
                <td>{$row['credit_line']}</td>
                <td>{$row['classification']}</td>
                <td>{$row['department_name']}</td>
                <td>{$row['century']}</td>
                <td>{$row['label_text']}</td>
            </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No results found.</p>";
    }

    echo "<script>
    function sortTable(n) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById('artPiecesTable');
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