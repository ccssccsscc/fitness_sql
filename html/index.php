<?php
$servername = "host.docker.internal";
$username = "root";
$password = "443453";
$database = "fc2";

// Подключение к базе данных
$mysqli = new mysqli($servername, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Вывод списка доступных баз данных
$queryDatabases = "SHOW DATABASES";
$resultDatabases = $mysqli->query($queryDatabases);

if ($resultDatabases) {
    echo "<h2>List of Databases:</h2>";
    echo "<ul>";
    while ($row = $resultDatabases->fetch_array()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
    $resultDatabases->free();
} else {
    echo "Error: " . $mysqli->error;
}

$selectedDatabase = $mysqli->select_db("fc2");

if ($selectedDatabase) {
    $queryTables = "SHOW TABLES";
    $resultTables = $mysqli->query($queryTables);

    if ($resultTables) {
        $tables = array();
        while ($row = $resultTables->fetch_array()) {
            $tables[] = $row[0];
        }

        $tables = array_filter($tables, function ($table) {
            return $table !== 'AuditLog';
        });

        echo "<h2>Tables in bank:</h2>";
        echo "<ul>";
        
        foreach ($tables as $tableName) {
            if ($tableName !== 'auditlog') { // Пропустить таблицу AuditLog
                echo "<li><h3><a href=\"$tableName.php\">$tableName</a></h3>";
                echo "<table border='1'>";
                $queryTableData = "SELECT * FROM $tableName";
                $resultTableData = $mysqli->query($queryTableData);
                if ($resultTableData) {
                    if ($resultTableData->num_rows > 0) {
                        echo "<tr>";
                        $firstRow = $resultTableData->fetch_assoc();
                        foreach ($firstRow as $key => $value) {
                            echo "<th>$key</th>";
                        }
                        echo "</tr>";
        
                        echo "<tr>";
                        foreach ($firstRow as $value) {
                            echo "<td>$value</td>";
                        }
                        echo "</tr>";
        
                        while ($row = $resultTableData->fetch_assoc()) {
                            echo "<tr>";
                            foreach ($row as $value) {
                                echo "<td>$value</td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<p>No data in this table.</p>";
                    }
                    $resultTableData->free();
                } else {
                    echo "Error: " . $mysqli->error;
                }
                echo "</table></li>";
            }
        }
        
        
        echo "<table border='1'>";

        $queryTableData = "SELECT * FROM AuditLog";
        $resultTableData = $mysqli->query($queryTableData);
        if ($resultTableData) {
            if ($resultTableData->num_rows > 0) {
                echo "<tr>";
                $firstRow = $resultTableData->fetch_assoc();
                foreach ($firstRow as $key => $value) {
                    echo "<th>$key</th>";
                }
                echo "</tr>";

                echo "<tr>";
                foreach ($firstRow as $value) {
                    echo "<td>$value</td>";
                }
                echo "</tr>";

                while ($row = $resultTableData->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>$value</td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<p>No data in this table.</p>";
            }
            $resultTableData->free();
        } else {
            echo "Error: " . $mysqli->error;
        }
        echo "</table></li>";
        
        echo "</ul>";
        $resultTables->free();
    } else {
        echo "Error: " . $mysqli->error;
    }
} else {
    echo "Failed to select database fc2: " . $mysqli->error;
}

$mysqli->close();
?>