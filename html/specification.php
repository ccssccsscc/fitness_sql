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
    $specification_name = $_POST['specification_name'];

    $insertQuery = "INSERT INTO specification (specification_name) VALUES ('$specification_name')";

    if ($mysqli->query($insertQuery) === TRUE) {
        header('Location: specification.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_POST['edit'])) {
    $specification_code = $_POST['specification_code'];
    $specification_name = $_POST['specification_name'];

    $updateQuery = "UPDATE specification SET specification_name='$specification_name' WHERE specification_code=$specification_code";

    if ($mysqli->query($updateQuery) === TRUE) {
        header('Location: specification.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_GET['delete'])) {
    $specification_code = $_GET['delete'];
    $deleteQuery = "DELETE FROM specification WHERE specification_code=$specification_code";

    if ($mysqli->query($deleteQuery) === TRUE) {
        header('Location: specification.php');
    } else {
        showError($mysqli->error);
    }
}

$sortField = isset($_GET['sort']) ? $_GET['sort'] : 'specification_code';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';
$nextSortOrder = ($sortOrder === 'asc') ? 'desc' : 'asc';

$selectQuery = "SELECT * FROM specification ORDER BY $sortField $sortOrder";
$result = $mysqli->query($selectQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Specifications</title>
</head>
<body>
    <h1>Specifications</h1>

    <h2>Add Specification</h2>
    <form method="post">
        <label>Specification Name: <input type="text" name="specification_name"></label><br>
        <input type="submit" name="add" value="Add Specification">
    </form>

    <h2>Specification List</h2>
    <form method="get">
        <button type="submit" name="sort" value="specification_code">Sort by Specification Code</button>
        <input type="hidden" name="order" value="<?php echo $nextSortOrder; ?>">
    </form>
    <table border="1">
        <tr>
            <th>Specification Code</th>
            <th>Specification Name</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['specification_code'] . "</td>";
                echo "<td>" . $row['specification_name'] . "</td>";
                echo "<td><a href='specification.php?edit=" . $row['specification_code'] . "'>Edit</a> | <a href='specification.php?delete=" . $row['specification_code'] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No specifications found.</td></tr>";
        }
        ?>
    </table>

    <?php
    if (isset($_GET['edit'])) {
        $specification_code = $_GET['edit'];
        $editQuery = "SELECT * FROM specification WHERE specification_code=$specification_code";
        $editResult = $mysqli->query($editQuery);
        if ($editResult->num_rows > 0) {
            $editRow = $editResult->fetch_assoc();
            ?>
            <h2>Edit Specification</h2>
            <form method="post">
                <input type="hidden" name="specification_code" value="<?php echo $editRow['specification_code']; ?>">
                <label>Specification Name: <input type="text" name="specification_name" value="<?php echo $editRow['specification_name']; ?>"></label><br>
                <input type="submit" name="edit" value="Update Specification">
            </form>
        <?php
        }
    }
    ?>

    <a href="index.php">Back to Home</a>
</body>
</html>