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
    $number_of_classes = $_POST['number_of_classes'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $client_code = $_POST['client_code'];
    $subscription_code_type_code = $_POST['subscription_code_type_code'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $insertQuery = "INSERT INTO subscription (subscription_code, number_of_classes, price, duration, client_code, subscription_code_type_code, start_date, end_date) VALUES ($subscription_code, $number_of_classes, $price, $duration, $client_code, $subscription_code_type_code, '$start_date', '$end_date')";

    if ($mysqli->query($insertQuery) === TRUE) {
        header('Location: subscription.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_POST['edit'])) {
    $subscription_code = $_POST['subscription_code'];
    $number_of_classes = $_POST['number_of_classes'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $client_code = $_POST['client_code'];
    $subscription_code_type_code = $_POST['subscription_code_type_code'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $updateQuery = "UPDATE subscription SET number_of_classes=$number_of_classes, price=$price, duration=$duration, start_date='$start_date', end_date='$end_date' WHERE subscription_code=$subscription_code AND client_code=$client_code";

    if ($mysqli->query($updateQuery) === TRUE) {
        header('Location: subscription.php');
    } else {
        showError($mysqli->error);
    }
}
if (isset($_POST['checkSubscription'])) {
    $clientCode = $_POST['clientCode'];

    $result = $mysqli->query("SELECT GetActiveSubscriptionsForClient($clientCode) AS subscriptionStatus");
    
    if ($result) {
        $row = $result->fetch_assoc();
        $subscriptionStatus = $row['subscriptionStatus'];
        echo "<p>Subscription Status: $subscriptionStatus</p>";
    } else {
        showError($mysqli->error);
    }
}
if (isset($_GET['delete'])) {
    $subscription_code = $_GET['delete_subscription_code'];
    $client_code = $_GET['delete_client_code'];
    $deleteQuery = "DELETE FROM subscription WHERE subscription_code=$subscription_code AND client_code=$client_code";

    if ($mysqli->query($deleteQuery) === TRUE) {
        header('Location: subscription.php');
    } else {
        showError($mysqli->error);
    }
}

$sortField = isset($_GET['sort']) ? $_GET['sort'] : 'subscription_code';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';
$nextSortOrder = ($sortOrder === 'asc') ? 'desc' : 'asc';

$selectQuery = "SELECT * FROM subscription ORDER BY $sortField $sortOrder";
$result = $mysqli->query($selectQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subscriptions</title>
</head>
<body>
    <h1>Subscriptions</h1>
    
    <p id="result"></p>
    <h2>Add Subscription</h2>
    <form method="post">
        <label>Subscription Code: <input type="text" name="subscription_code"></label><br>
        <label>Number of Classes: <input type="text" name="number_of_classes"></label><br>
        <label>Price: <input type="text" name="price"></label><br>
        <label>Duration: <input type="text" name="duration"></label><br>
        <label>Client Code: <input type="text" name="client_code"></label><br>
        <label>Subscription Code Type Code: <input type="text" name="subscription_code_type_code"></label><br>
        <label>Start Date: <input type="text" name="start_date"></label><br>
        <label>End Date: <input type="text" name="end_date"></label><br>
        <input type="submit" name="add" value="Add Subscription">
    </form>

    <h2>Subscription List</h2>

    <form method="post">
        <label>Client Code: <input type="text" name="clientCode"></label>
        <input type="submit" name="checkSubscription" value="Check Subscriptions">
    </form>
    <form method="get">
        <button type="submit" name="sort" value="subscription_code">Sort by Subscription Code</button>
        <input type="hidden" name="order" value="<?php echo $nextSortOrder; ?>">
    </form>
    <table border="1">
        <tr>
            <th>Subscription Code</th>
            <th>Number of Classes</th>
            <th>Price</th>
            <th>Duration</th>
            <th>Client Code</th>
            <th>Subscription Code Type Code</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['subscription_code'] . "</td>";
                echo "<td>" . $row['number_of_classes'] . "</td>";
                echo "<td>" . $row['price'] . "</td>";
                echo "<td>" . $row['duration'] . "</td>";
                echo "<td>" . $row['client_code'] . "</td>";
                echo "<td>" . $row['subscription_code_type_code'] . "</td>";
                echo "<td>" . $row['start_date'] . "</td>";
                echo "<td>" . $row['end_date'] . "</td>";
                echo "<td><a href='subscription.php?edit_subscription_code=" . $row['subscription_code'] . "&edit_client_code=" . $row['client_code'] . "'>Edit</a> | <a href='subscription.php?delete_subscription_code=" . $row['subscription_code'] . "&delete_client_code=" . $row['client_code'] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No subscriptions found.</td></tr>";
        }
        ?>
    </table>

    <?php
    if (isset($_GET['edit_subscription_code'])) {
        $edit_subscription_code = $_GET['edit_subscription_code'];
        $edit_client_code = $_GET['edit_client_code'];
        $editQuery = "SELECT * FROM subscription WHERE subscription_code=$edit_subscription_code AND client_code=$edit_client_code";
        $editResult = $mysqli->query($editQuery);
        if ($editResult->num_rows > 0) {
            $editRow = $editResult->fetch_assoc();
            ?>
            <h2>Edit Subscription</h2>
            <form method="post">
                <input type="hidden" name="subscription_code" value="<?php echo $editRow['subscription_code']; ?>">
                <input type="hidden" name="client_code" value="<?php echo $editRow['client_code']; ?>">
                <label>Number of Classes: <input type="text" name="number_of_classes" value="<?php echo $editRow['number_of_classes']; ?>"></label><br>
                <label>Price: <input type="text" name="price" value="<?php echo $editRow['price']; ?>"></label><br>
                <label>Duration: <input type="text" name="duration" value="<?php echo $editRow['duration']; ?>"></label><br>
                <label>Start Date: <input type="text" name="start_date" value="<?php echo $editRow['start_date']; ?>"></label><br>
                <label>End Date: <input type="text" name="end_date" value="<?php echo $editRow['end_date']; ?>"></label><br>
                <input type="submit" name="edit" value="Update Subscription">
            </form>
        <?php
        }
    }
    ?>

    <a href="index.php">Back to Home</a>
</body>
</html>