<?php
$servername = "host.docker.internal";
$username = "root";
$password = "443453";
$database = "fc2";

$mysqli = new mysqli($servername, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

function showError($error) {
    echo "<p style='color: red;'>Error: $error</p>";
}

if (isset($_POST['add'])) {
    $visit_code = $_POST['visit_code'];
    $client_code = $_POST['client_code'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $insertQuery = "INSERT INTO visit_to_fitness_club (visit_code, client_code, date, start_time, end_time) VALUES ($visit_code, $client_code, '$date', '$start_time', '$end_time')";

    if ($mysqli->query($insertQuery) === TRUE) {
        header('Location: visit_to_fitness_club.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_POST['edit'])) {
    $visit_code = $_POST['visit_code'];
    $client_code = $_POST['client_code'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $updateQuery = "UPDATE visit_to_fitness_club SET date='$date', start_time='$start_time', end_time='$end_time' WHERE visit_code=$visit_code AND client_code=$client_code";

    if ($mysqli->query($updateQuery) === TRUE) {
        header('Location: visit_to_fitness_club.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_GET['delete'])) {
    $visit_code = $_GET['delete_visit_code'];
    $client_code = $_GET['delete_client_code'];
    $deleteQuery = "DELETE FROM visit_to_fitness_club WHERE visit_code=$visit_code AND client_code=$client_code";

    if ($mysqli->query($deleteQuery) === TRUE) {
        header('Location: visit_to_fitness_club.php');
    } else {
        showError($mysqli->error);
    }
}

$sortField = isset($_GET['sort']) ? $_GET['sort'] : 'visit_code';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';
$nextSortOrder = ($sortOrder === 'asc') ? 'desc' : 'asc';

$selectQuery = "SELECT * FROM visit_to_fitness_club ORDER BY $sortField $sortOrder";
$result = $mysqli->query($selectQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visits to Fitness Club</title>
</head>
<body>
    <h1>Visits to Fitness Club</h1>

    <h2>Add Visit</h2>
    <form method="post">
        <label>Visit Code: <input type="text" name="visit_code"></label><br>
        <label>Client Code: <input type="text" name="client_code"></label><br>
        <label>Date: <input type="text" name="date"></label><br>
        <label>Start Time: <input type="text" name="start_time"></label><br>
        <label>End Time: <input type="text" name="end_time"></label><br>
        <input type="submit" name="add" value="Add Visit">
    </form>

    <h2>Visit List</h2>
    <form method="get">
        <button type="submit" name="sort" value="visit_code">Sort by Visit Code</button>
        <input type="hidden" name="order" value="<?php echo $nextSortOrder; ?>">
    </form>
    <table border="1">
        <tr>
            <th>Visit Code</th>
            <th>Client Code</th>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['visit_code'] . "</td>";
                echo "<td>" . $row['client_code'] . "</td>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>" . $row['start_time'] . "</td>";
                echo "<td>" . $row['end_time'] . "</td>";
                echo "<td><a href='visit_to_fitness_club.php?edit_visit_code=" . $row['visit_code'] . "&edit_client_code=" . $row['client_code'] . "'>Edit</a> | <a href='visit_to_fitness_club.php?delete_visit_code=" . $row['visit_code'] . "&delete_client_code=" . $row['client_code'] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No visits found.</td></tr>";
        }
        ?>
    </table>

    <?php
    if (isset($_GET['edit_visit_code'])) {
        $edit_visit_code = $_GET['edit_visit_code'];
        $edit_client_code = $_GET['edit_client_code'];
        $editQuery = "SELECT * FROM visit_to_fitness_club WHERE visit_code=$edit_visit_code AND client_code=$edit_client_code";
        $editResult = $mysqli->query($editQuery);
        if ($editResult->num_rows > 0) {
            $editRow = $editResult->fetch_assoc();
            ?>
            <h2>Edit Visit</h2>
            <form method="post">
                <input type="hidden" name="visit_code" value="<?php echo $editRow['visit_code']; ?>">
                <input type="hidden" name="client_code" value="<?php echo $editRow['client_code']; ?>">
                <label>Date: <input type="text" name="date" value="<?php echo $editRow['date']; ?>"></label><br>
                <label>Start Time: <input type="text" name="start_time" value="<?php echo $editRow['start_time']; ?>"></label><br>
                <label>End Time: <input type="text" name="end_time" value="<?php echo $editRow['end_time']; ?>"></label><br>
                <input type="submit" name="edit" value="Update Visit">
            </form>
        <?php
        }
    }
    ?>

    <a href="index.php">Back to Home</a>
</body>
</html>