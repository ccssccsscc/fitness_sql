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
    $class_id = $_POST['class_id'];
    $type_code = $_POST['type_code'];
    $description = $_POST['description'];
    $time = $_POST['time'];
    $status = $_POST['status'];
    $subscription_code = $_POST['subscription_code'];
    $client_code = $_POST['client_code'];
    $visit_code = $_POST['visit_code'];
    $coach_code = $_POST['coach_code'];

    $insertQuery = "INSERT INTO class (class_id, type_code, description, time, status, subscription_code, client_code, visit_code, coach_code) VALUES ($class_id, $type_code, '$description', '$time', '$status', $subscription_code, $client_code, $visit_code, $coach_code)";

    if ($mysqli->query($insertQuery) === TRUE) {
        header('Location: class.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_POST['edit'])) {
    $class_id = $_POST['class_id'];
    $type_code = $_POST['type_code'];
    $description = $_POST['description'];
    $time = $_POST['time'];
    $status = $_POST['status'];
    $subscription_code = $_POST['subscription_code'];
    $client_code = $_POST['client_code'];
    $visit_code = $_POST['visit_code'];
    $coach_code = $_POST['coach_code'];

    $updateQuery = "UPDATE class SET type_code=$type_code, description='$description', time='$time', status='$status', subscription_code=$subscription_code, client_code=$client_code, visit_code=$visit_code, coach_code=$coach_code WHERE class_id='$class_id'";

    if ($mysqli->query($updateQuery) === TRUE) {
        header('Location: class.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_GET['delete'])) {
    $class_id = $_GET['delete'];
    $deleteQuery = "DELETE FROM class WHERE class_id='$class_id'";

    if ($mysqli->query($deleteQuery) === TRUE) {
        header('Location: class.php');
    } else {
        showError($mysqli->error);
    }
}

$sortField = isset($_GET['sort']) ? $_GET['sort'] : 'class_id';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';
$nextSortOrder = ($sortOrder === 'asc') ? 'desc' : 'asc';

$selectQuery = "SELECT * FROM class ORDER BY $sortField $sortOrder";
$result = $mysqli->query($selectQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class</title>
</head>
<body>
    <h1>Class</h1>

    <h2>Add Class</h2>
    <form method="post">
        <label>Class ID: <input type="text" name="class_id"></label><br>
        <label>Type Code: <input type="text" name="type_code"></label><br>
        <label>Description: <input type="text" name="description"></label><br>
        <label>Time: <input type="text" name="time"></label><br>
        <label>Status: <input type="text" name="status"></label><br>
        <label>Subscription Code: <input type="text" name="subscription_code"></label><br>
        <label>Client Code: <input type="text" name="client_code"></label><br>
        <label>Visit Code: <input type="text" name="visit_code"></label><br>
        <label>Coach Code: <input type="text" name="coach_code"></label><br>
        <input type="submit" name="add" value="Add Class">
    </form>

    <h2>Class List</h2>
    <form method="get">
        <button type="submit" name="sort" value="class_id">Sort by Class ID</button>
        <input type="hidden" name="order" value="<?php echo $nextSortOrder; ?>">
    </form>
    <table border="1">
        <tr>
            <th>Class ID</th>
            <th>Type Code</th>
            <th>Description</th>
            <th>Time</th>
            <th>Status</th>
            <th>Subscription Code</th>
            <th>Client Code</th>
            <th>Visit Code</th>
            <th>Coach Code</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['class_id'] . "</td>";
                echo "<td>" . $row['type_code'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['time'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>" . $row['subscription_code'] . "</td>";
                echo "<td>" . $row['client_code'] . "</td>";
                echo "<td>" . $row['visit_code'] . "</td>";
                echo "<td>" . $row['coach_code'] . "</td>";
                echo "<td><a href='class.php?edit=" . $row['class_id'] . "'>Edit</a> | <a href='class.php?delete=" . $row['class_id'] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No class found.</td></tr>";
        }
        ?>
    </table>

    <?php
    if (isset($_GET['edit'])) {
        $class_id = $_GET['edit'];
        $editQuery = "SELECT * FROM class WHERE class_id='$class_id'";
        $editResult = $mysqli->query($editQuery);
        if ($editResult->num_rows > 0) {
            $editRow = $editResult->fetch_assoc();
            ?>
            <h2>Edit Class</h2>
            <form method="post">
                <input type="hidden" name="class_id" value="<?php echo $editRow['class_id']; ?>">
                <label>Type Code: <input type="text" name="type_code" value="<?php echo $editRow['type_code']; ?>"></label><br>
                <label>Description: <input type="text" name="description" value="<?php echo $editRow['description']; ?>"></label><br>
                <label>Time: <input type="text" name="time" value="<?php echo $editRow['time']; ?>"></label><br>
                <label>Status: <input type="text" name="status" value="<?php echo $editRow['status']; ?>"></label><br>
                <label>Subscription Code: <input type="text" name="subscription_code" value="<?php echo $editRow['subscription_code']; ?>"></label><br>
                <label>Client Code: <input type="text" name="client_code" value="<?php echo $editRow['client_code']; ?>"></label><br>
                <label>Visit Code: <input type="text" name="visit_code" value="<?php echo $editRow['visit_code']; ?>"></label><br>
                <label>Coach Code: <input type="text" name="coach_code" value="<?php echo $editRow['coach_code']; ?>"></label><br>
                <input type="submit" name="edit" value="Update Class">
            </form>
        <?php
        }
    }
    ?>

    <a href="index.php">Back to Home</a>
</body>
</html>