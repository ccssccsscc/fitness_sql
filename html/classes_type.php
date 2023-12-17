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
    $type_name = $_POST['type_name'];

    $insertQuery = "INSERT INTO classes_type (type_name) VALUES ('$type_name')";

    if ($mysqli->query($insertQuery) === TRUE) {
        header('Location: classes_type.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_POST['edit'])) {
    $type_code = $_POST['type_code'];
    $type_name = $_POST['type_name'];

    $updateQuery = "UPDATE classes_type SET type_name='$type_name' WHERE type_code='$type_code'";

    if ($mysqli->query($updateQuery) === TRUE) {
        header('Location: classes_type.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_GET['delete'])) {
    $type_code = $_GET['delete'];
    $deleteQuery = "DELETE FROM classes_type WHERE type_code='$type_code'";

    if ($mysqli->query($deleteQuery) === TRUE) {
        header('Location: classes_type.php');
    } else {
        showError($mysqli->error);
    }
}

$sortField = isset($_GET['sort']) ? $_GET['sort'] : 'type_code';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';
$nextSortOrder = ($sortOrder === 'asc') ? 'desc' : 'asc';

$selectQuery = "SELECT * FROM classes_type ORDER BY $sortField $sortOrder";
$result = $mysqli->query($selectQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Classes Type</title>
</head>
<body>
    <h1>Classes Type</h1>

    <h2>Add Class Type</h2>
    <form method="post">
        <label>Type Name: <input type="text" name="type_name"></label><br>
        <input type="submit" name="add" value="Add Class Type">
    </form>

    <h2>Class Type List</h2>
    <form method="get">
        <button type="submit" name="sort" value="type_code">Sort by Type Code</button>
        <input type="hidden" name="order" value="<?php echo $nextSortOrder; ?>">
    </form>
    <table border="1">
        <tr>
            <th>Type Code</th>
            <th>Type Name</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['type_code'] . "</td>";
                echo "<td>" . $row['type_name'] . "</td>";
                echo "<td><a href='classes_type.php?edit=" . $row['type_code'] . "'>Edit</a> | <a href='classes_type.php?delete=" . $row['type_code'] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No class types found.</td></tr>";
        }
        ?>
    </table>

    <?php
    if (isset($_GET['edit'])) {
        $type_code = $_GET['edit'];
        $editQuery = "SELECT * FROM classes_type WHERE type_code='$type_code'";
        $editResult = $mysqli->query($editQuery);
        if ($editResult->num_rows > 0) {
            $editRow = $editResult->fetch_assoc();
            ?>
            <h2>Edit Class Type</h2>
            <form method="post">
                <input type="hidden" name="type_code" value="<?php echo $editRow['type_code']; ?>">
                <label>Type Name: <input type="text" name="type_name" value="<?php echo $editRow['type_name']; ?>"></label><br>
                <input type="submit" name="edit" value="Update Class Type">
            </form>
        <?php
        }
    }
    ?>

    <a href="index.php">Back to Home</a>
</body>
</html>