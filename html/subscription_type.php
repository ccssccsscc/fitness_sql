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
    $type_name = $_POST['type'];

    $insertQuery = "INSERT INTO subscription_type (type) VALUES ('$type_name')";

    if ($mysqli->query($insertQuery) === TRUE) {
        header('Location: subscription_type.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_POST['edit'])) {
    $type_code = $_POST['type_code'];
    $type_name = $_POST['type'];

    $updateQuery = "UPDATE subscription_type SET type='$type_name' WHERE subscription_code_type_code=$type_code";

    if ($mysqli->query($updateQuery) === TRUE) {
        header('Location: subscription_type.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_GET['delete_type_code'])) {
    $type_code = $_GET['delete_type_code'];
    $deleteQuery = "DELETE FROM subscription_type WHERE subscription_code_type_code=$type_code";

    if ($mysqli->query($deleteQuery) === TRUE) {
        header('Location: subscription_type.php');
    } else {
        showError($mysqli->error);
    }
}

$sortField = isset($_GET['sort']) ? $_GET['sort'] : 'subscription_code_type_code';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';
$nextSortOrder = ($sortOrder === 'asc') ? 'desc' : 'asc';

$selectQuery = "SELECT * FROM subscription_type ORDER BY $sortField $sortOrder";
$result = $mysqli->query($selectQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subscription Types</title>
</head>
<body>
    <h1>Subscription Types</h1>

    <h2>Add Subscription Type</h2>
    <form method="post">
        <label>Type: <input type="text" name="type"></label><br>
        <input type="submit" name="add" value="Add Subscription Type">
    </form>

    <h2>Subscription Types List</h2>
    <form method="get">
        <button type="submit" name="sort" value="subscription_code_type_code">Sort by Type Code</button>
        <input type="hidden" name="order" value="<?php echo $nextSortOrder; ?>">
    </form>
    <table border="1">
        <tr>
            <th>Type Code</th>
            <th>Type</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['subscription_code_type_code'] . "</td>";
                echo "<td>" . $row['type'] . "</td>";
                echo "<td><a href='subscription_type.php?edit_type_code=" . $row['subscription_code_type_code'] . "'>Edit</a> | <a href='subscription_type.php?delete_type_code=" . $row['subscription_code_type_code'] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No subscription types found.</td></tr>";
        }
        ?>
    </table>

    <?php
    if (isset($_GET['edit_type_code'])) {
        $edit_type_code = $_GET['edit_type_code'];
        $editQuery = "SELECT * FROM subscription_type WHERE subscription_code_type_code=$edit_type_code";
        $editResult = $mysqli->query($editQuery);
        if ($editResult->num_rows > 0) {
            $editRow = $editResult->fetch_assoc();
            ?>
            <h2>Edit Subscription Type</h2>
            <form method="post">
                <input type="hidden" name="type_code" value="<?php echo $editRow['subscription_code_type_code']; ?>">
                <label>Type: <input type="text" name="type" value="<?php echo $editRow['type']; ?>"></label><br>
                <input type="submit" name="edit" value="Update Subscription Type">
            </form>
        <?php
        }
    }
    ?>

    <a href="index.php">Back to Home</a>
</body>
</html>