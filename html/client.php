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
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $age = $_POST['age'];
    $phone = $_POST['phone'];
    $review_code = $_POST['review_code'];
    $gender = $_POST['gender'];

    $insertQuery = "INSERT INTO client (first_name, last_name, email, address, age, phone, review_code, gender) VALUES ('$first_name', '$last_name', '$email', '$address', $age, '$phone', $review_code, '$gender')";

    if ($mysqli->query($insertQuery) === TRUE) {
        header('Location: client.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_POST['edit'])) {
    $client_code = $_POST['client_code'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $age = $_POST['age'];
    $phone = $_POST['phone'];
    $review_code = $_POST['review_code'];
    $gender = $_POST['gender'];

    $updateQuery = "UPDATE client SET first_name='$first_name', last_name='$last_name', email='$email', address='$address', age=$age, phone='$phone', review_code=$review_code, gender='$gender' WHERE client_code='$client_code'";

    if ($mysqli->query($updateQuery) === TRUE) {
        header('Location: client.php');
    } else {
        showError($mysqli->error);
    }
}

if (isset($_GET['delete'])) {
    $client_code = $_GET['delete'];
    $deleteQuery = "DELETE FROM client WHERE client_code='$client_code'";

    if ($mysqli->query($deleteQuery) === TRUE) {
        header('Location: client.php');
    } else {
        showError($mysqli->error);
    }
}

$sortField = isset($_GET['sort']) ? $_GET['sort'] : 'client_code';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';
$nextSortOrder = ($sortOrder === 'asc') ? 'desc' : 'asc';

$selectQuery = "SELECT * FROM client ORDER BY $sortField $sortOrder";
$result = $mysqli->query($selectQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clients</title>
</head>
<body>
    <h1>Clients</h1>

    <h2>Add Client</h2>
    <form method="post">
        <label>First Name: <input type="text" name="first_name"></label><br>
        <label>Last Name: <input type="text" name="last_name"></label><br>
        <label>Email: <input type="text" name="email"></label><br>
        <label>Address: <input type="text" name="address"></label><br>
        <label>Age: <input type="text" name="age"></label><br>
        <label>Phone: <input type="text" name="phone"></label><br>
        <label>Review Code: <input type="text" name="review_code"></label><br>
        <label>Gender: <input type="text" name="gender"></label><br>
        <input type="submit" name="add" value="Add Client">
    </form>

    <h2>Client List</h2>
    <form method="get">
        <button type="submit" name="sort" value="client_code">Sort by Client Code</button>
        <input type="hidden" name="order" value="<?php echo $nextSortOrder; ?>">
    </form>
    <table border="1">
        <tr>
            <th>Client Code</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Address</th>
            <th>Age</th>
            <th>Phone</th>
            <th>Review Code</th>
            <th>Gender</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['client_code'] . "</td>";
                echo "<td>" . $row['first_name'] . "</td>";
                echo "<td>" . $row['last_name'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['address'] . "</td>";
                echo "<td>" . $row['age'] . "</td>";
                echo "<td>" . $row['phone'] . "</td>";
                echo "<td>" . $row['review_code'] . "</td>";
                echo "<td>" . $row['gender'] . "</td>";
                echo "<td><a href='client.php?edit=" . $row['client_code'] . "'>Edit</a> | <a href='client.php?delete=" . $row['client_code'] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No clients found.</td></tr>";
        }
        ?>
    </table>

    <?php
    if (isset($_GET['edit'])) {
        $client_code = $_GET['edit'];
        $editQuery = "SELECT * FROM client WHERE client_code='$client_code'";
        $editResult = $mysqli->query($editQuery);
        if ($editResult->num_rows > 0) {
            $editRow = $editResult->fetch_assoc();
            ?>
            <h2>Edit Client</h2>
            <form method="post">
                <input type="hidden" name="client_code" value="<?php echo $editRow['client_code']; ?>">
                <label>First Name: <input type="text" name="first_name" value="<?php echo $editRow['first_name']; ?>"></label><br>
                <label>Last Name: <input type="text" name="last_name" value="<?php echo $editRow['last_name']; ?>"></label><br>
                <label>Email: <input type="text" name="email" value="<?php echo $editRow['email']; ?>"></label><br>
                <label>Address: <input type="text" name="address" value="<?php echo $editRow['address']; ?>"></label><br>
                <label>Age: <input type="text" name="age" value="<?php echo $editRow['age']; ?>"></label><br>
                <label>Phone: <input type="text" name="phone" value="<?php echo $editRow['phone']; ?>"></label><br>
                <label>Review Code: <input type="text" name="review_code" value="<?php echo $editRow['review_code']; ?>"></label><br>
                <label>Gender: <input type="text" name="gender" value="<?php echo $editRow['gender']; ?>"></label><br>
                <input type="submit" name="edit" value="Update Client">
            </form>
        <?php
        }
    }
    ?>

    <a href="index.php">Back to Home</a>
</body>
</html>