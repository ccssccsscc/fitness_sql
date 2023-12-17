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
    $specification_code = $_POST['specification_code'];
    $name = $_POST['name'];

    $insertQuery = "INSERT INTO coach (specification_code, name) VALUES ($specification_code, '$name')";

    if ($mysqli->query($insertQuery) === TRUE) {
        header('Location: coach.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_POST['edit'])) {
    $coach_code = $_POST['coach_code'];
    $specification_code = $_POST['specification_code'];
    $name = $_POST['name'];

    $updateQuery = "UPDATE coach SET specification_code=$specification_code, name='$name' WHERE coach_code=$coach_code";

    if ($mysqli->query($updateQuery) === TRUE) {
        header('Location: coach.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_GET['delete'])) {
    $coach_code = $_GET['delete'];
    $deleteQuery = "DELETE FROM coach WHERE coach_code=$coach_code";

    if ($mysqli->query($deleteQuery) === TRUE) {
        header('Location: coach.php');
    } else {
        showError($mysqli->error);
    }
}

$sortField = isset($_GET['sort']) ? $_GET['sort'] : 'coach_code';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';
$nextSortOrder = ($sortOrder === 'asc') ? 'desc' : 'asc';

$selectQuery = "SELECT * FROM coach ORDER BY $sortField $sortOrder";
$result = $mysqli->query($selectQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Coaches</title>
</head>
<body>
    <h1>Coaches</h1>

    <h2>Add Coach</h2>
    <form method="post">
        <label>Specification Code: <input type="text" name="specification_code"></label><br>
        <label>Name: <input type="text" name="name"></label><br>
        <input type="submit" name="add" value="Add Coach">
    </form>

    <h2>Coach List</h2>
    <form method="get">
        <button type="submit" name="sort" value="coach_code">Sort by Coach Code</button>
        <input type="hidden" name="order" value="<?php echo $nextSortOrder; ?>">
    </form>
    <table border="1">
        <tr>
            <th>Coach Code</th>
            <th>Specification Code</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['coach_code'] . "</td>";
                echo "<td>" . $row['specification_code'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td><a href='coach.php?edit=" . $row['coach_code'] . "'>Edit</a> | <a href='coach.php?delete=" . $row['coach_code'] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No coaches found.</td></tr>";
        }
        ?>
    </table>

    <?php
    if (isset($_GET['edit'])) {
        $coach_code = $_GET['edit'];
        $editQuery = "SELECT * FROM coach WHERE coach_code=$coach_code";
        $editResult = $mysqli->query($editQuery);
        if ($editResult->num_rows > 0) {
            $editRow = $editResult->fetch_assoc();
            ?>
            <h2>Edit Coach</h2>
            <form method="post">
                <input type="hidden" name="coach_code" value="<?php echo $editRow['coach_code']; ?>">
                <label>Specification Code: <input type="text" name="specification_code" value="<?php echo $editRow['specification_code']; ?>"></label><br>
                <label>Name: <input type="text" name="name" value="<?php echo $editRow['name']; ?>"></label><br>
                <input type="submit" name="edit" value="Update Coach">
            </form>
        <?php
        }
    }
    ?>

    <a href="index.php">Back to Home</a>
</body>
</html>