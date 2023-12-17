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
    $subscription_code = $_POST['subscription_code'];
    $client_code = $_POST['client_code'];
    $info_about_subs = $_POST['info_about_subs'];

    $insertQuery = "INSERT INTO subscription_options (subscription_code, client_code, info_about_subs) VALUES ($subscription_code, $client_code, '$info_about_subs')";

    if ($mysqli->query($insertQuery) === TRUE) {
        header('Location: subscription_options.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_POST['edit'])) {
    $subscription_code = $_POST['subscription_code'];
    $client_code = $_POST['client_code'];
    $info_about_subs = $_POST['info_about_subs'];

    $updateQuery = "UPDATE subscription_options SET info_about_subs='$info_about_subs' WHERE subscription_code=$subscription_code AND client_code=$client_code";

    if ($mysqli->query($updateQuery) === TRUE) {
        header('Location: subscription_options.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_GET['delete'])) {
    $subscription_code = $_GET['delete_subscription_code'];
    $client_code = $_GET['delete_client_code'];
    $deleteQuery = "DELETE FROM subscription_options WHERE subscription_code=$subscription_code AND client_code=$client_code";

    if ($mysqli->query($deleteQuery) === TRUE) {
        header('Location: subscription_options.php');
    } else {
        showError($mysqli->error);
    }
}

$sortField = isset($_GET['sort']) ? $_GET['sort'] : 'subscription_code';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';
$nextSortOrder = ($sortOrder === 'asc') ? 'desc' : 'asc';

$selectQuery = "SELECT * FROM subscription_options ORDER BY $sortField $sortOrder";
$result = $mysqli->query($selectQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subscription Options</title>
</head>
<body>
    <h1>Subscription Options</h1>

    <h2>Add Subscription Option</h2>
    <form method="post">
        <label>Subscription Code: <input type="text" name="subscription_code"></label><br>
        <label>Client Code: <input type="text" name="client_code"></label><br>
        <label>Info about Subscription: <input type="text" name="info_about_subs"></label><br>
        <input type="submit" name="add" value="Add Subscription Option">
    </form>

    <h2>Subscription Options List</h2>
    <form method="get">
        <button type="submit" name="sort" value="subscription_code">Sort by Subscription Code</button>
        <input type="hidden" name="order" value="<?php echo $nextSortOrder; ?>">
    </form>
    <table border="1">
        <tr>
            <th>Subscription Code</th>
            <th>Client Code</th>
            <th>Info about Subscription</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['subscription_code'] . "</td>";
                echo "<td>" . $row['client_code'] . "</td>";
                echo "<td>" . $row['info_about_subs'] . "</td>";
                echo "<td><a href='subscription_options.php?edit_subscription_code=" . $row['subscription_code'] . "&edit_client_code=" . $row['client_code'] . "'>Edit</a> | <a href='subscription_options.php?delete_subscription_code=" . $row['subscription_code'] . "&delete_client_code=" . $row['client_code'] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No subscription options found.</td></tr>";
        }
        ?>
    </table>

    <?php
    if (isset($_GET['edit_subscription_code']) && isset($_GET['edit_client_code'])) {
        $edit_subscription_code = $_GET['edit_subscription_code'];
        $edit_client_code = $_GET['edit_client_code'];
        $editQuery = "SELECT * FROM subscription_options WHERE subscription_code=$edit_subscription_code AND client_code=$edit_client_code";
        $editResult = $mysqli->query($editQuery);
        if ($editResult->num_rows > 0) {
            $editRow = $editResult->fetch_assoc();
            ?>
            <h2>Edit Subscription Option</h2>
            <form method="post">
                <input type="hidden" name="subscription_code" value="<?php echo $editRow['subscription_code']; ?>">
                <input type="hidden" name="client_code" value="<?php echo $editRow['client_code']; ?>">
                <label>Info about Subscription: <input type="text" name="info_about_subs" value="<?php echo $editRow['info_about_subs']; ?>"></label><br>
                <input type="submit" name="edit" value="Update Subscription Option">
            </form>
        <?php
        }
    }
    ?>

    <a href="index.php">Back to Home</a>
</body>
</html>